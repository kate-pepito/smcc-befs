<?php

authenticated_page("admin");

$c_id = mysqli_real_escape_string(conn(), $_REQUEST['c_id']);

// Check if parameters are passed correctly
if (!user_id() || !$c_id) {
    echo "Error: Missing authentication or course id.";
    exit();
}

// Update query to mark course as inactive
$query = "UPDATE course SET status = 'Inactive' WHERE id = '$c_id'";

// Debugging: Check the SQL query
echo "Query: $query<br>";  // Remove this after debugging
$result = mysqli_query(conn(), $query);

if ($result) {
    // If the update is successful, redirect back to the admin_course page
    header("Location: admin_course");
    exit();
} else {
    // If there's an error, display a message
    echo "Error updating course: " . mysqli_error(conn());
    exit();
}
?>
