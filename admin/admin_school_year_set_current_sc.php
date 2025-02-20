<?php session_start();
include('../dbconnect.php');

$user_id = mysqli_real_escape_string($conn, $_REQUEST['user_id']);
$year_code = mysqli_real_escape_string($conn, $_REQUEST['year_code']);

	$query="UPDATE school_year SET status = 'Not Set' where user_id = $user_id" or die(mysqli_error($conn));	  
	if (mysqli_query($conn, $query)) 
	{
		
	} 
	else 
	{
			echo "Error: " . $query . "<br>" . mysqli_error($conn);
	}
  	
?>

<?php
		$query="UPDATE school_year SET status = 'Current Set' where id = $year_code and user_id = $user_id" or die(mysqli_error($conn));	  
		if (mysqli_query($conn, $query)) 
		{
				echo "<script type='text/javascript'>alert('School Year Successfully Set to Current!');
			document.location='admin_school_year.php?user_id=$user_id'</script>";
		} 
		else 
		{
				echo "Error: " . $query . "<br>" . mysqli_error($conn);
		}
?>

