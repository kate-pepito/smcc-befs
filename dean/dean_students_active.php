<?php
include('../dbconnect.php');
$user_id = $_REQUEST['user_id'];

// Fetch the current school year
$current_sy_query = mysqli_query($conn, "SELECT id FROM school_year WHERE status = 'Current Set' LIMIT 1");
$current_school_year = mysqli_fetch_assoc($current_sy_query)['id'] ?? null; 

$school_year = $_GET['school_year'] ?? $current_school_year; // Get the selected school year, if any

// Base query to get students assigned to the courses of the dean
$sql = "
    SELECT students.id AS stud_id, students.lrn_num, students.fname, students.lname, 
           students.gender, students.date_registered, course.description AS course, 
           section.description AS section, students.course_id AS c_id 
    FROM students 
    INNER JOIN course ON students.course_id = course.id 
    INNER JOIN section ON students.section_id = section.id
    INNER JOIN dean_course ON students.course_id = dean_course.course_id 
    WHERE students.status = 'Active' AND dean_course.user_id = ?";

// Add school year filter if provided
if (!empty($school_year)) {
    $sql .= " AND students.school_year_id = ?";
}

$sql .= " ORDER BY students.lname ASC";

// Prepare the SQL query
$stmt = $conn->prepare($sql);

// Bind parameters
if (!empty($school_year)) {
    $stmt->bind_param("ii", $user_id, $school_year); // Bind user_id and school_year_id
} else {
    $stmt->bind_param("i", $user_id); // Bind only user_id
}

// Execute and fetch results
$stmt->execute();
$result = $stmt->get_result();
$counter = 1; // Initialize counter outside the loop
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Active Students - SMCC</title>

  <!-- Favicons -->
  <link href="../images/Smcc_logo.gif" rel="icon">
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
  <?php 
    $query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'") or die(mysqli_error());
    if($row = mysqli_fetch_array($query)) {
        $fname = $row['fname'];
        $lname = $row['lname'];
        $type = $row['type'];
        $fname = ucfirst(strtolower($fname));
        $lname = ucfirst(strtolower($lname));
        $type = ucfirst(strtolower($type));
    }
  ?>
  <!-- ======= Header ======= -->
  <?php include('dean_header.php'); ?>
  <!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <?php include('dean_sidebar.php'); ?>
  <!-- End Sidebar -->

  <main id="main" class="main">

    <div class="pagetitle">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h1>List of Active Students</h1>
          <nav>
            <ol class="breadcrumb">
              <li class="breadcrumb-item">
                <a href="dean_home_page.php?user_id=<?php echo $user_id; ?>">Dashboard</a>
              </li>
              <li class="breadcrumb-item">Active Students</li>
            </ol>
          </nav>
        </div>

        <div>
  <form method="GET" action="dean_students_active.php">
    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($_GET['user_id'] ?? ''); ?>">

    <div class="d-flex align-items-center justify-content-end">
      <label for="school_year_filter" class="card-title me-2">School Year:</label>
      <select class="form-select" style="width: 200px;" name="school_year" id="school_year_filter" onchange="this.form.submit()">
        <option value="" <?= empty($school_year) ? 'selected' : ''; ?>>All</option>
        <?php
          // Fetch all available school years
          $sy_query = mysqli_query($conn, "SELECT id, description FROM school_year ORDER BY description ASC");
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
              <table class="table datatable">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>ID No.</th>
                    <th>Last Name</th>
                    <th>First Name</th>
                    <th>Gender</th>
                    <th>Course</th>
                    <th>Section</th>
                    <th>Date Registered</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                <?php
                  while ($row = $result->fetch_assoc()) {
                ?>
                  <tr>
                    <td><?php echo $counter++; ?></td>
                    <td><?= htmlspecialchars($row['lrn_num']) ?></td>
                    <td><?= htmlspecialchars($row['lname']) ?></td>
                    <td><?= htmlspecialchars($row['fname']) ?></td>
                    <td><?= htmlspecialchars($row['gender']) ?></td>
                    <td><?= htmlspecialchars($row['course']) ?></td>
                    <td><?= htmlspecialchars($row['section']) ?></td>
                    <td><?= htmlspecialchars($row['date_registered']) ?></td>
                    <td>
                      <a href="dean_students_assign_subjects.php?user_id=<?= htmlspecialchars($user_id) ?>&stud_id=<?= htmlspecialchars($row['stud_id']) ?>&c_id=<?= htmlspecialchars($row['c_id']) ?>">Assign Subjects</a> 
                      &nbsp;&nbsp;/&nbsp;&nbsp;
                      <a href="dean_students_remove_sc.php?user_id=<?= htmlspecialchars($user_id) ?>&stud_id=<?= htmlspecialchars($row['stud_id']) ?>">
                        <i class="ri-delete-bin-5-fill"></i>
                      </a>
                    </td>
                  </tr>
                <?php
                  }
                  $stmt->close();
                ?>
                </tbody>
              </table>
            </div>
          </div>

        </div>
      </div>
    </section>

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <?php include('../footer.php'); ?>
  <!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="../assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/vendor/chart.js/chart.umd.js"></script>
  <script src="../assets/vendor/echarts/echarts.min.js"></script>
  <script src="../assets/vendor/quill/quill.js"></script>
  <script src="../assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="../assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="../assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="../assets/js/main.js"></script>

</body>
</html>
