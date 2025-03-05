<?php 

authenticated_page("dean");

if ($_SERVER['REQUEST_METHOD'] === "POST"): // method POST

  $sy_id = $_POST['sy_id'] ?? null;
  $stud_id = $_POST['stud_id'] ?? null;
  $action = $_POST['action'] ?? null;
  $revalida_grade = $_POST['revalida_score'] ?? null;

  if ($sy_id === null || $revalida_grade === null || $stud_id === null || $action === null) {
    http_response_code(400);
    die("Invalid Request");
  }

  switch ($action) {
    case 'insert':
      $stmt = conn()->prepare("INSERT INTO revalida_grade (school_year_id, student_id, revalida_grade) VALUES (?, ?, ?)");
      $stmt->bind_param("iid", $sy_id, $stud_id, $revalida_grade);
      if (!$stmt->execute()) {
        http_response_code(500);
        die("Failed to insert revalida grade.");
      }
      die("Revalida grade inserted successfully.");
    case 'update':
      $stmt = conn()->prepare("UPDATE revalida_grade SET revalida_grade = ? WHERE school_year_id = ? AND student_id = ?");
      $stmt->bind_param("dii", $revalida_grade, $sy_id, $stud_id);
      if (!$stmt->execute()) {
        http_response_code(500);
        die("Failed to update revalida grade.");
      }
      die("Revalida grade updated successfully.");
    default:
      http_response_code(400);
      die("Invalid Request");
  }

else: // method GET
// Fetch the current school year
$current_sy_query = conn()->query("SELECT id FROM school_year WHERE status = 'Current Set' LIMIT 1");
$current_school_year = mysqli_fetch_assoc($current_sy_query)['id'] ?? null;

// Get the selected school year, or use the current school year as the default
$school_year = conn()->sanitize($_GET['school_year'] ?? $current_school_year);

// Fetch the dean's course from the dean_course table (assuming the relationship is via user_id)
$query = conn()->query("SELECT course_id FROM dean_course WHERE user_id = '" . user_id() . "'");
$dean_course = mysqli_fetch_assoc($query)['course_id']; // Get the course_id associated with the dean

// Fetch user details
$query = conn()->query("SELECT * FROM users WHERE id = '" . user_id() . "'");
if ($row = mysqli_fetch_array($query)) {
    $fname = $row['fname'];
    $lname = $row['lname'];
    $type = $row['type'];
    $fname = ucfirst(strtolower($fname));
    $lname = ucfirst(strtolower($lname));
    $type = ucfirst(strtolower($type));
}

admin_html_head("Student's Revalida", [
  [ "type" => "style", "href" => "assets/vendor/simple-datatables/style.css" ],
  [ "type" => "style", "href" => "https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.15.10/sweetalert2.min.css" ],
  [ "type" => "style", "href" => "assets/css/style.css" ],
]); // html head

?>

