<?php
include('../dbconnect.php');
$user_id = $_REQUEST['user_id'];
$stud_id = $_REQUEST['stud_id'];
$sub_id = $_REQUEST['sub_id'];
$c_id = $_REQUEST['c_id'];

// Delete the assigned subject
$query_delete = "
    DELETE FROM students_subjects
    WHERE students_id = '$stud_id' AND subjects_id = '$sub_id'
";

if (mysqli_query($conn, $query_delete)) {
    // Redirect back to the main page
    header("Location: dean_students_assign_subjects.php?user_id=$user_id&stud_id=$stud_id&c_id=$c_id");
    exit;
} else {
    echo "Error removing subject: " . mysqli_error($conn);
}
?>

