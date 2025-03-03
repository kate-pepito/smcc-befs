<?php 

authenticated_page("admin");

if ($_SERVER['REQUEST_METHOD'] === "POST"): // POST METHOD
  $sy_id = $_POST['sy_id'] ?? null;
  $model_id = $_POST['model_id'] ?? null;
  $model_name = $_POST['model_name'] ?? null;
  $action = $_POST['action'] ?? null;
  if ($model_id === null || $action === null) {
    http_response_code(400);
    die("Invalid Request");
  }
  switch ($action) {
    case "delete":
      $q = "DELETE FROM `inference_model` WHERE id = ?";
      $stmt = conn()->prepare($q);
      $stmt->bind_param("s", $model_id);
      $r = $stmt->execute();
      $stmt->close();
      if ($r === false) {
        http_response_code(500);
        die("Failed to delete model.");
      }
      die("Deleted Successfully");
    case "rename":
      if (!$model_name) {
        http_response_code(400);
        die("Model name is required");
      }
      $q = "UPDATE `inference_model` SET name = ? WHERE id = ?";
      $stmt = conn()->prepare($q);
      $stmt->bind_param("ss", $model_name, $model_id);
      $r = $stmt->execute();
      $stmt->close();
      if ($r === false) {
        http_response_code(500);
        die("Failed to rename model.");
      }
      die("Renamed Successfully");
    case "choose":
      if (!$sy_id) {
        http_response_code(400);
        die("School Year is required");
      }
      $q = "SELECT school_year_id FROM `selected_model` WHERE school_year_id = ?";
      $stmt = conn()->prepare($q);
      $stmt->bind_param("s", $sy_id);
      $stmt->execute();
      $rs = $stmt->get_result();
      if ($row = $rs->fetch_assoc()) {
        // update selected model
        $qs = "UPDATE `selected_model` SET inference_model_id = ? WHERE school_year_id = ?";
        $stmt_u = conn()->prepare($qs);
        $stmt_u->bind_param("ss", $model_id, $sy_id);
        $r = $stmt_u->execute();
      } else {
        // insert selected model
        $qs = "INSERT INTO `selected_model` (school_year_id, inference_model_id) VALUES(?, ?)";
        $stmt_u = conn()->prepare($qs);
        $stmt_u->bind_param("ss", $sy_id, $model_id);
        $r = $stmt_u->execute();
      }
      $stmt->close();
      if ($r === false) {
        http_response_code(500);
        die("Failed to set model to school year");
      }
      die("Set Model to School Year Successfully.");
    default:
      http_response_code(400);
      die("Invalid Request");
  }

else: // GET METHOD
$query=conn()->query("select * from users where id = '" . user_id() . "'")or die(mysqli_error(conn()->get_conn()));
if($row=mysqli_fetch_array($query))
{
  $fname=$row['fname'];
  $lname=$row['lname'];
  $type=$row['type'];
  $fname = ucfirst(strtolower($fname));
  $lname = ucfirst(strtolower($lname));
  $type = ucfirst(strtolower($type));
}
$models = [];
$selected_models = [];
$school_years = [];

$q_models=conn()->query("SELECT id,name,size,filename,file_extension,fullpath,accuracy,created_at from `inference_model`");
while ($q_row=mysqli_fetch_assoc($q_models))
{
  $models[] = $q_row;
}
mysqli_free_result($q_models);

$q_smodels=conn()->query("SELECT id,school_year_id,inference_model_id from `selected_model`");
while ($q_srow=mysqli_fetch_assoc($q_smodels))
{
  $selected_models[strval($q_srow["school_year_id"])] = $q_srow;
}
mysqli_free_result($q_smodels);

$q_y=conn()->query("SELECT id,description from `school_year`");
while ($q_r=mysqli_fetch_assoc($q_y))
{
  $school_years[strval($q_r["id"])] = $q_r["description"];
}
mysqli_free_result($q_y);

admin_html_head("Trained Models", [
  [ "type" => "style", "href" => "https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.15.10/sweetalert2.min.css" ],
  [ "type" => "style", "href" => "assets/vendor/remixicon/remixicon.css" ],
  [ "type" => "style", "href" => "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.1/css/all.min.css", "integrity" => "sha256-2XFplPlrFClt0bIdPgpz8H7ojnk10H69xRqd9+uTShA=", "crossorigin" => "anonymous" ],
  [ "type" => "style", "href" => "https://cdnjs.cloudflare.com/ajax/libs/ionicons/4.5.6/css/ionicons.min.css", "integrity" => "sha512-0/rEDduZGrqo4riUlwqyuHDQzp2D1ZCgH/gFIfjMIL5az8so6ZiXyhf1Rg8i6xsjv+z/Ubc4tt1thLigEcu6Ug==", "crossorigin" => "anonymous", "referrerpolicy" => "no-referrer" ],
  [ "type" => "style", "href" => "assets/css/style.css" ],
]); // html head

