<?php 

authenticated_page("reviewer");


// Fetch the current school year
$current_sy_query = conn()->query("SELECT id FROM school_year WHERE status = 'Current Set' LIMIT 1");
$current_school_year = mysqli_fetch_assoc($current_sy_query)['id'] ?? null;

// Get the selected school year, or use the current school year as the default
$school_year = conn()->sanitize($_GET['school_year'] ?? $current_school_year);

// Fetch the faculty's course and school year from faculty_course_school_year table
$query = conn()->query("SELECT course_id, school_year_id FROM faculty_course_school_year WHERE user_id = '" . user_id() . "'");
$faculty_data = mysqli_fetch_assoc($query);
$faculty_course = $faculty_data['course_id']; // Get the course_id associated with the faculty
$faculty_school_year = $faculty_data['school_year_id']; // Get the school_year_id associated with the faculty

$query = conn()->query("SELECT * FROM users WHERE id = '" . user_id() . "'");
if ($row = mysqli_fetch_array($query)) {
    $fname = $row['fname'];
    $lname = $row['lname'];
    $type = $row['type'];
    $fname = ucfirst(strtolower($fname));
    $lname = ucfirst(strtolower($lname));
    $type = ucfirst(strtolower($type));
}

admin_html_head("Students", [
  [ "type" => "style", "href" => "assets/vendor/simple-datatables/style.css" ],
  [ "type" => "style", "href" => "assets/css/style.css" ],
]); // html head

?>

<body>
  <!-- Header and Sidebar -->
  <?php require_once get_reviewer_header(); ?>
  <?php require_once get_reviewer_sidebar(); ?>

  <main id="main" class="main">
    <div class="pagetitle">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h1>List of Students</h1>
          <nav>
            <ol class="breadcrumb">
              <li class="breadcrumb-item">
                <a href="reviewer_home">Dashboard</a>
              </li>
              <li class="breadcrumb-item">List of Students</li>
            </ol>
          </nav>
        </div>
        <div>
          <form method="GET">
          <input type="hidden" name="user_id" value="<?= htmlspecialchars(user_id()); ?>">
    <div class="d-flex align-items-center justify-content-end">
        <label for="school_year_filter" class="card-title me-2">School Year:</label>
        <select class="form-select" style="width: 200px;" name="school_year" id="school_year_filter" onchange="this.form.submit()">
            <option value="" <?= empty($school_year) ? 'selected' : ''; ?>>All</option>
            <?php
            $sy_query = conn()->query("SELECT id, description FROM school_year ORDER BY description ASC");
            while ($sy_row = mysqli_fetch_array($sy_query)) {
                $selected = $school_year == $sy_row['id'] ? 'selected' : '';
                echo "<option value='{$sy_row['id']}' $selected>{$sy_row['description']}</option>";
            }
            ?>
              </select>
            </div>
          </form>
        </div>
      </div>
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
          <th>#</th> <!-- Sequence Number -->
          <th>ID No.</th>
          <th>Last Name</th>
          <th>First Name</th>
          <th>Gender</th>
          <th>Course</th>
          <th>Section</th>
          <th>Status</th>
          <th>School Year</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
          // Query to fetch students based on faculty's course and filter by school year
          $sql = "SELECT 
                      students.lrn_num AS lrn_num,
                      students.id AS stud_id,
                      students.course_id AS c_id,
                      students.gender AS gender,
                      course.description AS course,
                      section.description AS section,
                      students.lname AS lname,
                      students.fname AS fname,
                      school_year.description AS sy,
                      students.level as level
                  FROM 
                      students
                  INNER JOIN 
                      course ON students.course_id = course.id
                  INNER JOIN 
                      section ON students.section_id = section.id
                  INNER JOIN 
                      school_year ON students.school_year_id = school_year.id
                  WHERE 
                      students.status = 'Active' 
                      AND students.course_id = '$faculty_course'"; // Filter by faculty's course

if (empty($_GET['school_year']) && $current_school_year) {
  $school_year_id = $current_school_year;
  $sql .= " AND students.school_year_id = '$school_year_id'";
} elseif (!empty($_GET['school_year'])) {
  $school_year_id = conn()->sanitize($_GET['school_year']);
  $sql .= " AND students.school_year_id = '$school_year_id'";
}

          $sql .= " ORDER BY students.lname ASC"; // Order by last name

          $query = conn()->query($sql) or die(mysqli_error(conn()->get_conn()));
          $counter = 1;

          while ($row = mysqli_fetch_array($query)) {
              $stud_id = $row['stud_id'];
              $lrn_num = $row['lrn_num'];
              $lname = $row['lname'];
              $fname = $row['fname'];
              $gender = $row['gender'];
              $course = $row['course'];
              $section = $row['section'];
              $level = $row['level'];
              $sy = $row['sy'];
        ?>
        <tr>
          <td><?php echo $counter++; ?></td>
          <td><?php echo $lrn_num; ?></td>
          <td><?php echo $lname; ?></td>
          <td><?php echo $fname; ?></td>
          <td><?php echo $gender; ?></td>
          <td><?php echo $course; ?></td>
          <td><?php echo $section; ?></td>
          <td><?php echo $level; ?></td>
          <td><?php echo $sy; ?></td>
          <td class="text-left">
            <a href="reviewer_student_profile?stud_id=<?php echo $stud_id; ?>" 
               class="btn btn-primary btn-sm">View</a>
          </td>
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

  <!-- Footer -->
  <?php require_once get_footer(); ?>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <?php admin_html_body_end([
      ["type" => "script", "src" => "assets/vendor/simple-datatables/simple-datatables.js"],
      ["type" => "script", "src" => "assets/js/main.js"],
  ]); ?>

</body>

</html>
