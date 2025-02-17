<?php
session_start();
include('../dbconnect.php');

$user_id = $_REQUEST['user_id'];
$c_id = $_REQUEST['c_id'];

// Check if parameters are passed correctly
if (!$user_id || !$c_id) {
    echo "Error: Missing user_id or c_id.";
    exit();
}

// Update query to mark course as inactive
$query = "UPDATE course SET status = 'Inactive' WHERE id = '$c_id'";

// Debugging: Check the SQL query
echo "Query: $query<br>";  // Remove this after debugging
$result = mysqli_query($conn, $query);

if ($result) {
    // If the update is successful, redirect back to the admin_course.php page
    header("Location: admin_course.php?user_id=$user_id");
    exit();
} else {
    // If there's an error, display a message
    echo "Error updating course: " . mysqli_error($conn);
    exit();
}
?>
