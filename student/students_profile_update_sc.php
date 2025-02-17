<?php 
session_start();
include('../dbconnect.php'); // Adjust the path as needed

$stud_id = $_REQUEST['stud_id'];

if (isset($_POST['update_profile'])) {
    $about = mysqli_real_escape_string($conn, $_POST['about']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $image_path = '';

    // Handle profile image upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == UPLOAD_ERR_OK) {
        $image_name = $_FILES['profile_image']['name'];
        $image_tmp_name = $_FILES['profile_image']['tmp_name'];
        $image_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));

        $allowed_extensions = ['jpg', 'jpeg', 'png'];
        if (in_array($image_ext, $allowed_extensions)) {
            $new_image_name = uniqid() . '.' . $image_ext;
            $image_upload_path = 'uploads/' . $new_image_name;

            // Ensure the uploads directory exists and is writable
            if (!is_dir('uploads')) {
                mkdir('uploads', 0777, true);
            }

            if (move_uploaded_file($image_tmp_name, $image_upload_path)) {
                $image_path = $image_upload_path;
            } else {
                echo "<script>alert('Failed to move uploaded file.');</script>";
            }
        } else {
            echo "<script>alert('Invalid image format. Only JPG, JPEG, and PNG allowed.');</script>";
        }
    }

    // Update the database
    $query = "UPDATE students SET 
                  about = '$about', 
                  complete_address = '$address', 
                  profile_image = '$image_path' 
              WHERE id = '$stud_id'";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Profile Successfully Updated!');
        document.location='students_profile.php?stud_id=$stud_id';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
