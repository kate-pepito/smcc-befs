document.addEventListener("DOMContentLoaded", function () {

        async function logisticRegression(feeds) {
                const { session, inputNames, outputNames, error } = await ml_inference_session("/inference/logistic_regression.onnx");
                if (error) {
                        alert(error);
                        return;
                }
                const result = await ml_inference_run(session, feeds);
                const numberArray = Array.from(result[outputNames[0]], Number);

                session.release();
                return numberArray[0] === 1;
        }
        
        async function xgBoost(feeds) {
                const { session, inputNames, outputNames, error } = await ml_inference_session("/inference/xgboost.onnx");
                if (error) {
                        alert(error);
                        return;
                }
                const result = await ml_inference_run(session, feeds);
                const numberArray = Array.from(result[outputNames[0]], Number);
                session.release();
                return numberArray[0] === 1;
        }
        
        function randomTensor(dtype, shape) {
                const size = shape.reduce((a, b) => a * b, 1);
                const values = Array.from({ length: size }, () => (Math.random() * 100).toFixed(1)); // Random float values (0-100)
                return ml_inference_input_tensor(dtype, values.map(Number), shape);
        }

        async function main() {
                const feeds_array = Array.from({ length: 25 }, () => ({
                        input: randomTensor("float32", [1, 3])
                }));
                for (let i = 0; i < feeds_array.length; i++) {
                        let feeds = feeds_array[i];
                        console.log("feed:", feeds.input.cpuData);
                        const lr = await logisticRegression(feeds);
                        const xgb = await xgBoost(feeds);
                        console.log("Logistic Regression:", lr);
                        console.log("XGBoost:", xgb);
                }
        }
        main()
});