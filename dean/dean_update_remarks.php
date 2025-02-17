<?php
include('../dbconnect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the necessary POST variables are set
    $sid = isset($_POST['sid']) ? mysqli_real_escape_string($conn, $_POST['sid']) : null;
    $sub_id = isset($_POST['sub_id']) ? mysqli_real_escape_string($conn, $_POST['sub_id']) : null;
    $user_id = isset($_POST['user_id']) ? mysqli_real_escape_string($conn, $_POST['user_id']) : null;
    $remarks2 = isset($_POST['remarks']) ? mysqli_real_escape_string($conn, $_POST['remarks']) : null;
    $level = isset($_POST['level']) ? mysqli_real_escape_string($conn, $_POST['level']) : null;

    // Determine which tab was active
    $active_tab = isset($_POST['tab']) ? $_POST['tab'] : '';

    // Make sure all required fields are set
    if ($sid && $sub_id && $remarks2 !== null && $level) {
        // Update remarks2 in the database
        $query = "
            UPDATE student_score 
            SET remarks2 = '$remarks2' 
            WHERE stud_id = (SELECT id FROM students WHERE lrn_num = '$sid') 
            AND sub_id = '$sub_id' AND level = '$level'
        ";

        if (mysqli_query($conn, $query)) {
            // Redirect with success message and active tab
            header("Location: dean_students.php?user_id=$user_id&sub_id=$sub_id&status=success&tab=$active_tab");
            exit;
        } else {
            // If there's an error in the query
            echo "Error: " . mysqli_error($conn);
            header("Location: dean_students.php?user_id=$user_id&sub_id=$sub_id&status=error&tab=$active_tab");
            exit;
        }
    } else {
        // Missing data handling
        header("Location: dean_students.php?user_id=$user_id&sub_id=$sub_id&status=missing&tab=$active_tab");
        exit;
    }
}
?>
