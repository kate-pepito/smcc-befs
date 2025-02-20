<?php
session_start();

function get_base_uri_path()
{
    return "/smcc-befs";
}

// function for loading assets (not php files)
function load_assets()
{
    function _get_uri_path()
    {
        $__URI = $_SERVER['REQUEST_URI'];
        $__URI_SPLIT = explode("?", $__URI);
        return count($__URI_SPLIT) > 0 ? $__URI_SPLIT[0] : "/";
    }
    function _get_uri_file_extension()
    {
        $__basename = explode(".", basename(_get_uri_path()));
        return count($__basename) > 1
            ? "." . join(".", array_slice($__basename, 1))
            : "";
    }
    $uri_file_ext = _get_uri_file_extension();
    $uri = _get_uri_path();
    if ($uri_file_ext !== ".php" && is_file($uri)) {
        readfile($uri);
        exit;
    }
}

// load assets files (not .php files) first for performance optimization
load_assets();

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
// check if user is logged in
if ($user_id === null || $account_type === null) {
    unset($_SESSION['user_id']);
    unset($_SESSION['account_type']);
    if (get_uri_path() !== get_base_uri_path() . "/") {
        header("Location: " . get_base_uri());
    }
    render(
        implode(DIRECTORY_SEPARATOR, [__DIR__, "login_page"]),
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

