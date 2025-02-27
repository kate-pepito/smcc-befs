<?php

authenticated_page("dean");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the necessary POST variables are set
    $sid = isset($_POST['sid']) ? conn()->sanitize($_POST['sid']) : null;
    $sub_id = isset($_POST['sub_id']) ? conn()->sanitize($_POST['sub_id']) : null;
    $remarks2 = isset($_POST['remarks']) ? conn()->sanitize($_POST['remarks']) : null;
    $level = isset($_POST['level']) ? conn()->sanitize($_POST['level']) : null;

    // Determine which tab was active
    $active_tab = $_POST['tab'] ?? '';

    // Make sure all required fields are set
    if ($sid && $sub_id && $remarks2 !== null && $level) {
        // Update remarks2 in the database
        $query = "
            UPDATE student_score 
            SET remarks2 = '$remarks2' 
            WHERE stud_id = (SELECT id FROM students WHERE lrn_num = '$sid') 
            AND sub_id = '$sub_id' AND level = '$level'
        ";

        if (conn()->query($query)) {
            // Redirect with success message and active tab
            header("Location: dean_students?sub_id=$sub_id&status=success&tab=$active_tab");
            exit;
        } else {
            // If there's an error in the query
            echo "Error: " . mysqli_error(conn()->get_conn());
            header("Location: dean_students?sub_id=$sub_id&status=error&tab=$active_tab");
            exit;
        }
    } else {
        // Missing data handling
        header("Location: dean_students?sub_id=$sub_id&status=missing&tab=$active_tab");
        exit;
    }
}
