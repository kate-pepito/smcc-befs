<?php

enable_CORS();
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === "GET"):
    check_api_key($_GET['api_key'] ?? []);
    $username = $_GET['username'] ?? null;
    $session_key = $_GET['session_key'] ?? null;
    $token = $_GET['token'] ?? null;
    if ($username === null || $session_key === null || $token === null) {
        http_response_code(400);
        die(json_encode(["detail" => "Bad Request"]));
    }
    $STATE_BASE_DIR = dirname(__DIR__) . DIRECTORY_SEPARATOR . "training_states";
    $filepath = $STATE_BASE_DIR . DIRECTORY_SEPARATOR . "$token.json";
    if (is_file($filepath)) {
        $f = file_get_contents($filepath);
        $sess = json_decode($f, true);
        if (($sess["username"] ?? null) === $username && ($sess["session_key"] ?? null) === $session_key) {
            die(json_encode(['valid' => true]));
        }
    }
    echo json_encode(["valid" => false]);
else:
    http_response_code(401);
    echo json_encode(["detail" => "Invalid Access"]);
endif;