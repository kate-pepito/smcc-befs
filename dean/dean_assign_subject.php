<?php

authenticated_page("dean");

// Validate required parameters
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
$course_id = isset($_GET['course_id']) ? $_GET['course_id'] : null;
$sub_id = isset($_GET['sub_id']) ? $_GET['sub_id'] : null;
$faculty_id = isset($_GET['faculty_id']) ? $_GET['faculty_id'] : null;
$school_year = isset($_GET['school_year']) ? $_GET['school_year'] : null;

// Ensure all required parameters are provided
if (!$user_id || !$course_id || !$sub_id || !$faculty_id || !$school_year) {
    die("Error: Missing required parameters. Please log in again.");
}

// Escape input to prevent SQL injection
$user_id = mysqli_real_escape_string($conn, $user_id);
$course_id = mysqli_real_escape_string($conn, $course_id);
$sub_id = mysqli_real_escape_string($conn, $sub_id);
$faculty_id = mysqli_real_escape_string($conn, $faculty_id);
$school_year = mysqli_real_escape_string($conn, $school_year);

// Check if the subject is already assigned to the faculty
$check_query = "
    SELECT * 
    FROM faculty_subjects 
    WHERE faculty_id = '$faculty_id' AND subjects_id = '$sub_id'
";
$check_result = mysqli_query($conn, $check_query);

if (!$check_result) {
    die("Error: Failed to check subject assignment. " . mysqli_error($conn));
}

if (mysqli_num_rows($check_result) > 0) {
    die("Error: Subject is already assigned to this faculty.");
}

// Assign the subject
$assign_query = "
    INSERT INTO faculty_subjects (faculty_id, subjects_id, course_id, school_year_id, assigned_date)
    VALUES ('$faculty_id', '$sub_id', '$course_id', '$school_year', NOW())
";
$assign_result = mysqli_query($conn, $assign_query);

if ($assign_result) {
    // Redirect with all required parameters
    $redirect_url = "dean_reviewer_assign_subjects?faculty_id=$faculty_id&school_year=$school_year&course_id=$course_id";
    echo "<script>window.location.href='" . $redirect_url . "';</script>";    
} else {
    die("Error: Could not assign the subject. " . mysqli_error($conn));
}
?>
