$(function () {
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
                        console.log(data);
                        $sel.empty().append($("<option>"));//.text("Select Session"));
                        data.forEach(([username, session_key, algo]) => {
                            if (username === window.MY_USERNAME) {
                                let $opt = $("<option>").val(session_key).text(`${algo} - ${session_key}`);
                                $sel.append($opt);
                            }
                        });
                        $sel.chosen();
                    })
                    .catch(console.log);
                $sel.on("change", function(ev) {
                    ev.preventDefault();
                    let target = ev.target.value;
                    let val = "";
                    if (target !== "Select Session") {
                        let text = $(this).find(`option[value=${target}]`);
                        val = text.text();
                    }
                    val = val && val.length > 0 ? val.split(" - ")[0] : "";
                    const $cta = $("input#continueTrainingAlgo");
                    if ($cta.length > 0) {
                        $cta.val(val);
                        if (val) {
                            $("button#continue-train-submit-btn").prop("disabled", false);
                        } else {
                            $("button#continue-train-submit-btn").prop("disabled", true);
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
        $("select.chosen-select").chosen();
    }
})