<body>

  <!-- Header and Sidebar -->
  <?php require_once get_dean_header(); ?>
  <?php require_once get_dean_sidebar(); ?>

  <main id="main" class="main">
    <div class="pagetitle">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h1>Revalida Records</h1>
          <nav>
            <ol class="breadcrumb">
              <li class="breadcrumb-item">
                <a href="dean_home_page">Dashboard</a>
              </li>
              <li class="breadcrumb-item">Revalida Records</li>
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
              <h5 class="card-title">Active Students</h5>
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
                    <th>School Year</th>
                    <th>Revalida Grade (%)</th>
                  </tr>
                </thead>
                <tbody>
                <?php
                  // Query to fetch students based on dean's course and filter by school year
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
                        MAX(rg.revalida_grade) AS revalida_grade
                    FROM 
                        students
                    INNER JOIN 
                        course ON students.course_id = course.id
                    INNER JOIN 
                        section ON students.section_id = section.id
                    INNER JOIN 
                        school_year ON students.school_year_id = school_year.id
                    LEFT JOIN 
                        `revalida_grade` as rg
                        ON rg.school_year_id = school_year.id
                        AND rg.student_id = students.id
                    WHERE 
                        students.status = 'active' 
                        AND students.course_id = '$dean_course'
                  ";

                  if (empty($_GET['school_year']) && $current_school_year) {
                    $school_year_id = $current_school_year;
                    $sql .= " AND students.school_year_id = '$school_year_id'";
                  } elseif (!empty($_GET['school_year'])) {
                    $school_year_id = conn()->sanitize($_GET['school_year']);
                    $sql .= " AND students.school_year_id = '$school_year_id'";
                  }


                  $sql .= " GROUP BY students.id"; // Group by ID

                  $sql .= " ORDER BY students.lname ASC"; // Order by last name


                  $query = conn()->query($sql) or die(mysqli_error(conn()->get_conn()));
                  $counter = 1;

                  while ($row = mysqli_fetch_assoc($query)) {
                      $stud_id = $row['stud_id'];
                      $lrn_num = $row['lrn_num'];
                      $lname = $row['lname'];
                      $fname = $row['fname'];
                      $gender = $row['gender'];
                      $course = $row['course'];
                      $section = $row['section'];
                      $sy = $row['sy'];
                      $REVALIDA_GRADE = $row["revalida_grade"] ?? null;
                      $REVALIDA_GRADE = $REVALIDA_GRADE !== null ? "$REVALIDA_GRADE %" : "";
                      
                  ?>
                    <tr>
                      <td><?php echo $counter++; ?></td>
                      <td><?php echo $lrn_num; ?></td>
                      <td><?php echo $lname; ?></td>
                      <td><?php echo $fname; ?></td>
                      <td><?php echo $gender; ?></td>
                      <td><?php echo $course; ?></td>
                      <td><?php echo $section; ?></td>
                      <td><?php echo $sy; ?></td>
                      <td class="d-flex justify-content-end gap-3">
                        <span class="fw-bold"><?= $REVALIDA_GRADE ?: "" ?></span>
                        <button type="button"
                          data-befs-student-id="<?= $stud_id ?>" data-befs-action="<?= !$REVALIDA_GRADE ? "insert" : "update" ?>"
                          data-befs-value="<?= $REVALIDA_GRADE ?: "" ?>"
                          title="<?= !$REVALIDA_GRADE ? "Add Revalida Grade" : "Edit Revalida Grade" ?>"
                          class="btn btn-success btn-sm befs-action" 
                          <?php if (!$REVALIDA_GRADE): ?>
                            style="background-color: DodgerBlue; color: white;">
                            <i class="bi bi-plus-circle"></i> Add Revalida Grade
                          <?php else: ?>
                            >
                            <i class="bi bi-pencil"></i>
                          <?php endif; ?>
                        </button>
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
      ["type" => "script", "src" => "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"],
      ["type" => "script", "src" => "https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.15.10/sweetalert2.min.js"],
      ["type" => "script", "src" => "assets/js/main.js"],
  ]); ?>

  <script>
    // on load page
    $(function() {
      $(".befs-action").on("click", function() {
        let action = $(this).attr("data-befs-action");
        let stud_id = $(this).attr("data-befs-student-id");
        let prev_value = $(this).attr("data-befs-value");
        if (!stud_id) {
          Swal.fire({
            text: "Invalid Action",
            toast: true,
            timer: 1000,
          });
          return;
        }
        switch (action) {
          case "insert":
            Swal.fire({
              title: "Add Revalida Score",
              input: "number",
              inputLabel: "Enter revalida grade (%):",
              inputAttributes: {
                step: "any" // Allows any decimal value
              },
              inputValidator: (value) => {
                if (!value) {
                  return "Please enter a number!";
                }
              },
              showLoaderOnConfirm: true,
              allowOutsideClick: () => !Swal.isLoading(),
              preConfirm: async (revalida_score) => {
                  if (!revalida_score) {
                      Swal.showValidationMessage(`Fill in required field.`);
                      return;
                  }
                  const sy_id = $("select#school_year_filter").val();
                  return new Promise(async (resolve) => {
                      $.post(window.location.href, {sy_id, stud_id, revalida_score, action: "insert"})
                          .done(function (result) {
                              resolve([true, result]);
                          })
                          .fail((error, statusText) => resolve([false, statusText]));
                  }).then(([success, message]) => {
                      if (success && !!message) {
                          Swal.fire({
                              icon: "success",
                              title: message,
                              showConfirmButton: false,
                              position: "center",
                              timer: 1000 
                          }).then(() => {
                              window.location.reload()
                          })
                      } else {
                          Swal.showValidationMessage("Failed to add revalida score.");
                      }
                  });
              }
            });
            break;
          case "update":
            Swal.fire({
              title: "Add Revalida Score",
              input: "number",
              inputLabel: "Enter revalida grade (%):",
              inputValue: prev_value, 
              showLoaderOnConfirm: true,
              inputAttributes: {
                step: "any" // Allows any decimal value
              },
              inputValidator: (value) => {
                if (!value) {
                  return "Please enter a number!";
                }
              },
              allowOutsideClick: () => !Swal.isLoading(),
              preConfirm: async (revalida_score) => {
                  if (!revalida_score) {
                      Swal.showValidationMessage(`Fill in required field.`);
                      return;
                  }
                  const sy_id = $("select#school_year_filter").val();
                  return new Promise(async (resolve) => {
                      $.post(window.location.href, {sy_id, stud_id, revalida_score, action: "update"})
                          .done(function (result) {
                              resolve([true, result]);
                          })
                          .fail((error, statusText) => resolve([false, statusText]));
                  }).then(([success, message]) => {
                      if (success && !!message) {
                          Swal.fire({
                              icon: "success",
                              title: message,
                              showConfirmButton: false,
                              position: "center",
                              timer: 1000 
                          }).then(() => {
                              window.location.reload()
                          })
                      } else {
                          Swal.showValidationMessage("Failed to update revalida score.");
                      }
                  });
              }
            });
            break;
          default:
            Swal.fire({
              text: "Invalid Action",
              toast: true,
              timer: 1000,
            });
        }
      })
    
    });
  </script>

</body>

</html>

<?php endif; ?>