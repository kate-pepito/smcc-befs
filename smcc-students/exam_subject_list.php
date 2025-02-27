<?php

authenticated_page("student");

$stud_id = user_id();

$query = conn()->query("select * from students where id = '" . user_id() . "'") or die(mysqli_error(conn()->get_conn()));

while ($row = mysqli_fetch_array($query)) {
    $fname = $row['fname'];
    $lname = $row['lname'];
    $level = $row['level'];
}


student_html_head('Home', [
    [ "type" => "style", "href" => "smcc-students/lib/owlcarousel/assets/owl.carousel.min.css" ],
    [ "type" => "style", "href" => "smcc-students/css/style.css" ],
    [ "type" => "custom", "content" => function () {
            ?>
            <script type="text/javascript">
                window.onload = function() {

                    sessionStorage.clear();


                };
            </script>
            <?php
        }
    ],
]);

?>

<body>
    <?php student_nav(base_url() . "/smcc-students", "Back to Dashboard"); ?>

    <!-- Service Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-4">

                <?php


                // Modified query to include faculty name
                $query = conn()->query("
    SELECT 
        subjects.id AS sub_id, 
        subjects.code AS code, 
        subjects.description AS description, 
        CONCAT(users.fname, ' ', users.lname) AS faculty_name
    FROM students_subjects
    INNER JOIN subjects ON students_subjects.subjects_id = subjects.id
    LEFT JOIN faculty_subjects ON faculty_subjects.subjects_id = subjects.id
    LEFT JOIN users ON faculty_subjects.faculty_id = users.id
    WHERE students_subjects.students_id = $stud_id
    AND students_subjects.status = 'NOT TAKEN'
    AND students_subjects.level = '$level'
") or die(mysqli_error(conn()->get_conn()));

                while ($row = mysqli_fetch_array($query)) {
                    $sub_id = $row['sub_id'];
                    $code = $row['code'];
                    $description = $row['description'];
                    $faculty_name = $row['faculty_name']; // Get the faculty name
                ?>
                    <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="service-item text-center pt-3">
                            <a href="exam_form?sub_id=<?php echo $sub_id; ?>">
                                <div class="p-4">
                                    <i class="fa fa-3x fa-graduation-cap text-primary mb-4"></i>
                                    <p><?php echo $code; ?></p>
                                    <h5 class="mb-3"><?php echo $description; ?></h5>
                                    <p><strong>Reviewer: </strong><?php echo $faculty_name ? $faculty_name : 'No faculty assigned'; ?></p> <!-- Display faculty name -->
                                </div>
                            </a>
                        </div>
                    </div>
                <?php
                }
                ?>



            </div>
        </div>
    </div>
    <!-- Service End -->



    <!-- Testimonial Start -->
    <div class="container-xxl py-5 wow fadeInUp" data-wow-delay="0.1s">
        <div class="container">
            <div class="text-center">
                <h6 class="section-title bg-white text-center text-primary px-3">Testimonial</h6>
                <h1 class="mb-5">Our Students Say!</h1>
            </div>
            <div class="owl-carousel testimonial-carousel position-relative">

                <?php

                $query = conn()->query("select * from students") or die(mysqli_error(conn()->get_conn()));
                while ($row = mysqli_fetch_array($query)) {
                    $fname = $row['fname'];
                    $about = $row['about'];
                    if ($about == "") {
                        $about = "Nothing to say!";
                    } else {
                        $about = $row['about'];
                    }
                ?>
                    <div class="testimonial-item text-center">
                        <h5 class="mb-0"><?php echo $fname; ?></h5>
                        <p>Student</p>
                        <div class="testimonial-text bg-light text-center p-4">
                            <p class="mb-0"><?php echo $about; ?></p>
                        </div>
                    </div>

                <?php
                }
                ?>
            </div>
        </div>
    </div>
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