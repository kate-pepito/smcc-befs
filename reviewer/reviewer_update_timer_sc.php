<?php

authenticated_page("reviewer");

if(isset($_POST['set_timer']))
{
	$s_id=conn()->sanitize($_REQUEST['s_id']);
	$timer=conn()->sanitize($_POST['timer']);

	$query="update subjects_timer set timer = '$timer' where subjects_id = '$s_id'" or die(mysqli_error(conn()->get_conn()));	  
	if (conn()->query($query)) 
	{
		echo "<script type='text/javascript'>window.alert('Updated');
	</script>";

		header("location: reviewer_subjects");
	} 
}
