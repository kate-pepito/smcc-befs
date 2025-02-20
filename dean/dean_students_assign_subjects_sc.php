<?php 
include('../dbconnect.php');
$user_id = mysqli_real_escape_string($conn, $_REQUEST['user_id']);
$stud_id = mysqli_real_escape_string($conn, $_REQUEST['stud_id']);
$sub_id = mysqli_real_escape_string($conn, $_REQUEST['sub_id']);
$c_id = mysqli_real_escape_string($conn, $_REQUEST['c_id']);

date_default_timezone_set("Asia/Manila");
$dt = date("Y-m-d") . " " . date("h:i:sa");

$query = "INSERT INTO students_subjects (students_id, subjects_id, status, level) VALUES ('$stud_id', '$sub_id', 'NOT TAKEN', 'PREBOARD1')";
if (mysqli_query($conn, $query)) {
    $query = "INSERT INTO students_subjects (students_id, subjects_id, status, level) VALUES ('$stud_id', '$sub_id', 'NOT TAKEN', 'PREBOARD2')";
    if (mysqli_query($conn, $query)) {
        // Instead of an alert, just redirect or echo success message
        header("Location: dean_students_assign_subjects.php?user_id=$user_id&stud_id=$stud_id&c_id=$c_id");
        exit;
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($conn);
    }
} else {
    echo "Error: " . $query . "<br>" . mysqli_error($conn);
}
?>
