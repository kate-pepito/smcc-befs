import redis.asyncio as redis
from befs.config import settings

# Dependency for Redis connection
async def get_redis():
    return redis.Redis.from_url(settings.REDIS_URL, decode_responses=True)