<?php 

authenticated_page("admin");

// Handle course registration
if (isset($_POST['add_course'])) {
    $code_no = mysqli_real_escape_string($conn, $_POST['code_no']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    date_default_timezone_set("Asia/Manila");
    $dt = date("Y-m-d") . " " . date("h:i:sa");

    $query = "INSERT INTO course (description, date_entry, status, code_no) 
              VALUES ('$description', '$dt', 'Active', '$code_no')";

    if (mysqli_query($conn, $query)) {
        echo "<script type='text/javascript'>alert('Course Successfully Saved!'); document.location='admin_course';</script>";
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Course - SMCC</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="<?= $BASE_URL ?>/images/Smcc_logo.gif" rel="icon">
    <link href="<?= $BASE_URL ?>/assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="<?= $BASE_URL ?>/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= $BASE_URL ?>/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= $BASE_URL ?>/assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="<?= $BASE_URL ?>/assets/vendor/quill/quill.snow.css" rel="stylesheet">
    <link href="<?= $BASE_URL ?>/assets/vendor/quill/quill.bubble.css" rel="stylesheet">
    <link href="<?= $BASE_URL ?>/assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="<?= $BASE_URL ?>/assets/vendor/simple-datatables/style.css" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="<?= $BASE_URL ?>/assets/css/style.css" rel="stylesheet">

</head>

<body>

<?php 
// User info fetching
$query = mysqli_query($conn,"SELECT * FROM users WHERE id = '$user_id'")or die(mysqli_error($conn));
if ($row = mysqli_fetch_array($query)) {
    $fname = $row['fname'];
    $lname = $row['lname'];
    $type = $row['type'];
    $fname = ucfirst(strtolower($fname));
    $lname = ucfirst(strtolower($lname));
    $type = ucfirst(strtolower($type));
}
?>

<!-- ======= Header ======= -->
<?php require_once get_admin_header(); ?>
<!-- End Header -->

<!-- ======= Sidebar ======= -->
<?php require_once get_admin_sidebar(); ?>
<!-- End Sidebar -->

<main id="main" class="main">

    <div class="pagetitle">
        <div align="right">
            <a href="#" data-bs-toggle="modal" data-bs-target="#addCourseModal" class="btn btn-primary">Add Course</a>
        </div>
        <h1>List of Course</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="admin_home">Dashboard</a></li>
                <li class="breadcrumb-item">Course</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Lists</h5>

                        <!-- Table with stripped rows -->
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>Course</th>
                                    <th>Description</th>
                                    <th>Date Entry</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                                $query = mysqli_query($conn, "SELECT course.id as i, course.code_no as cn, course.description as c_desc, 
                                                              course.date_entry as de, course.status as s 
                                                              FROM course 
                                                              WHERE course.status = 'Active'") or die(mysqli_error($conn));
                                while ($row = mysqli_fetch_array($query)) {
                                    $id = $row['i'];
                                    $code_no = $row['cn'];
                                    $description = $row['c_desc'];
                                    $date_entry = $row['de'];
                                    $status = ucfirst(strtolower($row['s']));
                            ?>
                                <tr>
                                    <td><?php echo $code_no; ?></td>
                                    <td><?php echo $description; ?></td>
                                    <td><?php echo $date_entry; ?></td>
                                    <td><?php echo $status; ?></td>
                                    <td><a href="admin_course_remove?c_id=<?php echo $id; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to remove this course?');">
                                        Remove</a></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                        <!-- End Table with stripped rows -->

                    </div>
                </div>

            </div>
        </div>
    </section>

</main><!-- End #main -->

<!-- ======= Footer ======= -->
<?php require_once get_footer(); ?>
<!-- End Footer -->

<!-- Add Course Modal -->
<div class="modal fade" id="addCourseModal" tabindex="-1" aria-labelledby="addCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCourseModalLabel" style="font-weight: bold; color: #2b4aa1;">Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="inputCodeNo" class="form-label">Course</label>
                        <input type="text" class="form-control" name="code_no" required>
                    </div>
                    <div class="mb-3">
                        <label for="inputDescription" class="form-label">Description</label>
                        <input type="text" class="form-control" name="description" required>
                    </div>
                    <div class="mb-3">
                        <button type="submit" name="add_course" class="btn btn-primary">Register Course</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Vendor JS Files -->
<script src="<?= $BASE_URL ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Template Main JS File -->
<script src="<?= $BASE_URL ?>/assets/js/main.js"></script>

</body>
</html>
