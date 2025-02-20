
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Login - SMCC</title>
    <link href="<?= $BASE_URL ?>/images/Smcc_logo.gif" rel="icon">
    <link href="<?= $BASE_URL ?>/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= $BASE_URL ?>/assets/css/style.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

</head>

<body>
    <main>
        <div class="container">
            <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">
                            <div class="d-flex justify-content-center py-4">
                                <img src="<?= $BASE_URL ?>/images/Smcc_logo.gif" alt="" width="150" height="150">
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
                                            Line <?= $error->getLine(); ?> in <?= substr($error->getFile(), strpos($error->getFile(),str_replace("/", DIRECTORY_SEPARATOR, get_base_uri_path()))); ?><br />
                                        </code>
                                    </div>
                                    <div class="p-4 w-100 text-center mx-auto">
                                        <a href="<?= $BASE_URL ?>" class="btn btn-primary btn-lg btn-block">Back to Home</a>
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
    <script src="<?= $BASE_URL ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>