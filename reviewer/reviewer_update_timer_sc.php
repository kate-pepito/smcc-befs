<?php session_start();
include('../dbconnect.php');
if(isset($_POST['set_timer']))
{
$user_id=mysqli_real_escape_string($conn, $_REQUEST['user_id']);
$s_id=mysqli_real_escape_string($conn, $_REQUEST['s_id']);
$timer=mysqli_real_escape_string($conn, $_POST['timer']);

$query="update subjects_timer set timer = '$timer' where subjects_id = '$s_id'" or die(mysqli_error($conn));	  
if (mysqli_query($conn, $query)) 
{
			echo "<script type='text/javascript'>window.alert('hello world');
</script>";

			header("location: reviewer_subjects.php?user_id=".$user_id."");
} 
}	  	
?>
