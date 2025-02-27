<?php
	$query="UPDATE students SET logged_in = 'NO' WHERE id = '" . user_id() . "'";
	if (conn()->query($query))
	{
		unset($_SESSION["user_id"]);
		unset($_SESSION['account_type']);
		echo "<script type='text/javascript'>alert('You have logged out!');
		document.location='" . base_url() . "'</script>";
	} 
	else 
	{
		echo "Error: " . $query . "<br>" . mysqli_error(conn()->get_conn());
	}
?>