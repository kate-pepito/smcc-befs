<?php 

authenticated_page("admin");


$query=conn()->query("select * from users where id = '" . user_id() . "'")or die(mysqli_error(conn()->get_conn()));
if($row=mysqli_fetch_array($query))
{
  $fname=$row['fname'];
  $lname=$row['lname'];
  $username=$row['username'];
  $type=$row['type'];
  $fname = ucfirst(strtolower($fname));
  $lname = ucfirst(strtolower($lname));
  $type = ucfirst(strtolower($type));
}

if ($_SERVER['REQUEST_METHOD'] === "POST"):
    if(($_POST["username"] ?? false) && ($_POST["session_key"] ?? false) && ($_POST["algo"] ?? false)):
        if (($_POST["action"] ?? "") === "create"):
            $_SESSION['train_username'] = $_POST["username"];
            $_SESSION['train_session_key'] = $_POST["session_key"];
            $_SESSION['train_algo'] = $_POST["algo"];
            default_html_head("Creating Train Session..");
            ?>
            <body>
            <script>
                function sendPostCreateOrContinue(username, session_key, algo, url) {
                    fetch(url, {
                        method: "POST",
                        body: JSON.stringify({ username, session_key, algo }),
                        headers: {
                            "Content-Type": "application/json",
                        },
                        credentials: "include",
                    })
                        .then(response => response.json())
                        .then(({ session_token }) => {
                            window.location.href = `<?= base_url() ?>/admin/admin_train?train_token=${session_token}`;
                        })
                        .catch((error) => {
                            window.location.href = `<?= base_url() ?>/admin/admin_train`;
                            alert('Failed to create train session. ' + error.message);
                        })
                }
                sendPostCreateOrContinue(
                    `<?= $_SESSION['train_username'] ?>`,
                    `<?= $_SESSION['train_session_key'] ?>`,
                    `<?=$_SESSION['train_algo'] ?>`,
                    `<?= base_api_uri() ?>/api/v1/train/create?api_key=<?= api_key() ?>`,
                );
            </script>
            </body>
            </html>
        <?php elseif (($_POST['action'] ?? "") === "destroy"):
            $tu = $_SESSION['train_username'];
            $tsk =$_SESSION['train_session_key'];
            $ta = $_SESSION['train_algo'];
            unset($_SESSION['train_username']);
            unset($_SESSION['train_session_key']);
            unset($_SESSION['train_algo']);
            unset($_SESSION['train_token']);
            default_html_head("Creating Train Session..");
        ?>
            <body>
            <script>
                function sendPostDestroy(username, session_key, algo, url) {
                    fetch(url, {
                        method: "POST",
                        body: JSON.stringify({ username, session_key, algo }),
                        headers: {
                            "Content-Type": "application/json"
                        },
                        credentials: "include",
                    })
                        .then(response => response.json())
                        .then(({ success, detail }) => {
                            window.location.href = `<?= base_url() ?>/admin/admin_train`;
                            alert(detail);
                        })
                        .catch((error) => {
                            window.history.back();
                            alert('Failed to delete train session. ' + error.message);
                        });
                }
                sendPostDestroy(
                    `<?= $_SESSION['train_username'] ?>`,
                    `<?= $_SESSION['train_session_key'] ?>`,
                    `<?=$_SESSION['train_algo'] ?>`,
                    `<?= base_api_uri() ?>/api/v1/train/destroy?api_key=<?= api_key() ?>`,
                )
            </script>
            </body>
            </html>
        <?php else: ?>
            <script>
                alert('Invalid Access');
            </script>
        <?php endif;
    endif;
elseif (!isset($_SESSION['TRAIN_API_KEY'])):
    $_SESSION['TRAIN_API_KEY'] = api_key();
?>
<script>
    window.sessionStorage.setItem("TRAIN_API_KEY", `<?= api_key() ?>`);
    window.location.reload();
