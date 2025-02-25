<?php

authenticated_page("dean");

$stud_id=mysqli_real_escape_string(conn(), $_REQUEST['stud_id']);

$query="update students set level = 'PREBOARD2' where id = '$stud_id'" or die(mysqli_error(conn()));	  
if (mysqli_query(conn(), $query)) 
{
			echo "<script type='text/javascript'>window.alert('Updated');
</script>";

			header("location: dean_students_all");
}

