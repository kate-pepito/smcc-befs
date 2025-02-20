<?php

authenticated_page("admin");


if (isset($_POST['add_dean'])) {
    $fname = mysqli_real_escape_string($conn, $_POST['fname']);
    $lname = mysqli_real_escape_string($conn, $_POST['lname']);
    $course = mysqli_real_escape_string($conn, $_POST['course']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Set the timezone to Asia/Manila
    date_default_timezone_set("Asia/Manila");
    $dt = date("Y-m-d") . " " . date("h:i:sa");

    if ($password == $confirm_password) {
        $password = password_hash($password, PASSWORD_DEFAULT);
        // Insert user data into the 'users' table
        $query = "INSERT INTO users (username, password, type, status, fname, lname, date_created, logged_in) 
                  VALUES ('$username', '$password', 'DEAN', 'Active', '$fname', '$lname', '$dt', 'NO')";
        if (mysqli_query($conn, $query)) {
            // Get the newly inserted user ID
            $query = "SELECT * FROM users WHERE fname = '$fname' AND lname = '$lname'";
            $result = mysqli_query($conn, $query);
            if ($row = mysqli_fetch_array($result)) {
                $f_id = $row['id'];

                // Get the current school year
                $query = "SELECT * FROM school_year WHERE status = 'Current Set'";
                $result = mysqli_query($conn, $query);
                if ($row = mysqli_fetch_array($result)) {
                    $school_year_id = $row['id'];

                    // Insert the faculty course and school year relation
                    $query = "INSERT INTO dean_course (user_id, course_id) 
                              VALUES ('$f_id', '$course')";
                    if (mysqli_query($conn, $query)) {
                        echo "<script type='text/javascript'>alert('Dean Successfully Saved!'); 
                        document.location='admin_dean'</script>";
                    } else {
                        echo "Error: " . $query . "<br>" . mysqli_error($conn);
                    }
                } else {
                    echo "Error: Could not find current school year.<br>" . mysqli_error($conn);
                }
            } else {
                echo "Error: Faculty not found after insertion.<br>" . mysqli_error($conn);
            }
        } else {
            echo "Error: " . $query . "<br>" . mysqli_error($conn);
        }
    } else {
        // Passwords do not match
        echo "<script type='text/javascript'>alert('Password does not match!'); 
        document.location='admin_dean'</script>";
    }
}

$query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'") or die(mysqli_error($conn));
if ($row = mysqli_fetch_array($query)) {
    $fname = $row['fname'];
    $lname = $row['lname'];
    $type = $row['type'];
    $fname = ucfirst(strtolower($fname));
    $lname = ucfirst(strtolower($lname));
    $type = ucfirst(strtolower($type));
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Reviewer - SMCC</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <link href="<?= $BASE_URL ?>/images/Smcc_logo.gif" rel="icon">
    <link href="<?= $BASE_URL ?>/assets/img/apple-touch-icon.png" rel="apple-touch-icon">
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
    <link href="<?= $BASE_URL ?>/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= $BASE_URL ?>/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= $BASE_URL ?>/assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="<?= $BASE_URL ?>/assets/vendor/quill/quill.snow.css" rel="stylesheet">
    <link href="<?= $BASE_URL ?>/assets/vendor/quill/quill.bubble.css" rel="stylesheet">
    <link href="<?= $BASE_URL ?>/assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="<?= $BASE_URL ?>/assets/vendor/simple-datatables/style.css" rel="stylesheet">
    <link href="<?= $BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>

<body>
    <?php require_once get_admin_header(); ?>
    <?php require_once get_admin_sidebar(); ?>

    <main id="main" class="main">
        <div class="pagetitle">
            <div align="right">
                <button class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#addDeanForm">Add Dean Account</button>
            </div>
            <h1>List of Deans</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="admin_home">Dashboard</a></li>
                    <li class="breadcrumb-item">Dean</li>
                </ol>
            </nav>
        </div>

        <div class="collapse" id="addDeanForm">
            <section class="section">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Registration Form</h5>

                                <?php
                                if (isset($_SESSION['error'])) {
                                    echo "<div class='alert alert-danger'>" . $_SESSION['error'] . "</div>";
                                    unset($_SESSION['error']);
                                }
                                ?>

                                <form method="POST" enctype="multipart/form-data" class="row g-3 user needs-validation" novalidate>
                                    <div class="row mb-3">
                                        <label for="inputText" class="col-sm-2 col-form-label">First Name</label>
                                        <div class="col-sm-10">
                                            <input name="fname" type="text" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="inputEmail" class="col-sm-2 col-form-label">Last Name</label>
                                        <div class="col-sm-10">
                                            <input name="lname" type="text" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label class="col-sm-2 col-form-label">Course to be Handled</label>
                                        <div class="col-sm-10">
                                            <select name="course" class="form-select">
                                                <?php
                                                $query = mysqli_query($conn, "SELECT * FROM course WHERE status = 'Active' ORDER BY description ASC");
                                                while ($row = mysqli_fetch_array($query)) {
                                                    $id = $row['id'];
                                                    $description = $row['description'];
                                                ?>
                                                    <option value="<?php echo $id; ?>"><?php echo $description; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="inputNumber" class="col-sm-2 col-form-label">Username</label>
                                        <div class="col-sm-10">
                                            <input name="username" type="text" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="inputNumber" class="col-sm-2 col-form-label">Password</label>
                                        <div class="col-sm-10">
                                            <input name="password" class="form-control" type="password" id="formFile" required>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="inputDate" class="col-sm-2 col-form-label">Confirm Password</label>
                                        <div class="col-sm-10">
                                            <input name="confirm_password" class="form-control" type="password" id="formFile" required>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div align="right">
                                            <button name="add_dean" type="submit" class="btn btn-primary">Save Record</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Dean</h5>
                            <table class="table datatable">
                                <thead>
                                    <tr>
                                        <th>ID No.</th>
                                        <th>Last Name</th>
                                        <th>First Name</th>
                                        <th>Course Handled</th>
                                        <th>Date Registered</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query = mysqli_query($conn, "SELECT DISTINCT 
                        users.id AS id, 
                        users.lname AS lname, 
                        users.fname AS fname, 
                        course.description AS c_desc, 
                        users.date_created AS date_created,
                        users.status AS status 
                        FROM 
                        users, course, dean_course
                        WHERE 
                        dean_course.user_id = users.id 
                        AND dean_course.course_id = course.id 
                        AND users.type = 'DEAN' 
                        AND users.status = 'Active'");

                                    while ($row = mysqli_fetch_array($query)) {
                                        $id = $row['id'];
                                        $lname = $row['lname'];
                                        $fname = $row['fname'];
                                        $c_desc = $row['c_desc'];
                                        $date_created = $row['date_created'];
                                        $status = $row['status'];
                                    ?>
                                        <tr>
                                            <td><?php echo $id; ?></td>
                                            <td><?php echo $lname; ?></td>
                                            <td><?php echo $fname; ?></td>
                                            <td><?php echo $c_desc; ?></td>
                                            <td><?php echo $date_created; ?></td>
                                            <td><?php echo $status; ?></td>
                                            <td><a href="admin_dean_update?f_id=<?php echo $id; ?>"><i class="ri-edit-box-fill"></i></a> / <a href="admin_dean_remove?f_id=<?php echo $id; ?>"><i class="ri-delete-bin-5-fill"></i></a></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php require_once get_footer(); ?>
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <script src="<?= $BASE_URL ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $BASE_URL ?>/assets/js/main.js"></script>
</body>

</html>