from pydantic import BaseModel, Field
from typing import List, Optional

class SimulationRequest(BaseModel):
    ciclo_productivo_id: int
    arbol_origen_id: int = Field(..., description="ID del Paciente Cero")
    max_distancia_m: Optional[float] = Field(20.0, description="Radio de contagio en metros")

class GraphNode(BaseModel):
    id: int
    label: str
    estado_vital: str
    lat: Optional[float]
    lng: Optional[float]
    distancia_al_origen_m: Optional[float] = None
    centralidad: float = 0.0
    es_paciente_cero: bool = False
    en_riesgo: bool = False

class GraphEdge(BaseModel):
    from_node: int = Field(..., alias="from")
    to_node: int = Field(..., alias="to")
    distancia_m: float
    probabilidad: float
    tipo_contacto: str
    en_camino_critico: bool = False

    class Config:
        populate_by_name = True

class SimulationResponse(BaseModel):
    success: bool
    ciclo_productivo_id: int
    paciente_cero_id: int
    total_nodos: int
    total_en_riesgo: int
    arboles_criticos_ids: List[int]
    nodes: List[GraphNode]
    edges: List[GraphEdge]  