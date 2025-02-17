<?php 
session_start();
include('dbconnect.php');

// Fetch the current school year
$query = mysqli_query($conn, "SELECT * FROM school_year WHERE status = 'Current Set'") or die(mysqli_error($conn));
if ($row = mysqli_fetch_array($query)) {
    $school_year_id = $row['id'];
}

if (isset($_POST['add_student'])) {
    $lrn_num = $_POST['lrn_num'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $gender = $_POST['gender'];
    $course = $_POST['course'];
    $year_level = $_POST['year_level'];
    $section = $_POST['section'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    date_default_timezone_set("Asia/Manila");    
    $dt = date("Y-m-d") . " " . date("h:i:sa");
    
    if ($password == $confirm_password) {
        // Insert student data including the current school year ID
        $query = "INSERT INTO students (lrn_num, fname, lname, gender, course_id, year_level_id, section_id, username, password, date_registered, status, logged_in, level, school_year_id) 
                  VALUES ('$lrn_num', '$fname', '$lname', '$gender', '$course', '$year_level', '$section', '$username', '$password', '$dt', 'For Approval', 'NO', 'PREBOARD1', '$school_year_id')" 
                  or die(mysqli_error($conn));      
        if (mysqli_query($conn, $query)) {
            echo "<script type='text/javascript'>alert('Student Successfully Registered!');
            document.location='index.php'</script>";
        } else {
            echo "Error: " . $query . "<br>" . mysqli_error($conn);
        }
    } else {
        echo "<script type='text/javascript'>alert('Password did not match!');
            document.location='register.php'</script>";
    }    
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Pages / Register</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="images/Smcc_logo.gif" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">

  <!-- =======================================================
  * Template Name: NiceAdmin
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Updated: Apr 20 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body>

  <main>
    <div class="container">

      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

              <div class="d-flex justify-content-center py-4">
              <a>
                  <img src="images/Smcc_logo.gif" alt="" width="150" height="150">
                </a>
              </div><!-- End Logo -->

              <div class="card mb-3">

                <div class="card-body">

                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Create an Account</h5>
                    <p class="text-center small">Enter your personal details to create account</p>
                  </div>

                  <form method="POST" enctype="multipart/form-data" class="row g-3 user needs-validation" novalidate>
                    <div class="col-12">
                      <label for="yourID" class="form-label">ID No.</label>
                      <input type="text" name="lrn_num" class="form-control" id="yourID" required>
                      <div class="invalid-feedback">Please, enter your ID Number!</div>
                    </div>

                    <div class="col-12">
                      <label for="yourName" class="form-label">First Name</label>
                      <input type="text" name="fname" class="form-control" id="yourName" required>
                      <div class="invalid-feedback">Please, enter your First Name!</div>
                    </div>

                    <div class="col-12">
                      <label for="yourEmail" class="form-label">Last Name</label>
                      <input type="text" name="lname" class="form-control" id="yourEmail" required>
                      <div class="invalid-feedback">Please enter your Last Name!</div>
                    </div>

                    <div class="col-12">
                      <label for="yourGender" class="form-label">Gender</label>
                      <select name="gender" class="form-control" id="yourGender" required>
                        <option value="" disabled selected>Select your gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                      </select>
                      <div class="invalid-feedback">Please select your gender!</div>
                    </div>

                    <div class="col-12">
                      <label for="yourEmail" class="form-label">Course</label>
                      <select name="course" class="form-select" aria-label="Default select example">
                        <?php
                          include('dbconnect.php');
                          $query=mysqli_query($conn,"select * from course where status = 'Active' ORDER BY description asc")or die(mysqli_error());
                          while($row=mysqli_fetch_array($query)) {
                              $id=$row['id'];
                              $description=$row['description'];
                        ?>
                          <option value="<?php echo $id; ?>"><?php echo $description; ?></option>
                        <?php } ?>
                      </select>
                      <div class="invalid-feedback">Please select your Course!</div>
                    </div>

                    <div class="col-12">
                      <label for="yourYearLevel" class="form-label">Year Level</label>
                      <select name="year_level" class="form-select" id="yourYearLevel" required>
                        <option value="" selected disabled>Select Year Level</option>
                        <?php
                          $query = mysqli_query($conn, "SELECT * FROM year_level WHERE status = 'Active' ORDER BY description ASC") or die(mysqli_error($conn));
                          while ($row = mysqli_fetch_array($query)) {
                            $y_id = $row['id'];
                            $y_desc = $row['description'];
                        ?>
                          <option value="<?php echo $y_id; ?>"><?php echo $y_desc; ?></option>
                        <?php } ?>
                      </select>
                      <div class="invalid-feedback">Please select your Year Level!</div>
                    </div>

                    <div class="col-12">
                      <label for="yourSection" class="form-label">Section</label>
                      <select name="section" class="form-select" id="yourSection" required>
                        <option value="" selected disabled>Select Section</option>
                        <?php
                          $query = mysqli_query($conn, "SELECT * FROM section WHERE status = 'Active' ORDER BY description ASC") or die(mysqli_error($conn));
                          while ($row = mysqli_fetch_array($query)) {
                            $y_id = $row['id'];
                            $y_desc = $row['description'];
                        ?>
                          <option value="<?php echo $y_id; ?>"><?php echo $y_desc; ?></option>
                        <?php } ?>
                      </select>
                      <div class="invalid-feedback">Please select your Section!</div>
                    </div>

                    <div class="col-12">
                      <label for="yourUsername" class="form-label">Username</label>
                      <div class="input-group has-validation">
                        <input type="text" name="username" class="form-control" id="yourUsername" required>
                        <div class="invalid-feedback">Please enter your username.</div>
                      </div>
                    </div>

                    <div class="col-12">
                      <label for="yourPassword" class="form-label">Password</label>
                      <input type="password" name="password" class="form-control" id="yourPassword" required>
                      <div class="invalid-feedback">Please enter your password!</div>
                    </div>

                    <div class="col-12">
                      <label for="yourPassword" class="form-label">Confirm Password</label>
                      <input type="password" name="confirm_password" class="form-control" id="yourPassword" required>
                      <div class="invalid-feedback">Please enter your Confirm password!</div>
                    </div>

                    <div class="col-12">
                      <button class="btn btn-primary w-100" name="add_student" type="submit">Create Account</button>
                    </div>
                    <div class="col-12">
                      <p class="small mb-0">Already have an account? <a href="index.php">Log in</a></p>
                    </div>
                  </form>

                </div>
              </div>

              <div class="credits">
                &copy <a href="#">SMCC CAPSTONE GROUP</a>
              </div>

            </div>
          </div>
        </div>

      </section>

    </div>
  </main><!-- End #main -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>
