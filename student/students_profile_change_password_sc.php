<?php session_start();
include('dbconnect.php');

$stud_id = $_REQUEST['stud_id'];

$query=mysqli_query($conn,"select * from students where id = '$stud_id'")or die(mysqli_error());
if($row=mysqli_fetch_array($query))
{
$password = $row['password'];
}
?>


<?php 

if(isset($_POST['change_password']))
{

$current_password = $_POST['current_password'];
$new_password = $_POST['new_password'];
$renew_password = $_POST['renew_password'];


if($current_password==$password){
    if($new_password==$renew_password){
        $query="update students set password = '$new_password' where id = '$stud_id'" or die(mysqli_error($conn));	  
        if (mysqli_query($conn, $query)) 
        {
                echo "<script type='text/javascript'>alert('Paswwrod Successfully Changed!');
            document.location='students_profile.php?stud_id=$stud_id'</script>";
        } 
        else 
        {
                echo "Error: " . $query . "<br>" . mysqli_error($conn);
        }	
    }
    else{
        echo "<script type='text/javascript'>alert('Password did not match!');
        document.location='students_profile.php?stud_id=$stud_id'</script>";
    }
    
}
else{
    echo "<script type='text/javascript'>alert('Incorrect Current Password!');
		document.location='students_profile.php?stud_id=$stud_id'</script>";
}


			
}	  	
?>

