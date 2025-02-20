<?php

authenticated_page("dean");

$s_id = mysqli_real_escape_string($conn, $_REQUEST['s_id']);
$s_code = mysqli_real_escape_string($conn, $_REQUEST['s_code']);
$s_desc = mysqli_real_escape_string($conn, $_REQUEST['s_desc']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Add Subjects - SMCC</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="<?= $BASE_URL ?>/images/Smcc_logo.gif" rel="icon">
  <link href="<?= $BASE_URL ?>/assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="<?= $BASE_URL ?>/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= $BASE_URL ?>/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="<?= $BASE_URL ?>/assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="<?= $BASE_URL ?>/assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="<?= $BASE_URL ?>/assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="<?= $BASE_URL ?>/assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="<?= $BASE_URL ?>/assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="<?= $BASE_URL ?>/assets/css/style.css" rel="stylesheet">

  <!-- =======================================================
  * Template Name: NiceAdmin
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Updated: Apr 20 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body>
<?php 

  $query=mysqli_query($conn,"select * from users where id = '$user_id'")or die(mysqli_error($conn));
    if($row=mysqli_fetch_array($query))
    {
      $fname=$row['fname'];
      $lname=$row['lname'];
      $type=$row['type'];
      $fname = ucfirst(strtolower($fname));
      $lname = ucfirst(strtolower($lname));
      $type = ucfirst(strtolower($type));
    }

?>
  <!-- ======= Header ======= -->
  <?php
   require_once get_dean_header();
   ?><!-- End Header -->
  <!-- ======= Sidebar ======= -->
  <?php
  require_once get_dean_sidebar();
  ?>
  <!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Add Subjects</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="admin_home">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="admin_subjects">Subjects</a></li>
          <li class="breadcrumb-item active">Add Subjects</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Form</h5>

              <!-- General Form Elements -->
              <form action="./dean_subjects_update_sc?user_id=<?php echo $user_id; ?>&s_id=<?php echo $s_id; ?>" method="POST" enctype="multipart/form-data" class="row g-3 user needs-validation" novalidate>
                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label">Subject Code</label>
                  <div class="col-sm-10">
                    <input name="subject_code" type="text" value="<?php echo $s_code; ?>" class="form-control" required>
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputEmail" class="col-sm-2 col-form-label">Description</label>
                  <div class="col-sm-10">
                    <input name="description" type="text" value="<?php echo $s_desc; ?>" class="form-control" required>
                  </div>
                </div>
               

                <div class="row mb-3">
                  <div align="right">
                    <button name="update_subject" type="submit" class="btn btn-primary">Update Subject</button>
                  </div>
                </div>

              </form><!-- End General Form Elements -->

            </div>
          </div>
        </div>
      </div>
    </section>

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
   <?php
   require_once get_footer();
   ?>
   <!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="<?= $BASE_URL ?>/assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="<?= $BASE_URL ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="<?= $BASE_URL ?>/assets/vendor/chart.js/chart.umd.js"></script>
  <script src="<?= $BASE_URL ?>/assets/vendor/echarts/echarts.min.js"></script>
  <script src="<?= $BASE_URL ?>/assets/vendor/quill/quill.js"></script>
  <script src="<?= $BASE_URL ?>/assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="<?= $BASE_URL ?>/assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="<?= $BASE_URL ?>/assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="<?= $BASE_URL ?>/assets/js/main.js"></script>

</body>

</html>