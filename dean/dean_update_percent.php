<?php

authenticated_page("dean");

$s_id = mysqli_real_escape_string($conn, $_POST['s_id'] ?? $_REQUEST['s_id']);
$percent = mysqli_real_escape_string($conn, $_POST['percent'] ?? null);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $percent !== null) {
    $query = mysqli_query($conn, "UPDATE subject_percent SET percent = '$percent' WHERE sub_id = '$s_id'") or die(mysqli_error($conn));
    if ($query) {
        echo "<script>window.location.href = 'dean_subjects';</script>";
    } else {
        echo "<script>alert('Error updating percent.');</script>";
    }
}
?>
