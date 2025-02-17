<!DOCTYPE html>
<?php 
include('../dbconnect.php');
$user_id = $_REQUEST['user_id'];
$query=mysqli_query($conn,"select * from users where id = '$user_id'")or die(mysqli_error());
if($row=mysqli_fetch_array($query))
{
   $logged_in = $row['logged_in'];
   if($logged_in=="NO" || $logged_in==null){
    echo "<script type='text/javascript'>alert('Please login again!');
		document.location='index.php'</script>";
   }
   else{}}
    ?>
<html lang="en">
<?php 
$user_id = $_REQUEST['user_id'];

// Get the current school year
$current_school_year_query = mysqli_query($conn, "SELECT id, description FROM school_year WHERE status = 'Current Set' LIMIT 1") or die(mysqli_error($conn));
if ($current_school_year_row = mysqli_fetch_array($current_school_year_query)) {
    $current_school_year_id = $current_school_year_row['id'];
    $current_school_year_description = $current_school_year_row['description'];
} else {
    $current_school_year_id = null;
    $current_school_year_description = "No Current Set";
}
?>


<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Dashboard - SMCC</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

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

  $query=mysqli_query($conn,"select * from users where id = '$user_id'")or die(mysqli_error());
    if($row=mysqli_fetch_array($query))
    {
      $fname=$row['fname'];
      $lname=$row['lname'];
      $type=$row['type'];
      $fname = ucfirst(strtolower($fname));
      $lname = ucfirst(strtolower($lname));
      $type = ucfirst(strtolower($type));
    }

?>
<!-- Header -->
<?php
   include('dean_header.php');
   ?>
 <!-- End Header -->
  <!-- ======= Sidebar ======= -->
  <?php

    include('dean_sidebar.php');

  ?>
  <!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Dashboard</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item">Dashboard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <!-- Sales Cards Row -->
<div class="row">
  <!-- Pending Students Card -->
