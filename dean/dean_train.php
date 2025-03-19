<?php 

authenticated_page("dean");


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
            $_SESSION['train_token'] = $_POST["train_token"] ?? null;
            
            default_html_head("Creating Train Session..");
            ?>
            <body>
            <script>
                function sendPostCreateOrContinue(username, session_key, algo, token, url) {
                    fetch(url, {
                        method: "POST",
                        body: JSON.stringify({ username, session_key, algo, token }),
                        headers: {
                            "Content-Type": "application/json",
                        },
                        credentials: "include",
                    })
                        .then(response => response.json())
                        .then(({ session_token }) => {
                            window.location.href = `<?= base_url() ?>/dean/dean_train?train_token=${session_token}`;
                        })
                        .catch((error) => {
                            window.location.href = `<?= base_url() ?>/dean/dean_train`;
                            alert('Failed to create train session. ' + error.message);
                        })
                }
                sendPostCreateOrContinue(
                    `<?= $_SESSION['train_username'] ?>`,
                    `<?= $_SESSION['train_session_key'] ?>`,
                    `<?= $_SESSION['train_algo'] ?>`,
                    `<?= $_SESSION['train_token'] ?>`,
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
                            window.location.href = `<?= base_url() ?>/dean/dean_train`;
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
    <style>
        button[type=button] {
            cursor:pointer;
        }
        .drop-zone {
            border: 2px dashed #007bff;
            border-radius: 5px;
            padding: 10px;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .drop-zone.dragover {
            background-color: #f8f9fa;
        }
    </style>
    <script>
        window.MY_USERNAME = `<?= $username ?>`;
        window.BASE_API_URL = `<?= base_api_uri() ?>`;
    </script>
    <?php
  }]
]); // html head
?>
<body>
  
  <!-- Header and Sidebar -->
  <?php require_once get_dean_header(); ?>
  <?php require_once get_dean_sidebar(); ?>


  <main id="main" class="main position-relative">

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
        <?php 
            unset($_SESSION['train_username']);
            unset($_SESSION['train_session_key']);
            unset($_SESSION['train_algo']);
        ?>
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
                                    <input type="hidden" name="train_token" id="continueTrainingActionToken" />
                                    <select data-placeholder="Select Session" name="session_key" id="continueTrainingSessionKey" class="chosen-select">
                                    </select>
                                    <code class="d-block mt-1" id="continueTrainingDateSession"></code>
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
                    window.location.href = `<?= base_url() ?>/dean/dean_train`;
                </script>
                </body>
                </html>
            <?php
                exit;
            }
            // get initial states
            $STATE_BASE_DIR = dirname(__DIR__) . DIRECTORY_SEPARATOR . "training_states";
            $filepath = $STATE_BASE_DIR . DIRECTORY_SEPARATOR . $_GET['train_token'] . ".json";
            if (!is_file($filepath)) {
                ?>
                <script>
                    alert("No Session State Found.");
                    window.location.href = `<?= base_url() ?>/dean/dean_train`;
                </script>
                </body>
                </html>
                <?php
                exit;
            }
            $INITIAL_STATES = json_decode(file_get_contents($filepath), true);
            $state = $INITIAL_STATES["state"];
        ?>
        <input type="hidden" name="username" id="trainingSessionUsername" value="<?= $_SESSION['train_username'] ?>" />
        <input type="hidden" name="session_key" id="trainingSessionId" value="<?= $_SESSION['train_session_key'] ?>" />
        <input type="hidden" name="token" id="trainingToken" value="<?= $_GET['train_token'] ?>" />
        <a href="<?= base_url() ?>/dean/dean_train" class="btn btn-outline-secondary" style="position: absolute; left: 28rem; top: 1.5em; z-index:100;"><i class="bi bi-arrow-left-short"></i> Back</a>
        <div
            class="alert alert-warning pt-1 pb-1"
            role="alert"
            id="trainingAlertConnectionStatus"
        >
            <p class="mb-0 text-center">Disconnected.</p>
        </div>
        
        <div class="row" id="training-ground">
            <div class="col-lg">
                <div class="card info-card">
                    <div class="card-body">
                        <h5 class="card-title">Train using <?= $_SESSION['train_algo'] ?? "" ?></h5>
                        <div>
                            <div class="d-flex align-items-center gap-3">
                                <label for="trainingDatasetFile" class="drop-zone" <?= isset($state["dataset"]) ? "hidden" : "" ?>>
                                    <p class="mb-2">Upload Dataset (*.csv) or <span class="text-primary">click to browse</span></p>
                                    <input type="file" id="trainingDatasetFile" accept=".csv" hidden>
                                    <p id="fileName" class="text-muted"><?= isset($state["dataset"]) ? $state['dataset']['filename'] : "" ?></p>
                                </label>
                                <div>
                                    <button type="button" class="btn btn-outline-success" id="trainingDatasetSelectBtn" hidden><i class="bi bi-check"></i>Use Dataset</button>
                                </div>
                                <code id="trainingDatasetUsed" <?= isset($state["dataset"]) ? "" : "hidden" ?>>Using dataset: <?= isset($state["dataset"]) ? $state['dataset']['filename'] : "" ?></code>
                                <div>
                                    <button type="button" class="btn btn-outline-warning" id="trainingDatasetDeselectBtn" <?= isset($state["dataset"]) ? "" : "hidden" ?>>Reupload Dataset</button>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between gap-3 mt-4 flex-wrap">
                                <div class="d-flex flex-column" style="min-width: 450px;">
                                    <label for="trainingFeatures">Features: (must select 4)</label>
                                    <select data-placeholder="Select Features" name="features" id="trainingFeatures" multiple class="chosen-select">
                                    </select>
                                </div>
                                <div class="d-flex flex-column" style="min-width: 120px;">
                                    <label for="trainingTarget">Target:</label>
                                    <select data-placeholder="Select Target" name="target" id="trainingTarget" class="chosen-select">
                                    </select>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between gap-3 mt-4">
                                <div class="d-flex flex-column">
                                    <label for="trainingTestSize">Test Size:</label>
                                    <input type="number" class="form-control" name="test_size" value="<?= $state["test_size"] ?? "0.2" ?>" id="trainingTestSize"  />
                                </div>
                                <!-- <div class="d-flex flex-column">
                                    <label for="trainingRandomState">Random State:</label>
                                    <input type="number" class="form-control" name="random_state" value="<?= ($state["random_state"] ?? null) ?>" id="trainingRandomState" />
                                </div> -->
                            </div>
                            <!-- <div class="container mt-4">
                                <label for="trainingHyperparametersContainer">Hyperparameters:</label>
                                <div class="row justify-content-evenly flex-wrap mt-2 gap-2" id="trainingHyperparametersContainer">
                                    <?php
                                    // if (count($state["valid_hyperparameters"] ?? []) > 0) {
                                    //     foreach ($state["valid_hyperparameters"] as $vhk):
                                        ?>
                                        <div class="col-md">
                                            <div class="form-floating" style="min-width: 150px;">                                                
                                                <input type="text" class="form-control befs-hyperparameters" name="<?= $vhk ?>"  <?= isset($state["hyperparameters"]) ? "value=\"".($state["hyperparameters"][$vhk] ?? ""). "\"" : "value=\"\"" ?> id="trainingHyperparameters_<?= $vhk ?>" placeholder="<?= $vhk ?>" />
                                                <label for="trainingHyperparameters_<?= $vhk ?>" class="text-secondary"><?= $vhk ?></label>
                                            </div>
                                        </div>
                                        <?php
                                    //     endforeach;
                                    // }
                                    ?>
                                </div>
                            </div> -->
                            <div class="w-100">
                                <h5 class="card-title">Training Results:</h5>
                                <div class="w-100">
                                    <div class="d-block">
                                        <code id="trainingContainer">
                                        </code>
                                    </div>
                                    <div class="d-block w-100 border-t mt-2">
                                        <div class="w-100">
                                            <h6 class="text-success">Confusion Matrix</h6>
                                            <div id="confusion-matrix-container" >

                                            </div>
                                        </div>
                                        
                                        <div class="w-100">
                                            <h6 class="text-success">ROC Curve</h6>
                                            <canvas id="rocCurveChart"></canvas>
                                        </div>

                                        <div class="w-100">
                                            <h6 class="text-success">Precision-Recall Curve</h6>
                                            <canvas id="prCurveChart"></canvas>
                                        </div>
                                    </div>
                                    <div class="w-100 mt-4 d-flex justify-content-evenly">
                                        <div style="width: fit-content;">
                                            <button type="button" class="btn btn-primary" id="trainingTestModelButton" data-bs-toggle="modal" data-bs-target="#testPredictModal" disabled>
                                                Test Model
                                            </button>
                                        </div>
                                        <div style="width: fit-content;">
                                            <button type="button" class="btn btn-primary" id="trainingSaveModelButton" data-bs-toggle="modal" data-bs-target="#saveModelModal" disabled>
                                                Save Model
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="w-100 mt-4">
                                <div class="mx-auto">
                                    <button type="button" class="btn btn-primary" id="trainingTrainButton" disabled>
                                        Train
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="testPredictModal" tabindex="-1" aria-labelledby="testPredictModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="testPredictModalLabel">Test Model</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Input Fields -->
                        <div class="mb-3">
                            <label for="feature1" class="form-label"></label>
                            <input type="number" class="form-control" id="feature1" placeholder="Enter Feature 1">
                        </div>
                        <div class="mb-3">
                            <label for="feature2" class="form-label">Feature 2</label>
                            <input type="number" class="form-control" id="feature2" placeholder="Enter Feature 2">
                        </div>
                        <div class="mb-3">
                            <label for="feature3" class="form-label">Feature 3</label>
                            <input type="number" class="form-control" id="feature3" placeholder="Enter Feature 3">
                        </div>

                        <div class="mb-3">
                            <label for="feature4" class="form-label">Feature 4</label>
                            <input type="number" class="form-control" id="feature4" placeholder="Enter Feature 4">
                        </div>
                        <!-- Predict Button -->
                        <button type="button" class="btn btn-success w-100" id="predictBtn">Predict</button>

                        <!-- Prediction Result -->
                        <div class="mt-3">
                            <strong>Prediction Result:</strong>
                            <code id="predictionOutput">Waiting for input...</code>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Save Modal -->
        <div class="modal fade" id="saveModelModal" tabindex="-1" aria-labelledby="saveModelModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="saveModelModalLabel">Save Trained Model</h5>
                        <button type="button" class="btn-close" id="model-save-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Do you want to save the trained model?</p>
                        <p class="fst-italic">Note: saving the model will end the current training session.</p>
                        <label for="modelNameSaveInput">Enter the desired name for the model:</label>
                        <input type="text" name="model_name" class="form-control" id="modelNameSaveInput" placeholder="Enter the model name" />
                        <div class="d-flex justify-content-evenly gap-4 mt-4">
                            <button type="button" class="btn btn-secondary w-100" id="noSaveBtn" data-bs-dismiss="modal" aria-label="Close">No</button>
                            <button type="button" class="btn btn-success w-100" id="yesSaveBtn">Yes</button>
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
      ["type" => "script", "src" => "https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"],
      ["type" => "script", "src" => "https://cdn.jsdelivr.net/npm/onnxruntime-web/dist/ort.min.js"],
      ["type" => "script", "src" => "assets/js/inference.js"],
      ["type" => "script", "src" => "assets/js/main.js"],
      ["type" => "script", "src" => "assets/js/train.js"],
  ]); ?>

</body>

</html>

<?php endif; ?>