<?php
include('../dbconnect.php');
session_start();
$user_id = mysqli_real_escape_string($conn, $_REQUEST['user_id']);

// Handle Add Section
if (isset($_POST['add_section'])) {
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    date_default_timezone_set("Asia/Manila");
    $dt = date("Y-m-d H:i:s");

    // Check for duplicate section
    $check_duplicate = mysqli_query($conn, "SELECT * FROM section WHERE description = '$description'");
    if (mysqli_num_rows($check_duplicate) > 0) {
        echo '<script>alert("Section already exists!");window.location="admin_section.php?user_id=' . $user_id . '";</script>';
    } else {
        // Insert new section
        $query = mysqli_query($conn, "INSERT INTO section (description, date_entry, status) VALUES ('$description', '$dt', 'Active')") or die(mysqli_error($conn));

        if ($query) {
            echo '<script>alert("Section added successfully!");window.location="admin_section.php?user_id=' . $user_id . '";</script>';
        } else {
            echo '<script>alert("Failed to add section.");window.location="admin_section.php?user_id=' . $user_id . '";</script>';
        }
    }
}

// Fetch user info
$query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'") or die(mysqli_error($conn));
if ($row = mysqli_fetch_array($query)) {
    $fname = $row['fname'];
    $lname = $row['lname'];
    $type = $row['type'];
    $fname = ucfirst(strtolower($fname));
    $lname = ucfirst(strtolower($lname));
    $type = ucfirst(strtolower($type));
}

// Fetch sections
$sections = mysqli_query($conn, "SELECT * FROM section WHERE status = 'Active'") or die(mysqli_error($conn));
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Section - SMCC</title>
  <link href="../images/Smcc_logo.gif" rel="icon">
  <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="../assets/css/style.css" rel="stylesheet">

   <!-- Google Fonts -->
   <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

</head>

<body>
<?php include('../header.php'); ?>
<?php include('../sidebar.php'); ?>

<main id="main" class="main">
  <div class="pagetitle">
    <div align="right">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSectionModal">Add Section</button>
    </div>
    <h1>List of Sections</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="admin_home.php?user_id=<?php echo $user_id; ?>">Dashboard</a></li>
        <li class="breadcrumb-item">Section</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <section class="section">
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Lists</h5>
            <table class="table datatable">
              <thead>
                <tr>
                  <th>ID No.</th>
                  <th>Description</th>
                  <th>Date Entry</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($row = mysqli_fetch_array($sections)) { ?>
                <tr>
                  <td><?php echo $row['id']; ?></td>
                  <td><?php echo $row['description']; ?></td>
                  <td><?php echo $row['date_entry']; ?></td>
                  <td>
                    <a href="admin_section_remove.php?user_id=<?php echo $user_id; ?>&s_id=<?php echo $row['id']; ?>" 
                       class="btn btn-danger btn-sm" 
                       onclick="return confirm('Are you sure you want to remove this section?');">
                       Remove
                    </a>
                  </td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<!-- Add Section Modal -->
<div class="modal fade" id="addSectionModal" tabindex="-1" aria-labelledby="addSectionModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
      <h5 class="modal-title" id="addSectionModalLabel" style="font-weight: bold; color: #2b4aa1;">Section</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST">
        <div class="modal-body">
          <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <input name="description" type="text" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="add_section" class="btn btn-primary">Save Section</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Footer -->
<?php include('../footer.php'); ?>
<script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/main.js"></script>
</body>
</html>
