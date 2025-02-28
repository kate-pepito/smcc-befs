from datetime import datetime
import secrets
from typing import Dict
from fastapi import APIRouter,  Request, WebSocket, WebSocketDisconnect
from befs.http_request import create_train_session_api, get_train_session, get_train_sessions, invalidate_train_session, invalidate_train_session_token, validate_train_session

from befs.route import middleware
from befs.route.responses import InvalidateSessionRequest, SessionValidateRequest, SessionValidateResponse, TrainCreateSessionPost, TrainCreateSessionRequest, TrainCreateSessionResponse, TrainDestroySessionResponse, TrainSessionsResponse, TrainingStatesResponse
from befs.train import BaseMLTrainer, LogisticRegressionTrainer, XGBClassifierTrainer

router = APIRouter()
router.prefix = "/v1"

@router.post("/train/create", response_model=TrainCreateSessionResponse)
async def create_training_session(data: TrainCreateSessionRequest, request: Request):
    resp = await get_train_session(data)
    if resp is not None and resp.session_token is not None:
        return TrainCreateSessionResponse(session_token=str(resp.session_token))
    session_token = secrets.token_hex(16)
    # Initialize training state
    training_classes: Dict[str, BaseMLTrainer] = request.app.training_classes
    TrainerClass = LogisticRegressionTrainer if data.algo == "Logistic Regression" else XGBClassifierTrainer
    training_classes[str(session_token)] = TrainerClass(
        session_id=data.session_key,
        username=data.username,
        token=str(session_token)
    )
    await create_train_session_api(TrainCreateSessionPost(**data.model_dump(), token=str(session_token)))
    return TrainCreateSessionResponse(session_token=str(session_token))

@router.get("/validate/session", response_model=SessionValidateResponse)
async def validate_session(request: Request):
    params = request.query_params.items()
    param_dict = {p[0]:p[1] for p in params}
    data = SessionValidateRequest(**param_dict)
    resp = await validate_train_session(data)
    if not resp or not resp.valid:
        return SessionValidateResponse(valid=False)
    training_classes: Dict[str, BaseMLTrainer] = request.app.training_classes
    is_valid = data.token in training_classes.keys()
    if not is_valid:
        await invalidate_train_session(data)
    return SessionValidateResponse(valid=is_valid)

@router.get("/train/sessions", response_model=TrainSessionsResponse)
async def get_training_sessions(request: Request):
    resp = await get_train_sessions()
    keys = resp.data if resp is not None else []
    training_classes: Dict[str, BaseMLTrainer] = request.app.training_classes
    data = []
    for tck in keys:
        if tck in training_classes.keys():
            username = training_classes[str(tck)].username
            session_key = training_classes[str(tck)].session_id
            algo = training_classes[str(tck)].algo
            started = str(training_classes[str(tck)].state.started_at)
            data.append([username, session_key, algo, started])
        else:
            await invalidate_train_session_token(InvalidateSessionRequest(token=tck))
    data.sort(key=lambda x: datetime.fromisoformat(x[3]))
    return TrainSessionsResponse(data=data)

@router.post("/train/destroy", response_model=TrainDestroySessionResponse)
async def destroy_training_session(data: TrainCreateSessionRequest, request: Request):
    try:
        resp = await get_train_session(data)
        session_token = resp.session_token if resp is not None else None
        if session_token is not None:
            await invalidate_train_session_token(InvalidateSessionRequest(token=session_token))
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