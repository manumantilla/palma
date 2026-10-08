from fastapi import APIRouter, HTTPException, Depends
from sqlalchemy.orm import Session
from sqlalchemy import text

from app.core.database import get_db
from app.schemas.graph import SimulationRequest, SimulationResponse
from app.algorithms.graphs.epidemiology import TreeEpidemiologyGraph

api_router = APIRouter()  
import time



@api_router.post("/graphs/simulate-contagion", response_model=SimulationResponse)
def simulate_contagion(                       # ← sync, not async
    payload: SimulationRequest,
    db: Session = Depends(get_db),            # ← sync Session
):
    t0 = time.perf_counter()
    sql_nodos = text("""
        SELECT id, codigo_unico, estado_vital,
               ST_X(coordenada_precision) AS lng,
               ST_Y(coordenada_precision) AS lat
        FROM arboles
        WHERE ciclo_productivo_id = :ciclo_id AND deleted_at IS NULL
    """)
    res_nodos = db.execute(sql_nodos, {"ciclo_id": payload.ciclo_productivo_id})  # no await
    nodes = [dict(row._mapping) for row in res_nodos.fetchall()]
    t1 = time.perf_counter()
    print(f"Query nodos: {(t1-t0)*1000:.0f} ms · {len(nodes)} nodos")

    if not nodes:
        raise HTTPException(status_code=404, detail="No hay árboles registrados para este ciclo.")

    sql_aristas = text("""
        SELECT e.arbol_origen_id, e.arbol_destino_id, e.distancia_metros,
               e.probabilidad_contagio_base, e.tipo_contacto
        FROM arboles_red_vecindad e
        INNER JOIN arboles a ON a.id = e.arbol_origen_id
        WHERE a.ciclo_productivo_id = :ciclo_id AND a.deleted_at IS NULL
    """)
    res_aristas = db.execute(sql_aristas, {"ciclo_id": payload.ciclo_productivo_id})  # no await
    edges = [dict(row._mapping) for row in res_aristas.fetchall()]

    t2 = time.perf_counter()
    print(f" Query aristas: {(t2-t1)*1000:.0f} ms · {len(edges)} aristas")

    try:
        engine = TreeEpidemiologyGraph()
        engine.build_graph(nodes, edges)
        t3 = time.perf_counter()
        print(f"build_graph: {(t3-t2)*1000:.0f} ms")
        res = engine.run_simulation(payload.arbol_origen_id, payload.max_distancia_m)
        t4 = time.perf_counter()
        print(f"run_simulation: {(t4-t3)*1000:.0f} ms")

        print(f" TOTAL: {(t4-t0)*1000:.0f} ms")
        return SimulationResponse(
            success=True,
            ciclo_productivo_id=payload.ciclo_productivo_id,
            paciente_cero_id=payload.arbol_origen_id,
            total_nodos=res["total_nodos"],
            total_en_riesgo=res["total_en_riesgo"],
            arboles_criticos_ids=res["arboles_criticos_ids"],
            nodes=res["nodes"],
            edges=res["edges"],
        )
    except ValueError as ve:
        raise HTTPException(status_code=400, detail=str(ve))
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Error interno: {str(e)}")