<?php

$mysqli_object = [];

function get_base_uri_path()
{
    return $_ENV['BEFS_BASE_URI'] ?? "/smcc-befs";
}

function is_current_unauthenticated_page()
{
    $unauthenticated_pages = [
        # add or change uri path if needed
        "/",
        "/register",
        "/_hash_passwords",
        "/ftp",
        "/ftp_api",
    ];
    $bsp = strlen(get_base_uri_path()) === 0 ? null : get_base_uri_path();
    $trimed_uri = $bsp === null ? get_uri_path() : substr(get_uri_path(), strlen($bsp));
    return in_array($trimed_uri, $unauthenticated_pages);
}

function get_current_path()
{
    $bsp = strlen(get_base_uri_path()) === 0 ? null : get_base_uri_path();
    return $bsp === null ? get_uri_path() : substr(get_uri_path(), strlen($bsp));
}


function load_dotenv($filename = ".env")
{
    global $_ENV;
    if (!file_exists($filename)) {
        return;
    }
    $envFile = file_get_contents($filename);
    $envLines = explode("\n", $envFile);
    foreach ($envLines as $line) {
        if (strpos($line, "#") === 0 || $line === "" || $line === "=") {
            continue;
        }
        $linekv = explode("=", $line);
        if (count($linekv) !== 2) {
            continue;
        }
        $key = trim($linekv[0]);
        $value = trim($linekv[1]);
        $_ENV[$key] = $value;
    }
}

function conn()
{
    global $mysqli_object;
    try {
        end($mysqli_object)->ping();
        return end($mysqli_object);
    } catch (\Throwable $err) {/* mysqli object is null or $conn is already closed */}
    try {
        $mysql_servername = $_ENV["BEFS_MYSQL_HOST"] ?? "localhost";
        $mysql_username = $_ENV["BEFS_MYSQL_USERNAME"] ?? "root";
        $mysql_password = $_ENV["BEFS_MYSQL_PASSWORD"] ?? "";
        $mysql_dbname = $_ENV["BEFS_MYSQL_DBNAME"] ?? "smcc_befs";
        $sql_file = $_ENV["BEFS_MYSQL_IMPORT_FILE"] ?? "database/smcc_befs.sql";
        
        $c1 = new mysqli($mysql_servername, $mysql_username, $mysql_password);
        if (!$c1 || $c1->connect_error) {
            throw new mysqli_sql_exception("[Connection failed] " . $c1->connect_error);
        } else {
            if (!$c1->query("USE $mysql_dbname")) {
                $c1->query("CREATE DATABASE $mysql_dbname");
            }
            $c1->close();
        }

        // Create connection
        $mysqli = new mysqli($mysql_servername, $mysql_username, $mysql_password, $mysql_dbname);
        // Check connection
        if (!$mysqli || $mysqli->connect_error) {
            throw new mysqli_sql_exception("[Connection failed] " . $mysqli->connect_error);
        }
        array_splice($mysqli_object, 0);
        seed_database($mysqli, $sql_file);
        $mysqli_object[] = $mysqli;
        return $mysqli;
    } catch (\Throwable $error) {
        require_once __DIR__ . '/error_page.php';
        exit;
    }
}


function check_seed_exists($mysqli)
{
    $sq = "SELECT * FROM users WHERE id = 1";
    $result = mysqli_query($mysqli, $sq);
    return $result !== false && mysqli_num_rows($result) > 0;
}

function seed_database($mysqli, $sql_file)
{
    if (!check_seed_exists($mysqli)) {
        // Read the SQL file
        $sql = file_get_contents($sql_file);
        echo mysqli_next_result($mysqli) . "<br/>";
        // Execute the SQL file
        if ($mysqli->multi_query($sql)) {
            do {
                // Store the result set (if any)
                if ($result = $mysqli->store_result()) {
                    $result->free();
                }
            } while ($mysqli->more_results() && $mysqli->next_result());
        } else {
            echo "Error importing SQL file: " . $mysqli->error;
        }
    }
}

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

function base_url(): string
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
        ? "." . implode(".", array_slice($__basename, 1))
        : "";
}

