<?php

enable_CORS();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST'):
    check_api_key($_GET['api_key'] ?? []);
    $rawData = file_get_contents("php://input");
    $data = json_decode($rawData, true);
    $algo = $data['algo'] ?? null;
    $size = $data['size'] ?? null;
    $filename = $data['filename'] ?? null;
    $file_extension = $data['file_extension'] ?? null;
    $filepath = $data['filepath'] ?? null;
    $created_at = $data['created_at'] ?? null;
    $accuracy = $data['accuracy'] ?? null;
    $name = $data['name'] ?? null;
    // $scaler = $data['scaler'] ?? null;
    $fullpath = $data['fullpath'] ?? null;
    
    try {
        if ($algo === null || $size === null || $filename === null || $file_extension === null || $created_at === null || $name === null || $accuracy === null || /*$scaler === null ||*/ $fullpath === null) {
            throw new Exception("Missing required parameters");
        }

        $dt = new DateTime($created_at);
        $dt->setTimezone(new DateTimeZone("Asia/Manila"));
        $created_at_mysql = $dt->format("Y-m-d H:i:s");
        // $scalerJson = json_encode($scaler);
        $sqlquery = "INSERT INTO inference_model (name, algo, size, filename, file_extension, filepath, fullpath, accuracy, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = conn()->prepare($sqlquery);
        $stmt->bind_param("ssissssds", $name, $algo, $size, $filename, $file_extension, $filepath, $fullpath, $accuracy, $created_at_mysql);

        $result = $stmt->execute();
        if ($result === false) {
            throw new Exception(conn()->get_conn()->errno);
        }
        
        echo json_encode(["success" => true, "detail" => "Model saved successfully." ]);
    } catch (Exception $e) {
        if ($filename !== null) {
            $upload_dir = dirname(__DIR__) . DIRECTORY_SEPARATOR . trim(str_replace("/", DIRECTORY_SEPARATOR, $filepath), DIRECTORY_SEPARATOR); // Ensure this directory exists
            $file_dest_path = $upload_dir . DIRECTORY_SEPARATOR . $filename . $file_extension;
            if (is_file($file_dest_path)) {
                unlink($file_dest_path);
            }
        }
        echo json_encode(["success" => false, "detail" => $e->getMessage()]);
    }
else:
    echo json_encode(["success" => false, "detail" => "Invalid request method"]);
endif;
