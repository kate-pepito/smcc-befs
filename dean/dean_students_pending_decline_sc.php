<?php


$user_id=mysqli_real_escape_string($conn, $_REQUEST['user_id']);
$stud_id=mysqli_real_escape_string($conn, $_REQUEST['stud_id']);

$query="update students set status = 'Inactive' where id = '$stud_id'" or die(mysqli_error($conn));
if (mysqli_query($conn, $query))
{
			echo "<script type='text/javascript'>window.alert('hello world');
</script>";

			header("location: dean_students_pending");
}

?>

