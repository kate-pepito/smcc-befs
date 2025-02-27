
import asyncio
from datetime import datetime, timezone
import io
import json
import time
from typing import Any, List, Literal, Union
import pandas as pd
from fastapi import WebSocket
from redis import Redis
from sklearn.pipeline import Pipeline
from befs.route.responses import CommandRequest, DatasetMetadata, MLModelMetadata, SaveMLModelResponse, TrainingStatesResponse
from sklearn.linear_model import LogisticRegression
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import StandardScaler
from skl2onnx import convert_sklearn, update_registered_converter
from skl2onnx.common.data_types import FloatTensorType
from xgboost import XGBClassifier
from skl2onnx.common.shape_calculator import (
    calculate_linear_classifier_output_shapes,
)
from onnxmltools.convert.xgboost.operator_converters.XGBoost import convert_xgboost

class BaseMLTrainer:
    def __init__(self, session_id: str, username: str, token: str, redis: Redis, valid_hyperparameters: List[str], algo: Literal["Logistic Regression", "XGBoost Classifier"]):
        self.valid_hyperparameters = valid_hyperparameters
        self.algo = algo
        self.session_id = session_id
        self.username = username
        self.token = token
        self.model = None
        self.saved_model = None
        self.websocket = None
        self.redis = redis
        self.dataset = None
        self.features = []
        self.target = []
        self.hyperparameters = {}
        self.test_size = 0.2
        self.random_state = 42
        self.scaler_class = {}
        self.state = TrainingStatesResponse(
            connection="connected",
            status="idle",
            progress=0.0,
            algo=algo,
            session_id=session_id,
            username=username,
            token=token,
            scaler={},
            valid_hyperparameters=valid_hyperparameters,
            hyperparameters={},
            column_names=[],
            features=[],
            target=[],
            test_size=0.2,
            random_state=42,
            started_at=datetime.now(timezone.utc)
        )
    
    def connect(self, websocket: WebSocket):
        self.websocket = websocket
        self.state["connection"] = "connected"

    async def update_state(self):
        redis_key = f"session:{self.username}_{self.session_id}"
        await self.redis.hset(redis_key, mapping=self.state.model_dump())
        self.send_updates()

    async def send_updates(self):
        if self.websocket and self.websocket.application_state == 1:
            await self.websocket.send_json(self.state.model_dump())

    async def set_dataset(self, filename: str, size: str, dataset: Union[list, pd.DataFrame]):
        try:
            if type(dataset) is list:
                if isinstance(dataset, pd.DataFrame):
                    self.dataset = dataset
                elif len(dataset) > 0 and isinstance(dataset[0], dict):
                    self.dataset = pd.DataFrame(dataset)
                elif len(dataset) > 0:
                    column_names = [f"column_{i+1}" for i in range(len(dataset[0]))] if dataset and isinstance(dataset[0], (list, tuple)) else ["value"]
                    dataset = {col: [row[i] for row in dataset] for i, col in enumerate(column_names)} if dataset and isinstance(dataset[0], (list, tuple)) else {"value": dataset}
                    self.dataset = pd.DataFrame(dataset)
                else:
                    raise Exception("Invalid Dataset!")
            else:
                raise Exception("Invalid Dataset!")
            self.state.column_names = list(self.dataset.columns)
            self.state.dataset = DatasetMetadata(filename=filename,size=size,columns=len(self.dataset.columns),rows=self.dataset.shape[0])
        except Exception as e:
            self.state.status = "error"
            self.state.error = str(e)
        finally:
            await self.update_state()

    async def set_features(self, *features):
        self.features = list(filter(lambda ft: ft in self.state.column_names, features))
        self.state.features = self.features
        await self.update_state()

    async def set_target(self, *target):
        self.target = list(filter(lambda tg: tg in self.state.column_names and tg not in self.features, target))
        self.state.target = self.target
        await self.update_state()

    async def set_hyperparameters(self, **hyperparameters):
        my_hyperparameters = {
            key: value for key, value in hyperparameters.items() if key in self.valid_hyperparameters and value is not None and value != ""
        }
        self.hyperparameters = my_hyperparameters
        self.state.hyperparameters = self.hyperparameters
        await self.update_state()
    
    async def set_test_size(self, test_size: float):
        self.test_size = test_size
        self.state.test_size = self.test_size
        await self.update_state()
    
    async def set_random_state(self, random_state: float):
        self.random_state = random_state
        self.state.random_state = self.random_state
        await self.update_state()

    async def _train(self, X_train, y_train, X_test, y_test) -> tuple:
        pass

    async def train(self) -> bool:
        if self.dataset is not None and len(self.features) > 0 and len(self.target) > 0 and self.state.status != "training":
            self.state.status = "training"
            self.state.training_start_time = time.time()
            self.state.training_end_time = None
            await self.update_state()

            try:
                X = self.dataset[self.features].values  # Features
                y = self.dataset[self.target].values   # Target

                # Train-test split
                X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=self.test_size, random_state=self.random_state)

                self._train(X_train, y_train, X_test, y_test)

                # Training complete
                self.state.status = "completed"
                self.state.training_end_time = time.time()
                self.state.last_training_time = self.state.training_end_time - self.state.training_start_time
                await self.update_state()

            except Exception as e:
                self.state.status = "error"
                self.state.error = str(e)
                await self.update_state()
            finally:
                return True
        return False
    
    async def save_model(self):
        if self.state.status == "completed":
            if hasattr(self, "_save_model") and callable(self._save_model):
                serialized_model = self._save_model()
                self.saved_model = serialized_model
                self.state.model = MLModelMetadata(algo=self.algo, created_at=datetime(timezone.utc), size=len(serialized_model))
            else:
                initial_type = [("input", FloatTensorType([None, len(self.features)]))]
                onx = convert_sklearn(self.model, initial_types=initial_type)
                serialized_model = onx.SerializeToString()
                self.saved_model = serialized_model
                self.state.model = MLModelMetadata(algo=self.algo, created_at=datetime(timezone.utc), size=len(serialized_model))
            await self.update_state()
            
    async def send_model(self):
        CHUNK_SIZE = 8 * 1024   # 8KB per chunk
        if self.saved_model is not None and self.state.model is not None:
            await self.websocket.send_json(SaveMLModelResponse(state="save_start"))
            model_to_upload = self.saved_model  # ONNX serialized model
            total_size = len(model_to_upload)
            num_chunks = (total_size // CHUNK_SIZE) + (1 if total_size % CHUNK_SIZE else 0)
            j = 0

            for i in range(0, total_size, CHUNK_SIZE):
                chunk = model_to_upload[i : i + CHUNK_SIZE]
                await self.websocket.send_bytes(chunk)
                j += 1
                await asyncio.sleep(0.01)
            if j == num_chunks:
                await self.websocket.send_json(SaveMLModelResponse(state="save_end"))
            else:
                await self.websocket.send_json(SaveMLModelResponse(state="save_failed"))
    
    async def run_command(self, command: CommandRequest):
        if command.action == "get_updates":
            await self.send_updates()
        elif command.action == "upload_dataset_start":
            self._dataset_chunk = []
            self._dataset_filename = command.data.filename
            self._dataset_size = command.data.size
        elif command.action == "upload_dataset":
            if hasattr(self, "_dataset_chunk") and isinstance(self._dataset_chunk, list):
                self._dataset_chunk.append(command.data)
        elif command.action == "upload_dataset_end":
            if hasattr(self, "_dataset_chunk") and isinstance(self._dataset_chunk, list):
                dataset_str = "".join(self._dataset_chunk)
                dfilename = self._dataset_filename
                dsize = self._dataset_size
                del self._dataset_filename
                del self._dataset_size
                del self._dataset_chunk
                if dataset_str.startswith("[") and dataset_str.endswith("]"):
                    dataset = json.dumps(dataset_str)
                else:
                    dataset = pd.read_csv(io.StringIO(dataset_str))
                self.set_dataset(dfilename, dsize, dataset)
        elif command.action == "set_features":
            features = command.data
            await self.set_features(*features)
        elif command.action == "set_target":
            target = command.data
            await self.set_target(*target)
        elif command.action == "set_test_size":
            test_size = None if command.data == "" or command.data is None or not command.data else command.data
            await self.set_test_size(test_size)
        elif command.action == "set_random_state":
            random_state = None if not isinstance(command.data, dict) else command.data
            await self.set_random_state(**random_state)
        elif command.action == "set_hyperparameters":
            hyperparameters = None if command.data == "" or command.data is None or not command.data else command.data
            await self.set_hyperparameters(**hyperparameters)
        elif command.action == "save_model":
            await self.save_model()
        elif command.action == "download_model":
            await self.send_model()
        else:
            await self.send_updates()

    async def websocket_loop(self):
        if self.websocket:
            while True:
                try:
                    message = await self.websocket.receive_json()
                    self.run_command(CommandRequest(**message))
                    asyncio.sleep(0.01)
                except Exception as e:
                    print(f"Error: {e}")
                    break  # Break loop on error or disconnect

    async def disconnect(self):
        self.state.connection = "disconnected"
        await self.update_state()
        self.websocket = None

class LogisticRegressionTrainer(BaseMLTrainer):
    def __init__(self, session_id: str, username: str, token: str, redis: Redis):
        super().__init__(session_id, username, token, redis, valid_hyperparameters = [
            "penalty", "dual", "tol", "C", "fit_intercept", "intercept_scaling",
            "class_weight", "random_state", "solver", "max_iter", "multi_class",
            "verbose", "warm_start", "n_jobs", "l1_ratio"
        ], algo = "Logistic Regression")
    
    async def _train(self, X_train, y_train, X_test, y_test):
        self.scaler_class = StandardScaler()
        self.model: Pipeline = Pipeline([("scaler", self.scaler_class), ("logreg", LogisticRegression(**self.hyperparameters))])
        self.model.fit(X_train, y_train)
        self.state.metrics = {
            "accuracy": self.model.score(X_test, y_test)
        }
        self.state.scaler =  {
            "mean": self.scaler_class.mean_.tolist(),
            "scale": self.scaler_class.scale_.tolist()
        }
    
    async def _save_model(self) -> Any:
        xgb_onnx = convert_sklearn(
            self.model,
            "pipeline_logreg",
            [("input", FloatTensorType([None, len(self.features)]))],
            target_opset={"": 17, "ai.onnx.ml": 3},
            options={id(self.model.steps[-1][1]): {"zipmap": True}}  # Enable probability scores
        )

        return xgb_onnx.SerializeToString()

class XGBClassifierTrainer(BaseMLTrainer):
    def __init__(self, session_id: str, username: str, token: str, redis: Redis):
        super().__init__(session_id, username, token, redis, valid_hyperparameters = [
           "n_estimators", "max_depth", "learning_rate", "verbosity", "objective",
            "booster", "tree_method", "gamma", "min_child_weight", "max_delta_step",
            "subsample", "colsample_bytree", "colsample_bylevel", "colsample_bynode",
            "reg_alpha", "reg_lambda", "scale_pos_weight", "base_score", "random_state",
            "missing", "importance_type", "grow_policy", "max_leaves", "max_bin",
            "eval_metric", "early_stopping_rounds", "use_label_encoder"
        ], algo = "XGBoost Classifier")

    async def _train(self, X_train, y_train, X_test, y_test):
        self.scaler_class = StandardScaler()
        self.model: Pipeline = Pipeline([("scaler", self.scaler_class), ("xgb", XGBClassifier(**self.hyperparameters))])
        self.model.fit(X_train, y_train)
        self.state.metrics = {
            "accuracy": self.model.score(X_test, y_test)
        }
        self.state.scaler =  {
            "mean": self.scaler_class.mean_.tolist(),
            "scale": self.scaler_class.scale_.tolist()
        }

    async def _save_model(self) -> Any:
        update_registered_converter(
            XGBClassifier,
            "XGBoostXGBClassifier",
            calculate_linear_classifier_output_shapes,
            convert_xgboost,
            options={"nocl": [True, False], "zipmap": [True, False, "columns"]},
        )
        xgb_onnx = convert_sklearn(
            self.model,
            "pipeline_xgboost",
            [("input", FloatTensorType([None, len(self.features)]))],
            target_opset={"": 17, "ai.onnx.ml": 3},
            options={id(self.model.steps[-1][1]): {"zipmap": True}}  # Enable probability scores
        )
        return xgb_onnx.SerializeToString()