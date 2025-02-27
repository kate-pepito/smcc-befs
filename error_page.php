<?php

if (!function_exists("load_dotenv")) {
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
}
if (!function_exists("get_base_uri_path")) {
    function get_base_uri_path()
    {
        return $_ENV['BEFS_BASE_URI'] ?? "/smcc-befs";
    }
}
if (!function_exists("base_url")) {
    function base_url(): string
    {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . get_base_uri_path();
    }
}
load_dotenv();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <title>Internal Server Error - SMCC BEFS</title>
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
    <?php $imports = [
        [ "type" => "style", "href" => "https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" ],
        [ "type" => "style", "href" => "assets/vendor/bootstrap/css/bootstrap.min.css" ],
        [ "type" => "style", "href" => "https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" ],
        [ "type" => "style", "href" => "assets/js/main.js", ]
    ]; ?>
        
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
<body>
    <main>
        <div class="container">
            <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">
                            <div class="d-flex justify-content-center py-4">
                                <img src="<?= base_url() ?>/images/android-icon-192x192.png" alt="" width="150" height="150">
                            </div>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="pt-4 pb-2">
                                        <h5 class="card-title text-center pb-0 fs-4">Internal Server Error (500)</h5>
                                        <p class="text-center small">There seems to be a problem with the page at this point.</p>
                                    </div>
                                    <div class="ps-3 pe-3 py-2 pb-2 w-100 border border-danger rounded" style="min-height: 150px;">
                                        <code class="w-100">
                                            Error: <?= $error->getMessage(); ?><hr />
                                            <?php if (($_ENV['BEFS_LOG_LEVEL'] ?? "production") === "development"): ?>
                                            Line <?= $error->getLine(); ?> in <?= substr($error->getFile(), strpos($error->getFile(),str_replace("/", DIRECTORY_SEPARATOR, get_base_uri_path()))); ?><br />
                                            <?php endif; ?>
                                        </code>
                                    </div>
                                    <div class="p-4 w-100 text-center mx-auto">
                                        <a href="<?= base_url() ?>" class="btn btn-primary btn-lg btn-block">Back to Home</a>
                                    </div>
                                </div>
                            </div>
                            <div class="copyright">
                                &copy; <strong><span>SMCC</span></strong>. All Rights Reserved
                            </div>
                            <div class="credits">
                                Developed by <a href="#" title="Kate Pepito, Joshua Pilapil, Regie Torregosa">SMCC CAPSTONE GROUP 17</a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>
    
</body>

</html>