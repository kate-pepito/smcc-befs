<?php

enable_CORS();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST'):
    check_api_key($_GET['api_key'] ?? []);
    try {
        $algo = $_POST['algo'] ?? null;
        $size = $_POST['size'] ?? null;
        $filename = $_POST['filename'] ?? null;
        $file_extension = $_POST['file_extension'] ?? null;
        $filepath = $_POST['filepath'] ?? null;
        $created_at = $_POST['created_at'] ?? null;
        $accuracy = $_POST['accuracy'] ?? null;
        $files = $_FILES['inference'] ?? null;

        if ($algo === null || $size === null || $filename === null || $file_extension === null || $created_at === null || $files === null || $accuracy === null) {
            throw new Exception("Missing required parameters");
        }
        
        // Retrieve metadata
        $full_filename = "$filename$file_extension";
        
        // File handling
        $upload_dir = dirname(__DIR__) . DIRECTORY_SEPARATOR . trim(str_replace("/", DIRECTORY_SEPARATOR, $filepath), DIRECTORY_SEPARATOR); // Ensure this directory exists
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_tmp_path = $files['tmp_name'];
        $file_dest_path = $upload_dir . DIRECTORY_SEPARATOR . $full_filename;
        
        if (move_uploaded_file($file_tmp_path, $file_dest_path)) {
            $fullfilepath = "$filepath$full_filename";
            echo json_encode(["success" => true, "filepath" => $fullfilepath ]);
        } else {
            throw new Exception("Failed to save model");
        }
    } catch (Exception $e) {
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
    }
else:
    echo json_encode(["success" => false, "error" => "Invalid request method"]);
endif;
