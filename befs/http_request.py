import io

import httpx

from befs.config import settings
from befs.route.responses import FileModelData, FileModelResponse, InvalidateSessionRequest, MLModelMetadata, SessionValidateRequest, SessionValidateResponse, TrainCreateSessionPost, TrainCreateSessionRequest, TrainCreateSessionResponse, TrainDestroySessionResponse, TrainSessionsGet, TrainingStatesResponse


async def http_get(url: str, params: dict = None):
    headers = {"Accept": "application/json"}
    async with httpx.AsyncClient(verify=False) as client:
        response = await client.get(url, params=params, headers=headers)
        return response.json()

async def http_post(url: str, data: dict = None):
    async with httpx.AsyncClient(verify=False) as client:
        response = await client.post(url, data=data)
        return response.json()

async def get_train_session(data: TrainCreateSessionRequest) -> TrainCreateSessionResponse:
    return await http_get(f"{settings.MAIN_BASE_URL}/api/get_train_session", data.model_dump())

async def create_train_session_api(data: TrainCreateSessionPost):
    return await http_post(f"{settings.MAIN_BASE_URL}/api/create_train_session", data.model_dump())

async def validate_train_session(data: SessionValidateRequest) -> SessionValidateResponse:
    return await http_get(f"{settings.MAIN_BASE_URL}/api/validate_session", data.model_dump())

async def invalidate_train_session(data: SessionValidateRequest) -> TrainDestroySessionResponse:
    return await http_post(f"{settings.MAIN_BASE_URL}/api/invalidate_session", data.model_dump())

async def invalidate_train_session_token(data: InvalidateSessionRequest) -> TrainDestroySessionResponse:
    return await http_post(f"{settings.MAIN_BASE_URL}/api/invalidate_session_token", data.model_dump())

async def get_train_sessions() -> TrainSessionsGet:
    return await http_get(f"{settings.MAIN_BASE_URL}/api/get_all_sessions")

async def upload_model_to_database(onnx_model: bytes, metadata: MLModelMetadata) -> FileModelResponse:
    file_data = io.BytesIO(onnx_model)  # Wrap bytes in a file-like object

    files = FileModelData(inference=(f"{metadata.filename}{metadata.file_extension}", file_data, "application/octet-stream"))
    
    async with httpx.AsyncClient() as client:
        response = await client.post(f"{settings.MAIN_BASE_URL}/api/model_upload", data=metadata.model_dump(), files=files.model_dump())
        return response.json()
    return FileModelResponse(success=False)

async def update_training_state(token: str, state: TrainingStatesResponse):
    return await http_post(f"{settings.MAIN_BASE_URL}/api/train_update?token={token}", data=state.model_dump())