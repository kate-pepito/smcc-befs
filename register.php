<?php 


// Fetch the current school year
$query = conn()->query("SELECT * FROM school_year WHERE status = 'Current Set'") or die(mysqli_error(conn()->get_conn()));
if ($row = mysqli_fetch_array($query)) {
    $school_year_id = $row['id'];
}

if (isset($_POST['add_student'])) {
    $lrn_num = conn()->sanitize($_POST['lrn_num']);
    $fname = conn()->sanitize($_POST['fname']);
    $lname = conn()->sanitize($_POST['lname']);
    $gender = conn()->sanitize($_POST['gender']);
    $course = conn()->sanitize($_POST['course']);
    $year_level = conn()->sanitize($_POST['year_level']);
    $section = conn()->sanitize($_POST['section']);
    $username = conn()->sanitize($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    date_default_timezone_set("Asia/Manila");
    $dt = date("Y-m-d") . " " . date("h:i:sa");

    if ($password == $confirm_password) {
        $password = password_hash($password, PASSWORD_DEFAULT);
        // Insert student data including the current school year ID
        $query = "INSERT INTO students (lrn_num, fname, lname, gender, course_id, year_level_id, section_id, username, password, date_registered, status, logged_in, level, school_year_id) 
                  VALUES ('$lrn_num', '$fname', '$lname', '$gender', '$course', '$year_level', '$section', '$username', '$password', '$dt', 'For Approval', 'NO', 'PREBOARD1', '$school_year_id')" 
                  or die(mysqli_error(conn()->get_conn()));
        if (conn()->query($query)) {
            echo "<script type='text/javascript'>alert('Student Successfully Registered!');
            document.location='" . base_url() . "'</script>";
        } else {
            echo "Error: " . $query . "<br>" . mysqli_error(conn()->get_conn());
        }
    } else {
        echo "<script type='text/javascript'>alert('Password did not match!');
            document.location='register'</script>";
    }
}

admin_html_head("Register", [
  [ "type" => "style", "href" => "assets/css/style.css" ],
]);

?>

<body>

  <main>
    <div class="container">

      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

              <div class="d-flex justify-content-center py-4">
              <a>
                  <img src="<?= base_url() ?>/images/android-icon-192x192.png" alt="" width="150" height="150">
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
                          $query = conn()->query("select * from course where status = 'Active' ORDER BY description asc") or die(mysqli_error(conn()->get_conn()));
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
                          $query = conn()->query("SELECT * FROM year_level WHERE status = 'Active' ORDER BY description ASC") or die(mysqli_error(conn()->get_conn()));
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
                          $query = conn()->query("SELECT * FROM section WHERE status = 'Active' ORDER BY description ASC") or die(mysqli_error(conn()->get_conn()));
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
                      <p class="small mb-0">Already have an account? <a href="<?= base_url() ?>">Log in</a></p>
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

  <?php admin_html_body_end([
    [ "type" => "script", "href" => "assets/js/main.js" ],
  ]); ?>

</body>

</html>
