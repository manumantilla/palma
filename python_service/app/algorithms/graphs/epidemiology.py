import igraph as ig
import numpy as np
from typing import List, Dict, Any


class TreeEpidemiologyGraph:
    def __init__(self):
        self.graph = None
        self.node_ids = []           # índice → id original
        self.id_to_idx = {}          # id original → índice igraph
        self.node_meta = {}          # id → {codigo, estado, lat, lng}
        self.edge_meta = {}          # (u_idx, v_idx) → {distancia_m, prob, tipo}

    # ─────────────────────────────────────────────────────────────
    def build_graph(self, nodes: List[Dict[str, Any]], edges: List[Dict[str, Any]]):
        # 1. Indexar nodos
        self.node_ids = [n["id"] for n in nodes]
        self.id_to_idx = {n["id"]: i for i, n in enumerate(nodes)}
        self.node_meta = {
            n["id"]: {
                "codigo": n["codigo_unico"],
                "estado": n["estado_vital"],
                "lat":    n["lat"],
                "lng":    n["lng"],
            }
            for n in nodes
        }

        # 2. Preparar aristas
        edge_list, weights = [], []
        self.edge_meta = {}

        for e in edges:
            u = self.id_to_idx.get(e["arbol_origen_id"])
            v = self.id_to_idx.get(e["arbol_destino_id"])
            if u is None or v is None:
                continue

            dist = float(e["distancia_metros"])
            prob = float(e["probabilidad_contagio_base"])
            peso = dist * (1.0 - prob + 0.001)

            edge_list.append((u, v))
            weights.append(peso)
            self.edge_meta[(u, v)] = {
                "distancia_m":   dist,
                "probabilidad":  prob,
                "tipo_contacto": e["tipo_contacto"],
            }

        # 3. Construir grafo
        # OJO: el original usaba nx.DiGraph() → directed=True.
        # Si el dataset de vecindad ya tiene ambas direcciones,
        # cámbialo a directed=False y ganas ~30% extra.
        n = len(nodes)
        self.graph = ig.Graph(n=n, edges=edge_list, directed=True)
        self.graph.es["weight"] = weights

    # ─────────────────────────────────────────────────────────────
    def run_simulation(self, paciente_cero_id: int, max_distancia_m: float):
        if paciente_cero_id not in self.id_to_idx:
            raise ValueError(
                f"El árbol {paciente_cero_id} no pertenece al grafo de este ciclo."
            )

        origen_idx = self.id_to_idx[paciente_cero_id]
        n = self.graph.vcount()

        # ── 1. Caminos más cortos desde el paciente cero (C puro) ──
        # get_shortest_paths devuelve la lista de vértices del camino hacia cada target
        paths = self.graph.get_shortest_paths(
            origen_idx,
            to=range(n),
            weights="weight",
            output="vpath",
        )

        # ── 2. Sumar distancia REAL (metros) a lo largo del camino ──
        nodos_en_riesgo   = {}
        aristas_en_camino = set()

        for target_idx, path in enumerate(paths):
            if target_idx == origen_idx or not path:
                continue

            dist_real = 0.0
            for i in range(len(path) - 1):
                u, v = path[i], path[i + 1]
                meta = self.edge_meta.get((u, v)) or self.edge_meta.get((v, u))
                if meta:
                    dist_real += meta["distancia_m"]

            if dist_real <= max_distancia_m:
                target_id = self.node_ids[target_idx]
                nodos_en_riesgo[target_id] = round(dist_real, 2)
                for i in range(len(path) - 1):
                    aristas_en_camino.add((path[i], path[i + 1]))

        # ── 3. Betweenness — AQUÍ estaba el cuello de botella ──
        # igraph lo hace en C, ~100x más rápido que NetworkX.
        betweenness = self.graph.betweenness(weights="weight", directed=True)

        # Normalizar a rango 0..1 (igraph ya normaliza pero por seguridad)
        max_b = max(betweenness) if betweenness else 1.0
        if max_b > 0:
            betweenness = [b / max_b for b in betweenness]

        criticos_idx = sorted(
            range(n), key=lambda i: betweenness[i], reverse=True
        )[:5]
        criticos_ids = [self.node_ids[i] for i in criticos_idx]

        # ── 4. Respuesta ──
        output_nodes = []
        for idx, node_id in enumerate(self.node_ids):
            meta = self.node_meta[node_id]
            output_nodes.append({
                "id":                     node_id,
                "label":                  meta["codigo"],
                "estado_vital":           meta["estado"],
                "lat":                    meta["lat"],
                "lng":                    meta["lng"],
                "distancia_al_origen_m":  nodos_en_riesgo.get(node_id),
                "centralidad":            round(float(betweenness[idx]), 4),
                "es_paciente_cero":       node_id == paciente_cero_id,
                "en_riesgo":              node_id in nodos_en_riesgo,
            })

        output_edges = []
        for (u_idx, v_idx), meta in self.edge_meta.items():
            output_edges.append({
                "from":              self.node_ids[u_idx],
                "to":                self.node_ids[v_idx],
                "distancia_m":       meta["distancia_m"],
                "probabilidad":      meta["probabilidad"],
                "tipo_contacto":     meta["tipo_contacto"],
                "en_camino_critico": (u_idx, v_idx) in aristas_en_camino,
            })

        return {
            "total_nodos":         n,
            "total_en_riesgo":     len(nodos_en_riesgo),
            "arboles_criticos_ids": criticos_ids,
            "nodes":               output_nodes,
            "edges":               output_edges,
        }