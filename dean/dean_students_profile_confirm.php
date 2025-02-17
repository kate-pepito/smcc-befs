<!DOCTYPE html>
<html lang="en">
<?php
include('../dbconnect.php');
$user_id = $_REQUEST['user_id'];
$stud_id = $_REQUEST['stud_id'];
?>
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Users / Profile - SMCC</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="../images/Smcc_logo.gif" rel="icon">
  <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="../assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="../assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="../assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="../assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="../assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="../assets/css/style.css" rel="stylesheet">

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

  $query=mysqli_query($conn,"select * from users where id = '$user_id'")or die(mysqli_error());
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
   include('dean_header.php');
   ?><!-- End Header -->
  <!-- ======= Sidebar ======= -->
 <?php 
 include('dean_sidebar.php');
 ?>
  <!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Students Approval</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="dean_home_page.php?user_id=<?php echo $user_id; ?>">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="dean_students_pending.php?user_id=<?php echo $user_id; ?>">List of Pending Students</a></li>
          <li class="breadcrumb-item">Students Approval</h1>
          <nav></li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section profile">
      <div class="row">
  

        <div class="col-xl-12">

          <div class="card">
            <div class="card-body pt-3">
              <!-- Bordered Tabs -->
              <ul class="nav nav-tabs nav-tabs-bordered">

                <li class="nav-item">
                  <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview">Validation</button>
                </li>

              </ul>
              <div class="tab-content pt-2">           
                <div class="tab-pane fade show active profile-overview" id="profile-overview">
  </br>
  <?php
// Ensure the student data is fetched from the database correctly
$query = mysqli_query($conn, "SELECT students.lrn_num AS lrn_num, students.fname AS fname, students.lname AS lname, students.gender AS gender, students.username AS username, course.description AS course, section.description AS section, year_level.description AS year_level FROM students JOIN course ON students.course_id = course.id JOIN section ON students.section_id = section.id JOIN year_level ON students.year_level_id = year_level.id WHERE students.id = '$stud_id'") or die(mysqli_error($conn));

// Check if the query fetched data
if ($row = mysqli_fetch_array($query)) {
    $lrn_num = $row['lrn_num'];
    $fname = $row['fname'];
    $lname = $row['lname'];
    $gender = $row['gender'];
    $username = $row['username'];
    $year_level = $row['year_level'];
    $course = $row['course'];
    $section = $row['section'];
} else {
    // Debug: Output if no data was found
    echo "No data found for student with ID: $stud_id";
}
?>

                <form action="dean_students_profile_confirm_sc.php?user_id=<?php echo $user_id; ?>&stud_id=<?php echo $stud_id; ?>" method="POST" enctype="multipart/form-data" class="row g-3 user needs-validation" novalidate>
                    <div class="row mb-3">
                      <label for="IDnum" class="col-md-4 col-lg-3 col-form-label">ID No.</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="lrn_num" type="text" class="form-control" id="Idnum" value="<?php echo $lrn_num; ?>" ReadOnly>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Full Name</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="fullName" type="text" class="form-control" id="fullName" value="<?php echo $fname." ".$lname; ?>" ReadOnly>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Gender" class="col-md-4 col-lg-3 col-form-label">Gender</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="Gender" type="text" class="form-control" id="Gender" value="<?php echo $gender; ?>" ReadOnly>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="company" class="col-md-4 col-lg-3 col-form-label">Username</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="company" type="text" class="form-control" id="company" value="<?php echo $username; ?> " ReadOnly>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="year_level" class="col-md-4 col-lg-3 col-form-label">Year Level</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="year_level" type="text" class="form-control" id="year_level" value="<?php echo $year_level; ?> " ReadOnly>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Address" class="col-md-4 col-lg-3 col-form-label">Course</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="address" type="text" class="form-control" id="Address" value="<?php echo $course; ?>" ReadOnly>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="section" class="col-md-4 col-lg-3 col-form-label">Section</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="section" type="text" class="form-control" id="section" value="<?php echo $section; ?> " ReadOnly>
                      </div>
                    </div>

                    <div class="text-center">
                      <button name="enroll_student" type="submit" class="btn btn-primary">Approve Student</button>
                    </div>
                  </form>
              </div>


              </div><!-- End Bordered Tabs -->

            </div>
          </div>

        </div>
      </div>
    </section>

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
   <?php 
   include('../footer.php');
   ?>
   <!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="../assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/vendor/chart.js/chart.umd.js"></script>
  <script src="../assets/vendor/echarts/echarts.min.js"></script>
  <script src="../assets/vendor/quill/quill.js"></script>
  <script src="../assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="../assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="../assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="../assets/js/main.js"></script>

</body>

</html>