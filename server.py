from fastapi import FastAPI, WebSocket
from pydantic import BaseModel
import numpy as np
import joblib
import asyncio
import onnx
import skl2onnx
from skl2onnx.common.data_types import FloatTensorType
from sklearn.linear_model import LogisticRegression
import io

app = FastAPI()
model = None  # Global model storage

class TrainData(BaseModel):
    X: list
    y: list

async def train_model(websocket: WebSocket, X_train: np.ndarray, y_train: np.ndarray):
    global model
    model = LogisticRegression()

    await websocket.send_text("Training started...")

    # Simulating step-wise training progress
    for i in range(1, 6):
        await asyncio.sleep(1)
        await websocket.send_text(f"Training progress: {i * 20}%")

    # Train model
    model.fit(X_train, y_train)
    joblib.dump(model, "model.pkl")
    await websocket.send_text("Training completed!")

    # Convert to ONNX
    await websocket.send_text("Converting to ONNX format...")
    initial_type = [("input", FloatTensorType([None, X_train.shape[1]]))]
    onnx_model = skl2onnx.convert_sklearn(model, initial_types=initial_type)

    # Serialize ONNX model to bytes
    model_bytes = io.BytesIO()
    model_bytes.write(onnx_model.SerializeToString())
    model_bytes.seek(0)

    await websocket.send_text("Sending ONNX model...")

    # Send ONNX model as binary data via WebSocket
    await websocket.send_bytes(model_bytes.getvalue())

@app.websocket("/train")
async def websocket_train(websocket: WebSocket):
    await websocket.accept()
    
    try:
        data = await websocket.receive_json()
        X_train = np.array(data["X"])
        y_train = np.array(data["y"])

        await train_model(websocket, X_train, y_train)

    except Exception as e:
        await websocket.send_text(f"Error: {str(e)}")

    finally:
        await websocket.close()