function get_file_extension(string $filename)
{
    $__basename = explode(".", basename($filename));
    return count($__basename) > 1
        ? "." . implode(".", array_slice($__basename, 1))
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

function user_id()
{
    return $_SESSION['user_id'] ?? null;
}

function account_type()
{
    return $_SESSION['account_type'] ?? null;
}

function authenticated_page(string $accType = "")
{
    if ((user_id() === null || account_type() === null)) {
        header("Location: " . base_url());
        exit;
    }
    $accs = [
        "admin" => "admin",
        "dean" => "dean",
        "reviewer" => "reviewer",
        "student" => "smcc-students"
    ];
    if ($accType !== account_type()) {
        $redirect_to = get_base_uri_path() . "/" . $accs[account_type()];
        switch (account_type()) {
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

function render(string $page_file_path = "")
{
    try {
        if (user_id() !== null && get_uri_path() === get_base_uri_path() . "/") {
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
        conn()->close();
        exit;
    }
}


function default_html_head(string $title_page = "Login", array $imports = [])
{
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <title><?= $title_page ?> - SMCC BEFS</title>
    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="57x57" href="<?= base_url() ?>/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="<?= base_url() ?>/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="<?= base_url() ?>/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="<?= base_url() ?>/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="<?= base_url() ?>/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="<?= base_url() ?>/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="<?= base_url() ?>/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="<?= base_url() ?>/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url() ?>/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="<?= base_url() ?>/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url() ?>/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="<?= base_url() ?>/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url() ?>/favicon-16x16.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <?php foreach ($imports as $import_item): ?>
    <?php   switch ($import_item['type'] ?? ""):
                case 'style': ?>
    <link href="<?= strpos($import_item['href'] ?? "", "http") === 0 ? $import_item['href'] : base_url() . "/" . ltrim($import_item['href'] ?? "", "/") ?>" rel="<?= ($import_item['rel'] ?? 'stylesheet') ?: 'stylesheet' ?>">
    <?php       break;
                case 'script': ?>
    <script src="<?= strpos($import_item['src'] ?? "", "http") === 0 ? $import_item['src'] : base_url() . "/" . ltrim($import_item['src'] ?? "", "/") ?>" <?= isset($import_item['script_type']) ? 'type="' . ($import_item['script_type'] ?: 'text/javascript') . '"' : '' ?>></script>
    <?php       break;
                case 'custom': ?>
    <?php $import_item['content'] ?? "" ?>
    <?php       break;
            endswitch;
        endforeach;
    ?>
</head>
<?php
}

function default_html_body_end(array $imports = [])
{
?>
<script>window.BASE_URL = `<?= base_url() ?>`;</script>
<?php foreach ($imports as $import_item):
        switch ($import_item['type'] ?? ""):
            case 'script': ?>
<script src="<?= strpos($import_item['src'] ?? "", "http") === 0 ? $import_item['src'] : base_url() . "/" . ltrim($import_item['src'] ?? "", "/") ?>" <?= isset($import_item['script_type']) ? 'type="' . ($import_item['script_type'] ?: 'text/javascript') . '"' : '' ?>></script>
<?php       break;
            case 'custom': ?>
<?php isset($import_item['content']) ? (is_callable($import_item['content']) ? call_user_func($import_item['content']) : $import_item['content']) : $import_item['content'] ?>    <?php       break;
        endswitch;
    endforeach;
}


function admin_html_head(string $title_page = "Page Title", array $imports = [])
{
    default_html_head($title_page, [
        [ "type" => "style", "href" => "https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" ],
        [ "type" => "style", "href" => "assets/vendor/bootstrap/css/bootstrap.min.css" ],
        [ "type" => "style", "href" => "https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" ],
        ...$imports
    ]);
}

function admin_html_body_end(array $imports = [])
{
    default_html_body_end([
        [ "type" => "script", "src" => "assets/vendor/bootstrap/js/bootstrap.bundle.min.js" ],
        ...$imports
    ]);
}


function student_html_head(string $title_page = "Page Title", array $imports = [])
{
    default_html_head($title_page, [
        [ "tyoe" => "style", "href" => "https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&display=swap" ],
        [ "type" => "style", "href" => "smcc-students/css/bootstrap.min.css" ],
        [ "type" => "style", "href" => "https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" ],
        [ "type" => "style", "href" => "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"],
        ...$imports
    ]);
}

function student_html_body_end(array $imports = [])
{
    default_html_body_end([
        [ "type" => "script", "src" => "assets/vendor/bootstrap/js/bootstrap.bundle.min.js" ],
        ...$imports
    ]);
}


function student_nav($main_nav_link = null, $main_nav_label = null)
{
?>
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <!-- Spinner End -->


    <!-- Navbar Start -->
    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
        <a href="<?= base_url() ?>/smcc-students" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
            <h2 class="m-0 text-primary"><i class="fa fa-book me-3"></i>Saint Michael College of Caraga - BEFS</h2>
        </a>
        <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a href="<?= base_url() ?>/smcc-students" class="nav-item nav-link active">Home</a>
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">PROFILE</a>
                    <div class="dropdown-menu fade-down m-0">
                        <a href="edit_profile_sc" class="dropdown-item">My Profile</a>
                        <a href="log_out_sc" class="dropdown-item">Log Out</a>
                    </div>
                </div>
            </div>
            <?php if ($main_nav_link !== null && $main_nav_label !== null): ?>
                <a href="<?= $main_nav_link ?>" class="btn btn-primary py-4 px-lg-5 d-none d-lg-block"><?= $main_nav_label ?><i class="fa fa-arrow-right ms-3"></i></a>
            <?php endif; ?>
        </div>
    </nav>
    <!-- Navbar End -->
<?php
}