<?php 
session_start();
include('../dbconnect.php');

$user_id = mysqli_real_escape_string($conn, $_REQUEST['user_id']);  // The admin's user ID
$stud_id = mysqli_real_escape_string($conn, $_REQUEST['stud_id']);  // The student ID to enroll

if (isset($_POST['enroll_student'])) {

    // Set timezone and current date/time
    date_default_timezone_set("Asia/Manila");
    $dt = date("Y-m-d") . " " . date("h:i:sa");

    // Only change the student's status to 'Enrolled'
    $query = "UPDATE students 
              SET status = 'Active' 
              WHERE id = '$stud_id'";

    // Execute the query
    if (mysqli_query($conn, $query)) {
        // Successfully enrolled, redirect with alert
        echo "<script type='text/javascript'>
                alert('Student Successfully Approve!');
                document.location='dean_students_pending.php?user_id=$user_id';
              </script>";
    } else {
        // Query failed, display error message
        echo "Error: " . mysqli_error($conn);
    }
}
?>
