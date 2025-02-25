<?php

$homepage = '';

// Check if user_id is passed via the request



if (isset($_POST['change_password'])) {
  $password = $_POST['password'];
  $confirm_password = $_POST['confirm_password'];

  if ($password === $confirm_password) {
    $password = password_hash($password, PASSWORD_DEFAULT);
    $query = "UPDATE users SET password = '$password' WHERE id = '" . user_id() . "'";

    if (mysqli_query(conn(), $query)) {
      // Fetch the user type based on the user_id
      $user_type_query = "SELECT type FROM users WHERE id = '" . user_id() . "'";
      $result = mysqli_query(conn(), $user_type_query);

      if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $type = $row['type'];

        // Determine the appropriate homepage
        switch ($type) {
          case 'REVIEWER':
            $homepage = '';
            break;
          case 'DEAN':
            $homepage = '';
            break;
          case 'ADMIN':
            $homepage = ''; // Change to admin_profile for admin
            break;
          default:
            // Default page in case type doesn't match
            $homepage = '';
            break;
        }
        echo "Homepage: " . $homepage . "<br>"; // Debugging homepage

        // Redirect to the appropriate homepage
        echo "<script type='text/javascript'>
                      alert('Password Successfully Changed!');
                      window.location.href = '" . base_url() ."/$homepage';
                    </script>";
      } else {
        echo "<script type='text/javascript'>
                        alert('Error: Unable to determine user type.');
                        window.location.href = '" . base_url() ."';
                      </script>";
      }
    } else {
      echo "<script type='text/javascript'>
                    alert('Error: Failed to update password.');
                    window.location.href = 'change_password';
                  </script>";
    }
  } else {
    echo "<script type='text/javascript'>
                alert('Passwords do not match. Please try again.');
                window.location.href = 'change_password';
              </script>";
  }
}

admin_html_head("Change Password", [
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
                <a><img src="<?= base_url() ?>/images/android-icon-192x192.png" alt="" width="150" height="150"></a>
              </div>
              <div class="card mb-3">
                <div class="card-body">
                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Change Password</h5>
                    <p class="text-center small">Please don't forget your password, Thank you!</p>
                  </div>

                  <form method="POST" enctype="multipart/form-data" class="row g-3 user needs-validation" novalidate>
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

  <?php admin_html_body_end([
    [ "type" => "script", "href" => "assets/js/main.js" ],
  ]); ?>

</body>

</html>