<?php 
session_start();
include('../dbconnect.php');

$user_id=$_REQUEST['user_id'];
$stud_id=$_REQUEST['stud_id'];

$query="update students set status = 'Inactive' where id = '$stud_id'" or die(mysqli_error($conn));	  
if (mysqli_query($conn, $query)) 
{
			echo "<script type='text/javascript'>window.alert('hello world');
</script>";

			header("location: dean_students_active.php?user_id=".$user_id."");
} 
  	
?>