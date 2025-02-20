<?php 
session_start();
include('../dbconnect.php');

$user_id = mysqli_real_escape_string($conn, $_REQUEST['user_id']);
$f_id = mysqli_real_escape_string($conn, $_REQUEST['f_id']);

// Fetch faculty details to populate the form
$query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$f_id'") or die(mysqli_error($conn));
if ($row = mysqli_fetch_array($query)) {
    $f_fname = $row['fname'];
    $f_lname = $row['lname'];
}

// Check if form is submitted to update the faculty
if (isset($_POST['update_faculty'])) {
    $f_fname = mysqli_real_escape_string($conn, $_POST['fname']);
    $f_lname = mysqli_real_escape_string($conn, $_POST['lname']);

    // Update faculty details in the database
    $update_query = "UPDATE users SET fname = '$f_fname', lname = '$f_lname' WHERE id = '$f_id'";
    if (mysqli_query($conn, $update_query)) {
        echo "<script type='text/javascript'>
                alert('Reviewer Successfully Updated!');
                window.location.href = 'admin_faculty.php?user_id=$user_id';
            </script>";
    } else {
        echo "<script type='text/javascript'>
                alert('Error updating. Please try again.');
                window.location.href = 'admin_faculty.php?user_id=$user_id';
            </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Update Reviewer - SMCC</title>

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
$query=mysqli_query($conn,"SELECT * FROM users WHERE id = '$user_id'")or die(mysqli_error($conn));
if($row=mysqli_fetch_array($query)) {
    $fname = ucfirst(strtolower($row['fname']));
    $lname = ucfirst(strtolower($row['lname']));
    $type = ucfirst(strtolower($row['type']));
}
?>

  <!-- ======= Header ======= -->
  <?php include('../header.php'); ?>
  <!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <?php include('../sidebar.php'); ?>
  <!-- End Sidebar -->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Update Reviewer</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="admin_home.php?user_id=<?php echo $user_id; ?>">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="admin_faculty.php?user_id=<?php echo $user_id; ?>">Reviewer</a></li>
          <li class="breadcrumb-item active">Update Reviewer</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Update Form</h5>

              <!-- General Form Elements -->
              <form action="" method="POST" enctype="multipart/form-data" class="row g-3 user needs-validation" novalidate>
                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label">First Name</label>
                  <div class="col-sm-10">
                    <input name="fname" type="text" value="<?php echo $f_fname; ?>" class="form-control" required>
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputEmail" class="col-sm-2 col-form-label">Last Name</label>
                  <div class="col-sm-10">
                    <input name="lname" type="text" value="<?php echo $f_lname; ?>" class="form-control" required>
                  </div>
                </div>
            
                <div class="row mb-3">
                  <div align="right">
                    <button name="update_faculty" type="submit" class="btn btn-primary">Update Record</button>
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
