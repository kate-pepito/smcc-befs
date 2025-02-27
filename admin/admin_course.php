<?php 

authenticated_page("admin");

// Handle course registration
if (isset($_POST['add_course'])) {
    $code_no = conn()->sanitize($_POST['code_no']);
    $description = conn()->sanitize($_POST['description']);

    date_default_timezone_set("Asia/Manila");
    $dt = date("Y-m-d") . " " . date("h:i:sa");

    $query = "INSERT INTO course (description, date_entry, status, code_no) 
              VALUES ('$description', '$dt', 'Active', '$code_no')";

    if (conn()->query($query)) {
        echo "<script type='text/javascript'>alert('Course Successfully Saved!'); document.location='admin_course';</script>";
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error(conn()->get_conn());
    }
}

admin_html_head("Course", [
    [ "type" => "style", "href" => "assets/vendor/simple-datatables/style.css" ],
    [ "type" => "style", "href" => "assets/css/style.css" ],
]); // html head

?>
<body>

<?php 
// User info fetching
$query = conn()->query("SELECT * FROM users WHERE id = '" . user_id() . "'")or die(mysqli_error(conn()->get_conn()));
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
                                $query = conn()->query("SELECT course.id as i, course.code_no as cn, course.description as c_desc, 
                                                              course.date_entry as de, course.status as s 
                                                              FROM course 
                                                              WHERE course.status = 'Active'") or die(mysqli_error(conn()->get_conn()));
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

<?php admin_html_body_end([
    ["type" => "script", "src" => "assets/js/main.js"],
]); ?>

</body>
</html>
