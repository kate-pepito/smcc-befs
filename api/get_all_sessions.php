<?php

enable_CORS();
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === "GET"):
    check_api_key($_GET['api_key'] ?? []);
    $STATE_BASE_DIR = dirname(__DIR__) . DIRECTORY_SEPARATOR . "training_states";
    $allSessions = array_diff(glob($STATE_BASE_DIR . DIRECTORY_SEPARATOR . "*.json"), ['.', '..']);
    $sessions = [];
    foreach ($allSessions as $session) {
        $sessions[] = pathinfo(basename($session), PATHINFO_FILENAME);
    }
    echo json_encode(["data" => $sessions]);
else:
    http_response_code(401);
    echo json_encode(["detail" => "Invalid Access"]);
endif;