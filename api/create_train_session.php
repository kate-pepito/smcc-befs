<?php

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === "POST"):
    $username = $_POST["username"] ?? null;
    $session_key = $_POST["session_key"] ?? null;
    $algo = $_POST["algo"] ?? null;
    $token = $_POST["token"] ?? null;
    if ($username === null || $session_key === null || $algo === null) {
        http_response_code(400);
        die(json_encode(["detail" => "Bad Request"]));
    }
    $STATE_BASE_DIR = dirname(__DIR__) . DIRECTORY_SEPARATOR . "training_states";
    $filepath = $STATE_BASE_DIR . DIRECTORY_SEPARATOR . $token;
    if (is_file($filepath)) {
        http_response_code(400);
        die(json_encode(["detail" => "Already Has Session"]));
    }
    file_put_contents(
        $filepath,
        [
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
                "token"=> $token,
            ],
        ]
    );
    http_response_code(201);
    echo json_encode(["id" => $token]);
else:
    http_response_code(401);
    echo json_encode(["detail" => "Invalid Access"]);
endif;