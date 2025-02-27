<?php

authenticated_page("dean");

if(isset($_POST['update_subject']))
{
	$s_id=conn()->sanitize($_REQUEST['s_id']);
	$subject_code=conn()->sanitize($_POST['subject_code']);
	$description=conn()->sanitize($_POST['description']);

	$query="update subjects set code = '$subject_code', description = '$description'  where id = '$s_id'" or die(mysqli_error(conn()->get_conn()));	  
	if (conn()->query($query)) 
	{
		echo "<script type='text/javascript'>window.alert('Updated');
	</script>";

		header("location: dean_subjects");
	} 
}
