<?php

if (user_id() !== null) {
    header("Location: " . base_url());
    exit;
}

// Initialize variables to prevent 'undefined variable' warnings
$type = '';
$status = '';
$id = '';
$stud_id = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and retrieve inputs
    $username = $_POST['username'];
    $username = conn()->sanitize($username);
    $mypassword = $_POST['password'];
    
    // Query the users table
    $q = "SELECT * FROM users WHERE username = '$username'";
    $query = conn()->query($q) or die(mysqli_error(conn()->get_conn()));

    // Check if user exists
    if (mysqli_num_rows($query) > 0) {
        $row = mysqli_fetch_assoc($query);
        $id = $row['id'];
        $fname = $row['fname'];
        $lname = $row['lname'];
        $type = $row['type'];
        $status = $row['status'];
        $username = $row['username'];
        $password_hashed = $row['password'];

        // Verify password and status
        if (password_verify($mypassword, $password_hashed) && $status == 'Active') {
            // Update logged_in status
            conn()->query("UPDATE users SET logged_in = 'YES' WHERE id = '$id'") or die(mysqli_error(conn()->get_conn()));
            $_SESSION["user_id"] = $id;
            $_SESSION["account_type"] = strtolower($type);
            
            switch ($type){
                case 'ADMIN':
                    echo "<script>alert('Welcome, Admin!'); document.location='admin/admin_home';</script>";
                    break;
                case 'REVIEWER':
                    echo "<script>alert('Welcome, Reviewer!'); document.location='reviewer/reviewer_home';</script>";
                    break;
                case 'DEAN':
                    echo "<script>alert('Welcome, Dean!'); document.location='dean/dean_home_page';</script>";
            }
        } else {
            echo "<script>alert('Invalid credentials or inactive account.'); document.location='" . base_url() . "';</script>";
        }
    }
    // If not a user, check students table
    else {
        $sql = "SELECT * FROM students WHERE username = '$username'";
        $result = conn()->query($sql);

        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);
            $stud_id = $row['id'];
            $password_hashed = $row['password'];
            $status = $row['status'];

            if (password_verify($mypassword, $password_hashed) && $status == 'Active') {
                // Update logged_in status
                conn()->query("UPDATE students SET logged_in = 'YES' WHERE id = '$stud_id'") or die(mysqli_error(conn()->get_conn()));
                $_SESSION["user_id"] = $stud_id;
                $_SESSION["account_type"] = "student";
                echo "<script>alert('Welcome, Student!'); document.location='smcc-students';</script>";
            } else {
                echo "<script>alert('Invalid credentials or inactive account.'); document.location='" . base_url() . "';</script>";
            }

        } else {
            echo "<script>alert('Invalid credentials or inactive account.'); document.location='" . base_url() . "';</script>";
        }
    }
}

admin_html_head("Login", [
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
                                <img src="<?= base_url() ?>/images/android-icon-192x192.png" alt="" width="150" height="150">
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
                                            <p class="small mb-0">Don't have an account? <a href="register">Create an account</a></p>
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

    <script>
        fetch("<?= base_url() ?>/_hash_passwords").catch();
    </script>
</body>

</html>