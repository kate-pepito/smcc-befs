<?php

enable_CORS();
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === "GET"):
    check_api_key($_GET['api_key'] ?? []);
    $username = $_GET['username'] ?? null;
    $session_key = $_GET['session_key'] ?? null;
    $algo = $_GET['algo'] ?? null;
    $train_token = $_GET['train_token'] ?? null;

    if ($username === null || $session_key === null || $algo === null) {
        http_response_code(400);
        die(json_encode(["detail" => "Bad Request"]));
    }

    $STATE_BASE_DIR = dirname(__DIR__) . DIRECTORY_SEPARATOR . "training_states";
    $allSessions = glob($STATE_BASE_DIR . DIRECTORY_SEPARATOR . "*.json");
    $session = null;

    foreach ($allSessions as $s) {
        if ($train_token === null || $train_token === pathinfo(basename($s), PATHINFO_FILENAME)) {
            $f = file_get_contents($s);
            $sess = json_decode($f, true);
            if (($sess["username"] ?? null) === $username && ($sess["session_key"] ?? null) === $session_key && ($sess[""] ?? null) === $algo) {
                $session = $s;
                break;
            }
        }
    }
    echo json_encode(["session_token" => $session]);
else:
    http_response_code(401);
    echo json_encode(["detail" => "Invalid Access"]);
endif;