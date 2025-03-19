$(function () {

        async function model_inference(model_path, feed_data) {
                const { session, inputNames, outputNames, error } = await ml_inference_session(model_path);
                if (error) {
                        alert(error);
                        return;
                }
                const feeds = { [inputNames[0]]: feed_data }
                const result = await ml_inference_run(session, feeds, outputNames);
                session.release();
                return Object.fromEntries([...outputNames].map((outname) => [outname.startsWith("i") ? `probability_${outname.substring(1)}` : 'result', result[outname].data]));
        }

        
        $("button#forecastRecommendationBtn").on("click", function(ev) {
                ev.preventDefault();
                const model_path = $(this).attr("data-befs-model-path");
                // const scaler = JSON.parse($(this).attr("data-befs-model-scaler"));
                const dtable = window.myDataTables?.[0] || null;
                const data = Object.entries(dtable.data.data).map((v, i) => {
                        const attr = v[1]?.cells?.[12]?.data?.[1]?.attributes;
                        const d = attr?.["data-befs-data"];
                        const available = attr?.["data-befs-forecast-available"];
                        return available === "true" ? JSON.parse(d) : null;
                }).filter(d => !!d);
                const countAvailable = data.length;
                data.forEach(function(data, i) {
                        const stud_id = data.id;
                        const sy_id = data.sy_id;
                        const preboard1 = data.preboard1;
                        const preboard2 = data.preboard2;
                        const revalida = data.revalida;
                        const gwa = Number.parseFloat(data.gwa);
                        const $thisElem = $(this);
                        $thisElem.html("Loading ... Please wait.");

                        ml_inference_input_tensor("float32", [preboard1, preboard2, revalida, gwa])
                                .then(async (feed_data) => {
                                        const result = await model_inference(model_path, feed_data);
                                        const rs = [];
                                        try {
                                                rs.push(Number.parseFloat(result["result"]) === 1 ? "Passing" : "Not Passing");
                                        } catch(e) {
                                                rs.push(result["result"]);
                                        }
                                        Object.keys(result).filter((rk) => rk !== "result").forEach((rk) => {
                                                let cls = "";
                                                try {
                                                        cls = Number.parseFloat(rk.substring("probability_".length)) === 1 ? "Passing" : "Not Passing";
                                                } catch (e) {
                                                        console.log("Error on", e);
                                                        cls = rk.substring("probability_".length)
                                                }
                                                rs.push(`Chance of <span class="fw-bold">${cls}</span> is <span class="fw-bold">${Math.floor(Number.parseFloat(result[rk]) * 10000) / 100}%</spa>`)
                                        });
                                        const inference_result = /*html*/`<p class="${rs[0].toLowerCase() !== "not passing" ? "text-success" : "text-danger"}">${rs[0]}</p><p style="font-size: 10px; font-weight: normal;">${rs[1]}</p><p style="font-size: 10px; font-weight: normal;">${rs[2]}</p>
                                        `;
                                        $.post(window.location.href, {
                                                stud_id, sy_id, inference_result
                                        }).done((status) => {
                                                alert(status)
                                                if (i+1 === countAvailable) {
                                                        window.location.reload();
                                                }
                                        }).fail(() => {
                                                if (i+1 === countAvailable) {
                                                        window.location.reload();
                                                }
                                        });

                                })
                                .catch(console.warn)
                        

                });
        });

        const $dtotal = $("<div class='text-right'>");
        // Function to update filtered row count
        function updateFilteredCount(el, e, i) {
                const filteredRows = window.myDataTables?.[0].data.data.length; // Filtered rows count
                if (!$(".datatable-bottom").hasClass("d-flex")) {
                        $(".datatable-bottom").addClass("d-flex justify-content-between");
                }
                const $pagination = $(".datatable-bottom").children(0);
                if (!$pagination.hasClass("flex-grow-1")) {
                        $pagination.addClass("flex-grow-1");
                }

                const $fil = $("#filter-container");
                const defaultVal = $fil.find("select#filterPassing").attr("default-value");
                if ($fil.parent().hasClass("card-body")) {
                        $fil.remove();
                        $fil.insertAfter(".datatable-top .datatable-dropdown");
                        $fil.find("select#filterPassing").val(defaultVal).on("change", function() {
                                const val = $(this).val();
                                const url = new URL(window.location.href);
                                switch (val) {
                                        case "Passing":
                                                url.searchParams.set("filter", "Passing");
                                                break;
                                        case "Not Passing":
                                                url.searchParams.set("filter", "Not Passing");
                                                break;
                                        case "Available":
                                                url.searchParams.set("filter", "Available");
                                                break;
                                        case "N/A":
                                                url.searchParams.set("filter", "Not Available");
                                                break;
                                        default:
                                                url.searchParams.set("filter", "");
                                }
                                window.location.href = url.toString();
                        });
                }
        }

        window.myDataTables?.[0]?.on('datatable.search', updateFilteredCount);
        updateFilteredCount();
});