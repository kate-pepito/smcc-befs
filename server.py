from contextlib import asynccontextmanager
from typing import Dict
from fastapi import FastAPI
from befs.config import settings
from befs.route import middleware, v1
from befs.train import BaseMLTrainer


@asynccontextmanager
async def lifespan(app: FastAPI):
    # on startup
    training_classes: Dict[str, BaseMLTrainer] = {}
    app.training_classes = training_classes
    yield  # Running server
    # on shutdown

app = FastAPI(lifespan=lifespan)

app.add_middleware(middleware.ProcessTimeMiddleware)

app.add_middleware(middleware.APIKeyMiddleware)

# Include API routes
app.include_router(v1.router, prefix="/api")

if __name__ == "__main__":
    import uvicorn
    uvicorn.run("server:app", host=settings.FASTAPI_SERVER_HOST, port=int(settings.FASTAPI_SERVER_PORT), reload=True)
