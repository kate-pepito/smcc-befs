<?php

authenticated_page("dean");

$stud_id=conn()->sanitize($_REQUEST['stud_id']);

$query="update students set level = 'PREBOARD2' where id = '$stud_id'" or die(mysqli_error(conn()->get_conn()));	  
if (conn()->query($query)) 
{
			echo "<script type='text/javascript'>window.alert('Updated');
</script>";

			header("location: dean_students_all");
}

