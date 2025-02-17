<?php
include('dbconnect.php');
$user_id = $_REQUEST['user_id'];

	$query="UPDATE students SET logged_in = 'NO' WHERE id = '$user_id'" or die(mysqli_error($conn));	  
	if (mysqli_query($conn, $query)) 
	{
        echo "<script type='text/javascript'>alert('You have logged out!')";
        header('Location: http://localhost/smcc-befs/student/students_profile.php?stud_id='.$user_id);
	} 
	else 
	{
			echo "Error: " . $query . "<br>" . mysqli_error($conn);
	}	
?>