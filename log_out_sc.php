<?php
require_once './dbconnect.php';
$user_id = mysqli_real_escape_string($conn, $_REQUEST['user_id']);

	$query="UPDATE users SET logged_in = 'NO' WHERE id = '$user_id'" or die(mysqli_error($conn));	  
	if (mysqli_query($conn, $query)) 
	{
			echo "<script type='text/javascript'>alert('You have logged out!');
		document.location='index.php'</script>";
	} 
	else 
	{
			echo "Error: " . $query . "<br>" . mysqli_error($conn);
	}	
?>