window.DTYPES = {
    bool: Uint8Array,
    float16: Uint16Array,
    float32: Float32Array,
    float64: Float64Array,
    int16: Int16Array,
    int32: Int32Array,
    int4: Int8Array,
    int64: BigInt64Array,
    int8: Int8Array,
    uint16: Uint16Array,
    uint32: Uint32Array,
    uint4: Uint8Array,
    uint64: BigUint64Array,
    uint8: Uint8Array,
    string: Array,
};
document.addEventListener("DOMContentLoaded", function () {
    async function ml_inference_session(model_path = '/inference/logistic_regression.onnx') {
        try {
            const session = await ort.InferenceSession.create(`${BASE_URL || window.location.origin}${model_path}`);
            return {
                session,
                inputNames: session.inputNames,
                outputNames: session.outputNames.filter((v) => v !== 'output_probability')
            };
        } catch (e) {
            return {
                session: null,
                inputNames: [],
                outputNames: [],
                error: `failed to load ONNX model session: ${e}.`
            }
        }
    }
    
    function ml_inference_input_tensor(dtype = 'float32', inputDataArray = [0], dims = [1, 1]) {
        const DTypeClass = Object.keys(DTYPES).includes(dtype) ? DTYPES[dtype] : null;
        if (!DTypeClass) {
            alert(`input datatype ${dtype} is not a valid type`);
            return;
        }
        const data = new DTypeClass(inputDataArray);
        const tensor = new ort.Tensor(dtype, data, !dims ? [1, dataA.length] : dims);
        return tensor;
    }
    
    async function ml_inference_run(session, feeds = {}, output_label = ['output_label']) {
        try {
            const results = await session.run(feeds, ['output_label'])
            return Object.fromEntries(output_label.map((label) => [label, results[label].data]));
        } catch (e) {
            return {
                error: e
            };
        }
    }
    window.ml_inference_session = ml_inference_session;
    window.ml_inference_input_tensor = ml_inference_input_tensor;
    window.ml_inference_run = ml_inference_run;
});
