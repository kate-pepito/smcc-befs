<?php 
session_start();
include('../dbconnect.php');

$user_id = $_REQUEST['user_id'];
$f_id = $_REQUEST['f_id'];

if (isset($_REQUEST['confirmed']) && $_REQUEST['confirmed'] == '1') {
    // If the confirmation is received, process the query
    $query = "UPDATE users SET status = 'Inactive' WHERE id = '$f_id'";
    if (mysqli_query($conn, $query)) {
        echo "<script type='text/javascript'>
                alert('Dean successfully removed!');
                window.location.href = 'admin_dean.php?user_id=$user_id';
              </script>";
    } else {
        echo "<script type='text/javascript'>
                alert('Error removing dean. Please try again.');
                window.location.href = 'admin_dean.php?user_id=$user_id';
              </script>";
    }
} else {
    // If not confirmed yet, show confirmation prompt
    echo "<script type='text/javascript'>
            if (confirm('Are you sure you want to remove this dean?')) {
                window.location.href = 'admin_dean_remove.php?user_id=$user_id&f_id=$f_id&confirmed=1';
            } else {
                alert('Action canceled.');
                window.location.href = 'admin_dean.php?user_id=$user_id';
            }
          </script>";
}
?>


