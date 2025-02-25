<?php
session_start();

try {
// load necessary functions
require_once __DIR__ . '/functions.php';
} catch (\Throwable $error) {
    require_once "error_page.php";
    exit;
}

// Load environment variables inside .env file
load_dotenv(".env");

// rewrite uri (removing .php) if uri ends with .php
redirect_to_no_php_path();

// try to load database connection first for early connection errors
conn()->close();

// check if user is logged in
if (user_id() === null || account_type() === null) {
    unset($_SESSION['user_id']);
    unset($_SESSION['account_type']);
    if (!is_current_unauthenticated_page()) {
        header("Location: " . base_url());
    }
    $page_to_redirect = strlen(get_current_path()) > 1 && is_current_unauthenticated_page() ? array_filter(explode("/", get_current_path()), fn($v) => strlen($v) > 0) : ["login_page"];
    render(implode(DIRECTORY_SEPARATOR, [__DIR__, ...$page_to_redirect]));
}

// if logged in, render the page
render(get_file_uri_path());

