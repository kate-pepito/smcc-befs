$(function () {
    $("select.chosen-select").chosen();
    if ($('form#train-create-form').length > 0) {
        // create session page
        function fillTrainingSessions() {
            const $cts = $('form#continue-train-session');
            const api_key = window.sessionStorage.getItem("TRAIN_API_KEY");
            if ($cts.length > 0 && api_key) {
                const $sel = $cts.find('select#continueTrainingSessionKey');
                fetch(`${window.BASE_API_URL}/api/v1/train/sessions?api_key=${api_key}`, {
                    method: "GET",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    credentials: "include",
                })
                    .then(response => response.json())
                    .then(({ data }) => {
                        $sel.empty().append($("<option>"));
                        data.forEach(([username, session_key, algo, date_session, token]) => {
                            if (username === window.MY_USERNAME) {
                                let $opt = $("<option>").val(session_key).text(`${algo} - ${(new Date(date_session)).toLocaleString('en-US', { month: "short", day: "numeric", year: "numeric", hour12: true, hour: "numeric", minute: "numeric"})} - ${token}`);
                                $sel.append($opt);
                            }
                        });
                        setTimeout(() => {
                            $sel.trigger("chosen:updated");
                            $sel.css("width: 100%")
                        }, 1000);
                    })
                    .catch(console.log);
                $sel.on("change", function(ev) {
                    ev.preventDefault();
                    let target = ev.target.value;
                    let v = "";
                    if (target !== "Select Session") {
                        let text = $(this).find(`option[value=${target}]`);
                        v = text.text();
                    }
                    let vsplit = v && v.length > 0 ? v.split(" - ") : [];
                    let val = vsplit?.[0] || "";
                    let dateval = vsplit?.[1] || "";
                    let token = vsplit?.[2] ||  "";
                    const $cta = $("input#continueTrainingAlgo");
                    if ($cta.length > 0) {
                        $cta.val(val);
                        if (val) {
                            $("button#continue-train-submit-btn").prop("disabled", false);
                            $("code#continueTrainingDateSession").html(`<div>Algorithm: ${val}</div><div>Created at: ${dateval}</div>`);
                            $("input#continueTrainingActionToken").val(token);
                        } else {
                            $("button#continue-train-submit-btn").prop("disabled", true);
                            $("code#continueTrainingDateSession").text("");
                            $("input#continueTrainingActionToken").val("");
                        }
                    }
                });
                $("select#trainingAlgo").chosen();
            } else {

            }
        }
        fillTrainingSessions()
    } else {
        // train session page
        // initilize and connect websocket
        var wsConnected = false, lastCheckConnection = false;
        const api_key = window.sessionStorage.getItem("TRAIN_API_KEY");
        const token = (new URLSearchParams(window.location.search)).get("train_token");
        const wsUrl = `${window.BASE_API_URL}/api/v1/train?api_key=${api_key}&token=${token}`.replaceAll("https://", "wss://").replaceAll("http://", "ws://");

        const wsConn = new WebSocket(wsUrl);
        var wsIntervalSync = null, checkConnectedDisableButtonsInterval = null;
        var savedTestModelPath = "", /*savedScaler = {},*/ savedModelMetadata = {};

        // Function to send data
        function sendAction(action, data = undefined) {
            if (wsConn.readyState === WebSocket.OPEN) {
                wsConn.send(JSON.stringify({ action, data }));
            } else {
                console.warn("WebSocket is not open. Message not sent.");
            }
        }

        function generateUUID() {
            return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                const r = Math.random() * 16 | 0;
                const v = c === 'x' ? r : (r & 0x3 | 0x8);
                return v.toString(16);
            });
        }

        function plotConfusionMatrix(matrix) {
            const $table = $(/*html*/`
                <table class="table table-responsive table-bordered">
                    <thead>
                        <tr>
                            <th colspan="2" rowspan="2" style="text-align: center;"  class="text-dark bg-light">
                                <p style="text-align: center;"><span>Count: ${matrix.reduce((caller, x) => caller + x[0] + x[1], 0)}</span></p>
                            </th>
                            <th colspan="2" style="text-align: center;">
                                <p dir="ltr" style="text-align: center;"><span>Predicted</span></p>
                            </th>
                        </tr>
                        <tr>
                            <th style="text-align: center;">
                                <p dir="ltr" style="text-align: center;"><span>Passer</span></p>
                            </th>
                            <th style="text-align: center;">
                                <p dir="ltr" style="text-align: center;"><span>Not a Passer</span></p>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th rowspan="2" style="text-align: center;">
                                <p dir="ltr" style="text-align: center;"><br></p>
                                <p dir="ltr" style="text-align: center;"><span>Actual</span></p>
                            </th>
                            <th style="text-align: center;">
                                <p dir="ltr" style="text-align: center;"><span>Passer</span></p>
                            </th>
                            <td style="text-align: center;">
                                <p dir="ltr" style="text-align: center;"><span>True Positive</span><br><span>(TP = ${matrix[1][1]})</span></p>
                            </td>
                            <td style="text-align: center;">
                                <p dir="ltr" style="text-align: center;"><span>False Negative</span><br><span>(FN = ${matrix[1][0]})</span></p>
                            </td>
                        </tr>
                        <tr>
                            <th style="width: 233.333px; text-align: center;">
                                <p dir="ltr" style="text-align: center;"><span>Not a Passer</span></p>
                            </th>
                            <td style="width: 233.333px; text-align: center;">
                                <p dir="ltr" style="text-align: center;"><span>False Positive</span><br><span>(FP = ${matrix[0][1]})</span></p>
                            </td>
                            <td style="width: 233.333px; text-align: center;">
                                <p dir="ltr" style="text-align: center;"><span>True Negative</span><br><span>(TN = ${matrix[0][0]})</span></p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            `);
            const $tableDescription = $(/*html*/`
                <code class="text-secondary fst-italic">
                    <div>1. True Positives (TP = 14): Correctly identified students who will pass.</div>
                    <div>2. True Negatives (TN = 28): Correctly identified students who will not pass.</div>
                    <div>3. False Positives (FP = 7): Incorrectly predicted "Passer" when the student actually failed.</div> 
                    <div>4. False Negatives (FN = 11): Incorrectly predicted "Not a Passer" when the student actually passed.</div>
                </code><br />
            `)
            $("#confusion-matrix-container").empty().append($table).append($tableDescription);
        }

        function plotRocCurve(roc) {
            const ctx = document.getElementById("rocCurveChart").getContext("2d");
            if (window.plotRocCurveChart) {
                return;
            }
            const rocData = roc.fpr.map((recallValue, index) => ({
                x: recallValue,
                y: roc.tpr[index]
            }));
            window.plotRocCurveChart = new Chart(ctx, {
                type: 'line',
                data: {
                    datasets: [{
                        label: "ROC Curve",
                        data: rocData,
                        borderColor: "blue",
                        backgroundColor: "transparent",
                        fill: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    scales: {
                        x: {
                            type: "linear",
                            position: "bottom",
                            title: {
                                display: true,
                                text: "False Positive Rate",
                            },
                            min: 0,
                            max: 1
                        },
                        y: {
                            title: {
                                display: true,
                                text: "True Positive Rate",
                            },
                            min: 0,
                            max: 1
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: "ROC Curve",
                        },
                        legend: { display: false }
                    }
                }
            });
        }

        function plotPrCurve(pr) {
            const ctx = document.getElementById("prCurveChart").getContext("2d");
            if (window.plotPrCurveChart) {
                return;
            }
            const prData = pr.recall.map((recallValue, index) => ({
                x: recallValue,
                y: pr.precision[index]
            }));
            window.plotPrCurveChart = new Chart(ctx, {
                type: 'line',
                data: {
                    datasets: [{
                        label: "Precision-Recall Curve",
                        data: prData,
                        borderColor: "purple",
                        backgroundColor: "transparent",
                        fill: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    scales: {
                        x: {
                            type: "linear",
                            position: "bottom",
                            title: {
                                display: true,
                                text: "Recall",
                            },
                            min: 0,
                            max: 1
                        },
                        y: {
                            title: {
                                display: true,
                                text: "Precision",
                            },
                            min: 0,
                            max: 1
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: "Precision-Recall Curve",
                        },
                        legend: { display: false }
                    }
                }
            });
        }


        function initializeDatasetDragAndDrop() {
            const $dropZone = $(".drop-zone");
            const $fileInput = $("input#trainingDatasetFile");
            const $fileNameDisplay = $("#fileName");
            const $selectBtn = $("button#trainingDatasetSelectBtn");
            const $deselectBtn = $("button#trainingDatasetDeselectBtn");

            $selectBtn.on("click", function(e) {
                e.preventDefault();
                let file = $fileInput.get(0).files.length > 0 ? $fileInput.get(0).files[0] : null;
                if (file) {
                    const formData = new FormData();
                    const filename = `dataset_${generateUUID()}.csv`;
                    const csvBlob = new Blob([file], { type: "text/csv" });
                    formData.append('dataset', csvBlob, filename);
                    const api_key = window.sessionStorage.getItem("TRAIN_API_KEY");
                    let url = `${BASE_URL}/api/upload_dataset?api_key=${api_key}`;
                    $.ajax({
                        url, 
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function({ success, error, filepath }) {
                            if (success) {
                                console.log('Upload successful:', filepath);
                                sendAction(
                                    "set_dataset",
                                    {
                                        filename,
                                        size: file.size,
                                        filepath
                                    }
                                )
                                $dropZone.prop("hidden", true);
                                $selectBtn.prop("hidden", true);
                                $("code#trainingDatasetUsed").html(`Using dataset:<br />${filename}`).prop("hidden", false);
                                $deselectBtn.prop("hidden", false);
                            } else {
                                console.log(error)
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Upload failed:', error);
                        }
                    });
                }
            });
            
            $deselectBtn.on("click", function(e) {
                e.preventDefault();
                sendAction(
                    "set_dataset",
                    null
                );
                $fileInput.val(null);
                $dropZone.prop("hidden", false);
                $selectBtn.prop("hidden", false);
                $("code#trainingDatasetUsed").prop("hidden", true);
                $(this).prop("hidden", true);
            });

            $fileInput.on("change", function() {
                if (this.files.length > 0) {
                    $selectBtn.prop("hidden", false);
                    $fileNameDisplay.text(`Selected File: ${this.files[0].name}`);
                } else {
                    $selectBtn.prop("hidden", true);
                }
            });

            $dropZone.on("dragover", function(e) {
                e.preventDefault();
                $(this).addClass("dragover");
            });

            $dropZone.on("dragleave drop", function(e) {
                e.preventDefault();
                $(this).removeClass("dragover");
            });

            $dropZone.on("drop", function(e) {
                let files = e.originalEvent.dataTransfer.files;
                if (files.length > 0) {
                    $fileInput.prop("files", files);
                    $fileNameDisplay.text(`Selected File: ${files[0].name}`);
                }
            });
        }

        // Event: Connection opened
        wsConn.onopen = function () {
            console.log("WebSocket connection established.");
            wsIntervalSync = setInterval(() => sendAction("get_updates"), 10000); // sync data every 10 seconds
        };

        function updateAllStatus(data) {
            if (!!data.ended_at) {
                const dateEnded = (new Date(data.ended_at)).toLocaleString('en-US', { month: "short", day: "numeric", year: "numeric", hour12: true, hour: "numeric", minute: "numeric"});
                window.location.href = `${BASE_URL}/admin/admin_train`;
                alert("Session Ended at " + dateEnded);
                return;
            }
            const $alertConn = $("#trainingAlertConnectionStatus");
            if ($alertConn.length > 0) {
                if (data.connection === "connected") {
                    $alertConn.find("p").text("Connected.");
                    if ($alertConn.hasClass("alert-warning")) {
                        $alertConn.removeClass("alert-warning").addClass("alert-success");
                    }
                } else {
                    $alertConn.find("p").text("Disconnected.");
                    if ($alertConn.hasClass("alert-success")) {
                        $alertConn.removeClass("alert-success").addClass("alert-warning");
                    }
                }
            }
            // dataset
            if (data.dataset?.filename && data.dataset?.size > 0 && data.column_names?.length > 0) {
                const $dropZone = $(".drop-zone");
                const $selectBtn = $("button#trainingDatasetSelectBtn");
                const $deselectBtn = $("button#trainingDatasetDeselectBtn");
                !$dropZone.prop("hidden") && $dropZone.prop("hidden", true);
                !$selectBtn.prop("hidden") && $selectBtn.prop("hidden", true);
                ($("code#trainingDatasetUsed").html() !== `Using dataset:<br />${data.dataset.filename}`) && $("code#trainingDatasetUsed").html(`Using dataset:<br />${data.dataset.filename}`).prop("hidden", false);
                !!$deselectBtn.prop("hidden") && $deselectBtn.prop("hidden", false);
                // feature column options
                const $featureSelect = $("select#trainingFeatures");
                if ($featureSelect.find("option").length !== data.column_names.length) {
                    $featureSelect.empty();
                    data.column_names.forEach((cn) => {
                        $featureSelect.append($('<option>').val(cn).text(cn));
                    });
                    $featureSelect.trigger("chosen:updated");
                }
                // target column options
                const $targetSelect = $("select#trainingTarget");
                if ($targetSelect.find("option").length !== (data.column_names.length + 1)) {
                    $targetSelect.empty();
                    $targetSelect.append($("<option value=''></option>"));
                    [...data.column_names].reverse().forEach((cn) => {
                        $targetSelect.append($('<option>').val(cn).text(cn));
                    });
                    $targetSelect.trigger("chosen:updated");
                }
            }
            // features
            const $featureSelect = $("select#trainingFeatures");
            if ($featureSelect.length > 0 && $featureSelect.val() !== data.features) {
                $featureSelect.val(data.features).trigger("chosen:updated");
            }
            if (data.column_names?.length === 0) {
                $featureSelect.empty().trigger("chosen:updated");
            }

            // target
            const $targetSelect = $("select#trainingTarget");
            if ($targetSelect.length > 0 && $targetSelect.val() !== data.target?.[0]) {
                $targetSelect.val(data.target).trigger("chosen:updated");
            }
            if (data.column_names?.length === 0) {
                $targetSelect.empty().trigger("chosen:updated");
            }

            // test size
            const $testSizeInput = $("input#trainingTestSize");
            if ($testSizeInput.length > 0) {
                $testSizeInput.val(data.test_size);
            }

            // random state
            // const $randomStateInput = $("input#trainingRandomState");
            // if ($randomStateInput.length > 0) {
            //     $randomStateInput.val(data.random_state);
            // }
            
            // hyperparameters
            // const $hyperparametersRow = $("#trainingHyperparametersContainer");
            // if ($hyperparametersRow.length > 0) {
            //     const $hpc = $hyperparametersRow.find("input");
            //     if ($hpc.length === 0) {
            //         const $allValidHyperparameters = data.valid_hyperparameters?.map((vhk) =>
            //             $(/*html*/`
            //             <div class="col-md">
            //                 <div class="form-floating" style="min-width: 150px;">                                                
            //                     <input type="text" class="form-control" name="${vhk}" value="${data.hyperparameters && Object.keys(data.hyperparameters).includes(vhk) ? data.hyperparameters[vhk] : ''}" id="trainingHyperparameters_${vhk}" placeholder="${vhk}" />
            //                     <label for="trainingHyperparameters_${vhk}" class="text-secondary">${vhk}</label>
            //                 </div>
            //             </div>`)
            //         );
            //         $hyperparametersRow.append($allValidHyperparameters);
            //     } else {
            //         $hpc.each(function () {
            //             const param_name = $(this).attr("placeholder");
            //             $(this).val(data.hyperparameters?.[param_name] || "");
            //         });
            //     }
            // }
            if (data.status === "completed") {
                const $containerMetric = $("#trainingContainer");
                !!data.metrics && $containerMetric.empty();
                // metrics
                !!data.metrics && Object.keys(data.metrics)?.forEach((metrics) => {
                    let $content = null;
                    if (metrics === "accuracy" || metrics === "precision" || metrics === "recall" || metrics === "f1_score" || metrics === "roc_auc" || metrics === "pr_auc") {
                        let description;
                        switch (metrics) {
                            case "accuracy":
                                description = "Measures how often the model makes the correct prediction overall.";
                                break;
                            case "precision":
                                description = `Out of all students predicted as "Passers," this shows how many were actually`;
                                break;
                            case "recall":
                                description = "Out of all actual Passers, this indicates how many were correctly identified by the model.";
                                break;
                            case "f1_score":
                                description = "A balance between Precision and Recall.";
                                break;
                            case "roc_auc":
                                description = "Indicates how well the model distinguishes between students who will pass and the better the separation.";
                                break;
                            case "pr_auc":
                                description = "Reflects how reliable the model’s predictions are when identifying Passers, with higher values indicating fewer mistakes in classifying them.";
                                break;
                        }
                        $content = $(/*html*/`<div>
                            ${metrics.split("_").map((v) => v[0].toUpperCase() + v.substring(1)).join(" ")}: <span class="fw-bold">${(Math.round(Number.parseFloat(data.metrics[metrics]) * 100000) / 1000)} %</span>
                            &nbsp;
                            <span class="text-secondary fst-italic">- ${description}</span>
                        </div>`);
                    } else if (metrics === "classification_report") {
                        $content = $(/*html*/`
                            <table class="table table-bordered table-striped text-center">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Class</th>
                                        <th>Precision</th>
                                        <th>Recall</th>
                                        <th>F1-Score</th>
                                        <th>Support</th>
                                    </tr>
                                </thead>
                                <tbody id="classificationTable">
                                </tbody>
                            </table>
                        `);
                        const $tableBody = $content.find("tbody");
                        const classification_reports = typeof(data.metrics[metrics]) === "string" ? JSON.parse(data.metrics[metrics].replaceAll("'", "\"")) : data.metrics[metrics];
                        $.each(classification_reports, function (key, report) {
                            if (typeof report === 'number') return;
                            const row = `
                                <tr class="${key.includes('avg') ? (key === 'macro avg' ? 'table-info' : 'table-primary') : ''}">
                                    <td><strong>${key}</strong></td>
                                    <td>${(report.precision * 100).toFixed(2)}%</td>
                                    <td>${(report.recall * 100).toFixed(2)}%</td>
                                    <td>${(report['f1-score'] * 100).toFixed(2)}%</td>
                                    <td>${report.support}</td>
                                </tr>
                            `;
                            $tableBody.append(row);
                        });
                        const accuracyRow = `
                            <tr class="table-success">
                                <td><strong>Accuracy</strong></td>
                                <td colspan="3">${(classification_reports.accuracy * 100).toFixed(2)}%</td>
                                <td>-</td>
                            </tr>
                        `;
                        $tableBody.append(accuracyRow);
                    } else if (metrics === "confusion_matrix") {
                        plotConfusionMatrix(data.metrics[metrics]);
                    } else if (metrics === "roc_curve") {
                        plotRocCurve(data.metrics[metrics]);
                    } else if (metrics === "precision_recall_curve") {
                        plotPrCurve(data.metrics[metrics]);
                    }
                    if ($content) {
                        $containerMetric.append($content);
                    }
                });
                const $testPredictModal = $("#testPredictModal");
                $testPredictModal.find(".modal-body .mb-3").each(function (i) {
                    $(this).find("label").text(i === 3 ? data.features[i] + " (optional)" : data.features[i]);
                    $(this).find("input").attr("placeholder", i === 3 ? data.features[i] + " (optional)" : data.features[i]);
                });

                savedTestModelPath = `${data.model?.filepath}testmodels/`;
                // savedScaler = data.scaler;
                savedModelMetadata = data.model
            }
        }

        $("button#predictBtn").on("click", function (ev) {
            ev.preventDefault();
            const d1 = $("#testPredictModal").find("input#feature1").val();
            const d2 = $("#testPredictModal").find("input#feature2").val();
            const d3 = $("#testPredictModal").find("input#feature3").val();
            const d4 = $("#testPredictModal").find("input#feature4").val();
            if (!savedTestModelPath /*|| !savedScaler*/ || !savedModelMetadata) {
                alert("No model created yet. Please train the model first.");
                return;
            }
            if (!d1 || !d2 || !d3) {
                alert("Fill in all input features");
                return;
            }
            // do inference here if model is available
            if (savedTestModelPath && savedModelMetadata && /* savedScaler &&*/ !!d1 && !!d2 && !!d3) {
                $("#predictionOutput").empty().html("Predicting...");
                setTimeout(async () => {
                    const testmodel = `${savedTestModelPath}${savedModelMetadata.filename}${savedModelMetadata.file_extension}`
                    const { session, inputNames, outputNames, error } = await ml_inference_session(testmodel);
                    console.log("output Names", outputNames);
                    if (error) {
                        $("#predictionOutput").empty().html("ERROR: " + error);
                        return;
                    }
                    const data = await ml_inference_input_tensor('float32', [Number.parseFloat(d1), Number.parseFloat(d2), Number.parseFloat(d3), Number.parseFloat(d4)])
                    const feeds = { [inputNames[0]]: data }
                    const result = await ml_inference_run(session, feeds, outputNames);
                    
                    session.release();
                    const result_label = result[outputNames[0]].data;
                    let label_result = "";
                    try {
                        label_result = Number.parseInt(result_label) === 0 ? "Not a Passer" : "Passer";
                    } catch (err) {
                        label_result = result_label
                    }
                    const result_probability_class_0 = result[outputNames[1]].data;
                    const result_probability_class_1 = result[outputNames[2]].data;
                    $("#predictionOutput").empty().html(`
                        <div>Result:</div>
                        <div>Predicted Class: ${result_label} (${label_result})</div>
                        <div>Probability to Class ${outputNames[1].substring(1)}: ${result_probability_class_0 * 100} %</div>
                        <div>Probability to Class ${outputNames[2].substring(1)}: ${result_probability_class_1 * 100} %</div>
                    `);
                }, 2000);
            }
        });

        $("#testPredictModal").on('shown.bs.modal', event => {
            if (savedTestModelPath) {
                sendAction("upload_model", { filepath: savedTestModelPath });
            }
        });

        $("#testPredictModal").on('hidden.bs.modal', event => {
            if (savedTestModelPath) {
                sendAction("remove_model", { filepath: savedTestModelPath });
            }
        });

        $("#saveModelModal").on('shown.bs.modal', event => {
            sendAction("upload_model");
            $(this).find("#yesSaveBtn").on("click", function (ev) {
                ev.preventDefault();
                const modelname = $("input#modelNameSaveInput").val();
                // const scaler = savedScaler
                const modelmetadata = savedModelMetadata;
                if (/*!scaler ||*/ !modelmetadata) {
                    alert("No model created yet. Please train the model first.");
                    return;
                }
                if (!modelname) {
                    alert("Fill in model name.");
                    $("input#modelNameSaveInput").focus();
                    return;
                }
                const api_key = window.sessionStorage.getItem("TRAIN_API_KEY");
                const url = `${BASE_URL}/api/save_model?api_key=${api_key}`;
                const fullpath = `${modelmetadata.filepath}${modelmetadata.filename}${modelmetadata.file_extension}`;
                const data = {
                    ...modelmetadata,
                    name: modelname,
                    fullpath,
                    // scaler,
                };
                $.ajax({
                    url: url,
                    type: "POST",
                    contentType: "application/json",
                    data: JSON.stringify(data),
                    success: function({ success, detail }) {
                        alert(detail);
                        if (success) {
                            sendAction("end_session");
                            $("button#model-save-close").trigger("click");
                        }
                    },
                    error: function(err, statusText) {
                        console.log(err);
                        alert("ERROR Saving model: " + statusText);
                    }
                });
                
            });
        });

        $("#saveModelModal").on('hidden.bs.modal', event => {
            $(this).find("#yesSaveBtn").off("click");
        });


        $("select#trainingFeatures").on("change", function (ev) {
            ev.preventDefault();
            const featuresSelected = $(this).val();
            sendAction("set_features", featuresSelected);
        });
        
        $("select#trainingTarget").on("change", function (ev) {
            ev.preventDefault();
            const targetSelected = $(this).val();
            const sel = [targetSelected];
            sendAction("set_target", sel);
        });

        $("input#trainingTestSize").on("change", function (ev) {
            ev.preventDefault();
            const testSize = $(this).val();
            sendAction("set_test_size", Number.parseFloat(testSize));
        });

        // $("input#trainingRandomState").on("change", function (ev) {
        //     ev.preventDefault();
        //     const random_state = $(this).val();
        //     sendAction("set_random_state", Number.parseFloat(random_state));
        // });

        // $("input.befs-hyperparameters").on("change", function () {
        //     const result = {};
        
        //     $("input.befs-hyperparameters").each(function () {
        //         const name = $(this).attr("name");
        //         const value = $(this).val();
                
        //         if (name && value !== "") {
        //             result[name] = value;
        //         }
        //     });
        
        //     sendAction("set_hyperparameters", result);
        // });
        

        wsConn.addEventListener("message", function (event) {
            try {
                const data = JSON.parse(event.data);
                wsConnected = data.connection === "connected";
                switch (data.status) {
                    case "idle":
                        updateAllStatus(data)
                        break;
                    case "training":
                        updateAllStatus(data)
                        console.log(`Training progress: ${data.progress}%`);
                        break;
                    case "completed":
                        updateAllStatus(data)
                        console.log("Training completed successfully!");
                        break;
                    case "error":
                        console.error("Training error:", data.error);
                        alert("Training error: " + data.error);
                        break;
                    default:
                        console.log("OTHERS:", data);
                }
            } catch (error) {
                console.error("Error parsing WebSocket message:", error);
            }
        })

        // Event: Connection closed
        wsConn.onclose = function (event) {
            if (wsIntervalSync) {
                clearInterval(wsIntervalSync);
            }
            if (event.wasClean) {
                console.log(`WebSocket closed cleanly, code=${event.code}, reason=${event.reason}`);
            } else {
                console.warn("WebSocket connection closed unexpectedly.");
            }
        };

        // Event: Error occurred
        wsConn.onerror = function (error) {
            console.error("WebSocket error:", error);
        };

        $(window).on('beforeunload', function() {
            if (wsIntervalSync) {
                clearInterval(wsIntervalSync);
            }
            if (checkConnectedDisableButtonsInterval) {
                clearInterval(checkConnectedDisableButtonsInterval);
            }
        });

        initializeDatasetDragAndDrop();

        const $trainBtn = $("button#trainingTrainButton");
        $trainBtn.on("click", function (ev) {
            ev.preventDefault();
            sendAction("save_model");
            window.plotRocCurveChart && window.plotRocCurveChart.destroy()
            window.plotRocCurveChart = null;
            window.plotPrCurveChart
            window.plotPrCurveChart && window.plotPrCurveChart.destroy()
            window.plotPrCurveChart = null;
        });
        $("#training-ground").find("input").prop("disabled", false);
        $("#training-ground").find("select").prop("disabled", false);
        $("#training-ground").find("button").prop("disabled", false);
        checkConnectedDisableButtonsInterval = setInterval(() => {
            if (lastCheckConnection !== wsConnected) {
                lastCheckConnection = wsConnected;
                if (lastCheckConnection) {
                    $("#training-ground").find("input").prop("disabled", false);
                    $("#training-ground").find("select").prop("disabled", false);
                    $("#training-ground").find("button").prop("disabled", false);
                } else {
                    $("#training-ground").find("input").prop("disabled", true);
                    $("#training-ground").find("select").prop("disabled", true);
                    $("#training-ground").find("button").prop("disabled", true);
                }
            }
            const $featureSelect = $("select#trainingFeatures");
            const $targetSelect = $("select#trainingTarget");
            const $testSizeInput = $("input#trainingTestSize");
            const $trainBtn = $("button#trainingTrainButton");

            if ($featureSelect.length > 0 && $targetSelect.length > 0 && $testSizeInput.length > 0 && 
               !!$featureSelect.val() && $featureSelect.val().length === 4 && $targetSelect.val() &&
               !!$testSizeInput.val()
            ) {
                $trainBtn.prop("disabled", false);
            } else {
                $trainBtn.prop("disabled", true);
            }

        }, 1000);
    }
});