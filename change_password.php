<?php
session_start();
require_once './dbconnect.php';

$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . "/smcc-befs";
$homepage = '';

// Check if user_id is passed via the request
$user_id = mysqli_real_escape_string($conn, $_REQUEST['user_id']);


if (isset($_POST['change_password'])) {
  $password = $_POST['password'];
  $confirm_password = $_POST['confirm_password'];

  if ($password === $confirm_password) {
    $password = password_hash($password, PASSWORD_DEFAULT);
    $query = "UPDATE users SET password = '$password' WHERE id = '$user_id'";

    if (mysqli_query($conn, $query)) {
      // Fetch the user type based on the user_id
      $user_type_query = "SELECT type FROM users WHERE id = '$user_id'";
      $result = mysqli_query($conn, $user_type_query);

      if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $type = $row['type'];

        // Determine the appropriate homepage
        switch ($type) {
          case 'REVIEWER':
            $homepage = 'index.php';
            break;
          case 'DEAN':
            $homepage = 'index.php';
            break;
          case 'ADMIN':
            $homepage = 'index.php'; // Change to admin_profile.php for admin
            break;
          default:
            // Default page in case type doesn't match
            $homepage = 'index.php';
            break;
        }
        echo "Homepage: " . $homepage . "<br>"; // Debugging homepage

        // Redirect to the appropriate homepage
        echo "<script type='text/javascript'>
                      alert('Password Successfully Changed!');
                      window.location.href = '$base_url/$homepage?user_id=$user_id';
                    </script>";
      } else {
        echo "<script type='text/javascript'>
                        alert('Error: Unable to determine user type.');
                        window.location.href = 'login.php';
                      </script>";
      }
    } else {
      echo "<script type='text/javascript'>
                    alert('Error: Failed to update password.');
                    window.location.href = 'change_password.php?user_id=$user_id';
                  </script>";
    }
  } else {
    echo "<script type='text/javascript'>
                alert('Passwords do not match. Please try again.');
                window.location.href = 'change_password.php?user_id=$user_id';
              </script>";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Change Password</title>
  <link href="images/Smcc_logo.gif" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
</head>

<body>
  <main>
    <div class="container">
      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">
              <div class="d-flex justify-content-center py-4">
                <a><img src="images/Smcc_logo.gif" alt="" width="150" height="150"></a>
              </div>
              <div class="card mb-3">
                <div class="card-body">
                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Change Password</h5>
                    <p class="text-center small">Please don't forget your password, Thank you!</p>
                  </div>

                  <form action="change_password.php?user_id=<?php echo $user_id; ?>" method="POST" enctype="multipart/form-data" class="row g-3 user needs-validation" novalidate>
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
                      <button class="btn btn-primary w-100" name="change_password" type="submit">Change Password</button>
                    </div>
                    <!-- <div class="col-12">
                    <a class="btn btn-warning w-100" href="<?php echo $base_url . '/' . $homepage . '?user_id=' . $user_id; ?>">Back to Dashboard</a>
                  </div> -->
                  </form>
                </div>
              </div>

              <div class="credits">
                Designed by <a href="#">SMCC CAPSTONE GROUP 17</a>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </main>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/js/main.js"></script>
</body>

</html>