<?php

authenticated_page("dean");


// Get the current school year
$current_school_year_query = conn()->query("SELECT id, description FROM school_year WHERE status = 'Current Set' LIMIT 1") or die(mysqli_error(conn()->get_conn()));
if ($current_school_year_row = mysqli_fetch_array($current_school_year_query)) {
    $current_school_year_id = $current_school_year_row['id'];
    $current_school_year_description = $current_school_year_row['description'];
} else {
    $current_school_year_id = null;
    $current_school_year_description = "No Current Set";
}


$query = conn()->query("select * from users where id = '" . user_id() . "'") or die(mysqli_error(conn()->get_conn()));
if ($row = mysqli_fetch_array($query)) {
    $fname = $row['fname'];
    $lname = $row['lname'];
    $type = $row['type'];
    $fname = ucfirst(strtolower($fname));
    $lname = ucfirst(strtolower($lname));
    $type = ucfirst(strtolower($type));
}

admin_html_head("Recommended Board Takers", [
    [ "type" => "style", "href" => "assets/vendor/simple-datatables/style.css" ],
    [ "type" => "style", "href" => "assets/css/style.css" ],
]); // html head

?>

<body>
    <!-- Header -->
    <?php require_once get_dean_header(); ?>
    <!-- End Header -->
    <!-- ======= Sidebar ======= -->
    <?php  require_once get_dean_sidebar(); ?>
    <!-- End Sidebar-->

    <main id="main" class="main">

        <div class="pagetitle">
            <h1>Recommended Board Takers</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">Recommended Board Takers</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section">
            
        </section>

    </main><!-- End #main -->

    <!-- ======= Footer ======= -->
    <?php require_once get_footer(); ?>
    <!-- End Footer -->

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <?php admin_html_body_end([
        ["type" => "script", "src" => "assets/vendor/simple-datatables/simple-datatables.js"],
        ["type" => "script", "src" => "https://cdn.jsdelivr.net/npm/onnxruntime-web/dist/ort.min.js"],
        ["type" => "script", "src" => "assets/js/main.js"],
        ["type" => "script", "src" => "assets/js/inference.js"],
        ["type" => "script", "src" => "assets/js/forecast.js"],
    ]); ?>
</body>

</html>