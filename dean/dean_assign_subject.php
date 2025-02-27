<?php

authenticated_page("dean");

// Validate required parameters
$course_id = $_GET['course_id'] ?? null;
$sub_id = $_GET['sub_id'] ?? null;
$faculty_id = $_GET['faculty_id'] ?? null;
$school_year = $_GET['school_year'] ?? null;

// Ensure all required parameters are provided
if (!user_id() || !$course_id || !$sub_id || !$faculty_id || !$school_year) {
    die("Error: Missing required parameters. Please log in again.");
}

// Escape input to prevent SQL injection
$course_id = conn()->sanitize($course_id);
$sub_id = conn()->sanitize($sub_id);
$faculty_id = conn()->sanitize($faculty_id);
$school_year = conn()->sanitize($school_year);

// Check if the subject is already assigned to the faculty
$check_query = "
    SELECT * 
    FROM faculty_subjects 
    WHERE faculty_id = '$faculty_id' AND subjects_id = '$sub_id'
";
$check_result = conn()->query($check_query);

if (!$check_result) {
    die("Error: Failed to check subject assignment. " . mysqli_error(conn()->get_conn()));
}

if (mysqli_num_rows($check_result) > 0) {
    die("Error: Subject is already assigned to this faculty.");
}

// Assign the subject
$assign_query = "
    INSERT INTO faculty_subjects (faculty_id, subjects_id, course_id, school_year_id, assigned_date)
    VALUES ('$faculty_id', '$sub_id', '$course_id', '$school_year', NOW())
";
$assign_result = conn()->query($assign_query);

if ($assign_result) {
    // Redirect with all required parameters
    $redirect_url = "dean_reviewer_assign_subjects?faculty_id=$faculty_id&school_year=$school_year&course_id=$course_id";
    echo "<script>window.location.href='" . $redirect_url . "';</script>";    
} else {
    die("Error: Could not assign the subject. " . mysqli_error(conn()->get_conn()));
}
