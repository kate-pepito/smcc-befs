<?php session_start();
include('../dbconnect.php');
if(isset($_POST['update_subject']))
{
$user_id=$_REQUEST['user_id'];
$s_id=$_REQUEST['s_id'];
$subject_code=$_POST['subject_code'];
$description=$_POST['description'];

$query="update subjects set code = '$subject_code', description = '$description'  where id = '$s_id'" or die(mysqli_error($conn));	  
if (mysqli_query($conn, $query)) 
{
			echo "<script type='text/javascript'>window.alert('hello world');
</script>";

			header("location: dean_subjects.php?user_id=".$user_id."");
} 
}	  	
?>

