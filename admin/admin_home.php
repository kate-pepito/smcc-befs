<?php 

authenticated_page("admin");

$query=mysqli_query($conn,"select * from users where id = '$user_id'")or die(mysqli_error($conn));
if($row=mysqli_fetch_array($query))
{
   $logged_in = $row['logged_in'];
   if($logged_in=="NO" || $logged_in==null){
    echo "<script type='text/javascript'>alert('Please login again!');
		document.location='$BASE_URL'</script>";
   }
   else{
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Dashboard - SMCC</title>
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
  <?php require_once get_admin_header(); ?>
  <!-- End Header -->
  <!-- ======= Sidebar ======= -->
  <?php
  $query=mysqli_query($conn,"select * from school_year where status = 'Current Set' and user_id = $user_id")or die(mysqli_error($conn));
  if($row=mysqli_fetch_array($query))
  {
    require_once get_admin_sidebar();
  }
  ?>
  <!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Dashboard</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    
    <div class="row">
  <!-- Column for Reviewers Count -->
  <div class="col-md-6">
    <div class="card info-card">
      <div class="card-body">
        <h5 class="card-title">Reviewers</h5>
        <div class="d-flex align-items-center">
          <div class="card-icon rounded-circle d-flex align-items-center justify-content-center" style="width: 100px; height: 100px; background-color: #f1f1f1;">
            <i class="ri-user-2-fill" style="font-size: 50px; color: #4caf50;"></i>
          </div>
          <?php 
          // Fetch the number of active users where the type is 'FACULTY'
          $query = mysqli_query($conn, "SELECT COUNT(id) AS reviewer_count FROM users WHERE status = 'Active' AND type = 'REVIEWER'") or die(mysqli_error($conn));
          $reviewer_count = 0; // Default value in case of no data
          if ($row = mysqli_fetch_array($query)) {
              $reviewer_count = $row['reviewer_count'];
          }
          ?>
          <div class="ps-3">
            <h6 style="font-size: 35px; font-weight: bold; color: #4caf50;"><?php echo $reviewer_count; ?></h6>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Column for Dean Count -->
  <div class="col-md-6">
    <div class="card info-card">
      <div class="card-body">
        <h5 class="card-title">Dean</h5>
        <div class="d-flex align-items-center">
          <div class="card-icon rounded-circle d-flex align-items-center justify-content-center" style="width: 100px; height: 100px; background-color: #f1f1f1;">
            <i class="ri-user-2-fill" style="font-size: 50px; color: #2196f3;"></i>
          </div>
          <?php 
          // Fetch the number of active users where the type is 'DEAN'
          $query = mysqli_query($conn, "SELECT COUNT(id) AS dean_count FROM users WHERE status = 'Active' AND type = 'DEAN'") or die(mysqli_error($conn));
          $dean_count = 0; // Default value in case of no data
          if ($row = mysqli_fetch_array($query)) {
              $dean_count = $row['dean_count'];
          }
          ?>
          <div class="ps-3">
            <h6 style="font-size: 35px; font-weight: bold; color: #2196f3;"><?php echo $dean_count; ?></h6>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>



<!-- YouTube Video Section Below -->
<div class="row mt-3">
  <div class="col-12">
    <iframe width="100%" height="550" src="https://www.youtube.com/embed/P8vKdsgV1t8" title="Saint Michael College of Caraga Full Corporate AVP 2023" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
  </div>
</div>

  
  </br>
    

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
<?php
}
}
else{
    echo "<script type='text/javascript'>alert('Please login again!');
		document.location='index.php'</script>";
}
?>