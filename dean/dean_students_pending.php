<?php 

authenticated_page("dean");

admin_html_head("Pending Students", [
  [ "type" => "style", "href" => "assets/vendor/simple-datatables/style.css" ],
  [ "type" => "style", "href" => "assets/css/style.css" ],
]); // html head

?>

<body>
<?php 
  // Fetch the Dean's details along with their course ID and profile image
  $query = conn()->query("
      SELECT u.fname, u.lname, u.type, u.profile_image, dc.course_id 
      FROM users u 
      JOIN dean_course dc ON u.id = dc.user_id 
      WHERE u.id = '" . user_id() . "'
  ") or die(mysqli_error(conn()->get_conn()));

  if ($row = mysqli_fetch_array($query)) {
      $fname = ucfirst(strtolower($row['fname']));
      $lname = ucfirst(strtolower($row['lname']));
      $type = ucfirst(strtolower($row['type']));
      $profile_image = $row['profile_image'];  // Assuming this field stores the image path
      $course_id = $row['course_id'];
  } else {
      die("Dean not found or no associated course.");
  }

  // Fetch the course description using the course_id
  $course_query = conn()->query("SELECT description FROM course WHERE id = '$course_id'") or die(mysqli_error(conn()->get_conn()));
  $course_row = mysqli_fetch_array($course_query);
  $deans_course = $course_row ? $course_row['description'] : "Unknown";
?>

  <!-- ======= Header ======= -->
  <?php require_once get_dean_header(); ?>
  <!-- End Header -->
  
  <!-- ======= Sidebar ======= -->
  <?php require_once get_dean_sidebar(); ?>
  <!-- End Sidebar -->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>List of Pending Students</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="dean_home_page">Dashboard</a></li>
          <li class="breadcrumb-item">Pending Students</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Students</h5>
              
              <!-- Table with stripped rows -->
              <table class="table datatable">
                <thead>
                  <tr>
                    <th>ID No.</th>
                    <th>Last Name</th>
                    <th>First Name</th>
                    <th>Course</th>
                    <th data-type="date" data-format="YYYY/DD/MM">Date Registered</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                <?php
                    // Query to fetch only students in the same course as the Dean
                    $query = conn()->query("
                        SELECT course.description AS course, 
                               students.id AS stud_id, 
                               students.lname AS lname, 
                               students.fname AS fname, 
                               students.date_registered AS date_registered 
                        FROM course 
                        JOIN students ON students.course_id = course.id 
                        WHERE students.status = 'For Approval' 
                        AND course.description = '$deans_course'
                    ") or die(mysqli_error(conn()->get_conn()));
                    
                    while($row = mysqli_fetch_array($query)) {
                        $stud_id = $row['stud_id'];
                        $lname = $row['lname'];
                        $fname = $row['fname'];
                        $course = $row['course'];
                        $date_registered = $row['date_registered'];
                ?>
                  <tr>
                      <td><?php echo $stud_id; ?></td>
                      <td><?php echo $lname; ?></td>
                      <td><?php echo $fname; ?></td>
                      <td><?php echo $course; ?></td>
                      <td><?php echo $date_registered; ?></td>
                      <td>
                        <a href="dean_students_profile_confirm?stud_id=<?php echo $stud_id; ?>">Confirm</a> / 
                        <a href="dean_students_pending_decline_sc?stud_id=<?php echo $stud_id; ?>">Decline</a>
                      </td>
                  </tr>
                <?php 
                    } 
                ?>
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

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <?php admin_html_body_end([
      ["type" => "script", "src" => "assets/vendor/simple-datatables/simple-datatables.js"],
      ["type" => "script", "src" => "assets/js/main.js"],
  ]); ?>

</body>

</html>
