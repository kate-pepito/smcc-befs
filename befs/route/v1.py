
import secrets
from typing import Dict
from fastapi import APIRouter, Depends, Request, WebSocket, WebSocketDisconnect
from fastapi.responses import JSONResponse
from redis import Redis

from befs.redis import get_redis
from befs.route import middleware
from befs.route.responses import TrainCreateSessionRequest, TrainCreateSessionResponse, TrainingStatesResponse
from befs.train import LogisticRegressionTrainer

router = APIRouter()
router.prefix = "/v1"

@router.post("/train/create")
async def create_training_session(data: TrainCreateSessionRequest, request: Request, redis: Redis = Depends(get_redis)):
    session_token = await redis.get(f"training_session:{data.username}_{data.session_key}")
    if session_token is not None:
        return JSONResponse(TrainCreateSessionResponse(session_token=session_token), 200)
    session_token = secrets.token_hex(16)
    # Initialize training state
    training_classes: Dict[str, LogisticRegressionTrainer] = request.app.training_classes
    training_classes[session_token] = LogisticRegressionTrainer(session_id=data.session_key, username=data.username, token=session_token, redis=redis)
    train_key = f"training_session:{data.username}_{data.session_key}"
    await redis.set(train_key, session_token)
    return JSONResponse(TrainCreateSessionResponse(session_token=session_token), 201)


@router.post("/train/destroy")
async def destroy_training_session(data: TrainCreateSessionRequest, request: Request, redis: Redis = Depends(get_redis)):
    session_token = await redis.get(f"training_session:{data.username}_{data.session_key}")
    if session_token is not None:
        train_key = f"training_session:{data.username}_{data.session_key}"
        await redis.delete(train_key)
        training_classes: Dict[str, LogisticRegressionTrainer] = request.app.training_classes
        if session_token in training_classes.keys():
            del training_classes[session_token]
    return JSONResponse({"success": True}, 200)

@router.websocket("/train")
async def websocket_endpoint(websocket: WebSocket):
    api_key = websocket.query_params.get("api_key")
    session_token = websocket.query_params.get("token")
    if not await middleware.check_api_key(websocket, api_key):
        return

    trainer = middleware.get_trainer_class(websocket, session_token)
    if trainer is None:
        await websocket.send_json(TrainingStatesResponse(connection="disconnected", state="error", progress=0.0, error="Training Session not yet initiated"))
        await websocket.close()
    await websocket.accept()
    try:
        trainer.connect(websocket)
        await trainer.websocket_loop()
    except WebSocketDisconnect:
        print(f"WebSocket disconnected: {trainer.session_id}")
        await trainer.disconnect()
    except Exception as e:
        trainer.state.status = "error"
        trainer.state.error = str(e)
        await trainer.update_state()