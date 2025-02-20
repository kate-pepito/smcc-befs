<?php
	$query="UPDATE users SET logged_in = 'NO' WHERE id = '$user_id'";
	if ($conn->query($query))
	{
		unset($_SESSION["user_id"]);
		unset($_SESSION['account_type']);
		echo "<script type='text/javascript'>alert('You have logged out!');
		document.location='$BASE_URL'</script>";
	} 
	else 
	{
		echo "Error: " . $query . "<br>" . mysqli_error($conn);
	}
?>