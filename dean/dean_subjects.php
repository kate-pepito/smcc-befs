<?php 

authenticated_page("dean");

// Fetch the "Current Set" school year
$current_school_year_query = mysqli_query($conn, "SELECT id, description FROM school_year WHERE status = 'Current Set'") or die(mysqli_error($conn));
$current_school_year = mysqli_fetch_assoc($current_school_year_query);
$current_school_year_id = mysqli_real_escape_string($conn, $current_school_year['id'] ?? null);

// Determine selected school year (default to "Current Set")
$selected_school_year = mysqli_real_escape_string($conn, $_GET['school_year'] ?? $current_school_year_id);

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Subjects - SMCC</title>
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
  // Fetch user details
  $query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'") or die(mysqli_error($conn));
  if ($row = mysqli_fetch_array($query)) {
      $fname = ucfirst(strtolower($row['fname']));
      $lname = ucfirst(strtolower($row['lname']));
      $type = ucfirst(strtolower($row['type']));
  }
?>
  <!-- ======= Header ======= -->
  <?php require_once get_dean_header(); ?><!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <?php require_once get_dean_sidebar(); ?><!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Manage Subjects</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="dean_home_page">Dashboard</a></li>
          <li class="breadcrumb-item active">Subjects</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    <div>
        <!-- School Year Filter -->
        <form method="GET">
        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($_GET['user_id'] ?? ''); ?>">
          <div class="d-flex align-items-center justify-content-end">
            <label for="school_year_filter" class="card-title me-2">School Year:</label>
            <select class="form-select" style="width: 200px;" name="school_year" id="school_year_filter" onchange="this.form.submit()">
              <option value="" selected>All</option>
              <?php
               // Fetch all available school years
          $sy_query = mysqli_query($conn, "SELECT id, description FROM school_year ORDER BY description ASC");
          while ($sy_row = mysqli_fetch_assoc($sy_query)) {
            $selected = ($sy_row['id'] == $selected_school_year) ? 'selected' : '';
            echo "<option value='{$sy_row['id']}' $selected>{$sy_row['description']}</option>";
                }
              ?>
            </select>
          </div>
        </form>
    </div>

    <section class="section">
      <div class="row">
        <!-- Subjects List -->
        <div class="col-lg-8">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">List of Subjects</h5>
              <table class="table datatable">
                <thead>
                  <tr>
                    <th>Code No.</th>
                    <th>Description</th>
                    <th>Year Level</th>
                    <th>Course</th>
                    <th>School Year</th>
                    <th>Date Entry</th>
                    <th>Status</th>
                    <th>Exam (%)</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                <?php
 // Build the query with the school year filter
 $school_year_filter = $selected_school_year ? "AND subjects.school_year_id = '$selected_school_year'" : '';

// Fetch dean's assigned courses
$dean_course_query = mysqli_query($conn, "SELECT course_id FROM dean_course WHERE user_id = '$user_id'") or die(mysqli_error($conn));
$course_ids = [];
while ($row = mysqli_fetch_assoc($dean_course_query)) {
    $course_ids[] = $row['course_id'];
}

