<?php

authenticated_page("dean");

$s_id = mysqli_real_escape_string(conn(), $_REQUEST['s_id']);
$s_code = mysqli_real_escape_string(conn(), $_REQUEST['s_code']);
$s_desc = mysqli_real_escape_string(conn(), $_REQUEST['s_desc']);

admin_html_head("Add Subjects", [
  [ "type" => "style", "href" => "assets/css/style.css" ],
]); // html head

?>

<body>
<?php 

  $query=mysqli_query(conn(),"select * from users where id = '" . user_id() . "'")or die(mysqli_error(conn()));
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
              <form action="./dean_subjects_update_sc?s_id=<?php echo $s_id; ?>" method="POST" enctype="multipart/form-data" class="row g-3 user needs-validation" novalidate>
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

  <?php admin_html_body_end([
      ["type" => "script", "src" => "assets/js/main.js"],
  ]); ?>

</body>

</html>