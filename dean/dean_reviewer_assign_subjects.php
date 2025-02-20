<?php
include('../dbconnect.php');

// Validate and fetch parameters from the URL
$user_id = mysqli_real_escape_string($conn, isset($_GET['user_id']) ? $_GET['user_id'] : null); // Dean ID
$faculty_id = mysqli_real_escape_string($conn, isset($_GET['faculty_id']) ? $_GET['faculty_id'] : null); // Faculty ID
$school_year = mysqli_real_escape_string($conn, isset($_GET['school_year']) ? $_GET['school_year'] : null); // School Year
$course_id = mysqli_real_escape_string($conn, isset($_GET['course_id']) ? $_GET['course_id'] : null); // Course ID

// Ensure all required parameters are present
if (!$user_id || !$faculty_id || !$school_year || !$course_id) {
    die("Error: Missing required parameters. Please log in again.");
}

// Fetch DEAN's details
$query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id' AND type = 'DEAN'") or die(mysqli_error($conn));
if ($row = mysqli_fetch_assoc($query)) {
    $fname = ucfirst(strtolower($row['fname']));
    $lname = ucfirst(strtolower($row['lname']));
    $type = ucfirst(strtolower($row['type']));
} else {
    die("Error: Unauthorized access.");
}

// Fetch FACULTY's details
$faculty_query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$faculty_id' AND type = 'REVIEWER'") or die(mysqli_error($conn));
if ($faculty_row = mysqli_fetch_assoc($faculty_query)) {
    $faculty_fname = ucfirst(strtolower($faculty_row['fname']));
    $faculty_lname = ucfirst(strtolower($faculty_row['lname']));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Assign Subjects - SMCC</title>
    <link href="../images/Smcc_logo.gif" rel="icon" type="image/gif">
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
    <header id="header" class="header fixed-top d-flex align-items-center">
        <div class="d-flex align-items-center justify-content-between">
            <a href="admin_home.php?user_id=<?php echo $user_id; ?>" class="logo d-flex align-items-center">
                <img src="../images/Smcc_logo.gif" alt="">
                <span class="d-none d-lg-block">SMCC - BEFS</span>
            </a>
            <i class="bi bi-list toggle-sidebar-btn"></i>
        </div><!-- End Logo -->

        <div class="search-bar">
        </div><!-- End Search Bar -->

        <nav class="header-nav ms-auto">
            <ul class="d-flex align-items-center">
                <li class="nav-item d-block d-lg-none">
                    <a class="nav-link nav-icon search-bar-toggle" href="#">
                        <i class="bi bi-search"></i>
                    </a>
                </li><!-- End Search Icon-->
                <li class="nav-item dropdown pe-3">
                    <a class="nav-link nav-profile d-flex align-items-center pe-0" href="dean_reviewers.php?user_id=<?php echo $user_id; ?>" id="navbarDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php
                        // Assuming you have a field 'profile_image' in the users table
                        $profile_image = !empty($row['profile_image']) ? $row['profile_image'] : '../assets/img/profile-img2.jpg';
                        ?>
                        <img src="<?php echo $profile_image; ?>" alt="Profile" class="rounded-circle">
                        <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo $lname; ?></span>
                    </a><!-- End Profile Image Icon -->

                    <!-- Dropdown Menu -->
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile" aria-labelledby="navbarDropdown">
                        <li class="dropdown-header">
                            <h6><?php echo $fname . " " . $lname; ?></h6>
                            <span><?php echo $type; ?></span>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="dean_profile.php?user_id=<?php echo $user_id; ?>&faculty_id=<?php echo $faculty_id; ?>&school_year=<?php echo $school_year; ?>&course_id=<?php echo $course_id; ?>">
                                <i class="bi bi-person"></i>
                                <span>Profile</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="../change_password.php?user_id=<?php echo $user_id; ?>&faculty_id=<?php echo $faculty_id; ?>&school_year=<?php echo $school_year; ?>&course_id=<?php echo $course_id; ?>">
                                <i class="bi bi-question-circle"></i>
                                <span>Change Password</span>
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="../log_out_sc.php?user_id=<?php echo $user_id; ?>&faculty_id=<?php echo $faculty_id; ?>&school_year=<?php echo $school_year; ?>&course_id=<?php echo $course_id; ?>">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Sign Out</span>
                            </a>
                        </li>
                    </ul><!-- End Profile Dropdown Items -->
                </li><!-- End Profile Nav -->
            </ul>
        </nav><!-- End Icons Navigation -->
    </header>

    <?php include('dean_sidebar.php'); ?>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Assign Subjects</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dean_dashboard.php?user_id=<?php echo $user_id; ?>">Dashboard</a></li>
                    <li class="breadcrumb-item">Assign Subjects</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="row">
                <!-- List of Subjects -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Assign Subjects for: <?php echo $faculty_fname . " " . $faculty_lname; ?></h5>
                            <table class="table datatable">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Description</th>
                                        <th>Year Level</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query = mysqli_query($conn, "
                                SELECT 
                                    subjects.id AS sub_id, 
                                    subjects.code, 
                                    subjects.description, 
                                    year_level.description AS yr_desc 
                                FROM 
                                    subjects 
                                JOIN 
                                    year_level ON subjects.year_level_id = year_level.id 
                                WHERE 
                                    subjects.course_id = '$course_id' 
                                    AND subjects.status = 'Active' 
                                    AND subjects.id NOT IN (
                                        SELECT subjects_id 
                                        FROM faculty_subjects 
                                        WHERE faculty_id = '$faculty_id'
                                    )
                            ") or die(mysqli_error($conn));
                                    while ($row = mysqli_fetch_assoc($query)) {
                                    ?>
                                        <tr>
                                            <td><b><?php echo $row['code']; ?></b></td>
                                            <td><?php echo $row['description']; ?></td>
                                            <td><?php echo $row['yr_desc']; ?></td>
                                            <td>
                                                <a href="dean_assign_subject.php?user_id=<?php echo $user_id; ?>&course_id=<?php echo $course_id; ?>&sub_id=<?php echo $row['sub_id']; ?>&faculty_id=<?php echo $faculty_id; ?>&school_year=<?php echo $school_year; ?>">
                                                    <button type="button" class="btn btn-success">Assign</button>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Assigned Subjects -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Assigned Subjects</h5>
                            <table class="table datatable">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Description</th>
                                        <th>Year Level</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query = mysqli_query($conn, "
                                SELECT 
                                    faculty_subjects.subjects_id AS sid, 
                                    subjects.code, 
                                    subjects.description, 
                                    year_level.description AS yr_desc 
                                FROM 
                                    faculty_subjects
                                JOIN 
                                    subjects ON faculty_subjects.subjects_id = subjects.id
                                JOIN 
                                    year_level ON subjects.year_level_id = year_level.id
                                WHERE 
                                    faculty_subjects.faculty_id = '$faculty_id'
                            ") or die(mysqli_error($conn));
                                    while ($row = mysqli_fetch_assoc($query)) {
                                    ?>
                                        <tr>
                                            <td><b><?php echo $row['code']; ?></b></td>
                                            <td><?php echo $row['description']; ?></td>
                                            <td><?php echo $row['yr_desc']; ?></td>
                                            <td>
                                                <a href="dean_remove_subject.php?user_id=<?php echo $user_id; ?>&course_id=<?php echo $course_id; ?>&sub_id=<?php echo $row['sid']; ?>&faculty_id=<?php echo $faculty_id; ?>&school_year=<?php echo $school_year; ?>">
                                                    <button type="button" class="btn btn-danger">Remove</button>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include('../footer.php'); ?>
    <script src="../assets/js/main.js"></script>
</body>

</html>