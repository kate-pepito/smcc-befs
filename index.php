<?php 
session_start();
include('dbconnect.php'); 

// Initialize variables to prevent 'undefined variable' warnings
$type = '';
$status = '';
$id = '';
$stud_id = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and retrieve inputs
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $mypassword = mysqli_real_escape_string($conn, $_POST['password']);

    // Query the users table
    $query = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'") or die(mysqli_error($conn));

    // Check if user exists
    if (mysqli_num_rows($query) > 0) {
        $row = mysqli_fetch_assoc($query);
        $id = $row['id'];
        $username = $row['username'];
        $fname = $row['fname'];
        $lname = $row['lname'];
        $type = $row['type'];
        $status = $row['status'];

        // Verify password and status
        $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$mypassword' AND status = 'Active'";
        $result = mysqli_query($conn, $sql);
        $count = mysqli_num_rows($result);

        if ($count == 1) {
            // Update logged_in status
            mysqli_query($conn, "UPDATE users SET logged_in = 'YES' WHERE id = '$id'") or die(mysqli_error($conn));

            if ($type == 'ADMIN') {
                echo "<script>alert('Welcome, Admin!'); document.location='admin/admin_home.php?user_id=$id';</script>";
            } elseif ($type == 'REVIEWER') {
                echo "<script>alert('Welcome, Reviewer!'); document.location='reviewer/reviewer_home.php?user_id=$id';</script>";
            } elseif ($type == 'DEAN') {
                echo "<script>alert('Welcome, Dean!'); document.location='dean/dean_home_page.php?user_id=$id';</script>";
            }
        } else {
            echo "<script>alert('Invalid credentials or inactive account.'); document.location='index.php';</script>";
        }
    } 
    // If not a user, check students table
    else {
        $sql = "SELECT * FROM students WHERE username = '$username' AND password = '$mypassword' AND status = 'Active'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);
            $stud_id = $row['id'];

            // Update logged_in status
            mysqli_query($conn, "UPDATE students SET logged_in = 'YES' WHERE id = '$stud_id'") or die(mysqli_error($conn));

            echo "<script>alert('Welcome, Student!'); document.location='smcc-students/index.php?user_id=$stud_id';</script>";
        } else {
            echo "<script>alert('Invalid credentials or inactive account.'); document.location='index.php';</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Login - SMCC</title>
  <link href="images/Smcc_logo.gif" rel="icon">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">

   <!-- Google Fonts -->
   <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

</head>
<body>
  <main>
    <div class="container">
      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">
              <div class="d-flex justify-content-center py-4">
                <img src="images/Smcc_logo.gif" alt="" width="150" height="150">
              </div>
              <div class="card mb-3">
                <div class="card-body">
                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Login to Your Account</h5>
                    <p class="text-center small">Enter your username & password to login</p>
                  </div>
                  <form method="post" class="row g-3 needs-validation" novalidate>
                    <div class="col-12">
                      <label for="yourUsername" class="form-label">Username</label>
                      <input type="text" name="username" class="form-control" id="yourUsername" required>
                      <div class="invalid-feedback">Please enter your username.</div>
                    </div>
                    <div class="col-12">
                      <label for="yourPassword" class="form-label">Password</label>
                      <input type="password" name="password" class="form-control" id="yourPassword" required>
                      <div class="invalid-feedback">Please enter your password!</div>
                    </div>
                    <div class="col-12">
                      <button class="btn btn-primary w-100" type="submit">Login</button>
                    </div>
                    <div class="col-12">
                      <p class="small mb-0">Don't have an account? <a href="register.php">Create an account</a></p>
                    </div>
                  </form>
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
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>