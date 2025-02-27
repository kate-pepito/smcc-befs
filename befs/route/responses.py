from datetime import datetime
from typing import Any, List, Literal, Optional, Union
from pydantic import BaseModel

class TrainCreateSessionRequest(BaseModel):
    username: str
    session_key: str

class TrainCreateSessionResponse(BaseModel):
    session_token: str

class MLModelMetadata(BaseModel):
    algo: Literal["Logistic Regression", "XGBoost Classifier"]
    size: float
    create_at: datetime

class DatasetMetadata(BaseModel):
    filename: str
    size: str
    rows: int
    columns: int

class TrainingStatesResponse(BaseModel):
    connection: Literal["connected", "disconnected"]
    status: Literal["idle", "training", "completed", "error"]
    started_at: datetime
    token: str
    session_id: str
    username: str
    progress: float
    ended_at: Optional[datetime]
    algo: Literal["Logistic Regression", "XGBoost Classifier"]
    training_start_time: Optional[float]
    training_end_time: Optional[float]
    last_training_time: Optional[float]
    dataset: Optional[DatasetMetadata]
    column_names: Optional[List[str]]
    features: List[str]
    target: List[str]
    valid_parameters: List[str]
    hyperparameters: dict
    test_size: float
    random_state: int
    scaler: dict
    model: Optional[MLModelMetadata]
    metrics: Optional[Any]
    error: Optional[str]

class CommandRequest(BaseModel):
    action: str
    data: Union[str,dict,list,float,int]

class SaveMLModelResponse(BaseModel):
    state: Literal["save_start", "save_end", "save_failed"]