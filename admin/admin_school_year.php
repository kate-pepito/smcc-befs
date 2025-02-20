<?php

authenticated_page("admin");


if (isset($_POST['add_school_year'])) {
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    date_default_timezone_set("Asia/Manila");
    $dt = date("Y-m-d") . " " . date("h:i:sa");

    $query = "INSERT INTO school_year (description, status, user_id, date_created) 
              VALUES ('$description', 'Not Set', '$user_id', '$dt')" or die(mysqli_error($conn));
    if (mysqli_query($conn, $query)) {
        echo "<script type='text/javascript'>alert('Year Successfully Saved!');
              document.location='admin_school_year'</script>";
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($conn);
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

    <title>School Year - SMCC</title>
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
    <?php require_once get_admin_header(); ?>

    <!-- ======= Sidebar ======= -->
    <?php
    $query = mysqli_query($conn, "SELECT * FROM school_year WHERE status = 'Current Set' AND user_id = $user_id") or die(mysqli_error($conn));
    if ($row = mysqli_fetch_array($query)) {
        require_once get_admin_sidebar();
    }
    ?>
    <!-- End Sidebar -->

    <main id="main" class="main">
        <div class="pagetitle">
            <div align="right">
                <a href="admin_school_year" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSchoolYearModal">Add School Year</a>
            </div>
            <h1>List of School Year</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="admin_home">Dashboard</a></li>
                    <li class="breadcrumb-item">School Year</li>
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
                                        <th>Year Code</th>
                                        <th>Description</th>
                                        <th>Date Entry</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query = mysqli_query($conn, "SELECT * FROM school_year") or die(mysqli_error($conn));
                                    while ($row = mysqli_fetch_array($query)) {
                                        $id = $row['id'];
                                        $description = $row['description'];
                                        $status = $row['status'];
                                        $date_created = $row['date_created'];
                                    ?>
                                        <tr>
                                            <td><?php echo $id; ?></td>
                                            <td><?php echo $description; ?></td>
                                            <td><?php echo $date_created; ?></td>
                                            <?php
                                            if ($status == "Current Set") {
                                            ?>
                                                <td>
                                                    <div class="col-lg-9 col-md-8"><span class="badge bg-success">Currently Set</span></div>
                                                </td>
                                                <td>Already Set</td>
                                            <?php
                                            } else {
                                            ?>
                                                <td>
                                                    <div class="col-lg-9 col-md-8"><span class="badge bg-danger">Not Set</span></div>
                                                </td>
                                                <td><a href="admin_school_year_set_current_sc&year_code=<?php echo $id; ?>">Set as Current</a></td>
                                            <?php
                                            }
                                            ?>
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

    <!-- Add School Year Modal -->
    <div class="modal fade" id="addSchoolYearModal" tabindex="-1" aria-labelledby="addSchoolYearModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #f8f9fa; border-bottom: 1px solid #ddd; padding: 20px;">
                    <h5 class="modal-title" id="addSchoolYearModalLabel" style="font-weight: bold; color: #2b4aa1;">School Year</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>

                <div class="modal-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label for="description" class="form-label">Description <span>ex.(2023 - 2024, 2024 - 2025, etc.)</span></label>
                            <input type="text" name="description" class="form-control" required>
                        </div>
                        <div class="mb-3 text-right">
                            <button type="submit" name="add_school_year" class="btn btn-primary">Save Year</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div><!-- End Add School Year Modal -->

    <!-- ======= Footer ======= -->
    <?php require_once get_footer(); ?>
    <!-- End Footer -->

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS Files -->
    <script src="<?= $BASE_URL ?>/assets/vendor/apexcharts/apexcharts.min.js"></script>
    <script src="<?= $BASE_URL ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $BASE_URL ?>/assets/vendor/chart.js/chart.umd.js"></script>
    <script src="<?= $BASE_URL ?>/assets/vendor/echarts/echarts.min.js"></script>
    <script src="<?= $BASE_URL ?>/assets/vendor/quill/quill.js"></script>
    <script src="<?= $BASE_URL ?>/assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="<?= $BASE_URL ?>/assets/vendor/tinymce/tinymce.min.js"></script>
    <script src="<?= $BASE_URL ?>/assets/vendor/php-email-form/validate.js"></script>

    <!-- Template Main JS File -->
    <script src="<?= $BASE_URL ?>/assets/js/main.js"></script>

</body>

</html>