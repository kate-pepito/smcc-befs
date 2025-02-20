<?php 
include('../dbconnect.php');
$user_id = mysqli_real_escape_string($conn, $_REQUEST['user_id']);
$query=mysqli_query($conn,"select * from users where id = '$user_id'")or die(mysqli_error($conn));
if($row=mysqli_fetch_array($query))
{
  $logged_in = $row['logged_in'];
  if($logged_in=="NO" || $logged_in==null){
  echo "<script type='text/javascript'>alert('Please login again!');
  document.location='index.php'</script>";
  }
  else{
?>
<!DOCTYPE html>
<html lang="en">
  <?php
  $query=mysqli_query($conn,"select * from faculty_course_school_year where user_id = $user_id")or die(mysqli_error($conn));
  if($row=mysqli_fetch_array($query))
  {
    $course_id = $row['course_id'];
  }
  else
  {
    echo "Error: " . $query . "<br>" . mysqli_error($conn);
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

  $query=mysqli_query($conn,"select * from users where id = '$user_id'")or die(mysqli_error($conn));
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
  <!-- ======= Header ======= -->
  <?php
  include('reviewer_header.php');
  ?>
  <!-- End Header -->
  <!-- ======= Sidebar ======= -->
  <?php
  include('reviewer_sidebar.php');
  ?>
  <!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Dashboard</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <div class="row">
        <!-- Active Students Card -->
        <div class="col-md-6">
            <div class="card info-card">
                <div class="card-body">
                    <a href="reviewer_students_active.php?user_id=<?php echo $user_id; ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="Click to view Active Students">
                        <h5 class="card-title">Students <span>| Active</span></h5>
                        <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center" style="width: 100px; height: 100px; background-color: #f1f1f1;">                                
                          <i class="ri-user-2-fill" style="font-size: 50px; color: #4caf50;"></i>
                            </div>
                            <?php 
                            // Count active students
                            $active_query = mysqli_query($conn, "
                                SELECT COUNT(*) AS student_count_active 
                                FROM students 
                                WHERE status = 'Active' AND course_id = '$course_id'
                            ") or die(mysqli_error($conn));

                            $student_count_active = 0;
                            if ($active_row = mysqli_fetch_array($active_query)) {
                                $student_count_active = $active_row['student_count_active'];
                            }
                            ?>
                            <div class="ps-3">
                                <h6 style="font-size: 35px; font-weight: bold; color: #4caf50;"><?php echo $student_count_active; ?></h6>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Inactive Students Card -->
        <div class="col-md-6">
            <div class="card info-card">
                <div class="card-body">
                    <a href="reviewer_students_inactive.php?user_id=<?php echo $user_id; ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="Click to view Inactive Students">
                        <h5 class="card-title">Students <span>| Inactive</span></h5>
                        <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center" style="width: 100px; height: 100px; background-color: #f1f1f1;">                                <i class="ri-user-2-fill" style="font-size: 50px; color: #e80404;"></i>
                            </div>
                            <?php 
                            // Count inactive students
                            $inactive_query = mysqli_query($conn, "
                                SELECT COUNT(*) AS student_count_inactive 
                                FROM students 
                                WHERE status = 'Inactive' AND course_id = '$course_id'
                            ") or die(mysqli_error($conn));

                            $student_count_inactive = 0;
                            if ($inactive_row = mysqli_fetch_array($inactive_query)) {
                                $student_count_inactive = $inactive_row['student_count_inactive'];
                            }
                            ?>
                            <div class="ps-3">
                                <h6 style="font-size: 35px; font-weight: bold; color: #e80404;"><?php echo $student_count_inactive; ?></h6>
                            </div>
                </div>
            </a>
        </div>
    </div>
</div><!-- End Inactive Students Card -->

      </div>
    </div><!-- End Left side columns -->

  </div>
</section>

    <div class="row">
      <iframe width="100%" height="550" src="https://www.youtube.com/embed/P8vKdsgV1t8" title="Saint Michael College of Caraga Full Corporate AVP 2023" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
    </div>
  
  </br>

       
     <!--    <div class="col-12">
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
                    <?php
                    include('../dbconnect.php');
                    $query=mysqli_query($conn,"
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
                            SELECT sum(student_score.average)
                            FROM student_score
                            WHERE student_score.stud_id = students.id
                        ) AS sum_average
                    FROM
                        students
                    JOIN course ON students.course_id = course.id
                    JOIN year_level ON students.year_level_id = year_level.id
                    JOIN section ON students.section_id = section.id
                    ORDER BY sum_average DESC;
                    ")or die(mysqli_error($conn));
                    while($row=mysqli_fetch_array($query))
                    {
                      $stud_id=$row['stud_id'];
                      $lrn_num=$row['lrn_num'];
                      $yr_desc=$row['yr_desc'];
                      $lname=$row['lname'];
                      $fname=$row['fname'];
                      $course=$row['course'];
                      $sec_desc=$row['sec_desc'];
                      $sum_average=$row['sum_average'];
                      $level=$row['level'];
                      if($sum_average=="")
                      {
                        $sum_average=0;
                      }
                      else{
                        $sum_average=$row['sum_average'];
                      }
                      $formatted_sum_average = number_format($sum_average, 2);
                  ?>
                  <tr>
                      <td><?php echo $lrn_num; ?></td>
                      <td><?php echo $lname.", ".$fname; ?></td>
                      <td><?php echo $yr_desc; ?></td>
                      <td><?php echo $course; ?></td>
                      <td><?php echo $sec_desc; ?></td>
                      <?php 
                      if($level=='PREBOARD1'){
                      ?>
                      <td><?php echo $formatted_sum_average; ?> %</td>
                      <?php
                      }
                      else{
                        ?>
                        <td><?php echo $formatted_sum_average / 2; ?> %</td>
                        <?php
                      }
                      ?>
                      <?php
                      if($formatted_sum_average>='75'){
                        ?>
                        <td>Passed</td>
                        <?php

                      }
                      else{
                        ?>
                        <td>Failed</td>
                        <?php
                      }
                      ?>
                      
                  </tr>
                  
                  <?php 
                    } 
                  ?>
                  
                </tbody>
                  </table>

                </div>

              </div>
            </div>End Recent Sales -->
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
<?php
}
}
else{
    echo "<script type='text/javascript'>alert('Please login again!');
		document.location='index.php'</script>";
}
?>