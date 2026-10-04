import networkx as nx
from typing import List, Dict, Any

class TreeEpidemiologyGraph:
    def __init__(self):
        self.graph = nx.DiGraph()

    def build_graph(self, nodes: List[Dict[str, Any]], edges: List[Dict[str, Any]]):
        self.graph.clear()

        for n in nodes:
            self.graph.add_node(
                n["id"],
                codigo=n["codigo_unico"],
                estado=n["estado_vital"],
                lat=n["lat"],
                lng=n["lng"]
            )

        for e in edges:
            dist = float(e["distancia_metros"])
            prob = float(e["probabilidad_contagio_base"])
            peso = dist * (1.0 - prob + 0.001)

            self.graph.add_edge(
                e["arbol_origen_id"],
                e["arbol_destino_id"],
                weight=peso,
                distancia_m=dist,
                probabilidad=prob,
                tipo_contacto=e["tipo_contacto"]
            )

    def run_simulation(self, paciente_cero_id: int, max_distancia_m: float):
        if paciente_cero_id not in self.graph:
            raise ValueError(f"El árbol {paciente_cero_id} no pertenece al grafo de este ciclo.")

        distancias, caminos = nx.single_source_dijkstra(
            self.graph, source=paciente_cero_id, weight="weight"
        )
        centralidad = nx.betweenness_centrality(self.graph, weight="weight")

        criticos_ids = sorted(centralidad, key=centralidad.get, reverse=True)[:5]

        # Mapear distancias reales acumuladas
        nodos_en_riesgo = {}
        aristas_en_camino = set()

        for target_id, path in caminos.items():
            dist_real = 0.0
            for i in range(len(path) - 1):
                u, v = path[i], path[i+1]
                dist_real += self.graph[u][v]["distancia_m"]

            if dist_real <= max_distancia_m:
                nodos_en_riesgo[target_id] = round(dist_real, 2)
                for i in range(len(path) - 1):
                    aristas_en_camino.add((path[i], path[i+1]))

        # Construir listas para la respuesta JSON
        output_nodes = []
        for n_id, data in self.graph.nodes(data=True):
            en_riesgo = n_id in nodos_en_riesgo
            is_zero = (n_id == paciente_cero_id)
            
            output_nodes.append({
                "id": n_id,
                "label": data["codigo"],
                "estado_vital": data["estado"],
                "lat": data["lat"],
                "lng": data["lng"],
                "distancia_al_origen_m": nodos_en_riesgo.get(n_id),
                "centralidad": round(centralidad.get(n_id, 0.0), 4),
                "es_paciente_cero": is_zero,
                "en_riesgo": en_riesgo
            })

        output_edges = []
        for u, v, data in self.graph.edges(data=True):
            output_edges.append({
                "from": u,
                "to": v,
                "distancia_m": data["distancia_m"],
                "probabilidad": data["probabilidad"],
                "tipo_contacto": data["tipo_contacto"],
                "en_camino_critico": (u, v) in aristas_en_camino
            })

        return {
            "total_nodos": self.graph.number_of_nodes(),
            "total_en_riesgo": len(nodos_en_riesgo),
            "arboles_criticos_ids": criticos_ids,
            "nodes": output_nodes,
            "edges": output_edges
        }