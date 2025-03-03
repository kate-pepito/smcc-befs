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
                const scaler = JSON.parse($(this).attr("data-befs-model-scaler"));
                const $elem = $(".befs-forecast[data-befs-forecast-available=true]");
                const countElem = $elem.length;
                $elem.each(function(i) {
                        let data = $(this).attr("data-befs-data");
                        if (!data) {
                                $(this).text("Failed. No Data");
                                return;
                        }
                        data = JSON.parse(data);
                        const stud_id = data.id;
                        const sy_id = data.sy_id;
                        console.log(stud_id, "and", sy_id, "from", data);
                        const preboard1 = data.preboard1;
                        const preboard2 = data.preboard2;
                        const revalida = data.revalida;
                        const $thisElem = $(this);
                        $thisElem.html("Loading ... Please wait.");

                        ml_inference_input_tensor_with_scaler("float32", [preboard1, preboard2, revalida], scaler)
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
                                        const inference_result = /*html*/`<p class="${rs[0].toLowerCase() !== "not passing" ? "text-danger" : "text-success"}">${rs[0]}</p><p style="font-size: 10px; font-weight: normal;">${rs[1]}</p><p style="font-size: 10px; font-weight: normal;">${rs[2]}</p>
                                        `;
                                        console.log("s", stud_id, "y", sy_id,"i", inference_result);
                                        $.post(window.location.href, {
                                                stud_id, sy_id, inference_result
                                        }).done((status) => {
                                                alert(status)
                                                if (i+1 === countElem) {
                                                        window.location.reload();
                                                }
                                        }).fail(() => {
                                                
                                                if (i+1 === countElem) {
                                                        window.location.reload();
                                                }
                                        });

                                })
                                .catch(console.warn)
                        

                });
        })
});