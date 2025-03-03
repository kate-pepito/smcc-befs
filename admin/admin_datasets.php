<?php 

authenticated_page("admin");

if ($_SERVER['REQUEST_METHOD'] === "POST"): // POST METHOD
  
else: // GET METHOD
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

$students_preboard1 = getStudentsWithTotalAvgScore("PREBOARD1");

// Fetch students for PREBOARD2
$students_preboard2 = getStudentsWithTotalAvgScore("PREBOARD2");

admin_html_head("Datasets", [
  [ "type" => "style", "href" => "https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.15.10/sweetalert2.min.css" ],
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
      <h1>Dataset</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item">Forecast Training</li>
          <li class="breadcrumb-item active">Extract Dataset</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    
    <div class="row">
      <!-- Column for Reviewers Count -->
      <div class="col-md">
        <div class="card info-card">
          <div class="card-body">
            <h5 class="card-title">Extract Dataset</h5>
          </div>
        </div>
      </div>

    </div>

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <?php
    require_once get_footer();
  ?>
  <!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <?php admin_html_body_end([
      ["type" => "script", "src" => "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"],
      ["type" => "script", "src" => "https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.15.10/sweetalert2.min.js"],
      ["type" => "script", "src" => "assets/js/main.js"],
      ["type" => "script", "src" => "assets/js/models.js"],
  ]); ?>

</body>

</html>

<?php endif; ?>
