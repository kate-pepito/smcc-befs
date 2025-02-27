
<?php 

authenticated_page("dean");

$c_id = conn()->sanitize($_REQUEST['c_id']);
$stud_id = conn()->sanitize($_REQUEST['stud_id']);

admin_html_head("Student Assign Subject", [
  [ "type" => "style", "href" => "assets/vendor/remixicon/remixicon.css" ],
  [ "type" => "style", "href" => "assets/vendor/simple-datatables/style.css" ],
  [ "type" => "style", "href" => "assets/css/style.css" ],
]); // html head

?>

<body>
<?php 

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

?>
  <!-- ======= Header ======= -->
  <?php
   require_once get_dean_header();
   ?><!-- End Header -->
  <!-- ======= Sidebar ======= -->
  <?php 
  require_once get_dean_sidebar();
  ?>
  <!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Assign Subjects</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="dean_home_page">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="dean_students_active">Active Students</a></li>
          <li class="breadcrumb-item">Assign Subjects</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-6">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">List of Subjects</h5>
              <!-- Table with stripped rows -->
              <table class="table datatable">
                <thead>
                  <tr>
                    <th>Code</th>
                    <th>Description</th>
                    <th>Year Level</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                <?php
                    
                    $query=conn()->query("
                    SELECT 
                  subjects.id AS sub_id, 
                  subjects.code AS code, 
                  subjects.description AS description, 
                  year_level.description AS yr_desc 
              FROM 
                  subjects
              JOIN 
                  year_level ON subjects.year_level_id = year_level.id
              WHERE 
                  subjects.course_id = '$c_id' 
                  AND subjects.status = 'Active' 
                  AND subjects.id NOT IN (
                      SELECT subjects_id 
                      FROM students_subjects 
                      WHERE students_id = '$stud_id'
                        );
                    ")or die(mysqli_error(conn()->get_conn()));
                    while($row=mysqli_fetch_array($query))
                    {
                        $sub_id=$row['sub_id'];
                        $code=$row['code'];
                        $description=$row['description'];
                        $yr_desc=$row['yr_desc'];
                  ?>
                  <tr>
                  <td><b><?php echo $row['code']; ?></b></td>
                  <td><?php echo $row['description']; ?></td>
                  <td><?php echo $row['yr_desc']; ?></td>
                  <td>
                    <a href="dean_students_assign_subjects_sc?stud_id=<?php echo $stud_id; ?>&sub_id=<?php echo $row['sub_id']; ?>&c_id=<?php echo $c_id; ?>">
                    <button type="button" class="btn" style="background-color: #6FCF97; color: white; border-color: #6FCF97;">Pick Subject</button>
                    </a>
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
        <div class="col-lg-6">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Subject Assigned</h5>
              <!-- Table with stripped rows -->
              <table class="table datatable">
                <thead>
                  <tr>
                    <th>Code</th>
                    <th>Description</th>
                    <th>Year Level</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                <?php
                    
                    $query=conn()->query("SELECT 
                  students_subjects.subjects_id AS sid, 
                  students_subjects.id AS sub_id, 
                  year_level.description AS y_desc, 
                  subjects.code AS sub_code, 
                  subjects.description AS sub_desc 
              FROM 
                  students_subjects
              JOIN 
                  subjects ON students_subjects.subjects_id = subjects.id
              JOIN 
                  year_level ON subjects.year_level_id = year_level.id
              WHERE 
                  students_subjects.students_id = '$stud_id' AND students_subjects.level = 'PREBOARD1'")or die(mysqli_error(conn()->get_conn()));
                    while($row=mysqli_fetch_array($query))
                    {
                      $sub_id=$row['sub_id'];
                      $sid=$row['sid'];
                      $sub_code=$row['sub_code'];
                      $sub_desc=$row['sub_desc'];
                      $y_desc=$row['y_desc'];
                  ?>
                  <tr>
                  <td><b><?php echo $row['sub_code']; ?></b></td>
                  <td><?php echo $row['sub_desc']; ?></td>
                  <td><?php echo $row['y_desc']; ?></td>
                  <td>
                    <a href="dean_students_assign_subjects_delete_sc?stud_id=<?php echo $stud_id; ?>&sub_id=<?php echo $row['sid']; ?>&c_id=<?php echo $c_id; ?>">
                      <button type="button" class="btn btn-danger">Remove</button>
                    </a>
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
   <?php 
   require_once get_footer();
   ?>
<!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <?php admin_html_body_end([
      ["type" => "script", "src" => "assets/vendor/simple-datatables/simple-datatables.js"],
      ["type" => "script", "src" => "assets/js/main.js"],
  ]); ?>

</body>

</html>