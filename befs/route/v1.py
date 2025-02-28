from datetime import datetime
import secrets
from typing import Dict
from fastapi import APIRouter, Depends, Request, WebSocket, WebSocketDisconnect
from redis import Redis

from befs.redis import get_redis
from befs.route import middleware
from befs.route.responses import SessionValidateRequest, SessionValidateResponse, TrainCreateSessionRequest, TrainCreateSessionResponse, TrainDestroySessionResponse, TrainSessionsResponse, TrainingStatesResponse
from befs.train import BaseMLTrainer, LogisticRegressionTrainer, XGBClassifierTrainer

router = APIRouter()
router.prefix = "/v1"

@router.post("/train/create", response_model=TrainCreateSessionResponse)
async def create_training_session(data: TrainCreateSessionRequest, request: Request, redis: Redis = Depends(get_redis)):
    session_token = await redis.get(f"training_session:{data.username}::{data.session_key}")
    if session_token is not None:
        return TrainCreateSessionResponse(session_token=str(session_token))
    session_token = secrets.token_hex(16)
    # Initialize training state
    training_classes: Dict[str, BaseMLTrainer] = request.app.training_classes
    TrainerClass = LogisticRegressionTrainer if data.algo == "Logistic Regression" else XGBClassifierTrainer
    training_classes[str(session_token)] = TrainerClass(
        session_id=data.session_key,
        username=data.username,
        token=str(session_token),
        redis=redis,
    )
    train_key = f"training_session:{data.username}::{data.session_key}"
    await redis.set(train_key, str(session_token))
    return TrainCreateSessionResponse(session_token=str(session_token))

@router.get("/validate/session", response_model=SessionValidateResponse)
async def validate_session(request: Request, redis: Redis = Depends(get_redis)):
    params = request.query_params.items()
    param_dict = {p[0]:p[1] for p in params}
    data = SessionValidateRequest(**param_dict)
    session_token = await redis.get(f"training_session:{data.username}::{data.session_key}")
    if session_token is None:
        return SessionValidateResponse(valid=False)
    training_classes: Dict[str, BaseMLTrainer] = request.app.training_classes
    is_valid = session_token == data.token and session_token in training_classes.keys()
    return SessionValidateResponse(valid=is_valid)

@router.get("/train/sessions", response_model=TrainSessionsResponse)
async def get_training_sessions(request: Request, redis: Redis = Depends(get_redis)):
    keys = await redis.keys()
    tsl = len("training_session:")
    training_classes: Dict[str, BaseMLTrainer] = request.app.training_classes
    data = []
    for k in keys:
        tck = await redis.get(k)
        if tck in training_classes.keys():
            e1 = str(k).find('::')
            s2 = int(str(k).find('::')+2)
            algo = training_classes[str(tck)].algo
            started = str(training_classes[str(tck)].state.started_at)
            data.append([k[tsl:e1], k[s2:], algo, started])
        else:
            await redis.delete(k)
    data.sort(key=lambda x: datetime.fromisoformat(x[3]))
    return TrainSessionsResponse(data=data)

@router.post("/train/destroy", response_model=TrainDestroySessionResponse)
async def destroy_training_session(data: TrainCreateSessionRequest, request: Request, redis: Redis = Depends(get_redis)):
    try:
        session_token = await redis.get(f"training_session:{data.username}::{data.session_key}")
        if session_token is not None:
            train_key = f"training_session:{data.username}::{data.session_key}"
            await redis.delete(train_key)
            training_classes: Dict[str, LogisticRegressionTrainer] = request.app.training_classes
            if str(session_token) in training_classes.keys():
                del training_classes[str(session_token)]
                return TrainDestroySessionResponse(success=True, detail=f"{str(session_token)} session deleted")
            else:
                raise Exception("No Training Session Found")
        else:
            raise Exception("No Training Session Found")
    except Exception as e:
        return TrainDestroySessionResponse(success=False, detail=str(e))

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