if (count($course_ids) > 0) {
    $course_ids_imploded = implode(',', $course_ids);
    $query = mysqli_query($conn, "
        SELECT 
            subjects.id AS s_id, 
            subjects.code AS s_code, 
            subjects.description AS s_desc, 
            year_level.description AS y_desc, 
            course.description AS c_desc, 
            school_year.description AS sy_desc, 
            subjects.date_entry AS s_date, 
            subjects.status AS s_status, 
            subject_percent.percent AS percent
        FROM 
            subjects
        JOIN 
            year_level ON subjects.year_level_id = year_level.id
        JOIN 
            course ON subjects.course_id = course.id
        JOIN 
            school_year ON subjects.school_year_id = school_year.id
        JOIN 
            subject_percent ON subjects.id = subject_percent.sub_id
        WHERE 
            subjects.course_id IN ($course_ids_imploded) 
            AND subjects.status = 'Active'
            $school_year_filter
    ") or die(mysqli_error($conn));

    while ($row = mysqli_fetch_array($query)) {
        $s_id = $row['s_id'];
        $s_code = $row['s_code'];
        $s_desc = $row['s_desc'];
        $y_desc = $row['y_desc'];
        $c_desc = $row['c_desc'];
        $sy_desc = $row['sy_desc'];
        $s_date = $row['s_date'];
        $s_status = $row['s_status'];
        $percent = $row['percent'];
?>
<tr>
    <td><?php echo $s_code; ?></td>
    <td><?php echo $s_desc; ?></td>
    <td><?php echo $y_desc; ?></td>
    <td><?php echo $c_desc; ?></td>
    <td><?php echo $sy_desc; ?></td>
    <td><?php echo $s_date; ?></td>
    <td><?php echo $s_status; ?></td>
    <td><?php echo $percent; ?>%</td>
    <td>
    <!-- Update Button with Icon -->
    <a href="#" 
       class="btn btn-outline-info btn-sm me-2" 
       data-bs-toggle="modal" 
       data-bs-target="#updateSubjectModal" 
       data-s_id="<?php echo $s_id; ?>" 
       data-s_code="<?php echo $s_code; ?>" 
       data-s_desc="<?php echo $s_desc; ?>">
      <i class="bi bi-pencil-square"></i> Update
    </a>

    <!-- Remove Button with Icon and Confirm Prompt -->
    <a href="dean_subjects_remove_sc&s_id=<?php echo $s_id; ?>" 
       class="btn btn-outline-danger btn-sm me-2" 
       onclick="return confirm('Are you sure you want to remove this subject?');">
      <i class="bi bi-trash"></i> Remove
    </a>

    <!-- View Button with Icon -->
    <a href="dean_students&sub_id=<?php echo $s_id; ?>" 
       class="btn btn-outline-primary btn-sm me-2">
      <i class="bi bi-eye"></i> View
    </a>

    <!-- Set Percent Button with Icon -->
    <a href="#" 
       class="btn btn-outline-success btn-sm" 
       data-bs-toggle="modal" 
       data-bs-target="#updatePercentModal" 
       data-s_id="<?php echo $s_id; ?>" 
       data-s_code="<?php echo $s_code; ?>" 
       data-s_desc="<?php echo $s_desc; ?>"
       data-current_percent="<?php echo $percent; ?>">
      <i class="bi bi-percent"></i> Set Percent
    </a>
  </td>
</tr>

                <?php
                      }
                  } else {
                      echo "<tr><td colspan='8'>No subjects found for this dean.</td></tr>";
                  }
                ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Add Subject Form -->
<div class="col-lg-4">
  <div class="card">
    <div class="card-body">
      <h5 class="card-title">Add Subject</h5>
      <form action="./dean_subjects_add_sc?user_id=<?php echo $user_id; ?>" method="POST" enctype="multipart/form-data" class="row g-3 user needs-validation" novalidate>
        <div class="row mb-3">
          <label for="inputText" class="col-sm-4 col-form-label">Code</label>
          <div class="col-sm-8">
            <input name="subject_code" type="text" class="form-control" required>
          </div>
        </div>
        <div class="row mb-3">
          <label for="inputEmail" class="col-sm-4 col-form-label">Description</label>
          <div class="col-sm-8">
            <input name="description" type="text" class="form-control" required>
          </div>
        </div>

        <!-- Hidden Course and Year Level -->
        <?php
          // Fetch the dean's assigned courses
          $dean_course_query = mysqli_query($conn, "SELECT course_id FROM dean_course WHERE user_id = '$user_id'") or die(mysqli_error($conn));
          $course_ids = [];
          while ($row = mysqli_fetch_assoc($dean_course_query)) {
            $course_ids[] = $row['course_id'];
          }

          // Fetch the first course assigned to the dean and set it as the course ID
          $course_id = isset($course_ids[0]) ? $course_ids[0] : null;

          // Fetch the year level (this could also be dynamically set if required)
          $year_level_id = 1; // Assume a default year level ID for the form, you can adjust this if needed

          if ($course_id) {
            // Store the course ID as hidden
            echo "<input type='hidden' name='course' value='$course_id'>";
          } else {
            echo "<p>No courses assigned to this dean.</p>";
          }

          // Store the year level ID as hidden
          echo "<input type='hidden' name='year_level' value='$year_level_id'>";
        ?>

        <div class="text-end">
          <button name="add_subjects" type="submit" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- Add Bootstrap Modal -->
<div class="modal fade" id="updateSubjectModal" tabindex="-1" aria-labelledby="updateSubjectModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="updateSubjectModalLabel">Update Subject</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="./dean_subjects_update_sc?user_id=<?php echo $user_id; ?>" method="POST" enctype="multipart/form-data" class="row g-3 user needs-validation" novalidate>
          <input type="hidden" name="s_id" id="s_id">
          <div class="row mb-3">
            <label for="subject_code" class="col-sm-2 col-form-label">Subject Code</label>
            <div class="col-sm-10">
              <input name="subject_code" type="text" id="subject_code" class="form-control" required>
            </div>
          </div>
          <div class="row mb-3">
            <label for="description" class="col-sm-2 col-form-label">Description</label>
            <div class="col-sm-10">
              <input name="description" type="text" id="description" class="form-control" required>
            </div>
          </div>
          <div class="text-end">
            <button name="update_subject" type="submit" class="btn btn-primary">Update Subject</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  // JavaScript to populate modal with subject details
  const updateSubjectModal = document.getElementById('updateSubjectModal');
  updateSubjectModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const s_id = button.getAttribute('data-s_id');
    const s_code = button.getAttribute('data-s_code');
    const s_desc = button.getAttribute('data-s_desc');
    
    // Populate modal fields
    updateSubjectModal.querySelector('#s_id').value = s_id;
    updateSubjectModal.querySelector('#subject_code').value = s_code;
    updateSubjectModal.querySelector('#description').value = s_desc;
  });
</script>
<!-- Modal for Setting Percent -->
<div class="modal fade" id="updatePercentModal" tabindex="-1" aria-labelledby="updatePercentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="updatePercentModalLabel">Set Exam Percent</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="./dean_update_percent?user_id=<?php echo $user_id; ?>" method="POST">
            <input type="hidden" name="s_id" id="s_id">
            <div class="mb-3">
              <label for="percent" class="form-label">Exam Percentage</label>
              <input type="number" class="form-control" name="percent" id="percent" required>
            </div>
            <div class="text-end">
              <button type="submit" class="btn btn-primary">Save Percent</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script>
    const updatePercentModal = document.getElementById('updatePercentModal');
    updatePercentModal.addEventListener('show.bs.modal', function (event) {
      const button = event.relatedTarget;
      const s_id = button.getAttribute('data-s_id');
      const s_code = button.getAttribute('data-s_code');
      const s_desc = button.getAttribute('data-s_desc');
      const current_percent = button.getAttribute('data-current_percent');
      
      // Populate modal with data
      updatePercentModal.querySelector('#s_id').value = s_id;
      updatePercentModal.querySelector('#percent').value = current_percent;
    });
  </script>

    </section>

  </main><!-- End #main -->

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
  <script src="<?= $BASE_URL ?>/assets/js/main.js"></script>

</body>

</html>
