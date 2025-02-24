document.addEventListener("DOMContentLoaded", function () {
        async function main() {
                const { session, inputNames, outputNames, error } = await ml_inference_session();
                if (error) {
                        alert(error);
                        return;
                }
                const float_input = ml_inference_input_tensor('float32', [4,12.2,22.1,3.4], [1,4]);
                const feeds = { float_input };
                const result = await ml_inference_run(session, feeds);
                console.log(result[outputNames[0]]);
        }
        main()
});