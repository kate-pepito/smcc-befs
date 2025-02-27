<?php

authenticated_page("student");


	$query="UPDATE students SET logged_in = 'NO' WHERE id = '" . user_id() . "'" or die(mysqli_error(conn()->get_conn()));	  
	if (conn()->query($query)) 
	{
        echo "<script type='text/javascript'>alert('You have logged out!')";
        header("Location: " . base_url() . "/student/students_profile");
	} 
	else 
	{
		echo "Error: " . $query . "<br>" . mysqli_error(conn()->get_conn());
	}
?>