<?php

authenticated_page("student");

$stud_id = $user_id;
$sub_id = $_REQUEST['sub_id'];

require_once get_student_exam_form_sc();
shuffle($questions);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Home - SMCC</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="<?= $BASE_URL ?>/smcc-students/lib/animate/animate.min.css" rel="stylesheet">
    <link href="<?= $BASE_URL ?>/smcc-students/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="<?= $BASE_URL ?>/smcc-students/css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="<?= $BASE_URL ?>/smcc-students/css/style.css" rel="stylesheet">

    <script type="text/javascript">
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });

        // Disable backspace key (typically for going back in browser history)
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace') {
                e.preventDefault();
            }
        });

        // Prevent going back using the browser’s back button
        window.history.forward();
        window.addEventListener("popstate", function(event) {
            window.history.forward();
        });

        function startTimer(duration, display, subjectId) {
            var timer = duration,
                minutes, seconds;
            var interval = setInterval(function() {
                minutes = parseInt(timer / 60, 10);
                seconds = parseInt(timer % 60, 10);

                minutes = minutes < 10 ? "0" + minutes : minutes;
                seconds = seconds < 10 ? "0" + seconds : seconds;

                display.textContent = minutes + ":" + seconds;

                if (--timer < 0) {
                    clearInterval(interval);
                    document.getElementById("submitBtn").click();
                }

                // Save the remaining time in localStorage, keyed by the subject ID
                localStorage.setItem('remainingTime_' + subjectId, timer);
            }, 1000);
        }

        window.onload = function() {
            // Get the subject ID from the URL
            var subjectId = '<?php echo $sub_id; ?>';

            // Check if there's a saved remaining time in localStorage for this subject
            var savedTime = localStorage.getItem('remainingTime_' + subjectId);
            var duration = savedTime ? savedTime : 60 * <?php echo $timer; ?>; // Use the default timer if there's no saved time

            var display = document.querySelector('#timer');
            startTimer(duration, display, subjectId);
        };
    </script>


</head>

<?php

$query = mysqli_query($conn, "select * from subject_percent where sub_id = '$sub_id'") or die(mysqli_error($conn));
if ($row = mysqli_fetch_array($query)) {
    $percent = $row['percent'];
}

?>
<?php

$query = mysqli_query($conn, "select * from students where id = '$stud_id'") or die(mysqli_error($conn));
if ($row = mysqli_fetch_array($query)) {
    $level = $row['level'];
}

?>

<body>
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <!-- Spinner End -->

    <!-- Navbar Start -->
    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
        <a href="index" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
            <h2 class="m-0 text-primary"><i class="fa fa-book me-3"></i>Saint Michael College of Caraga - BEFS</h2>
        </a>
        <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a href="index" class="nav-item nav-link active">Home</a>
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">PROFILE</a>
                    <div class="dropdown-menu fade-down m-0">
                        <a href="edit_profile_sc" class="dropdown-item">My Profile</a>
                        <a href="log_out_sc" class="dropdown-item">Log Out</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <!-- Navbar End -->
    <?php
    $query = mysqli_query($conn, "select count(id) as c from question_answer where subject_id = '$sub_id'") or die(mysqli_error($conn));
    if ($row = mysqli_fetch_array($query)) {
        $c = $row['c'];
    }

    ?>
    <!-- Testimonial Start -->
    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
        <div class="container-sm py-2 wow fadeInUp" data-wow-delay="0.1s">
            <div class="container">
                <div class="text-center">
                    <h6 class="section-title bg-white text-center text-primary px-3">Time</h6>
                    <h1 class="mb-3 h4" id="timer"><?php echo $timer; ?>:00</h1>
                </div>
            </div>
        </div>
    </nav>
    <br>
    <div class="container">
        <h1 class="mb-5 text-center" id="timer"><?php echo $sub_description; ?></h1>

        <form method="POST">
            <?php foreach ($questions as $index =>  $question): ?>

                <div>
                    <p><b><?php echo htmlspecialchars(($index + 1) . ". " . $question['question']); ?></b></p>
                    <?php foreach ($question['options'] as $option): ?>
                        <label>
                            <input type="radio" name="answer[<?php echo $question['id']; ?>]" value="<?php echo htmlspecialchars($option); ?>"
                                <?php echo (isset($user_answers[$question['id']]) && $user_answers[$question['id']] == $option) ? 'checked' : ''; ?>
                                <?php echo $_SERVER['REQUEST_METHOD'] == 'POST' ? 'disabled' : ''; ?>>
                            <?php echo htmlspecialchars($option); ?>
                        </label><br>
                    <?php endforeach; ?>
                </div>
                <br>
            <?php endforeach; ?>
            <?php if ($_SERVER['REQUEST_METHOD'] != 'POST'): ?>
                <input type="submit" id="submitBtn" class="btn btn-primary btn-lg rounded-pill px-4 py-2" value="Submit Answers">

                <!-- <input type="submit" id="submitBtn" value="Submit Answers" hidden> -->
            <?php endif; ?>
            </br>
            </br>
            </br>
            </br>
            <!-- <a href="SMCC-Exam-students/exam_subject_list" class="btn btn-primary">Back to page</a> -->
        </form>
    </div>
    </div>
    </div>

    <?php if ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
        <?php
        $count_questions = count($questions);
        $get_average = ($score / $count_questions) * $percent;
        date_default_timezone_set("Asia/Manila");
        $dt = date("Y-m-d") . " " . date("h:i:sa");

        $query = "insert into student_score (score,total_items,stud_id,average,sub_id,date_accomplished,level) values ('$score','$count_questions','$stud_id','$get_average','$sub_id','$dt','$level') " or die(mysqli_error($conn));
        if (mysqli_query($conn, $query)) {
            $s_id = $_REQUEST['s_id'];

            $query = "update students_subjects set status = 'TAKEN' where students_id = '$stud_id' and subjects_id = '$sub_id' and level = '$level'" or die(mysqli_error($conn));
            if (mysqli_query($conn, $query)) {
                echo "<script type='text/javascript'>alert('Exam Successfully Submited!');
                    document.location='exam_subject_list'</script>";
                $conn->close();
            }
        } else {
            echo "Error: " . $query . "<br>" . mysqli_error($conn);
            $conn->close();
        }
        $conn->close();
        ?>
    <?php endif; ?>
    <!-- Testimonial End -->


    <!-- Footer Start -->
    <?php
    require_once get_student_footer();
    ?>
    <!-- Footer End -->


    <!-- Back to Top -->



    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>

</html>