<?php

authenticated_page("student");

$stud_id = user_id();
$sub_id = $_REQUEST['sub_id'];

require_once get_student_exam_form_sc();
shuffle($questions);

$query = conn()->query("select * from subject_percent where sub_id = '$sub_id'") or die(mysqli_error(conn()->get_conn()));
if ($row = mysqli_fetch_array($query)) {
    $percent = $row['percent'];
}


$query = conn()->query("select * from students where id = '$stud_id'") or die(mysqli_error(conn()->get_conn()));
if ($row = mysqli_fetch_array($query)) {
    $level = $row['level'];
}

$query = conn()->query("select count(id) as c from question_answer where subject_id = '$sub_id'") or die(mysqli_error(conn()->get_conn()));
if ($row = mysqli_fetch_array($query)) {
    $c = $row['c'];
}

student_html_head('Home', [
    [ "type" => "style", "href" => "smcc-students/lib/animate/animate.min.css"],
    [ "type" => "style", "href" => "smcc-students/css/style.css" ],
    [ "type" => "style", "href" => "smcc-students/lib/owlcarousel/assets/owl.carousel.min.css" ],
    [ "type" => "custom", "content" => function() use ($sub_id, $timer) {
        ?>
            <script>
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
        <?php
        }
    ],
]);

?>

<body>
    <?php student_nav(); ?>

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
            <?php if ($_SERVER['REQUEST_METHOD'] === 'GET'): ?>
                <input type="submit" id="submitBtn" class="btn btn-primary btn-lg rounded-pill px-4 py-2" value="Submit Answers">
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

    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <?php
        $count_questions = count($questions);
        $get_average = ($score / $count_questions) * $percent;
        date_default_timezone_set("Asia/Manila");
        $dt = date("Y-m-d") . " " . date("h:i:sa");

        $query = "insert into student_score (score,total_items,stud_id,average,sub_id,date_accomplished,level) values ('$score','$count_questions','$stud_id','$get_average','$sub_id','$dt','$level') " or die(mysqli_error(conn()->get_conn()));
        if (conn()->query($query)) {
            $s_id = $_REQUEST['s_id'];

            $query = "update students_subjects set status = 'TAKEN' where students_id = '$stud_id' and subjects_id = '$sub_id' and level = '$level'" or die(mysqli_error(conn()->get_conn()));
            if (conn()->query($query)) {
                echo "<script type='text/javascript'>alert('Exam Successfully Submited!');
                    document.location='exam_subject_list'</script>";
            }
        } else {
            echo "Error: " . $query . "<br>" . mysqli_error(conn()->get_conn());
        }
        ?>
    <?php endif; ?>
    <!-- Testimonial End -->


    <!-- Footer Start -->
    <?php
    require_once get_student_footer();
    ?>
    <!-- Footer End -->

    <?php student_html_body_end([
        [ "type" => "script", "src" => "/smcc-students/lib/owlcarousel/owl.carousel.min.js" ],
        [ "type" => "script", "src" => "/smcc-students/js/main.js" ],
    ]); ?>

</body>

</html>