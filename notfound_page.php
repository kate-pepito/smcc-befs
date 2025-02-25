<?php
admin_html_head("Page Not Found", [
    [ "type" => "style", "href" => "assets/css/style.css" ],
]);
?>

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
                                        <h5 class="card-title text-center pb-0 fs-4">Page Not Found</h5>
                                        <p class="text-center small">You are trying to access page that is not found.</p>
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