<?php

authenticated_page("admin");


$year_code = conn()->sanitize($_REQUEST['year_code']);

$query="UPDATE school_year SET status = 'Not Set' where user_id = '". user_id() . "'" or die(mysqli_error(conn()->get_conn()));	  
if (conn()->query($query)) 
{
	
} 
else 
{
		echo "Error: " . $query . "<br>" . mysqli_error(conn()->get_conn());
}

$query="UPDATE school_year SET status = 'Current Set' where id = $year_code and user_id = '". user_id() . "'" or die(mysqli_error(conn()->get_conn()));	  
if (conn()->query($query)) 
{
		echo "<script type='text/javascript'>alert('School Year Successfully Set to Current!');
	document.location='admin_school_year'</script>";
} 
else 
{
		echo "Error: " . $query . "<br>" . mysqli_error(conn()->get_conn());
}


