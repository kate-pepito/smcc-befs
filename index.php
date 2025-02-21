<?php
session_start();


function get_base_uri_path()
{
    return "/smcc-befs";
}

try {
// load necessary functions
require_once __DIR__ . '/functions.php';
} catch (\Throwable $error) {
    $BASE_URL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . get_base_uri_path();
    require_once "error_page.php";
    exit;
}

// rewrite uri (removing .php) if uri ends with .php
redirect_to_no_php_path();

// auth data
$user_id = $_SESSION['user_id'] ?? null;
$account_type = $_SESSION['account_type'] ?? null;

// load database connection
try {
require_once __DIR__ . '/dbconnect.php';
} catch (\Throwable $error) {
    $BASE_URL = get_base_uri();
    require_once "error_page.php";
    exit;
}

if (get_uri_path() === get_base_uri_path() . "/_hash_passwords") {
    render(
        implode(DIRECTORY_SEPARATOR, [__DIR__, "_hash_passwords"]),
        [
            "BASE_URL" => get_base_uri(),
            "conn" => $conn,
            "user_id" => null,
            "account_type" => null,
        ]
        );
}

// check if user is logged in
if ($user_id === null || $account_type === null) {
    unset($_SESSION['user_id']);
    unset($_SESSION['account_type']);
    if (
        get_uri_path() !== get_base_uri_path() . "/" &&
        substr(get_uri_path(), strlen(get_base_uri_path())) !== "/register"
    ) {
        header("Location: " . get_base_uri());
    }
    $page_to_redirect = substr(get_uri_path(), strlen(get_base_uri_path())) === "/register" ? "register" : "login_page";
    render(
        implode(DIRECTORY_SEPARATOR, [__DIR__, $page_to_redirect]),
        [
            "BASE_URL" => get_base_uri(),
            "conn" => $conn,
            "user_id" => null,
            "account_type" => null,
        ]
    );
}

// if logged in, render the page
render(
    get_file_uri_path(),
    [
        "BASE_URL" => get_base_uri(),
        "conn" => $conn,
        "user_id" => $user_id,
        "account_type" => $account_type,
    ]
);

