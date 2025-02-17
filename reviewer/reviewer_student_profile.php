<!DOCTYPE html>
<html lang="en">
<?php
include('../dbconnect.php');

// Sanitize input
$stud_id = htmlspecialchars($_REQUEST['stud_id'], ENT_QUOTES, 'UTF-8');
$user_id = htmlspecialchars($_REQUEST['user_id'], ENT_QUOTES, 'UTF-8');

// Fetch student details using prepared statement
$stmt = $conn->prepare("SELECT
    students.profile_image AS student_profile_image,
    students.lrn_num AS lrn_num,
    students.fname AS fname,
    students.lname AS lname,
    students.gender AS gender,
    students.username AS username,
    students.status AS status,
    students.complete_address AS complete_address,
    year_level.description AS yr_desc,
    course.description AS c_desc,
    section.description AS sec_desc,
    students.about AS about
FROM students
JOIN year_level ON students.year_level_id = year_level.id
JOIN course ON students.course_id = course.id
JOIN section ON students.section_id = section.id
WHERE students.id = ?");
$stmt->bind_param("i", $stud_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_array()) {
    $lrn_num = $row['lrn_num'];
    $sfname = $row['fname'];
    $slname = $row['lname'];
    $gender = $row['gender'];
    $username = $row['username'];
    $status = $row['status'];
    $complete_address = $row['complete_address'];
    $yr_desc = $row['yr_desc'];
    $c_desc = $row['c_desc'];
    $sec_desc = $row['sec_desc'];
    $about = $row['about'];
    $student_profile_image = $row['student_profile_image'];
} else {
    echo "Error: " . mysqli_error($conn);
}

// Fetch subject count using prepared statement
$stmt = $conn->prepare("SELECT COUNT(subjects_id) AS sub_count
    FROM students_subjects
    WHERE students_id = ?");
$stmt->bind_param("i", $stud_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_array()) {
    $sub_count = $row['sub_count'];
} else {
    echo "Error: " . mysqli_error($conn);
}

// Fetch dean/user details using prepared statement
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_array()) {
    $fname = ucfirst(strtolower($row['fname']));
    $lname = ucfirst(strtolower($row['lname']));
    $type = ucfirst(strtolower($row['type']));
    $dean_profile_image = !empty($row['profile_image']) ? $row['profile_image'] : '../assets/img/profile-img2.jpg';
}
?>

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Active Students - SMCC</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <link href="../images/Smcc_logo.gif" rel="icon">
    <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
    <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="../assets/vendor/quill/quill.snow.css" rel="stylesheet">
    <link href="../assets/vendor/quill/quill.bubble.css" rel="stylesheet">
    <link href="../assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="../assets/vendor/simple-datatables/style.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>

<body>
    <?php
    include('reviewer_header.php');
    include('reviewer_sidebar.php');
    ?>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Student's Profile</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="reviewer_home.php?user_id=<?php echo $user_id; ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="reviewer_students.php?user_id=<?php echo $user_id; ?>">List of Student</a></li>
                    <li class="breadcrumb-item">Student Profile</li>
                </ol>
            </nav>
        </div>

        <section class="section profile">
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body pt-3">
                            <ul class="nav nav-tabs nav-tabs-bordered">
                                <li class="nav-item">
                                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview">Overview</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#student-results">Results</button>
                                </li>
                            </ul>

                            <div class="tab-content pt-2">
                                <div class="tab-pane fade show active profile-overview" id="profile-overview">
                                    <form>
                                        </br>
                                        <img src='<?php echo !empty($student_profile_image) ? "../student/{$student_profile_image}" : "../assets/img/profile-img2.jpg"; ?>' alt='Profile Image' class='rounded-circle' width='100'>
                                        </br>

                                        <h5 class="card-title">About</h5>
                                        <?php
                                        if (empty($about)) {
                                            echo "<p class='small fst-italic'>Nothing to Show...</p>";
                                        } else {
                                            echo "<p class='small fst-italic'>{$about}</p>";
                                        }
                                        ?>
                                        <h5 class="card-title">Profile Details</h5>
                                        <div class="row">
                                            <div class="col-lg-3 col-md-4 label ">ID Number</div>
                                            <div class="col-lg-9 col-md-8"><?php echo $lrn_num ? "<b>{$lrn_num}</b>" : "<span class='badge bg-danger'>Not Yet Assign</span>"; ?></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-3 col-md-4 label ">Full Name</div>
                                            <div class="col-lg-9 col-md-8"><?php echo $sfname . " " . $slname; ?></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-3 col-md-4 label ">Gender</div>
                                            <div class="col-lg-9 col-md-8"><?php echo $gender; ?></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-3 col-md-4 label">Username</div>
                                            <div class="col-lg-9 col-md-8"><?php echo $username; ?></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-3 col-md-4 label">Password</div>
                                            <div class="col-lg-9 col-md-8">*****</div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-3 col-md-4 label ">Complete Address</div>
                                            <div class="col-lg-9 col-md-8"><?php echo $complete_address ? $complete_address : "<span class='badge bg-danger'>None</span>"; ?></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-3 col-md-4 label ">Year Level</div>
                                            <div class="col-lg-9 col-md-8"><?php echo $yr_desc ? $yr_desc : "<span class='badge bg-danger'>Not Yet Assign</span>"; ?></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-3 col-md-4 label ">Course</div>
                                            <div class="col-lg-9 col-md-8"><?php echo $c_desc ? $c_desc : "<span class='badge bg-danger'>Not Yet Assign</span>"; ?></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-3 col-md-4 label ">Section</div>
                                            <div class="col-lg-9 col-md-8"><?php echo $sec_desc ? $sec_desc : "<span class='badge bg-danger'>Not Yet Assign</span>"; ?></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-3 col-md-4 label ">Subject Counts</div>
                                            <div class="col-lg-9 col-md-8"><?php echo $sub_count > 0 ? $sub_count : "<span class='badge bg-danger'>Empty</span>"; ?></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-3 col-md-4 label ">Status</div>
                                            <div class="col-lg-9 col-md-8"><?php echo $status == "For Approval" ? "<span class='badge bg-danger'>For Approval</span>" : "<span class='badge bg-success'>{$status}</span>"; ?></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-3 col-md-4 label">Average Score</div>
                                            <?php
                                            $stmt = $conn->prepare("SELECT SUM(average) AS sum_average FROM student_score WHERE stud_id = ?");
                                            $stmt->bind_param("i", $stud_id);
                                            $stmt->execute();
                                            $result = $stmt->get_result();
                                            if ($row = $result->fetch_array()) {
                                                $sum_average = $row['sum_average'];
                                                echo "<div class='col-lg-9 col-md-8'>" . ($sum_average ? number_format($sum_average, 2) . " %" : "<span class='badge bg-danger'>Empty</span>") . "</div>";
                                            }
                                            ?>
                                        </div>
                                    </form>
                                </div>

                                <!-- Results Tab -->
                                <div class="tab-pane fade" id="student-results">
                                    <!-- Preboard 1 -->
                                    <section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">PREBOARD 1</h5>

                    <?php
                    // Query to calculate the total average score for PREBOARD 1
                    $stmt = $conn->prepare("
                        SELECT AVG(average) AS total_average
                        FROM student_score
                        WHERE stud_id = ? 
                            AND level = 'PREBOARD1'
                    ");
                    $stmt->bind_param("i", $stud_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $row = $result->fetch_array();
                    $total_average = $row['total_average'];

                    // Display total average score if available
                    if ($total_average !== null) {
                        echo "<p><strong>Total Average Score: </strong>" . number_format($total_average, 2) . " %</p>";
                    } else {
                        echo "<p><strong>Total Average Score: </strong>Not available</p>";
                    }
                    ?>

                    <!-- Table with stripped rows -->
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Code No.</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Score</th>
                                <th>Average</th>
                                <th>Percentile</th>
                                <th>Reviewer</th>
                                <th>Dean</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Query to fetch subjects and their individual averages for PREBOARD 1
                            $query = mysqli_query($conn, "
                                SELECT 
                                    subjects.code AS code,
                                    subjects.description AS description,
                                    students_subjects.status AS status,
                                    student_score.score AS score,
                                    student_score.total_items AS items,
                                    student_score.average AS avg_score,
                                    subject_percent.percent AS percent,
                                    student_score.remarks AS remarks,
                                    student_score.remarks2 AS remarks2
                                FROM 
                                    student_score
                                JOIN 
                                    students_subjects 
                                    ON students_subjects.subjects_id = student_score.sub_id
                                    AND students_subjects.students_id = student_score.stud_id
                                JOIN 
                                    subjects 
                                    ON students_subjects.subjects_id = subjects.id
                                LEFT JOIN 
                                    subject_percent 
                                    ON subject_percent.sub_id = subjects.id
                                WHERE 
                                    student_score.stud_id = '$stud_id' 
                                    AND student_score.level = 'PREBOARD1'
                                GROUP BY 
                                    subjects.code, subjects.description;
                            ") or die(mysqli_error($conn));

                            while ($row = mysqli_fetch_array($query)) {
                                $code = $row['code'];
                                $description = $row['description'];
                                $status = $row['status'];
                                $score = $row['score'];
                                $items = $row['items'];
                                $avg_score = $row['avg_score'];
                                $percent = $row['percent'];
                                $remarks = $row['remarks'];
                                $remarks2 = $row['remarks2'];
                                $formatted_avg_score = number_format($avg_score, 2); // Format individual subject average
                            ?>
                            <tr>
                                <td><?php echo $code; ?></td>
                                <td><?php echo $description; ?></td>
                                <td><?php echo $status; ?></td>
                                <td><?php echo $score . " / " . $items; ?></td>
                                <td><?php echo $formatted_avg_score; ?> %</td>
                                <td><?php echo $percent; ?>%</td>
                                <td style="max-width: 200px; overflow-x: auto;"><?php echo htmlspecialchars($remarks); ?></td>
                                <td style="max-width: 200px; overflow-x: auto;"><?php echo htmlspecialchars($remarks2); ?></td>
                            </tr>
                            <?php 
                            } 
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
            
                                    <!-- Preboard 2 -->
                                    <section class="section">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h5 class="card-title">PREBOARD 2</h5>
                                                        <!-- Table with stripped rows -->
                                                        <table class="table">
                                                            <thead>
                                                                <tr>
                                                                    <th>Code No.</th>
                                                                    <th>Description</th>
                                                                    <th>Status</th>
                                                                    <th>Score</th>
                                                                    <th>Average</th>
                                                                    <th>Percentile</th>
                                                                    <th>Reviewer</th>
                                                                    <th>Dean</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $query = mysqli_query($conn, "
                                                                    SELECT 
                                                                        subjects.code AS code,
                                                                        subjects.description AS description,
                                                                        MAX(students_subjects.status) AS status,
                                                                        MAX(student_score.score) AS score,
                                                                        MAX(student_score.total_items) AS items,
                                                                        MAX(student_score.average) AS avg_score,
                                                                        MAX(subject_percent.percent) AS percent,
                                                                        MAX(student_score.remarks) AS remarks,
                                                                        MAX(student_score.remarks2) AS remarks2
                                                                    FROM 
                                                                        student_score
                                                                    JOIN 
                                                                        students_subjects 
                                                                        ON students_subjects.students_id = student_score.stud_id
                                                                        AND students_subjects.subjects_id = student_score.sub_id
                                                                    JOIN 
                                                                        subjects 
                                                                        ON students_subjects.subjects_id = subjects.id
                                                                    LEFT JOIN 
                                                                        subject_percent 
                                                                        ON subject_percent.sub_id = subjects.id
                                                                    WHERE 
                                                                        student_score.stud_id = '$stud_id' 
                                                                        AND student_score.level = 'PREBOARD2'
                                                                        AND students_subjects.level = 'PREBOARD2'
                                                                    GROUP BY 
                                                                        subjects.code, subjects.description;
                                                                ") or die(mysqli_error($conn));

                                                                while ($row = mysqli_fetch_array($query)) {
                                                                    $code = $row['code'];
                                                                    $description = $row['description'];
                                                                    $status = $row['status'];
                                                                    $score = $row['score'];
                                                                    $items = $row['items'];
                                                                    $avg_score = $row['avg_score'];
                                                                    $percent = $row['percent'];
                                                                    $remarks = $row['remarks'];
                                                                    $remarks2 = $row['remarks2'];
                                                                    $formatted_sum_average = number_format($avg_score, 2);
                                                                ?>
                                                                <tr>
                                                                    <td><?php echo $code; ?></td>
                                                                    <td><?php echo $description; ?></td>
                                                                    <td><?php echo $status; ?></td>
                                                                    <td><?php echo $score . " / " . $items; ?></td>
                                                                    <td><?php echo $formatted_sum_average; ?> %</td>
                                                                    <td><?php echo $percent; ?>%</td>
                                                                    <td style="max-width: 200px; overflow-x: auto;"><?php echo htmlspecialchars($remarks); ?></td>
                                                                    <td style="max-width: 200px; overflow-x: auto;"><?php echo htmlspecialchars($remarks2); ?></td>
                                                                </tr>
                                                                <?php 
                                                                } 
                                                                ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Vendor JS Files -->
    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="../assets/vendor/tinymce/tinymce.min.js"></script>
    <script src="../assets/vendor/php-email-form/validate.js"></script>

    <!-- Template Main JS File -->
    <script src="../assets/js/main.js"></script>
</body>

</html>
