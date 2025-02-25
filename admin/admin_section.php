<?php

authenticated_page("admin");

// Handle Add Section
if (isset($_POST['add_section'])) {
    $description = mysqli_real_escape_string(conn(), $_POST['description']);

    date_default_timezone_set("Asia/Manila");
    $dt = date("Y-m-d H:i:s");

    // Check for duplicate section
    $check_duplicate = mysqli_query(conn(), "SELECT * FROM section WHERE description = '$description'");
    if (mysqli_num_rows($check_duplicate) > 0) {
        echo '<script>alert("Section already exists!");window.location="admin_section";</script>';
    } else {
        // Insert new section
        $query = mysqli_query(conn(), "INSERT INTO section (description, date_entry, status) VALUES ('$description', '$dt', 'Active')") or die(mysqli_error(conn()));

        if ($query) {
            echo '<script>alert("Section added successfully!");window.location="admin_section";</script>';
        } else {
            echo '<script>alert("Failed to add section.");window.location="admin_section";</script>';
        }
    }
}

// Fetch user info
$query = mysqli_query(conn(), "SELECT * FROM users WHERE id = '" . user_id() . "'") or die(mysqli_error(conn()));
if ($row = mysqli_fetch_array($query)) {
    $fname = $row['fname'];
    $lname = $row['lname'];
    $type = $row['type'];
    $fname = ucfirst(strtolower($fname));
    $lname = ucfirst(strtolower($lname));
    $type = ucfirst(strtolower($type));
}

// Fetch sections
$sections = mysqli_query(conn(), "SELECT * FROM section WHERE status = 'Active'") or die(mysqli_error(conn()));


admin_html_head("Section", [
  [ "type" => "style", "href" => "assets/vendor/simple-datatables/style.css" ],
  [ "type" => "style", "href" => "assets/css/style.css" ],
]); // html head

?>

<body>
<?php require_once get_admin_header(); ?>
<?php require_once get_admin_sidebar(); ?>

<main id="main" class="main">
  <div class="pagetitle">
    <div align="right">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSectionModal">Add Section</button>
    </div>
    <h1>List of Sections</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="admin_home">Dashboard</a></li>
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
                    <a href="admin_section_remove?s_id=<?php echo $row['id']; ?>" 
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
<?php require_once get_footer(); ?>

<?php admin_html_body_end([
    ["type" => "script", "src" => "assets/vendor/simple-datatables/simple-datatables.js"],
    ["type" => "script", "src" => "assets/js/main.js"],
]); ?>

</body>
</html>
