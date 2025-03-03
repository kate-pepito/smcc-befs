<?php

enable_CORS();
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === "POST"):
    check_api_key($_GET['api_key'] ?? []);
    $rawData = file_get_contents("php://input");
    $data = json_decode($rawData, true);
    $username = $data['username'] ?? null;
    $session_key = $data['session_key'] ?? null;
    $token = $data['token'] ?? null;
    if ($username === null || $session_key === null || $token === null) {
        http_response_code(400);
        die(json_encode(["detail" => "Bad Request"]));
    }
    $STATE_BASE_DIR = dirname(__DIR__) . DIRECTORY_SEPARATOR . "training_states";
    $filepath = $STATE_BASE_DIR . DIRECTORY_SEPARATOR . "$token.json";
    if (is_file($filepath)) {
        $f = file_get_contents($filepath);
        $sess = json_decode($f, true);
        if (($sess['username'] ?? null) === $username && ($sess['session_key'] ?? null) === $session_key) {
            unlink($filepath);
        }
    }
    echo json_encode(["success" => true, "detail" => "OK"]);
else:
    http_response_code(401);
    echo json_encode(["detail" => "Invalid Access"]);
endif;