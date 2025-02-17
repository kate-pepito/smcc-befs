<?php
include('../dbconnect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the necessary POST variables are set
    $sid = isset($_POST['sid']) ? mysqli_real_escape_string($conn, $_POST['sid']) : null;
    $sub_id = isset($_POST['sub_id']) ? mysqli_real_escape_string($conn, $_POST['sub_id']) : null;
    $user_id = isset($_POST['user_id']) ? mysqli_real_escape_string($conn, $_POST['user_id']) : null;
    $remarks = isset($_POST['remarks']) ? mysqli_real_escape_string($conn, $_POST['remarks']) : null;
    $level = isset($_POST['level']) ? mysqli_real_escape_string($conn, $_POST['level']) : null;
    $active_tab = isset($_POST['active-tab']) ? $_POST['active-tab'] : 'Preboard1'; // Default to Preboard1 if not set

    // Make sure all required fields are set
    if ($sid && $sub_id && $remarks !== null && $level) {
        // Update remarks in the remarks column depending on the level
        $query = "
            UPDATE student_score 
            SET remarks = '$remarks' 
            WHERE stud_id = (SELECT id FROM students WHERE lrn_num = '$sid') 
            AND sub_id = '$sub_id' AND level = '$level'
        ";

        if (mysqli_query($conn, $query)) {
            // Redirect with success message, preserving the active tab
            header("Location: reviewer_students_view.php?user_id=$user_id&sub_id=$sub_id&status=success&active_tab=$active_tab");
            exit;
        } else {
            // If there's an error in the query
            echo "Error: " . mysqli_error($conn);
            header("Location: reviewer_students_view.php?user_id=$user_id&sub_id=$sub_id&status=error&active_tab=$active_tab");
            exit;
        }
    } else {
        // Missing data handling
        header("Location: reviewer_students_view.php?user_id=$user_id&sub_id=$sub_id&status=missing&active_tab=$active_tab");
        exit;
    }
}
?>
