<?php 

$query = mysqli_query($conn, "select * from students where id = '$user_id'") or die(mysqli_error($conn));
if ($row = mysqli_fetch_array($query)) {
    $password_hashed = $row['password'];
}

if (isset($_POST['change_password'])) {

    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $renew_password = $_POST['renew_password'];


    if (password_verify($current_password, $password_hashed)) {
        if ($new_password == $renew_password) {
            $new_password = password_hash($new_password, PASSWORD_DEFAULT);
            $query = "update students set password = '$new_password' where id = '$user_id'" or die(mysqli_error($conn));
            if (mysqli_query($conn, $query)) {
                echo "<script type='text/javascript'>alert('Paswwrod Successfully Changed!');
            document.location='students_profile'</script>";
            } else {
                echo "Error: " . $query . "<br>" . mysqli_error($conn);
            }
        } else {
            echo "<script type='text/javascript'>alert('Password did not match!');
        document.location='students_profile'</script>";
        }
    } else {
        echo "<script type='text/javascript'>alert('Incorrect Current Password!');
		document.location='students_profile'</script>";
    }
}
