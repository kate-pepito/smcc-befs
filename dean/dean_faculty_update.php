<?php 

authenticated_page("dean");

$f_id = conn()->sanitize($_REQUEST['f_id']);

// Fetch faculty details to populate the form
$query = conn()->query("SELECT * FROM users WHERE id = '$f_id'") or die(mysqli_error(conn()->get_conn()));
if ($row = mysqli_fetch_array($query)) {
    $f_fname = $row['fname'];
    $f_lname = $row['lname'];
}

// Check if form is submitted to update the faculty
if (isset($_POST['update_faculty'])) {
    $f_fname = conn()->sanitize($_POST['fname']);
    $f_lname = conn()->sanitize($_POST['lname']);

    // Update faculty details in the database
    $update_query = "UPDATE users SET fname = '$f_fname', lname = '$f_lname' WHERE id = '$f_id'";
    if (conn()->query($update_query)) {
        echo "<script type='text/javascript'>
                alert('Reviewer Successfully Updated!');
                window.location.href = 'dean_faculty';
            </script>";
    } else {
        echo "<script type='text/javascript'>
                alert('Error updating. Please try again.');
                window.location.href = 'dean_faculty';
            </script>";
    }
}


admin_html_head("Update Reviewer", [
  [ "type" => "style", "href" => "assets/css/style.css" ],
]); // html head

?>

<body>

<?php 
$query=conn()->query("SELECT * FROM users WHERE id = '" . user_id() . "'")or die(mysqli_error(conn()->get_conn()));
if($row=mysqli_fetch_array($query)) {
    $fname = ucfirst(strtolower($row['fname']));
    $lname = ucfirst(strtolower($row['lname']));
    $type = ucfirst(strtolower($row['type']));
}
?>

  <!-- ======= Header ======= -->
  <?php require_once get_dean_header(); ?>
  <!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <?php require_once get_dean_sidebar(); ?>
  <!-- End Sidebar -->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Update Reviewer</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="dean_home_page">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="dean_faculty">Reviewer</a></li>
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
              <form method="POST" enctype="multipart/form-data" class="row g-3 user needs-validation" novalidate>
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
  <?php require_once get_footer(); ?>
  <!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <?php admin_html_body_end([
      ["type" => "script", "src" => "assets/js/main.js"],
  ]); ?>

</body>

</html>
