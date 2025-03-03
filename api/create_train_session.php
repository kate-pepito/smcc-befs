<?php

enable_CORS();
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === "POST"):
    check_api_key($_GET['api_key'] ?? []);
    $rawData = file_get_contents("php://input");
    $data = json_decode($rawData, true);
    $username = $data["username"] ?? null;
    $session_key = $data["session_key"] ?? null;
    $algo = $data["algo"] ?? null;
    $token = $data["token"] ?? null;
    if ($username === null || $session_key === null || $algo === null) {
        http_response_code(400);
        die(json_encode(["detail" => "Bad Request"]));
    }
    $STATE_BASE_DIR = dirname(__DIR__) . DIRECTORY_SEPARATOR . "training_states";
    $filepath = $STATE_BASE_DIR . DIRECTORY_SEPARATOR . "$token.json";
    if (is_file($filepath)) {
        http_response_code(400);
        die(json_encode(["detail" => "Already Has Session"]));
    }
    file_put_contents(
        $filepath,
        json_encode([
            "username" => $username,
            "session_key" => $session_key,
            "algo" => $algo,
            "token"=> $token,
            "state" => [
                "connection" => "disconnected",
                "status" => "idle",
                "username" => $username,
                "session_id" => $session_key,
                "algo" => $algo,
                "token" => $token,
                "random_state" => 42,
                "test_size" => 0.2
            ],
        ], JSON_PRETTY_PRINT)
    );
    http_response_code(201);
    echo json_encode(["token" => $token]);
else:
    http_response_code(401);
    echo json_encode(["detail" => "Invalid Access"]);
endif;