<?php

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === "POST"):
    $token = $_POST['token'] ?? null;
    if ($token === null) {
        http_response_code(400);
        die(json_encode(["detail" => "Bad Request"]));
    }
    $STATE_BASE_DIR = dirname(__DIR__) . DIRECTORY_SEPARATOR . "training_states";
    $filepath = $STATE_BASE_DIR . DIRECTORY_SEPARATOR . $token;
    if (is_file($filepath)) {
        unlink($filepath);
    }
    echo json_encode(["success" => true, "detail" => "OK"]);
else:
    http_response_code(401);
    echo json_encode(["detail" => "Invalid Access"]);
endif;