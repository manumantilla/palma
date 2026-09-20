from pydantic_settings import BaseSettings
from pydantic import computed_field

class Settings(BaseSettings):
    PROJECT_NAME: str = "AgroAnalytics Python Engine"
    VERSION: str = "1.0.0"
    API_V1_STR: str = "/api/v1"
    ENVIRONMENT: str = "development"
    DEBUG: bool = True

    # Configuracion db
    DB_HOST: str = "pgsql"
    DB_PORT: int = 5432
    DB_USERNAME: str = "sail"
    DB_PASSWORD: str = "password"
    DB_DATABASE: str = "laravel"

    @computed_field
    @property
    def SQLALCHEMY_DATABASE_URI(self) -> str:
        return f"postgresql://{self.DB_USERNAME}:{self.DB_PASSWORD}@{self.DB_HOST}:{self.DB_PORT}/{self.DB_DATABASE}"
        
    class Config:
        env_file = ".env"
        case_sensitive = True

settings = Settings()
