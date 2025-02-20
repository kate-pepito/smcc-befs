<?php
include('../dbconnect.php');

// Validate and fetch parameters from the URL
$user_id = mysqli_real_escape_string($conn, isset($_GET['user_id']) ? $_GET['user_id'] : null); // Dean ID
$faculty_id = mysqli_real_escape_string($conn, isset($_GET['faculty_id']) ? $_GET['faculty_id'] : null); // Faculty ID
$school_year = mysqli_real_escape_string($conn, isset($_GET['school_year']) ? $_GET['school_year'] : null); // School Year
$course_id = mysqli_real_escape_string($conn, isset($_GET['course_id']) ? $_GET['course_id'] : null); // Course ID
$sub_id = mysqli_real_escape_string($conn, isset($_GET['sub_id']) ? $_GET['sub_id'] : null); // Subject ID to remove

// Ensure all required parameters are present
if (!$user_id || !$faculty_id || !$school_year || !$course_id || !$sub_id) {
    die("Error: Missing required parameters. Please log in again.");
}

// Delete the assigned subject from the faculty_subjects table
$query = "DELETE FROM faculty_subjects WHERE faculty_id = '$faculty_id' AND subjects_id = '$sub_id'";
if (mysqli_query($conn, $query)) {
    // Successfully removed, redirect back to the Assign Subjects page
    header("Location: dean_reviewer_assign_subjects.php?user_id=$user_id&faculty_id=$faculty_id&school_year=$school_year&course_id=$course_id");
    exit();
} else {
    die("Error: Could not remove the subject. Please try again.");
}
?>
