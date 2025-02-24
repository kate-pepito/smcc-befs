<?php

header('Content-Type: application/json');

class ConnectionException extends Exception {
    public function __construct($message = "Connection error occurred", $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

class FTPException extends Exception {
    public function __construct($message = "FTP error occurred", $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

class HTTPRequestException extends Exception {
    public function __construct($message = "Invalid HTTP Request Method", $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

function deleteDirectory($dir) {
    if (!is_dir($dir)) return;
    foreach (array_diff(scandir($dir), ['.', '..']) as $file) {
        $path = "$dir/$file";
        is_dir($path) ? deleteDirectory($path) : unlink($path);
    }
    rmdir($dir);
}

function strEndsWith($string, $endString) {
    $len = strlen($endString);
    if ($len == 0) {
        return true;
    }
    return (substr($string, -$len) === $endString);
}

function makeDirIfNotExists($basepath, $dirpath) {
    $baseDir = rtrim($basepath, DIRECTORY_SEPARATOR);

    if (!is_dir($baseDir)) mkdir($baseDir, 0755, true);

    $dp = trim(str_replace("/", DIRECTORY_SEPARATOR, $dirpath), DIRECTORY_SEPARATOR);
    $paths = explode(DIRECTORY_SEPARATOR, $dp);
    $current_path = $baseDir;
    foreach ($paths as $path) {
        $current_path .= DIRECTORY_SEPARATOR . $path;
        if (!is_dir($current_path)) mkdir($current_path, 0755, true);
    }
}

function copyFilesRecursively($source, $destination, $excluded_files = []) {
    $source_full_path = __DIR__ . DIRECTORY_SEPARATOR . trim(trim(str_replace('/', DIRECTORY_SEPARATOR, $source), '/'), DIRECTORY_SEPARATOR);
    $dest_full_path = __DIR__ . DIRECTORY_SEPARATOR . trim(trim(str_replace('/', DIRECTORY_SEPARATOR, $destination), '/'), DIRECTORY_SEPARATOR);
    if (!is_file($source_full_path)) {
        return;
    }
    if (is_file($dest_full_path)) { // dest file exists
        copy($source_full_path, $dest_full_path);
    } else {
        $destBase = __DIR__ . DIRECTORY_SEPARATOR;
        $destBasename = DIRECTORY_SEPARATOR . basename($dest_full_path);
        $destdir = str_replace([$destBase, $destBasename], '', $dest_full_path);
        makeDirIfNotExists($destBase, $destdir);
        copy($source_full_path, $dest_full_path);
    }
}

function ftpDirectoryExists($conn_id, $directory) {
    $current = ftp_pwd($conn_id);
    if (@ftp_chdir($conn_id, $directory)) {
        ftp_chdir($conn_id, $current); // Restore original directory
        return true;
    }
    return false;
}

function ftpFileExists($conn_id, $remote_file) {
    return ftp_size($conn_id, $remote_file) !== -1;
}

function ftpMakeDirectoryRecursive($conn_id, $base_path, $remote_path) {
    $paths = explode('/', trim($remote_path, '/'));
    $current_path = rtrim($base_path, '/');
    if (!ftpDirectoryExists($conn_id, $current_path)) {
        ftp_mkdir($conn_id, $current_path);
    }
    foreach ($paths as $path) {
        $current_path .= '/' . $path;
        if (!ftpDirectoryExists($conn_id, $current_path)) {
            ftp_mkdir($conn_id, $current_path);
        }
    }
}

function checkFilesIsModified($conn_id, $local_path, $remote_path, $lpath_remote): bool
{
    $local_file_time = filemtime($local_path);
    $remote_full_path = "$remote_path/$lpath_remote";
    if ($local_file_time === false || !ftpFileExists($conn_id, $remote_full_path)) {
        return true;
    }

    $remote_file_time = ftp_mdtm($conn_id, $remote_full_path);
    return $remote_file_time == -1 || $local_file_time > $remote_file_time;
}

function uploadFilesRecursively($conn_id, $local_base_dir, $local_file, $remote_base_dir, &$uploaded_files)
{
    $local_dir_or_file = $local_base_dir . DIRECTORY_SEPARATOR . trim(str_replace("/", DIRECTORY_SEPARATOR, $local_file), DIRECTORY_SEPARATOR);
    if (is_dir($local_dir_or_file)) {
        $files = array_diff(scandir($local_dir_or_file), ['.', '..']);
        foreach ($files as $file) {
            $local_path = "$local_file/$file";
            if (is_dir($local_path)) {
                ftpMakeDirectoryRecursive($conn_id, $remote_base_dir, $local_path);
            }
            uploadFilesRecursively($conn_id, $local_base_dir, $local_path, $remote_base_dir,  $uploaded_files);
        }
    } else if (is_file($local_dir_or_file)) {
        $rl = str_replace(['/'. basename($local_file)], '', $local_file);
        ftpMakeDirectoryRecursive($conn_id, $remote_base_dir, $rl);
        $local_file_path = trim($local_file, '/');
        $remote_base = rtrim($remote_base_dir, '/');
        $should_be_uploaded = checkFilesIsModified($conn_id, $local_dir_or_file, $remote_base, $local_file_path);
        $remote_path = "$remote_base/$local_file_path";
        if ($should_be_uploaded) {
            if (!ftp_put($conn_id, $remote_path, $local_dir_or_file, FTP_BINARY)) {
                $uploaded_files[] = ['error' => "Failed to upload $remote_path from $local_dir_or_file or file is not modified"];
            } else {
                $uploaded_files[] = ['success' => $remote_path];
            }
        } else {
            $uploaded_files[] = ['skipped' => $remote_path, "reason" => "File is not modified"];
        }
    } else {
        $uploaded_files[] = ['error' => "Local file $local_file is not found"];
    }
}

function getDirectoryFilesRecursively($dir_path, &$directory_files, $excluded_files = [])
{
    $local_dir_path = __DIR__ . DIRECTORY_SEPARATOR . trim(rtrim(ltrim(str_replace("/", DIRECTORY_SEPARATOR, $dir_path), "/"), "/"));
    if (!is_dir($local_dir_path) && !is_file($local_dir_path)) return;
    $dirscan = scandir($local_dir_path);
    $dir = array_diff($dirscan, ['.', '..']);
    $files = array_filter($dir, fn($v) => !in_array($v, $excluded_files));
    $i = 0;
    foreach ($files as $file) {
        $directory_files[] = [
            "filename" => "$file",
        ];
        $local_dir_or_file_path = $local_dir_path . DIRECTORY_SEPARATOR . $file;
        if (is_dir($local_dir_or_file_path)) {
            $directory_files[$i]["type"] = "directory";
            $newFiles = [];
            getDirectoryFilesRecursively("$dir_path/$file", $newFiles);
            $directory_files[$i]["files"] = [...$newFiles];
        } else {
            $directory_files[$i]["file_extension"] = "." . pathinfo($file, PATHINFO_EXTENSION);
            $directory_files[$i]["type"] = "file";
            $directory_files[$i]["size"] = filesize($local_dir_or_file_path);
        }
        $i++;
    }
}

if (isset($_GET['command'])) {
// [Command Start]

    $command = $_GET['command'];
    $success = ['success' => true, 'request' => array_merge($_GET, $_POST)];
    try {
        switch ($command) {
            case 'get_directory_files':
                if ($_SERVER['REQUEST_METHOD'] !== "GET") throw new HTTPRequestException();
                if (!isset($_GET['path'])) throw new InvalidArgumentException("'path' query parameter required");
                $excluded_files = isset($_GET['excluded']) ? array_filter(explode(",", urldecode($_GET['excluded'])), fn($v) => strlen($v) > 0) : [];
                $dirpaths = [];
                getDirectoryFilesRecursively(urldecode($_GET['path']), $dirpaths, $excluded_files);
                $success['data'] = [...$dirpaths];
                break;
            case 'remove_directory':
                if ($_SERVER['REQUEST_METHOD'] !== "POST") throw new HTTPRequestException(); 
                if (!isset($_POST['path'])) throw new InvalidArgumentException("'path' query parameter required");
                deleteDirectory(urldecode($_POST['path']));
                break;
            // case 'copy_directory':
            //     $excluded_files = isset($_GET['excluded']) ? array_filter(explode(",", urldecode($_GET['excluded'])), fn($v) => strlen($v) > 0) : [];
            //     $workspace_folder = isset($_GET['source_directory']) ?  __DIR__ . DIRECTORY_SEPARATOR . trim(urldecode($_GET['source_directory'])) : __DIR__;
            //     if (!is_dir($workspace_folder)) {
            //         throw new InvalidArgumentException("'source_directory' path should be a directory not a file.");
            //     }
            //     $workspace_folder = trim(rtrim(rtrim($workspace_folder, DIRECTORY_SEPARATOR), "/")) . DIRECTORY_SEPARATOR;
            //     $dist_folder = isset($_GET['destination_directory']) ? __DIR__ . DIRECTORY_SEPARATOR . trim(urldecode($_GET['destination_directory'])) : __DIR__ . DIRECTORY_SEPARATOR . 'dist';
            //     if (!is_dir($dist_folder)) {
            //         throw new InvalidArgumentException("'destination_directory' path should be a directory not a file.");
            //     }
            //     $dist_folder = trim(rtrim(rtrim($dist_folder, DIRECTORY_SEPARATOR), "/")) . DIRECTORY_SEPARATOR;
            //     copyFilesRecursively($workspace_folder, $dist_folder, $excluded_files);
            //     break;
            case 'copy_file':
                if ($_SERVER['REQUEST_METHOD'] !== "POST") throw new HTTPRequestException();
                if (!isset($_POST['source']) || !isset($_POST['destination'])) throw new InvalidArgumentException("'source' and 'destination' query parameters are required");
                $source = $_POST['source'];
                $dist_folder = $_POST['destination'];
                if (is_dir($source)) {
                    throw new InvalidArgumentException("'source' path should be a file not a directory.");
                }

                copyFilesRecursively($source, $dist_folder);
                break;
            case 'check_ftp_modified':
                if ($_SERVER['REQUEST_METHOD'] !== "POST") throw new HTTPRequestException();
                if (
                    !isset($_POST['ftp_server']) || !isset($_POST['ftp_username']) ||
                    !isset($_POST['ftp_password']) || !isset($_POST['ftp_directory']) ||
                    !isset($_POST['local_src_directory']) || !isset($_POST['ftp_files'])
                ) throw new InvalidArgumentException("'ftp_server', 'ftp_username', 'ftp_password', 'ftp_directory', and 'ftp_files' query parameters are required");
                $ftp_server = $_POST['ftp_server'];
                $ftp_username = $_POST['ftp_username'];
                $ftp_password = $_POST['ftp_password'];
                $ftp_directory = rtrim($_POST['ftp_directory'], '/') . '/';
                $local_src_directory = __DIR__ . DIRECTORY_SEPARATOR . rtrim(str_replace('/', DIRECTORY_SEPARATOR, $_POST['local_src_directory']), DIRECTORY_SEPARATOR);
                $ftp_files = explode(",", $_POST['ftp_files']);
                $ftp_files = array_map(fn($v) => trim(trim(str_replace(DIRECTORY_SEPARATOR, '/', $v), '/')), $ftp_files);
                $conn_id = ftp_connect($ftp_server, 21);
                if (!$conn_id)
                {
                    throw new ConnectionException('Could not connect to ftp server');
                }

                if (!ftp_login($conn_id, $ftp_username, $ftp_password))
                {
                    throw new ConnectionException('FTP Login failed');
                }
                if (!ftpDirectoryExists($conn_id, $ftp_directory)) {
                    throw new FTPException("Directory $ftp_directory in ftp server does not exist.");
                }

                if (!ftp_pasv($conn_id, true) || !ftp_chdir($conn_id, $ftp_directory))
                {
                    throw new FTPException("FTP Server Failed");
                }
                $remote_base = rtrim($ftp_directory, '/');
                $modified_files = [];
                foreach ($ftp_files as $ftp_file) {
                    $local_dir_or_file = $local_src_directory . DIRECTORY_SEPARATOR . trim(str_replace("/", DIRECTORY_SEPARATOR, $ftp_file), DIRECTORY_SEPARATOR);
                    $modified_files[] = [
                        'local_path' => $local_dir_or_file,
                        'remote_path' => "$remote_base/$ftp_file",
                        'file' => $ftp_file,
                        'is_modified' => checkFilesIsModified($conn_id, $local_dir_or_file, $remote_base, $ftp_file),
                    ];
                }
                $success['data'] = $modified_files;
                break;
            case 'upload':
                if ($_SERVER['REQUEST_METHOD'] !== "POST") throw new HTTPRequestException();
                if (
                    !isset($_POST['ftp_server']) || !isset($_POST['ftp_username']) ||
                    !isset($_POST['ftp_password']) || !isset($_POST['ftp_directory']) ||
                    !isset($_POST['local_src_directory']) || !isset($_POST['files'])
                ) throw new InvalidArgumentException("'ftp_server', 'ftp_username', 'ftp_password', 'ftp_directory', and 'files' query parameters are required");

                $ftp_server = $_POST['ftp_server'];
                $ftp_username = $_POST['ftp_username'];
                $ftp_password = $_POST['ftp_password'];
                $ftp_directory = rtrim($_POST['ftp_directory'], '/') . '/';
                $local_src_directory = __DIR__ . DIRECTORY_SEPARATOR . rtrim(str_replace('/', DIRECTORY_SEPARATOR, $_POST['local_src_directory']), DIRECTORY_SEPARATOR);
                $files_to_upload = explode(",", $_POST['files']);
                $files_to_upload = array_map(fn($v) => trim(trim(str_replace(DIRECTORY_SEPARATOR, '/', $v), '/')), $files_to_upload);

                $conn_id = ftp_connect($ftp_server, 21);
                if (!$conn_id)
                {
                    throw new ConnectionException('Could not connect to ftp server');
                }

                if (!ftp_login($conn_id, $ftp_username, $ftp_password))
                {
                    throw new ConnectionException('FTP Login failed');
                }
                if (!ftpDirectoryExists($conn_id, $ftp_directory)) {
                    throw new FTPException("Directory $ftp_directory in ftp server does not exist.");
                }

                if (!ftp_pasv($conn_id, true) || !ftp_chdir($conn_id, $ftp_directory))
                {
                    throw new FTPException("FTP Server Failed");
                }


                $uploaded_files = [];
                foreach ($files_to_upload as $dist_folder_file) {
                    uploadFilesRecursively($conn_id, $local_src_directory, $dist_folder_file, $ftp_directory, $uploaded_files);    
                }

                $success['data'] = $uploaded_files;
                break;
            default:
                throw new InvalidArgumentException('Invalid command');
        }
        // if success
        echo json_encode($success);
    } catch (InvalidArgumentException $error) {
        echo json_encode(['error' => $error->getMessage(), "type" => 'Invalid Arguments']);
    } catch (ConnectionException $error) {
        echo json_encode(['error' => $error->getMessage(), "type" => 'Invalid FTP Connection']);
    } catch (FTPException $error) {
        echo json_encode(['error' => $error->getMessage(), "type" => 'FTP Error']);
    } catch (HTTPRequestException $error) {
        echo json_encode(['error' => $error->getMessage(), "type" => 'HTTP Request Error']);
    } catch (Throwable $error) {
        echo json_encode(['error' => $error->getMessage()]);
    } finally {
        if (isset($conn_id)) {
            ftp_close($conn_id);
        }
    }

// [Command End]
} else {

    echo json_encode(['error' => 'Bad Request']);

}