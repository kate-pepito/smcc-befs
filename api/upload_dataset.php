<?php

enable_CORS();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST'):
    check_api_key($_GET['api_key'] ?? []);
    try {
        $files = $_FILES['dataset'] ?? null;

        if ($files === null) {
            throw new Exception("Missing required parameters");
        }
    
        $filename = $files['name'] ?? null;
        if ($filename === null) {
            throw new Exception("Missing required filename");
        }

        $fext = pathinfo($filename, PATHINFO_EXTENSION);
    
        if ($fext !== "csv") {
            throw new Exception("Must be a csv file");
        }
    
        $filepath = "/training_datasets/";
                
        // File handling
        $upload_dir = dirname(__DIR__) . DIRECTORY_SEPARATOR . trim(str_replace("/", DIRECTORY_SEPARATOR, $filepath), DIRECTORY_SEPARATOR); // Ensure this directory exists
        if (!is_dir($upload_dir)) {
            $hasMade = mkdir($upload_dir, 0777, true);
        }

        $file_tmp_path = $files['tmp_name'];
        $file_dest_path = $upload_dir . DIRECTORY_SEPARATOR . $filename;
        
        if (move_uploaded_file($file_tmp_path, $file_dest_path)) {
            $fullfilepath = "$filepath$filename";
            echo json_encode(["success" => true, "filepath" => $fullfilepath ]);
        } else {
            throw new Exception("Failed to upload dataset");
        }
    } catch (Exception $e) {
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
    }
else:
    echo json_encode(["success" => false, "error" => "Invalid request method"]);
endif;
