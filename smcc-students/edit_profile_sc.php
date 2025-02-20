<?php

authenticated_page("student");


	$query="UPDATE students SET logged_in = 'NO' WHERE id = '$user_id'" or die(mysqli_error($conn));	  
	if (mysqli_query($conn, $query)) 
	{
        echo "<script type='text/javascript'>alert('You have logged out!')";
        header("Location: $BASE_URL/student/students_profile");
	} 
	else 
	{
		echo "Error: " . $query . "<br>" . mysqli_error($conn);
	}
?>