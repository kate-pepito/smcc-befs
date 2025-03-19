<?php 

authenticated_page("dean");

if ($_SERVER['REQUEST_METHOD'] === "POST"): // POST METHOD
  
else: // GET METHOD
$query=conn()->query("select * from users where id = '" . user_id() . "'")or die(mysqli_error(conn()->get_conn()));
if($row=mysqli_fetch_array($query))
{
  $fname=$row['fname'];
  $lname=$row['lname'];
  $type=$row['type'];
  $fname = ucfirst(strtolower($fname));
  $lname = ucfirst(strtolower($lname));
  $type = ucfirst(strtolower($type));
}

$students_preboard1 = getStudentsWithTotalAvgScore("PREBOARD1");

// Fetch students for PREBOARD2
$students_preboard2 = getStudentsWithTotalAvgScore("PREBOARD2");

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
          MAX(gwa_p.gwa) AS gwa,
          MAX(be.board_exam_grade) AS board_exam_score
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
      LEFT JOIN 
          `board_exam_grade` as be
          ON be.school_year_id = school_year.id
          AND be.student_id = students.id
      WHERE 
          students.status = 'active'
      ORDER BY students.lname ASC
";
$data_gathered = [];

$q = conn()->query($sql) or die(mysqli_error(conn()->get_conn()));
while ($rw = $q->fetch_assoc())
{
  $preboard1 = array_filter($students_preboard1, fn($sp1) => strval($sp1["lrn_num"]) === strval($rw["lrn_num"]));
  $preboard1 = end($preboard1);
  $preboard2 = array_filter($students_preboard2, fn($sp2) => strval($sp2["lrn_num"]) === strval($rw["lrn_num"]));
  $preboard2 = end($preboard2);
  $data_gathered[] = array_merge($rw, [
    "preboard1" => $preboard1["total_preboard_average"] ?? null,
    "preboard2" => $preboard2["total_preboard_average"] ?? null,
    "school_year" => $preboard2["school_year"] ?? null
  ]);
}


admin_html_head("Datasets", [
  [ "type" => "style", "href" => "https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.15.10/sweetalert2.min.css" ],
  [ "type" => "style", "href" => "assets/vendor/remixicon/remixicon.css" ],
  [ "type" => "style", "href" => "assets/vendor/simple-datatables/style.css" ],
  [ "type" => "style", "href" => "assets/css/style.css" ],
]); // html head

?>
<body>
  
  <!-- Header and Sidebar -->
  <?php require_once get_dean_header(); ?>
  <?php require_once get_dean_sidebar(); ?>


  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Dataset</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item">Forecast Training</li>
          <li class="breadcrumb-item active">Extract Dataset</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between gap-3">
                <h5 class="card-title">Extract Dataset</h5>
                <div class="mt-4 me-2">
                  <button type="button" class="btn btn-success" id="saveDatasetBtn"><i class="bi bi-file-earmark-arrow-down"></i> Save to CSV (Dataset)</button>
                </div>
              </div>
               <!-- Table with stripped rows -->
              <table class="table datatable">
                <thead>
                  <tr>
                    <th>#</th> <!-- Sequence Number -->
                    <th>Student ID.</th>
                    <th>School Year</th>
                    <th>Preboard 1 (%)</th>
                    <th>Preboard 2 (%)</th>
                    <th>Revalida (%)</th>
                    <th>GWA (%)</th>
                    <th>Board Exam (%)</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    $valid_dataset = array_filter($data_gathered, function($dg) {
                      $p1 = $dg["preboard1"] ?: null;
                      $p2 = $dg["preboard2"] ?: null;
                      $rv = $dg["revalida_score"] ?: null;
                      $bex = $dg["board_exam_score"] ?: null;
                      return $p1 !== null && $p2 !== null && $rv !== null && $bex !== null;
                    });
                    $counter = 1;
                    foreach ($valid_dataset as $vd) {
                  ?>
                    <tr>
                      <td><?= $counter++ ?></td>
                      <td><?= $vd["lrn_num"] ?></td>
                      <td><?= $vd["school_year"] ?></td>
                      <td><?= "{$vd["preboard1"]} %" ?></td>
                      <td><?= "{$vd["preboard2"]} %" ?></td>
                      <td><?= "{$vd["revalida_score"]} %" ?></td>
                      <td><?= $vd["gwa"] !== null ? $vd["gwa"] . " %" : "N/A" ?></td>
                      <td><?= "{$vd["board_exam_score"]} %" ?></td>
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
  <?php
    require_once get_footer();
  ?>
  <!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <?php admin_html_body_end([
      ["type" => "script", "src" => "assets/vendor/simple-datatables/simple-datatables.js"],
      ["type" => "script", "src" => "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"],
      ["type" => "script", "src" => "https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.15.10/sweetalert2.min.js"],
      ["type" => "script", "src" => "assets/js/main.js"],
  ]); ?>

  <script>
    $(function() {
      // Convert PHP array to JavaScript array
      var valid_dataset = <?= json_encode(array_map(
        fn($vd) => [
          "preboard1" => floatval($vd["preboard1"]),
          "preboard2" => floatval($vd["preboard2"]),
          "revalida" => floatval($vd["revalida_score"]),
          "gwa" => ($vd["gwa"] ?? null) === null ? NAN : floatval($vd["gwa"]),
          "board_passer" => floatval($vd["board_exam_score"]) >= 75 ? "PASSER" : "NOT_PASSER"
        ], $valid_dataset)
      ) ?>;
        
        $("button#saveDatasetBtn").on("click", function (ev) {
            ev.preventDefault();

            // Convert dataset to CSV
            function convertToCSV(data) {
                const headers = Object.keys(data[0]).join(",") + "\n"; // Extract column names
                const rows = data.map(row => Object.values(row).join(",")).join("\n"); // Convert each row
                return headers + rows;
            }

            const csvContent = convertToCSV(valid_dataset);
            const blob = new Blob([csvContent], { type: "text/csv" });
            const url = URL.createObjectURL(blob);
            const a = document.createElement("a");
            a.href = url;
            a.download = "dataset.csv";
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        });
    })
  </script>

</body>

</html>

<?php endif; ?>
