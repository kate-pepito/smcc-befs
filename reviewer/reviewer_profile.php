<?php 

authenticated_page("reviewer");


$query = mysqli_query(conn(), "SELECT * FROM users WHERE id = '" . user_id() . "'") or die(mysqli_error(conn()));
if ($row = mysqli_fetch_array($query)) {
    // Get user details
    $fname = ucfirst(strtolower($row['fname']));
    $lname = ucfirst(strtolower($row['lname']));
    $type = ucfirst(strtolower($row['type']));
    $profile_image = base_url() . "/" . ($row['profile_image'] ?: 'assets/img/default-profile.jpg');
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fname = mysqli_real_escape_string(conn(), $_POST['fname']);
    $lname = mysqli_real_escape_string(conn(), $_POST['lname']);

    // Validate input
    if (empty($fname) || empty($lname)) {
        echo "<script>alert('First Name and Last Name are required.'); history.back();</script>";
        exit;
    }

    // Initialize variables for image upload
    $image_path = $profile_image;

    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $image_name = $_FILES['profile_image']['name'];
        $image_tmp_name = $_FILES['profile_image']['tmp_name'];
        $image_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));

        $allowed_extensions = ['jpg', 'jpeg', 'png'];
        if (in_array($image_ext, $allowed_extensions)) {
            $new_image_name = uniqid() . '.' . $image_ext;
            $image_path = "../uploads/$new_image_name";
            $image_path_url = "uploads/$new_image_name";

            if (!move_uploaded_file($image_tmp_name, $image_path)) {
                echo "<script>alert('Failed to upload the image.'); history.back();</script>";
                exit;
            }
        } else {
            echo "<script>alert('Invalid image format. Only JPG, JPEG, and PNG allowed.'); history.back();</script>";
            exit;
        }
    }

    // Update user profile in the database
    $query_update = "UPDATE users SET fname = '$fname', lname = '$lname', profile_image = '$image_path_url' WHERE id = '" . user_id() . "'";
    if (mysqli_query(conn(), $query_update)) {
        echo "<script>alert('Profile updated successfully!'); window.location='reviewer_profile';</script>";
    } else {
        echo "<script>alert('Failed to update profile.'); history.back();</script>";
    }
}

admin_html_head("Profile", [
    [ "type" => "style", "href" => "assets/css/style.css" ],
]); // html head

?>

<body>
<?php require_once get_reviewer_header(); ?>
<?php require_once get_reviewer_sidebar(); ?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Profile</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="reviewer_home">Dashboard</a></li>
                <li class="breadcrumb-item active">Profile</li>
            </ol>
        </nav>
    </div>

    <section class="section profile">
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-body pt-3">
                        <ul class="nav nav-tabs nav-tabs-bordered">
                            <li class="nav-item">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview">Edit Profile</button>
                            </li>
                        </ul>

                        <div class="tab-content pt-2">
                            <div class="tab-pane fade show active profile-overview" id="profile-overview">
                                <form method="POST" enctype="multipart/form-data" class="row g-3 user needs-validation" novalidate>
                                    <div style="text-align: center; margin-top: 20px;">
                                        <br>
                                        <div>
                                            <img src="<?php echo $profile_image; ?>" alt="Profile Image" style="width: 200px; height: 200px; border-radius: 100%; display: block; margin: 0 auto;">
                                            <input type="file" name="profile_image" accept="image/*" style="display: block; margin: 10px auto;">
                                        </div>
                                    </div>
                                    </br>
                                    <br>
                                    <div class="row mb-3">
                                        <label for="fullName" class="col-md-4 col-lg-3 col-form-label">First Name</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="fname" type="text" class="form-control" id="fullName" value="<?php echo $fname; ?>" required>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="about" class="col-md-4 col-lg-3 col-form-label">Last Name</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="lname" type="text" class="form-control" id="fullName" value="<?php echo $lname; ?>" required>
                                        </div>
                                    </div>

                                    <div class="text-center">
                                        <button name="update_faculty" type="submit" class="btn btn-primary">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require_once get_footer(); ?>

<?php admin_html_body_end([
    ["type" => "script", "src" => "assets/js/main.js"],
]); ?>

</body>
</html>
