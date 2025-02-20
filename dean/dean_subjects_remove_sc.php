<?php

authenticated_page("dean");

$user_id=mysqli_real_escape_string($conn, $_REQUEST['user_id']);
$s_id=mysqli_real_escape_string($conn, $_REQUEST['s_id']);

$query="update subjects set status = 'Inactive' where id = '$s_id'" or die(mysqli_error($conn));	  
if (mysqli_query($conn, $query)) 
{
			echo "<script type='text/javascript'>window.alert('hello world');
</script>";

			header("location: dean_subjects");
} 
  	
?>

