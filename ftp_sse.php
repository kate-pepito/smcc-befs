<?php
// Set headers for Server-Sent Events
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

function sendEvent($data, $event = null) {
    if ($event) {
        echo "event: $event\n";
    }
    echo "data: " . $data . "\n\n";
    ob_flush();
    flush();
}

function copyFilesRecursively($source, $destination, $excluded_files = []) {
    if (is_dir($source)) {
        mkdir($destination, 0755, true);
        $files = scandir($source);

        foreach ($files as $file) {
            if ($file === '.' || $file === '..' || in_array($file, $excluded_files)) {
                continue;
            }
            $src_path = $source . DIRECTORY_SEPARATOR . $file;
            $dest_path = $destination . DIRECTORY_SEPARATOR . $file;

            if (is_dir($src_path)) {
                copyFilesRecursively($src_path, $dest_path, $excluded_files);
            } else {
                copy($src_path, $dest_path);
            }
        }
    } else if (is_file($source)) {
        copy($source, $destination);
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

function ftpFileExists($conn_id, $remote_file) {
    return ftp_size($conn_id, $remote_file) !== -1;
}

function ftpGetFileSize($conn_id, $remote_file) {
    return ftp_size($conn_id, $remote_file);
}

function ftpDownloadTempFile($conn_id, $remote_file) {
    $tempFile = tempnam(sys_get_temp_dir(), 'ftp');
    if (ftp_get($conn_id, $tempFile, $remote_file, FTP_BINARY)) {
        return $tempFile;
    }
    unlink($tempFile);
    return false;
}

function ftpDirectoryExists($conn_id, $directory) {
    $current = ftp_pwd($conn_id);
    if (@ftp_chdir($conn_id, $directory)) {
        ftp_chdir($conn_id, $current); // Restore original directory
        return true;
    }
    return false;
}

function uploadFilesRecursively($conn_id, $local_dir, $remote_dir, &$uploaded_files, $current_count)
{

    if (!ftpDirectoryExists($conn_id, $remote_dir)) {
        ftp_mkdir($conn_id, $remote_dir);
    }

    $files = array_diff(scandir($local_dir), ['.', '..']);

    foreach ($files as $file) {
        $local_path = $local_dir . DIRECTORY_SEPARATOR . $file;
        $remote_path = rtrim($remote_dir, '/') . '/' . $file;
        $current_count = max(min(99, $current_count++), 40);
        if (is_dir($local_path)) {
            uploadFilesRecursively($conn_id, $local_path, $remote_path,  $uploaded_files, $current_count); // Recur for subdir
        } else {
            if (ftpFileExists($conn_id, $remote_path)) {
                $local_size = filesize($local_path);
                $remote_size = ftpGetFileSize($conn_id, $remote_path);
                if ($local_size === $remote_size) {
                    $remote_temp = ftpDownloadTempFile($conn_id, $remote_path);
                    if ($remote_temp && md5_file($local_path) === md5_file($remote_temp)) {
                        unlink($remote_temp);
                        $uploaded_files[] = ['skipped' => $remote_path, 'reason' => 'Same size and content'];
                        sendEvent("{$current_count}|Skipped file: $remote_path");
                        continue;
                    }
                    if ($remote_temp) {
                        unlink($remote_temp);
                    }
                }
            }
            if (ftp_put($conn_id, $remote_path, $local_path, FTP_BINARY)) {
                $uploaded_files[] = ['success' => $remote_path];
                sendEvent(" $current_count|Uploaded file: $remote_path");
            } else {
                $uploaded_files[] = ['error' => "Failed to upload $remote_path"];
                sendEvent("$current_count|Error uploading file: $remote_path");
            }
        }
    }
}

// Get POST data
$ftp_server = urldecode($_GET['ftp_server']);
$ftp_username = urldecode($_GET['ftp_username']);
$ftp_password = urldecode($_GET['ftp_password']);
$ftp_directory = urldecode(rtrim($_GET['ftp_directory'], '/') . '/');
$dist_folder = isset($_GET['ftp_src']) ? __DIR__ . DIRECTORY_SEPARATOR . urldecode($_GET['ftp_src']) . DIRECTORY_SEPARATOR : __DIR__ . DIRECTORY_SEPARATOR . 'dist' . DIRECTORY_SEPARATOR;
$workspace_folder = isset($_GET['ftp_workspace']) ?  __DIR__ . DIRECTORY_SEPARATOR . urldecode($_GET['ftp_workspace']) . DIRECTORY_SEPARATOR : __DIR__ . DIRECTORY_SEPARATOR;

// 1. Delete dist folder
sendEvent('10|Deleting dist folder...');
deleteDirectory($dist_folder);

// 2. Copy files to dist
sendEvent('20|Copying files to dist...');
$excluded_files = [
    'dist', '.DS_Store', '.git', '.gitignore', 'node_modules', 'package.json', 'ftp_see.php',
    'ftp.php', 'yarn.lock', 'LICENSE', 'README.md', '.vscode', '.env', '.env.production', '_passwords.txt',
    'vendor', // remove vendor folder from exception if needed to upload (big files)
];
copyFilesRecursively($workspace_folder, $dist_folder, $excluded_files);
copyFilesRecursively("$workspace_folder.env.production", "$dist_folder.env");

// 3. Connect to FTP
sendEvent('40|Connecting to FTP...');
$conn_id = ftp_connect($ftp_server, 21);
if (!$conn_id) {
    sendEvent('45|Could not connect to FTP server');
    exit;
}

// 4. Authenticate to FTP
sendEvent('50|Authenticating to FTP...');
if (!ftp_login($conn_id, $ftp_username, $ftp_password)) {
    sendEvent('55|FTP login failed');
    exit;
}


// 5. Change directory
sendEvent('60|Change Current Directory on FTP...');
if (!ftp_pasv($conn_id, true) || !ftp_chdir($conn_id, $ftp_directory)) {
    sendEvent("65|Failed to change directory to $ftp_directory");
    exit;
}

// 6. Upload files
$files = array_diff(scandir($dist_folder), ['.', '..']);
$count_files = (1 / count($files)) * 39;
$i = 0;

$dirExists = ftpDirectoryExists($conn_id, $ftp_directory); // it says true;

if (!$dirExists) {
    sendEvent("65|Failed to change directory to $ftp_directory");
    exit;
}

$uploaded_files = [];
uploadFilesRecursively($conn_id, $dist_folder, $ftp_directory, $uploaded_files, 60);

ftp_close($conn_id);
sendEvent('100|' . json_encode($uploaded_files));

exit;