?>
<body>
  <!-- ======= Header ======= -->
  <?php require_once get_admin_header(); ?>
  <!-- End Header -->
  <!-- ======= Sidebar ======= -->
  <?php
  $query=conn()->query("select * from school_year where status = 'Current Set' and user_id = '". user_id() . "'")or die(mysqli_error(conn()->get_conn()));
  if($row=mysqli_fetch_array($query))
  {
    require_once get_admin_sidebar();
  }
  ?>
  <!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Trained Models</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item">Forecast Training</li>
          <li class="breadcrumb-item active">Manage Trained Models</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    
  <div class="row">
    <!-- Column for Reviewers Count -->
    <div class="col-md">
      <div class="card info-card">
        <div class="card-body">
          <h5 class="card-title">Manage Trained Model</h5>
          <div
            class="alert alert-info pt-1 pb-1"
            role="alert"
          >
          <h5 class="mt-2">Selected Models:</h5>
          <?php foreach ($school_years as $syid => $desc):
            $is_currently_selected = in_array(strval($syid), array_keys($selected_models)) && strval($selected_models[strval($syid)]["inference_model_id"]) === strval($model["id"]);
            if (in_array(strval($syid), array_keys($selected_models))) {
              $mid = $selected_models[strval($syid)]["inference_model_id"];
              $md = array_filter($models, fn($m) => strval($m["id"]) === strval($mid));
              $md = end($md);
              if ($md) {
                $mname = $md["name"];
                $mdate = (new DateTime($md["created_at"]))->format("Y-m-d H:i:s");
              }
            }
          ?>
            <p><?= $desc ?> <i class="bi bi-arrow-right-short"></i> <?= ($mname ?? false) ? "<span class=\"fw-bold\">&ldquo;".$mname."&rdquo;</span> <span class=\"fst-italic\">(".$mdate.")</span>": "<span class=\"fst-italic\">[Not Selected Yet]</span>"?></p>
          <?php
            $mname = "";
            $mdate = "";
            endforeach;
          ?>
          </div>
          <div class="container flex-grow-1 light-style container-p-y">
            <div class="file-manager-container file-manager-col-view">
              <?php foreach($models as $model): ?>
                <?php
                $selected_to_sy_count = 0;
                foreach ($selected_models as $syid => $sm) {
                  if (strval($sm["inference_model_id"]) === strval($model["id"])) {
                    $selected_to_sy_count++;
                  }
                }
                ?>
                <div class="file-item" id="model-<?= $model["id"] ?>">
                  <div class="file-item-select-bg bg-primary"></div>
                  <?php if (array_key_exists(strval($model["id"]), $selected_models)): ?>
                    <label class="file-item-selected">
                      <span><i class="bi bi-patch-check"></i> (<?= $selected_to_sy_count ?>)</span>
                    </label>
                  <?php endif; ?>
                  <div class="file-item-icon far fa-file-alt text-secondary"></div>
                  <a href="javascript:void(0)" class="file-item-name">
                      <?= $model["name"] . $model["type"] ?>
                  </a>
                  <div class="file-item-changed"><?= $model["created_at"] ?></div>
                  <div class="file-item-actions btn-group">
                      <button type="button" class="btn btn-default btn-sm rounded-pill icon-btn borderless md-btn-flat hide-arrow dropdown-toggle" data-bs-toggle="dropdown"><i class="ion ion-ios-more"></i></button>
                      <ul class="dropdown-menu dropdown-menu-right">
                        <div class="btn-group befs-dropend">
                          <button class="dropdown-item befs-dropdown-dropend" data-befs-toggle="dropdown">Select Model <i class="bi bi-chevron-compact-right"></i></button>
                          <ul class="dropdown-menu">
                            <?php foreach ($school_years as $syid => $desc):
                              $is_currently_selected = in_array(strval($syid), array_keys($selected_models)) && strval($selected_models[strval($syid)]["inference_model_id"]) === strval($model["id"]);
                              if (in_array(strval($syid), array_keys($selected_models))) {
                                $mid = $selected_models[strval($syid)]["inference_model_id"];
                                $md = array_filter($models, fn($m) => strval($m["id"]) === strval($mid));
                                $md = end($md);
                                if ($md) {
                                  $mname = "(".$md["name"].")";
                                }
                              }
                            ?>
                                <li>
                                  <button
                                    class="dropdown-item<?= $is_currently_selected ? " active text-dark" : "" ?>"
                                    data-befs-sy-id="<?= $syid ?>"
                                    data-befs-model-id="<?= $model["id"] ?>"
                                    <?= $is_currently_selected ? "disabled" : "" ?>
                                  >
                                    <?= $desc ?> <?= $mname ??  "" ?>
                                  </button>
                                </li>
                          <?php
                            $mname = "";
                            endforeach;
                          ?>
                          </ul>
                        </div>
                        <li><button class="dropdown-item" data-befs-dropdown-action="rename" data-befs-model-id="<?= $model["id"] ?>" data-befs-model-name="<?= $model["name"] ?>"><i class="bi bi-trash3"></i> Rename</button></li>
                        <li><button class="dropdown-item" data-befs-dropdown-action="delete" data-befs-model-id="<?= $model["id"] ?>" data-befs-model-name="<?= $model["name"] ?>"><i class="bi bi-trash3"></i> Delete</button></li>
                      </ul>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>




  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <?php
    require_once get_footer();
  ?>
  <!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <?php admin_html_body_end([
      ["type" => "script", "src" => "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"],
      ["type" => "script", "src" => "https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.15.10/sweetalert2.min.js"],
      ["type" => "script", "src" => "assets/js/main.js"],
      ["type" => "script", "src" => "assets/js/models.js"],
  ]); ?>

</body>

</html>

<?php endif; ?>
