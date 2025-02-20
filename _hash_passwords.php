<?php

require_once __DIR__ . '/dbconnect.php';

function saveToTxt($data) {
    $txt = "";
    foreach ($data as $k => $v) {
        foreach ($v as $d)
            $txt .= "[$k]: " . $d['username'] . " = " . $d['old_password'] . PHP_EOL;
    }
    file_put_contents("_passwords.txt", $txt);
}

$result = [
    "users" => [],
    "students" => []
];

$q = "SELECT id, username, password from users";

$query = mysqli_query($conn, $q);

if (mysqli_num_rows($query) > 0) {
    while ($row = mysqli_fetch_assoc($query)) {
        if (strpos($row['password'], "$") !== 0) {
            $id = $row['id'];
            $newPassword = password_hash($row['password'], PASSWORD_DEFAULT);
            if (mysqli_query($conn, "UPDATE users SET password = '$newPassword' WHERE id = $id")) {
                $result["users"][] = [
                    "id" => $id,
                    "username" => $row['username'],
                    "old_password" => $row['password'],
                    "new_password" => $newPassword
                ];
            }
        }
    }
}


$q = "SELECT id, username, password from students";

$query = mysqli_query($conn, $q);

if (mysqli_num_rows($query) > 0) {
    while ($row = mysqli_fetch_assoc($query)) {
        if (strpos($row['password'], "$") !== 0) {
            $id = $row['id'];
            $newPassword = password_hash($row['password'], PASSWORD_DEFAULT);
            if (mysqli_query($conn, "UPDATE students SET password = '$newPassword' WHERE id = $id")) {
                $result["students"][] = [
                    "id" => $id,
                    "username" => $row['username'],
                    "old_password" => $row['password'],
                    "new_password" => $newPassword
                ];
            }
        }
    }
}

saveToTxt($result);
header("Content-Type: application/json");
die(json_encode($result));