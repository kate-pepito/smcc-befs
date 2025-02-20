<?php 
include('../dbconnect.php');
$user_id = mysqli_real_escape_string($conn, $_REQUEST['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Pending Students - SMCC</title>
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

</head>

<body>
<?php 
  // Fetch the Dean's details along with their course ID and profile image
  $query = mysqli_query($conn, "
      SELECT u.fname, u.lname, u.type, u.profile_image, dc.course_id 
      FROM users u 
      JOIN dean_course dc ON u.id = dc.user_id 
      WHERE u.id = '$user_id'
  ") or die(mysqli_error($conn));

  if ($row = mysqli_fetch_array($query)) {
      $fname = ucfirst(strtolower($row['fname']));
      $lname = ucfirst(strtolower($row['lname']));
      $type = ucfirst(strtolower($row['type']));
      $profile_image = $row['profile_image'];  // Assuming this field stores the image path
      $course_id = $row['course_id'];
  } else {
      die("Dean not found or no associated course.");
  }

  // Fetch the course description using the course_id
  $course_query = mysqli_query($conn, "SELECT description FROM course WHERE id = '$course_id'") or die(mysqli_error($conn));
  $course_row = mysqli_fetch_array($course_query);
  $deans_course = $course_row ? $course_row['description'] : "Unknown";
?>

  <!-- ======= Header ======= -->
  <?php include('dean_header.php'); ?>
  <!-- End Header -->
  
  <!-- ======= Sidebar ======= -->
  <?php include('dean_sidebar.php'); ?>
  <!-- End Sidebar -->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>List of Pending Students</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="dean_home_page.php?user_id=<?php echo $user_id; ?>">Dashboard</a></li>
          <li class="breadcrumb-item">Pending Students</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Students</h5>
              
              <!-- Table with stripped rows -->
              <table class="table datatable">
                <thead>
                  <tr>
                    <th>ID No.</th>
                    <th>Last Name</th>
                    <th>First Name</th>
                    <th>Course</th>
                    <th data-type="date" data-format="YYYY/DD/MM">Date Registered</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                <?php
                    // Query to fetch only students in the same course as the Dean
                    $query = mysqli_query($conn, "
                        SELECT course.description AS course, 
                               students.id AS stud_id, 
                               students.lname AS lname, 
                               students.fname AS fname, 
                               students.date_registered AS date_registered 
                        FROM course 
                        JOIN students ON students.course_id = course.id 
                        WHERE students.status = 'For Approval' 
                        AND course.description = '$deans_course'
                    ") or die(mysqli_error($conn));
                    
                    while($row = mysqli_fetch_array($query)) {
                        $stud_id = $row['stud_id'];
                        $lname = $row['lname'];
                        $fname = $row['fname'];
                        $course = $row['course'];
                        $date_registered = $row['date_registered'];
                ?>
                  <tr>
                      <td><?php echo $stud_id; ?></td>
                      <td><?php echo $lname; ?></td>
                      <td><?php echo $fname; ?></td>
                      <td><?php echo $course; ?></td>
                      <td><?php echo $date_registered; ?></td>
                      <td>
                        <a href="dean_students_profile_confirm.php?user_id=<?php echo $user_id;?>&stud_id=<?php echo $stud_id; ?>">Confirm</a> / 
                        <a href="dean_students_pending_decline_sc.php?user_id=<?php echo $user_id;?>&stud_id=<?php echo $stud_id; ?>">Decline</a>
                      </td>
                  </tr>
                <?php 
                    } 
                ?>
                </tbody>
              </table>
              <!-- End Table with stripped rows -->

            </div>
          </div>

        </div>
      </div>
    </section>

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <?php include('../footer.php'); ?>
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