</script>
<?php
else:
unset($_SESSION['TRAIN_API_KEY']);
admin_html_head("Forecast Training", [
  [ "type" => "style", "href" => "assets/vendor/remixicon/remixicon.css" ],
  [ "type" => "style", "href" => "assets/css/chosen.css" ],
  [ "type" => "style", "href" => "assets/css/style.css" ],
  [ "type" => "custom", "content" => function () use ($username) {
    ?>
    <script>
        window.MY_USERNAME = `<?= $username ?>`;
        window.BASE_API_URL = `<?= base_api_uri() ?>`;
    </script>
    <?php
  }]
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
      <h1>Forecast Training (Machine Learning)</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item active">Forecast Training</li>
        </ol>
      </nav>
    </div>
    <!-- End Page Title -->

    <?php if (!isset($_SESSION['train_username']) || !isset($_SESSION['train_session_key']) || !isset($_SESSION['train_algo']) || !isset($_GET['train_token'])): ?>
        <div class="row">
            <div class="col-md-6">
                <div class="card info-card">
                    <div class="card-body">
                        <h5 class="card-title">Train Forecasting Model</h5>
                        <form method="post" id="train-create-form">
                            <div class="d-flex align-items-center gap-3">
                                <div>Choose Algorithm:</div>
                                <div>
                                    <input type="hidden" name="username" id="trainingUsername" value="<?= $username ?>" />
                                    <input type="hidden" name="session_key" id="trainingSessionKey" value="<?= generateUUIDv4() ?>" />
                                    <input type="hidden" name="action" id="trainingAction" value="create" />
                                    <select data-placeholder="Select Algorithm" name="algo" id="trainingAlgo" class="chosen-select">
                                        <option value="Logistic Regression">Logistic Regression</option>
                                        <option value="XGBoost Classifier">XGBoost Classifier</option>
                                    </select>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-3 mt-4">
                                <div>
                                    <button type="submit" class="btn btn-primary">Create Training Session</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card info-card">
                    <div class="card-body">
                        <h5 class="card-title">OR</h5>
                        <form method="post" id="continue-train-session">
                            <div class="d-flex align-items-center gap-3">
                                <div>Continue Training Sessions:</div>
                                <div>
                                    <input type="hidden" name="username" id="continueTrainingUsername" value="<?= $username ?>"/>
                                    <input type="hidden" name="algo" id="continueTrainingAlgo" />
                                    <input type="hidden" name="action" id="continueTrainingAction" value="create" />
                                    <select data-placeholder="Select Session" name="session_key" id="continueTrainingSessionKey" class="chosen-select">
                                    </select>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-3 mt-4">
                                <div>
                                    <button type="submit" class="btn btn-primary" id="continue-train-submit-btn" disabled>Continue Training Session</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <?php
            $response = http_request_get(
                base_api_uri() .
                "/api/v1/validate/session" .
                "?api_key=" . api_key() .
                "&username=" . $_SESSION['train_username'] .
                "&session_key=" . $_SESSION['train_session_key'] .
                "&token=" . $_GET['train_token']
            );
            if (!($response["valid"] ?? false)) {
            ?>
                <script>
                    alert("Invalid Session.");
                    window.location.href = `<?= base_url() ?>/admin/admin_train`;
                </script>
                </body>
                </html>
            <?php
                exit;
            }
        ?>
        <input type="hidden" name="username" id="trainingSessionUsername" value="<?= $_SESSION['train_username'] ?>" />
        <input type="hidden" name="session_key" id="trainingSessionId" value="<?= $_SESSION['train_session_key'] ?>" />
        <input type="hidden" name="token" id="trainingToken" value="<?= $_GET['train_token'] ?>" />
        <div class="row">
            <div class="col-md">
                <div class="card info-card">
                    <div class="card-body">
                        <h5 class="card-title">Train using <?= $_SESSION['train_algo'] ?? "" ?></h5>
                        <div>
                            <div class="d-flex align-items-center gap-3">
                                <label for="trainingDataset">Dataset:</label>
                                <input type="file" name="dataset" id="trainingDataset" />
                            </div>
                            <div class="d-flex align-items-center justify-content-between gap-3 mt-4">
                                <div class="d-flex flex-column flex-grow-1">
                                    <label for="trainingFeatures">Features:</label>
                                    <select data-placeholder="Select Features" name="features" id="trainingFeatures" multiple class="chosen-select">
                                        <option>Design</option>
                                        <option>HTML5</option>
                                        <option>CSS3</option>
                                        <option>jQuery</option>
                                        <option>BS4</option>
                                        <option>Bootstrap</option>
                                        <option>WordPress</option>
                                        <option>FrontEnd</option>
                                    </select>
                                </div>
                                <div class="d-flex flex-column">
                                    <label for="trainingTarget">Target:</label>
                                    <select data-placeholder="Select Target" name="target" id="trainingTarget" class="chosen-select">
                                        <option>Design</option>
                                        <option>HTML5</option>
                                        <option>CSS3</option>
                                        <option>jQuery</option>
                                        <option>BS4</option>
                                        <option>Bootstrap</option>
                                        <option>WordPress</option>
                                        <option>FrontEnd</option>
                                    </select>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between gap-3 mt-4">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card info-card">
                    <div class="card-body">
                        <h5 class="card-title">Training Results:</h5>
                        <div id="trainingContainer">
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <?php
    require_once get_footer();
  ?>
  <!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <?php admin_html_body_end([
      ["type" => "script", "src" => "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"],
      ["type" => "script", "src" => "https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.9.2/umd/popper.min.js"],
      ["type" => "script", "src" => "assets/js/chosen.jquery.min.js"],
      ["type" => "script", "src" => "assets/js/main.js"],
      ["type" => "script", "src" => "assets/js/train.js"],
  ]); ?>

</body>

</html>

<?php endif; ?>