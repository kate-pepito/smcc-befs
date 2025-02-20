<?php

function redirect_to_no_php_path()
{
    $bn = explode(".", basename(get_uri_path()));
    if (end($bn) === "php") {
        $uri_path = explode("/", get_uri_path());
        $redirect_to = "/" . implode(
            "/",
            [
                ...array_slice(array_filter($uri_path, fn($v) => strlen($v) > 0), 0, -1),
                explode(".", basename(get_uri_path()))[0]
            ]
        );
        $uri_query = get_url_query();
        $redirect_to .= strlen($uri_query) > 0 ? "?" . get_url_query() : "";
        header("Location: $redirect_to");
        exit;
    }
}

function get_base_uri()
{
    return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . get_base_uri_path();
}

function get_uri_path()
{
    $__URI = $_SERVER['REQUEST_URI'];
    $__URI_SPLIT = explode("?", $__URI);
    return count($__URI_SPLIT) > 0 ? $__URI_SPLIT[0] : "/";
}

function get_file_uri_path()
{
    $p = explode("/", get_uri_path());
    $p = array_filter($p, fn($v) => strlen($v) > 0);
    return strlen(get_base_uri_path()) > 0 ?
        implode(DIRECTORY_SEPARATOR, [__DIR__, ...array_slice($p, 1)])
        : implode(DIRECTORY_SEPARATOR, [__DIR__, ...explode("/", get_uri_path())]);

}

function get_url_query()
{
    $__URI = $_SERVER['REQUEST_URI'];
    $__URI_SPLIT = explode("?", $__URI);
    return count($__URI_SPLIT) > 1 ? $__URI_SPLIT[1] : "";
}

function get_uri_file_extension()
{
    $__basename = explode(".", basename(get_uri_path()));
    return count($__basename) > 1
        ? "." . join(".", array_slice($__basename, 1))
        : "";
}

function get_file_extension(string $filename)
{
    $__basename = explode(".", basename($filename));
    return count($__basename) > 1
        ? "." . join(".", array_slice($__basename, 1))
        : "";
}

function get_admin_header()
{
    return implode(DIRECTORY_SEPARATOR, [__DIR__, "header.php"]);
}

function get_admin_sidebar()
{
    return implode(DIRECTORY_SEPARATOR, [__DIR__, "sidebar.php"]);
}

function get_footer()
{
    return implode(DIRECTORY_SEPARATOR, [__DIR__, "footer.php"]);
}

function get_student_footer()
{
    return implode(DIRECTORY_SEPARATOR, [__DIR__, "smcc-students", "footer.php"]);
}

function get_dean_header()
{
    return implode(DIRECTORY_SEPARATOR, [__DIR__, "dean", "dean_header.php"]);
}

function get_dean_sidebar()
{
    return implode(DIRECTORY_SEPARATOR, [__DIR__, "dean", "dean_sidebar.php"]);
}

function get_reviewer_header()
{
    return implode(DIRECTORY_SEPARATOR, [__DIR__, "reviewer", "reviewer_header.php"]);
}

function get_reviewer_sidebar()
{
    return implode(DIRECTORY_SEPARATOR, [__DIR__, "reviewer", "reviewer_sidebar.php"]);
}

function get_student_exam_form_sc()
{
    return implode(DIRECTORY_SEPARATOR, [__DIR__, "smcc-students", "exam_form_sc.php"]);
}

function get_student_sidebar()
{
    return implode(DIRECTORY_SEPARATOR, [__DIR__, "student", "students_sidebar.php"]);
}

function is_nav_active(...$uri_paths): bool
{
    foreach ($uri_paths as $up) {
        if ((
            strpos($up, get_base_uri_path()) === 0
            && strpos($up, get_uri_path()) === 0
        ) || (
            strpos($up, get_base_uri_path()) !== 0
            && strpos(get_base_uri_path() . $up, get_uri_path()) === 0
        )) {
            return true;
        }
    }
    return false;
}

function authenticated_page(string $accType = "")
{
    $user_id = $_SESSION['user_id'] ?? null;
    $account_type = $_SESSION['account_type'] ?? null;
    if (($user_id === null || $account_type === null)) {
        header("Location: " . get_base_uri());
        exit;
    }
    $accs = [
        "admin" => "admin",
        "dean" => "dean",
        "reviewer" => "reviewer",
        "student" => "smcc-students"
    ];
    if ($accType !== $account_type) {
        $redirect_to = get_base_uri_path() . "/" . $accs[$account_type];
        switch ($account_type) {
            case "admin":
                $redirect_to .= "/admin_home";
                break;
            case "dean":
                $redirect_to .= "/dean_home_page";
                break;
            case "reviewer":
                $redirect_to .= "/reviewer_home";
                break;
            case "student":
                $redirect_to .= "/";
                break;
        }
        header("Location: $redirect_to");
        exit;
    }
}

function render(string $page_file_path = "", array $global_var_args = [])
{
    try {
        extract($global_var_args);
        if ($user_id !== null && get_uri_path() === get_base_uri_path() . "/") {
            authenticated_page();
        }
        if (strlen($page_file_path) > 0 &&
            is_dir($page_file_path) &&
            is_file($page_file_path . DIRECTORY_SEPARATOR . "index.php")
        ) {
            $page_file_path = $page_file_path . DIRECTORY_SEPARATOR . "index";
        }
        $file_ext = get_file_extension($page_file_path);
        if ($file_ext !== ".php") {
            $page_file_path = "$page_file_path.php";
        }
        if (strlen($page_file_path) > 0 &&
            (is_file($page_file_path))
        ) {
            require_once $page_file_path;
        } else {
            // Page not found
            require_once "notfound_page.php";
        }
    } catch (\Throwable $error) {
        // Page error
        require_once "error_page.php";
    } finally {
        exit;
    }
}