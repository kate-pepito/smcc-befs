<?php

authenticated_page("admin");


if (isset($_POST['add_school_year'])) {
    $description = conn()->sanitize($_POST['description']);
    date_default_timezone_set("Asia/Manila");
    $dt = date("Y-m-d") . " " . date("h:i:sa");

    $query = "INSERT INTO school_year (description, status, user_id, date_created) 
              VALUES ('$description', 'Not Set', '" . user_id() . "', '$dt')" or die(mysqli_error(conn()->get_conn()));
    if (conn()->query($query)) {
        echo "<script type='text/javascript'>alert('Year Successfully Saved!');
              document.location='admin_school_year'</script>";
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error(conn()->get_conn());
    }
}

$query = conn()->query("SELECT * FROM users WHERE id = '" . user_id() . "'") or die(mysqli_error(conn()->get_conn()));
if ($row = mysqli_fetch_array($query)) {
    $fname = $row['fname'];
    $lname = $row['lname'];
    $type = $row['type'];
    $fname = ucfirst(strtolower($fname));
    $lname = ucfirst(strtolower($lname));
    $type = ucfirst(strtolower($type));
}

admin_html_head("School Year", [
    [ "type" => "style", "href" => "assets/vendor/simple-datatables/style.css" ],
    [ "type" => "style", "href" => "assets/css/style.css" ],
]); // html head

?>

<body>
    <?php require_once get_admin_header(); ?>

    <!-- ======= Sidebar ======= -->
    <?php
    $query = conn()->query("SELECT * FROM school_year WHERE status = 'Current Set' AND user_id = '". user_id() . "'") or die(mysqli_error(conn()->get_conn()));
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
                                    $query = conn()->query("SELECT * FROM school_year") or die(mysqli_error(conn()->get_conn()));
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
                                                <td><a href="admin_school_year_set_current_sc?year_code=<?php echo $id; ?>">Set as Current</a></td>
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

    <?php admin_html_body_end([
        ["type" => "script", "src" => "assets/vendor/simple-datatables/simple-datatables.js"],
        ["type" => "script", "src" => "assets/js/main.js"],
    ]); ?>

</body>

</html>