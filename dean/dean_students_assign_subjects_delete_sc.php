<?php

authenticated_page("dean");

$stud_id = mysqli_real_escape_string($conn, $_REQUEST['stud_id']);
$sub_id = mysqli_real_escape_string($conn, $_REQUEST['sub_id']);
$c_id = mysqli_real_escape_string($conn, $_REQUEST['c_id']);

// Delete the assigned subject
$query_delete = "
    DELETE FROM students_subjects
    WHERE students_id = '$stud_id' AND subjects_id = '$sub_id'
";

if (mysqli_query($conn, $query_delete)) {
    // Redirect back to the main page
    header("Location: dean_students_assign_subjects&stud_id=$stud_id&c_id=$c_id");
    exit;
} else {
    echo "Error removing subject: " . mysqli_error($conn);
}
?>

