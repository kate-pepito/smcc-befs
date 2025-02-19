<header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between">
        <a href="dean_home_page.php?user_id=<?php echo $user_id; ?>" class="logo d-flex align-items-center">
            <img src="../images/Smcc_logo.gif" alt="">
            <span class="d-none d-lg-block">SMCC - BEFS</span>
        </a>
        <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->

    <div class="search-bar">
    </div><!-- End Search Bar -->

    <nav class="header-nav ms-auto">
        <ul class="d-flex align-items-center">
            <li class="nav-item d-block d-lg-none">
                <a class="nav-link nav-icon search-bar-toggle" href="#">
                    <i class="bi bi-search"></i>
                </a>
            </li><!-- End Search Icon-->
            <li class="nav-item dropdown pe-3">
                <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" id="navbarDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <?php 
                    // Assuming you have a field 'profile_image' in the users table
                    $profile_image = !empty($row['profile_image']) ? $row['profile_image'] : '../assets/img/profile-img2.jpg'; 
                    ?>
                    <img src="<?php echo $profile_image; ?>" alt="Profile" class="rounded-circle">
                    <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo $lname; ?></span>
                </a><!-- End Profile Image Icon -->

                <!-- Dropdown Menu -->
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile" aria-labelledby="navbarDropdown">
                    <li class="dropdown-header">
                        <h6><?php echo $fname." ".$lname; ?></h6>
                        <span><?php echo $type; ?></span>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="dean_profile.php?user_id=<?php echo $user_id; ?>">
                            <i class="bi bi-person"></i>
                            <span>Profile</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="../change_password.php?user_id=<?php echo $user_id; ?>">
                            <i class="bi bi-question-circle"></i>
                            <span>Change Password</span>
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="../log_out_sc.php?user_id=<?php echo $user_id; ?>">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Sign Out</span>
                        </a>
                    </li>
                </ul><!-- End Profile Dropdown Items -->
            </li><!-- End Profile Nav -->
        </ul>
    </nav><!-- End Icons Navigation -->
</header>
