<?php 

authenticated_page("dean");

if ($_SERVER['REQUEST_METHOD'] === "POST"): // method POST

  $inference_result = $_POST['inference_result'] ?? null;
  $stud_id = $_POST['stud_id'] ?? null;
  $sy_id = $_POST['sy_id'] ?? null;

  if ($sy_id === null || $stud_id === null || $inference_result === null) {
    http_response_code(400);
    die("Invalid Request");
  }
  $key = "inference_{$stud_id}_{$sy_id}";
  $_SESSION[$key] = trim($inference_result);
  die("Success");

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


$has_inference = false;
$ks = array_keys($_SESSION);
foreach ($ks as $ks_key) {
  if (strpos($ks_key, "inference_") === 0) {
    $has_inference = true;
    break;
  }
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
          <h1>Recommended Board Takers</h1>
          <nav>
            <ol class="breadcrumb">
              <li class="breadcrumb-item">
                <a href="dean_home_page">Dashboard</a>
              </li>
              <li class="breadcrumb-item">Recommended Board Takers</li>
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
              <?php
                    $q = "SELECT
                            imodel.id AS id,
                            imodel.name AS model_name,
                            imodel.algo AS model_algo,
                            imodel.created_at AS model_created,
                            imodel.fullpath AS model_path
                        FROM `selected_model` AS smodel
                        INNER JOIN `inference_model` AS imodel ON imodel.id = smodel.inference_model_id
                        WHERE smodel.school_year_id = '$school_year'
                    ";

                    $mq = conn()->query($q);
                    if ($mrow = $mq->fetch_assoc()) {
                        $model_id = $mrow["id"];
                        $model_name = $mrow["model_name"];
                        $model_algo = $mrow["model_algo"];
                        $model_created = (new DateTime($mrow["model_created"]))->format("M j, Y");
                        $model_filepath = $mrow["model_path"];
                    }
              ?>
              <div
                class="alert <?= ($model_id ?? false) ? "alert-success" : "alert-warning" ?> pt-1 pb-1"
                role="alert"
              >
                <p class="mb-0 text-center">
                    <?php if ($model_id ?? false): ?>
                    <i class="bi bi-check-circle"></i> Using AI model [<?= $model_algo ?>] &lsquo;<?= $model_name ?>&rsquo; (<?= $model_created ?>)
                    <?php else: ?>
                    <i class="bi bi-exclamation-circle"></i> No AI Model used. Contact Administrator to use this feature.
                    <?php endif; ?>
                </p>
              </div>
              
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
                    <th>Preboard 1 (%)</th>
                    <th>Preboard 2 (%)</th>
                    <th>Revalida (%)</th>
                    <th>GWA (%)</th>
                    <th>Passer? <button
                        type="button"
                        class="btn btn-success pl-2 pr-2 pt-1 pb-1 border border-success"
                        id="forecastRecommendationBtn"
                        title="Click to Forecast Recommendation to take board exam"
                        data-befs-model-path="<?= htmlspecialchars($model_filepath) ?>"
                    >
                    <i class="bi bi-check2-circle"></i>
                    </button></th>
                  </tr>
                </thead>
                <tbody>
                <?php
                  // preboard1 and preboard 2    
                  $preboard1 = getStudentsWithTotalAvgScore('PREBOARD1', $school_year);
                  $preboard2 = getStudentsWithTotalAvgScore('PREBOARD2', $school_year);
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
                        MAX(rg.revalida_grade) AS revalida_score,
                        MAX(gwa_p.gwa) AS gwa
                    FROM 
                        students
                    LEFT JOIN 
                        course ON students.course_id = course.id
                    LEFT JOIN 
                        section ON students.section_id = section.id
                    LEFT JOIN 
                        school_year ON students.school_year_id = school_year.id
                    LEFT JOIN 
                        `revalida_grade` as rg
                        ON rg.school_year_id = school_year.id
                        AND rg.student_id = students.id
                    LEFT JOIN 
                        `gwa_percentage` as gwa_p
                        ON gwa_p.school_year_id = school_year.id
                        AND gwa_p.student_id = students.id
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
                  $filter = $_GET['filter'] ?? "";
                  while ($row = mysqli_fetch_assoc($query)) {
                      $stud_id = $row['stud_id'];
                      $lrn_num = $row['lrn_num'];
                      $sy = $row['sy'];
                      $REVALIDA_GRADE = $row["revalida_score"] ?: null;
                      $REVALIDA_GRADE = $REVALIDA_GRADE !== null ? "$REVALIDA_GRADE %" : null;
                      $GWA = $row["gwa"] ?: null;
                      $GWA = $GWA !== null ? "$GWA %" : null;
                      $PREBOARD1 = array_filter($preboard1, fn($pb1) => strval($pb1["lrn_num"]) === strval($lrn_num));
                      $PREBOARD1 = end($PREBOARD1);
                      $PREBOARD2 = array_filter($preboard2, fn($pb2) => strval($pb2["lrn_num"]) === strval($lrn_num));
                      $PREBOARD2 = end($PREBOARD2);
                      $is_valid = ($PREBOARD1["s_status"] ?: "") === "TAKEN" && ($PREBOARD2["s_status"] ?: "") === "TAKEN" && $REVALIDA_GRADE !== null;
                      $ikey = "inference_{$stud_id}_{$school_year}";
                      if (!(($is_valid && $filter === "Available" && !$has_inference) || ($is_valid && $filter === "Passing" && $has_inference) || 
                        ($is_valid && $filter === "Not Passing" && $has_interference) || (!$is_valid && $filter === "Not Available") || ($filter === ""))) {
                        continue;                          
                      }
                      if (!($filter === "Not Passing" && strpos($_SESSION[$ikey] ?? "!!!", "<p class=\"text-danger\">Not Passing</p>") === 0) &&
                        !($filter === "Passing" && strpos($_SESSION[$ikey] ?? "!!!", "<p class=\"text-success\">Passing</p>") === 0) &&
                        !($filter === "Not Available" && ($_SESSION[$ikey] ?? null) === null) &&
                        !($filter === "Available" && $is_valid) && ($filter !== "")) {
                        continue;
                      }
                      
                      if ($is_valid) {
                        $inference_result = $_SESSION[$ikey] ?? null;
                        $jsonForecastData = json_encode([
                            "id" => $stud_id,
                            "sy_id" => $school_year,
                            "preboard1" => floatval($PREBOARD1["total_preboard_average"]),
                            "preboard2" => floatval($PREBOARD2["total_preboard_average"]),
                            "revalida" => floatval($REVALIDA_GRADE),
                            "gwa" => $GWA
                        ]);
                      }
                      
                      $lname = $row['lname'];
                      $fname = $row['fname'];
                      $gender = $row['gender'];
                      $course = $row['course'];
                      $section = $row['section'];
                      
                  ?>
                    <tr>
                      <td><?= $counter++ ?></td>
                      <td><?= $lrn_num ?></td>
                      <td><?= $lname ?></td>
                      <td><?= $fname ?></td>
                      <td><?= $gender ?></td>
                      <td><?= $course ?></td>
                      <td><?= $section ?></td>
                      <td><?= $sy ?></td>
                      <td><?= ($PREBOARD1["s_status"] ?: "") === "TAKEN" ? $PREBOARD1["total_preboard_average"] . "%" : "NOT TAKEN" ?></td>
                      <td><?= ($PREBOARD2["s_status"] ?: "") === "TAKEN" ? $PREBOARD2["total_preboard_average"] . "%" : "NOT TAKEN" ?></td>
                      <td><?= $REVALIDA_GRADE ?: "<a class='btn btn-outline-secondary border-0 text-start' href='".base_url()."/dean/dean_students_revalida?school_year=$school_year_id' title='Go to Revalida'>Add Revalida</a>" ?></td>
                      <td><?= $GWA ?: "<a class='btn btn-outline-secondary border-0 text-start' href='".base_url()."/dean/dean_students_gwa?school_year=$school_year_id' title='Go to GWA'>Add GWA</a>" ?></td>
                      <td>
                        <span
                            class="fw-bold befs-forecast"
                            data-befs-forecast-available="<?= $is_valid ? "true" : "false" ?>"
                            data-befs-data="<?= htmlspecialchars($jsonForecastData ?? "{}")  ?>"
                        ><?= $is_valid ? ($inference_result ?: "Available") : "N/A" ?></span>
                      </td>
                    </tr>
                  <?php
                    $inference_result = null;
                    $jsonForecastData = "{}";
                } ?>
                </tbody>
              </table>
              <!-- End Table with stripped rows -->
              <!-- Filter select -->
              <div class="d-block float-right ps-3 mb-3" id="filter-container">
                <div class="form-floating" style="max-width:200px;">
                  <?php
                  $filter = $_GET['filter'] ?? "";
                  ?>
                  <select class="form-select" id="filterPassing" default-value="<?= $filter === "Not Available" ? "N/A" : $filter ?>">
                    <option value="">-</option>
                    <?php if (!$has_inference): ?>
                    <option value="Available">Available</option>
                    <option value="N/A">Not Available</option>
                    <?php else: ?>
                    <option value="Not Passing">Not Passing</option>
                    <option value="Passing">Passing</option>
                    <option value="N/A">Not Available</option>
                    <?php endif; ?>
                  </select>
                  <label for="filterPassing" class="fw-bold">Filter Passer:</label>
                </div>
              </div>
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
      ["type" => "script", "src" => "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"],
      ["type" => "script", "src" => "assets/vendor/simple-datatables/simple-datatables.js"],
      ["type" => "script", "src" => "https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.15.10/sweetalert2.min.js"],
      ["type" => "script", "src" => "https://cdn.jsdelivr.net/npm/onnxruntime-web/dist/ort.min.js"],
      ["type" => "script", "src" => "assets/js/main.js"],
      ["type" => "script", "src" => "assets/js/inference.js"],
      ["type" => "script", "src" => "assets/js/forecast.js"],
  ]); ?>

</body>

</html>

<?php endif; ?>