<?php

require_once('dbconnect.php');

$result = [
    "users" => [],
    "students" => []
];

$q = "SELECT id, password from users";

$query = mysqli_query($conn, $q);

if (mysqli_num_rows($query) > 0) {
    while ($row = mysqli_fetch_assoc($query)) {
        if (strpos($row['password'], "$") !== 0) {
            $id = $row['id'];
            $newPassword = password_hash($row['password'], PASSWORD_DEFAULT);
            if (mysqli_query($conn, "UPDATE users SET password = '$newPassword' WHERE id = $id")) {
                $result["users"][] = [
                    "id" => $id,
                    "old_password" => $row['password'],
                    "new_password" => $newPassword
                ];
            }
        }
    }
}


$q = "SELECT id, password from students";

$query = mysqli_query($conn, $q);

if (mysqli_num_rows($query) > 0) {
    while ($row = mysqli_fetch_assoc($query)) {
        if (strpos($row['password'], "$") !== 0) {
            $id = $row['id'];
            $newPassword = password_hash($row['password'], PASSWORD_DEFAULT);
            if (mysqli_query($conn, "UPDATE students SET password = '$newPassword' WHERE id = $id")) {
                $result["students"][] = [
                    "id" => $id,
                    "old_password" => $row['password'],
                    "new_password" => $newPassword
                ];
            }
        }
    }
}

header("Content-Type: application/json");
die(json_encode($result));