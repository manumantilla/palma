from fastapi import FastAPI, Depends, HTTPException
from sqlalchemy.orm import Session
from sqlalchemy import text
from app.core.config import settings
from app.core.database import get_db
from app.api.v1.router import api_router
from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware  
app = FastAPI(
    title=settings.PROJECT_NAME,
    version=settings.VERSION,
    openapi_url=f"{settings.API_V1_STR}/openapi.json"
)

origins = [
    "http://localhost",
    "http://localhost:80",
    "http://localhost:8000",
    "http://127.0.0.1",
]

app.add_middleware(
    CORSMiddleware,
    allow_origins=[
        "http://localhost",
        "http://localhost:80",
        "http://localhost:8000",
        "http://localhost:8001",
        "http://127.0.0.1",
        "http://127.0.0.1:8001",
    ],
    allow_credentials=False,
    allow_methods=["*"],
    allow_headers=["*"],
)
@app.get("/health", tags=["Health"])
def health_check(db: Session = Depends(get_db)):
    try:
        postgis_version = db.execute(text("SELECT PostGIS_Full_Version();")).scalar()
        return {
            "status": "ok",
            "service": settings.PROJECT_NAME,
            "database": "connected",
            "postgis": postgis_version
        }
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Database connection error: {str(e)}")

app.include_router(api_router, prefix=settings.API_V1_STR)