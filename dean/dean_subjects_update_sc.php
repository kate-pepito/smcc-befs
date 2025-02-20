<?php

authenticated_page("dean");

if(isset($_POST['update_subject']))
{
$user_id=mysqli_real_escape_string($conn, $_REQUEST['user_id']);
$s_id=mysqli_real_escape_string($conn, $_REQUEST['s_id']);
$subject_code=mysqli_real_escape_string($conn, $_POST['subject_code']);
$description=mysqli_real_escape_string($conn, $_POST['description']);

$query="update subjects set code = '$subject_code', description = '$description'  where id = '$s_id'" or die(mysqli_error($conn));	  
if (mysqli_query($conn, $query)) 
{
			echo "<script type='text/javascript'>window.alert('hello world');
</script>";

			header("location: dean_subjects");
} 
}	  	
?>

