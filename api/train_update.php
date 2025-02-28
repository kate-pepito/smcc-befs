<?php

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === "POST"):
    $token = $_GET['token'] ?? null;
    $username = $_POST['username'] ?? null;
    $session_key = $_POST['session_key'] ?? null;
    $algo = $_POST['algo'] ?? null;
    if ($token === null || $username === null || $session_id === null || $algo === null) {
        http_response_code(400);
        die(json_encode(['detail' => 'Bad Request']));
    }
    $STATE_BASE_DIR = dirname(__DIR__) . DIRECTORY_SEPARATOR . "training_states";
    $filepath = $STATE_BASE_DIR . DIRECTORY_SEPARATOR . $token;
    if (is_file($filepath)) {
        $f = file_get_contents($filepath);
        $session = json_decode($f, true);
        if (($session["username"] ?? null) === $username && ($session['session_key'] ?? null) === $session_key && ($session['token'] ?? null) === $token && ($session['algo'] ?? null) === $algo) {
            $session["state"] = array_merge([], $_POST);
            file_put_contents($filepath, json_encode($session, JSON_PRETTY_PRINT));
            die(json_encode(["success" => true]));
        }
    }

    echo json_encode(["success" => false]);
else:
    http_response_code(401);
    echo json_encode(["detail" => "Invalid Access"]);
endif;