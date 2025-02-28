<?php 

authenticated_page("admin");


$query=conn()->query("select * from users where id = '" . user_id() . "'")or die(mysqli_error(conn()->get_conn()));
if($row=mysqli_fetch_array($query))
{
  $fname=$row['fname'];
  $lname=$row['lname'];
  $type=$row['type'];
  $fname = ucfirst(strtolower($fname));
  $lname = ucfirst(strtolower($lname));
  $type = ucfirst(strtolower($type));
}


admin_html_head("Dashboard", [
  [ "type" => "style", "href" => "assets/vendor/remixicon/remixicon.css" ],
  [ "type" => "style", "href" => "assets/css/style.css" ],
]); // html head

?>
<body>
  <!-- ======= Header ======= -->
  <?php require_once get_admin_header(); ?>
  <!-- End Header -->
  <!-- ======= Sidebar ======= -->
  <?php
  $query=conn()->query("select * from school_year where status = 'Current Set' and user_id = '". user_id() . "'")or die(mysqli_error(conn()->get_conn()));
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
          $query = conn()->query("SELECT COUNT(id) AS reviewer_count FROM users WHERE status = 'Active' AND type = 'REVIEWER'") or die(mysqli_error(conn()->get_conn()));
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
          $query = conn()->query("SELECT COUNT(id) AS dean_count FROM users WHERE status = 'Active' AND type = 'DEAN'") or die(mysqli_error(conn()->get_conn()));
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

  <?php admin_html_body_end([
      ["type" => "script", "src" => "assets/js/main.js"],
  ]); ?>

</body>

</html>