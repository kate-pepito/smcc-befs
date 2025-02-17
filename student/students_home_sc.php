<?php
include('../dbconnect.php');
$stud_id = $_REQUEST['stud_id'];
header('Location: ../smcc-students/index.php?user_id='.$stud_id);
?>