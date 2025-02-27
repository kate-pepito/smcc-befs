<?php

authenticated_page("dean");

$stud_id = conn()->sanitize($_REQUEST['stud_id']);
$sub_id = conn()->sanitize($_REQUEST['sub_id']);
$c_id = conn()->sanitize($_REQUEST['c_id']);

// Delete the assigned subject
$query_delete = "
    DELETE FROM students_subjects
    WHERE students_id = '$stud_id' AND subjects_id = '$sub_id'
";

if (conn()->query($query_delete)) {
    // Redirect back to the main page
    header("Location: dean_students_assign_subjects?stud_id=$stud_id&c_id=$c_id");
    exit;
} else {
    echo "Error removing subject: " . mysqli_error(conn()->get_conn());
}

