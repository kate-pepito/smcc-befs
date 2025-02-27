<?php

authenticated_page("dean");

$s_id=conn()->sanitize($_REQUEST['s_id']);

$query="update subjects set status = 'Inactive' where id = '$s_id'" or die(mysqli_error(conn()->get_conn()));	  
if (conn()->query($query)) 
{
			echo "<script type='text/javascript'>window.alert('Updated');
</script>";

			header("location: dean_subjects");
}

