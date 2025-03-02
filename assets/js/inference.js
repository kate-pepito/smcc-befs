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
    async function preprocessInput(input, scalerParams) {
        
        const mean = scalerParams.mean;
        const scale = scalerParams.scale;
    
        let scaledInput = input.map((value, index) => (value - mean[index]) / scale[index]);
    
        return scaledInput;
    }

    async function ml_inference_session(model_path = '/inference/logistic_regression.onnx') {
        try {
            url_model = `${BASE_URL || window.location.origin}${model_path}`;
            const session = await ort.InferenceSession.create(url_model);
            return {
                session,
                inputNames: session.inputNames,
                outputNames: session.outputNames
            };
        } catch (e) {
            console.warn("ERROR:", e)
            return {
                session: null,
                inputNames: [],
                outputNames: [],
                error: `failed to load ONNX model session: ${e}.`
            }
        }
    }
    
    async function ml_inference_input_tensor(dtype = 'float32', inputDataArray = [0]) {
        const DTypeClass = Object.keys(window.DTYPES).includes(dtype) ? window.DTYPES[dtype] : null;
        if (!DTypeClass) {
            alert(`input datatype ${dtype} is not a valid type`);
            return;
        }
        const data = new DTypeClass(inputDataArray);
        const tensor = new ort.Tensor(dtype, data, [1, inputDataArray.length]);
        return tensor;
    }
    
    async function ml_inference_input_tensor_with_scaler(dtype = 'float32', inputDataArray = [0], scalerParams = {scale:[], mean:[]}) {
        const DTypeClass = Object.keys(window.DTYPES).includes(dtype) ? window.DTYPES[dtype] : null;
        if (!DTypeClass) {
            alert(`input datatype ${dtype} is not a valid type`);
            return;
        }
        const inputData = await preprocessInput(inputDataArray, scalerParams)
        const data = new DTypeClass(inputData);
        const tensor = new ort.Tensor(dtype, data, [1, inputData.length]);
        return tensor;
    }
    
    
    async function ml_inference_run(session, feeds = {}, output_label = ['output_label']) {
        try {
            const results = await session.run(feeds, output_label)
            return results
        } catch (e) {
            return {
                error: e
            };
        }
    }
    window.ml_inference_session = ml_inference_session;
    window.ml_inference_input_tensor = ml_inference_input_tensor;
    window.ml_inference_input_tensor_with_scaler = ml_inference_input_tensor_with_scaler;
    window.ml_inference_run = ml_inference_run;
});