<div class="col-xxl-4 col-md-4">
    <div class="card info-card">
        <div class="card-body">
            <a href="dean_students_pending.php?user_id=<?php echo $user_id; ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="Click to view Pending Students">
                <h5 class="card-title">Students <span>| Pending</span></h5>
                <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px; background-color: #f1f1f1;">
                        <i class="ri-user-2-fill" style="font-size: 40px;"></i>
                    </div>

                    <?php 
                    // Fetch the dean's course
                    $course_query = mysqli_query($conn, "
                        SELECT description FROM course 
                        JOIN dean_course ON course.id = dean_course.course_id 
                        WHERE dean_course.user_id = '$user_id'
                    ") or die(mysqli_error($conn));
                    
                    if ($course_row = mysqli_fetch_array($course_query)) {
                        $deans_course = $course_row['description'];
                    } else {
                        $deans_course = '';
                    }

                    // Query to count the pending students based on the dean's course
                    $query = mysqli_query($conn, "
                        SELECT COUNT(students.id) AS student_count_for_approval 
                        FROM students 
                        JOIN course ON students.course_id = course.id 
                        WHERE students.status = 'For Approval' 
                        AND course.description = '$deans_course'
                    ") or die(mysqli_error($conn));
                    
                    if ($row = mysqli_fetch_array($query)) {
                        $student_count_for_approval = $row['student_count_for_approval'];
                    } else {
                        $student_count_for_approval = 0;
                    }
                    ?>
                    
                    <div class="ps-3">
                        <h6 style="font-size: 25px; font-weight: bold;"><?php echo $student_count_for_approval; ?></h6>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div><!-- End Pending Students Card -->


    <!-- Active Students Card -->
<div class="col-xxl-4 col-md-4">
    <div class="card info-card">
        <div class="card-body">
            <a href="dean_students_active.php?user_id=<?php echo $user_id; ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="Click to view Active Students for Assigning Subjects">
                <h5 class="card-title">Students <span>| Active</span></h5>
                <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px; background-color: #f1f1f1;">
                        <i class="ri-user-2-fill" style="font-size: 40px; color:#4caf50"></i>
                    </div>

                    <?php 
                    // Fetch the dean's course
                    $course_query = mysqli_query($conn, "
                        SELECT description FROM course 
                        JOIN dean_course ON course.id = dean_course.course_id 
                        WHERE dean_course.user_id = '$user_id'
                    ") or die(mysqli_error($conn));
                    
                    if ($course_row = mysqli_fetch_array($course_query)) {
                        $deans_course = $course_row['description'];
                    } else {
                        $deans_course = '';
                    }

                    // Query to count the active students based on the dean's course
                    $query = mysqli_query($conn, "
                        SELECT COUNT(students.id) AS student_count_active 
                        FROM students 
                        JOIN course ON students.course_id = course.id 
                        WHERE students.status = 'Active' 
                        AND course.description = '$deans_course'
                    ") or die(mysqli_error($conn));
                    
                    if ($row = mysqli_fetch_array($query)) {
                        $student_count_active = $row['student_count_active'];
                    } else {
                        $student_count_active = 0;
                    }
                    ?>
                    
                    <div class="ps-3">
                        <h6 style="font-size: 25px; font-weight: bold; color: #4caf50"><?php echo $student_count_active; ?></h6>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div><!-- End Active Students Card -->


    <!-- Inactive Students Card -->
<div class="col-xxl-4 col-md-4">
    <div class="card info-card">
        <div class="card-body">
            <a href="dean_students_inactive.php?user_id=<?php echo $user_id; ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="Click to view Inactive Students">
                <h5 class="card-title">Students <span>| Inactive</span></h5>
                <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px; background-color: #f1f1f1;">
                        <i class="ri-user-2-fill" style="font-size: 40px; color:#e80404"></i>
                    </div>

                    <?php 
                    // Query to count the inactive students based on the dean's course
                    $query = mysqli_query($conn, "
                        SELECT COUNT(students.id) AS student_count_inactive 
                        FROM students 
                        JOIN course ON students.course_id = course.id 
                        WHERE students.status = 'Inactive' 
                        AND course.description = '$deans_course'
                    ") or die(mysqli_error($conn));
                    
                    if ($row = mysqli_fetch_array($query)) {
                        $student_count_inactive = $row['student_count_inactive'];
                    } else {
                        $student_count_inactive = 0;
                    }
                    ?>

                    <div class="ps-3">
                        <h6 style="font-size: 25px; font-weight: bold; color:#e80404"><?php echo $student_count_inactive; ?></h6>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div><!-- End Inactive Students Card -->

</div><!-- End Sales Cards Row -->

<?php
include('../dbconnect.php');

// Define the school year to filter (you can retrieve this dynamically based on user input or session data)
$school_year_id = isset($_GET['school_year_id']) ? $_GET['school_year_id'] : $current_school_year_id; // Default to current school year if not provided

// Query to calculate the total average per subject for students under the dean's course, filtered by school year
$query = mysqli_query($conn, "
    SELECT 
        subjects.description AS subject_name,
        AVG(student_score.average) AS avg_score,
        subject_percent.percent AS percentile
    FROM 
        student_score
    JOIN 
        subjects ON student_score.sub_id = subjects.id
    JOIN 
        students ON student_score.stud_id = students.id
    JOIN 
        dean_course ON dean_course.course_id = students.course_id
    LEFT JOIN 
        subject_percent ON subject_percent.sub_id = subjects.id
    WHERE 
        dean_course.user_id = $user_id
        AND students.school_year_id = $school_year_id -- Filter by selected school year
    GROUP BY 
        subjects.id
    ORDER BY 
        avg_score DESC
") or die(mysqli_error($conn));

// Prepare arrays for chart data
$subject_names = [];
$avg_scores = [];
$percentiles = [];

// Fetch data from the query result
while ($row = mysqli_fetch_array($query)) {
    $subject_names[] = $row['subject_name'];
    $avg_scores[] = $row['avg_score'];
    $percentiles[] = $row['percentile'];
}
?>

<div class="col-lg-12"> 
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Subject Ranking</h5>

            <!-- Dropdown for filtering school year -->
            <form method="GET" action="">
                <label for="school_year_id">School Year:</label>
                <select name="school_year_id" id="school_year_id">
                    <?php
                    $school_year_query = mysqli_query($conn, "SELECT id, description FROM school_year ORDER BY id ASC");
                    while ($row = mysqli_fetch_array($school_year_query)) {
                        $selected = ($row['id'] == $school_year_id) ? 'selected' : '';
                        echo "<option value='{$row['id']}' $selected>{$row['description']}</option>";
                    }
                    ?>
                </select>
            </form>

            <script>
                document.getElementById('school_year_id').addEventListener('change', function () {
                    const params = new URLSearchParams(window.location.search);
                    params.set('school_year_id', this.value); // Update school_year_id
                    window.location.search = params.toString(); // Redirect with updated parameters
                });
            </script>

            <!-- Check if there is data -->
            <?php if (empty($subject_names)) : ?>
                <p>No data available for the selected school year.</p>
            <?php else : ?>
                <!-- Area Chart -->
                <div id="areaChart"></div>

                <script>
                    document.addEventListener("DOMContentLoaded", () => {
                        // PHP arrays converted to JavaScript arrays
                        const subjectNames = <?php echo json_encode($subject_names); ?>;
                        let avgScores = <?php echo json_encode($avg_scores); ?>;
                        let percentiles = <?php echo json_encode($percentiles); ?>;

                        // Format the average scores and percentiles to 2 decimal points
                        avgScores = avgScores.map(score => parseFloat(score).toFixed(2));
                        percentiles = percentiles.map(percentile => percentile ? parseFloat(percentile).toFixed(2) : 'N/A');

                        // Initialize the chart
                        new ApexCharts(document.querySelector("#areaChart"), {
                            series: [
                                {
                                    name: "Average Score",
                                    data: avgScores
                                },
                                {
                                    name: "Percentile",
                                    data: percentiles
                                }
                            ],
                            chart: {
                                type: 'bar',
                                height: 350,
                                zoom: {
                                    enabled: false
                                }
                            },
                            dataLabels: {
                                enabled: true,
                                formatter: function (val) {
                                    return parseFloat(val).toFixed(2); // Ensure data labels also show 2 decimal points
                                }
                            },
                            xaxis: {
                                categories: subjectNames,
                                title: {
                                    text: 'Subjects'
                                }
                            },
                            yaxis: {
                                title: {
                                    text: 'Value'
                                }
                            },
                            legend: {
                                position: 'top'
                            }
                        }).render();
                    });
                </script>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- End Area Chart -->

<div class="col-12">
    <div class="card recent-sales overflow-auto">
        <div class="card-body">
            <h5 class="card-title">List of Top 10 Students</h5>

            <table class="table table-borderless datatable">
                <thead>
                    <tr>
                        <th scope="col">ID No.</th>
                        <th scope="col">Student Fullname</th>
                        <th scope="col">Year Level</th>
                        <th scope="col">Course</th>
                        <th scope="col">Section</th>
                        <th scope="col">Average</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                // Get the selected school year, user_id, and dean_course from the query string
                $selected_school_year = isset($_GET['school_year_id']) ? $_GET['school_year_id'] : $current_school_year_id; // Default to current school year if not provided
                $dean_course = isset($_GET['dean_course']) ? $_GET['dean_course'] : '';
                $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : '';

                // Modify the query to only show students linked to the dean_course table for the given user_id
                $query = mysqli_query($conn, "
                SELECT
                    year_level.description AS yr_desc,
                    section.description AS sec_desc,
                    students.status AS status,
                    students.lrn_num AS lrn_num,
                    students.id AS stud_id,
                    students.course_id AS c_id,
                    course.description AS course,
                    students.lname AS lname,
                    students.fname AS fname,
                    students.level as level,
                    (
                        SELECT SUM(student_score.average)
                        FROM student_score
                        WHERE student_score.stud_id = students.id
                    ) AS sum_average
                FROM
                    students
                JOIN course ON students.course_id = course.id
                JOIN year_level ON students.year_level_id = year_level.id
                JOIN section ON students.section_id = section.id
                WHERE ('$selected_school_year' = '' OR students.school_year_id = '$selected_school_year')
                AND ('$dean_course' = '' OR students.course_id = '$dean_course')
                AND students.course_id IN (SELECT course_id FROM dean_course WHERE user_id = '$user_id')
                ORDER BY sum_average DESC
                LIMIT 10
                ") or die(mysqli_error($conn));

                while ($row = mysqli_fetch_array($query)) {
                    $stud_id = $row['stud_id'];
                    $lrn_num = $row['lrn_num'];
                    $yr_desc = $row['yr_desc'];
                    $lname = $row['lname'];
                    $fname = $row['fname'];
                    $course = $row['course'];
                    $sec_desc = $row['sec_desc'];
                    $sum_average = $row['sum_average'];
                    $level = $row['level'];

                    if ($sum_average == "") {
                        $sum_average = 0;
                    } else {
                        $sum_average = $row['sum_average'];
                    }
                    $formatted_sum_average = number_format($sum_average, 2);
                ?>
                    <tr>
                        <td><?php echo $lrn_num; ?></td>
                        <td><?php echo $lname . ", " . $fname; ?></td>
                        <td><?php echo $yr_desc; ?></td>
                        <td><?php echo $course; ?></td>
                        <td><?php echo $sec_desc; ?></td>
                        <?php if ($level == 'PREBOARD1') { ?>
                            <td><?php echo $formatted_sum_average; ?> %</td>
                        <?php } else { ?>
                            <td><?php echo $formatted_sum_average / 2; ?> %</td>
                        <?php } ?>
                        <?php if ($formatted_sum_average >= '75') { ?>
                            <td>Passed</td>
                        <?php } else { ?>
                            <td>Failed</td>
                        <?php } ?>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

    </section>

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <?php
   include('../footer.php');
   ?>
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
