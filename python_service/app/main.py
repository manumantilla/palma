from fastapi import FastAPI, Depends, HTTPException
from sqlalchemy.orm import Session
from sqlalchemy import text
from app.core.config import settings
from app.core.database import get_db
from app.api.v1.router import api_router

app = FastAPI(
    title=settings.PROJECT_NAME,
    version=settings.VERSION,
    openapi_url=f"{settings.API_V1_STR}/openapi.json"
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