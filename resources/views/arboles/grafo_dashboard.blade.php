<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>AgroGraph · Dashboard Epidemiológico</title>

<!-- Fuentes -->
<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@300;400;600;700;800&family=Fraunces:wght@700;900&display=swap" rel="stylesheet">

<!-- Librerías -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/d3/7.8.5/d3.min.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>

  :root {
      --bg: #f5f7f2;
      --surf: #ffffff;
      --card: #ffffff;
      --border: #dfe7dc;

      --g1: #16834a;
      --g2: #229653;
      --g3: #43b96d;
      --g-light: #eaf7ef;
      --g-soft: #f2faf5;

      --amber: #c98716;
      --amber-light: #fff7e5;

      --red: #d94a4a;
      --red-light: #fff0f0;

      --blue: #287db5;
      --blue-light: #edf7fd;

      --purple: #8055a6;
      --purple-light: #f5eff9;

      --soil: #8a6847;
      --soil-light: #f6f0e9;

      --text: #1e2b23;
      --text-soft: #435248;
      --muted: #718077;
      --label: #849188;

      --shadow-sm:
          0 1px 2px rgba(31, 55, 39, .05);

      --shadow:
          0 4px 14px rgba(31, 55, 39, .07);

      --shadow-lg:
          0 12px 35px rgba(31, 55, 39, .10);

      --mono: 'JetBrains Mono', monospace;
      --head: 'Fraunces', serif;

      --radius: 12px;
  }


  /* =========================================================
    RESET
    ========================================================= */

  * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
  }

  html {
      scroll-behavior: smooth;
  }

  body {
      font-family: var(--mono);
      background: var(--bg);
      color: var(--text);
      min-height: 100vh;
      overflow-x: hidden;

      /* Entrada suave de toda la aplicación */
      animation: pageEnter .55s ease-out;
  }


  /* =========================================================
    SCROLLBAR
    ========================================================= */

  *::-webkit-scrollbar {
      width: 6px;
      height: 6px;
  }

  *::-webkit-scrollbar-track {
      background: #eef2ed;
  }

  *::-webkit-scrollbar-thumb {
      background: #c6d2c8;
      border-radius: 10px;
  }

  *::-webkit-scrollbar-thumb:hover {
      background: var(--g2);
  }


  /* =========================================================
    HEADER
    ========================================================= */

  .hdr {
      background:
          linear-gradient(
              90deg,
              #ffffff 0%,
              #fbfdfb 65%,
              #f1f8f2 100%
          );

      border-bottom: 1px solid var(--border);

      padding: 13px 26px;

      display: flex;
      align-items: center;
      justify-content: space-between;

      position: relative;
      z-index: 20;

      box-shadow: var(--shadow-sm);
  }


  /* Línea agrícola animada debajo del header */

  .hdr::after {
      content: "";
      position: absolute;
      bottom: -1px;
      left: 0;

      width: 100%;
      height: 2px;

      background:
          linear-gradient(
              90deg,
              transparent,
              var(--g2),
              #77c98f,
              var(--g2),
              transparent
          );

      background-size: 200% 100%;

      animation: greenFlow 5s linear infinite;
  }


  /* Logo */

  .logo {
      font-family: var(--head);
      font-size: 20px;
      letter-spacing: 2px;
      color: var(--g1);

      position: relative;
  }

  .logo em {
      color: var(--soil);
      font-style: normal;
  }

  .logo-sub {
      font-size: 8px;
      color: var(--label);
      letter-spacing: 3px;
      margin-top: 1px;
  }


  /* =========================================================
    TABS
    ========================================================= */

  .tabs {
      display: flex;

      border-bottom: 1px solid var(--border);

      background: rgba(255, 255, 255, .92);

      overflow-x: auto;

      box-shadow: var(--shadow-sm);

      position: relative;
      z-index: 10;
  }

  .tab {
      padding: 10px 18px;

      font-size: 9px;
      font-family: var(--mono);
      letter-spacing: 1.5px;

      background: transparent;

      color: var(--label);

      border: none;
      border-bottom: 2px solid transparent;

      cursor: pointer;

      white-space: nowrap;

      transition:
          color .25s ease,
          background .25s ease,
          border-color .25s ease,
          transform .25s ease;
  }

  .tab:hover:not(.on) {
      color: var(--g1);
      background: var(--g-soft);
      transform: translateY(-1px);
  }

  .tab.on {
      color: var(--g1);

      border-bottom-color: var(--g1);

      background:
          linear-gradient(
              180deg,
              transparent,
              var(--g-light)
          );

      font-weight: 700;
  }


  /* =========================================================
    LAYOUT
    ========================================================= */

  .body {
      display: grid;

      grid-template-columns: 260px 1fr;

      height: calc(100vh - 88px);
  }

  .sidebar {
      border-right: 1px solid var(--border);

      background:
          linear-gradient(
              180deg,
              #ffffff,
              #f9fbf8
          );

      overflow-y: auto;

      display: flex;
      flex-direction: column;
      gap: 0;

      box-shadow: 3px 0 15px rgba(31, 55, 39, .035);

      position: relative;
      z-index: 5;
  }

  .main {
      overflow-y: auto;

      padding: 20px;

      background:
          radial-gradient(
              circle at 80% 10%,
              rgba(58, 155, 91, .035),
              transparent 28%
          ),
          var(--bg);
  }


  /* =========================================================
    CARDS
    ========================================================= */

  .card {
      background: var(--card);

      border: 1px solid var(--border);

      border-radius: var(--radius);

      padding: 16px;

      margin-bottom: 14px;

      box-shadow: var(--shadow-sm);

      transition:
          transform .25s ease,
          box-shadow .25s ease,
          border-color .25s ease;
  }

  .card:hover {
      transform: translateY(-2px);

      border-color: #cbdacb;

      box-shadow: var(--shadow);
  }

  .card-t {
      font-size: 9px;

      color: var(--label);

      letter-spacing: 2px;

      margin-bottom: 12px;

      display: flex;

      align-items: center;

      gap: 7px;
  }

  .card-t b {
      color: var(--g2);
  }


  /* =========================================================
    DIGITAL TWIN / CANVAS
    ========================================================= */

  .canvas {
      background: #ffffff;

      border: 1px solid var(--border);

      border-radius: var(--radius);

      overflow: hidden;

      position: relative;

      box-shadow: var(--shadow);

      transition:
          box-shadow .3s ease,
          border-color .3s ease;
  }

  .canvas:hover {
      border-color: #c9d9cb;

      box-shadow: var(--shadow-lg);
  }

  .canvas svg {
      display: block;
  }


  /*
    Decoración sutil de "campo"
  */

  .canvas::before {
      content: "";

      position: absolute;

      inset: 0;

      pointer-events: none;

      background:
          linear-gradient(
              90deg,
              rgba(39, 150, 83, .025) 1px,
              transparent 1px
          ),
          linear-gradient(
              rgba(39, 150, 83, .025) 1px,
              transparent 1px
          );

      background-size: 28px 28px;

      mask-image:
          linear-gradient(
              to bottom,
              black,
              transparent 85%
          );

      z-index: 0;
  }


  /* =========================================================
    CONTROLS
    ========================================================= */

  .ctrl {
      padding: 14px 16px;

      border-bottom: 1px solid #dfe7dc88;
  }

  .ctrl-title {
      font-size: 9px;

      color: var(--g1);

      letter-spacing: 2px;

      margin-bottom: 10px;

      font-weight: 700;

      display: flex;
      align-items: center;
      gap: 6px;
  }


  /* pequeño indicador vivo */

  .ctrl-title::before {
      content: "";

      width: 6px;
      height: 6px;

      border-radius: 50%;

      background: var(--g2);

      box-shadow:
          0 0 0 3px var(--g-light);

      animation: pulseGreen 2s infinite;
  }


  /* =========================================================
    BUTTONS
    ========================================================= */

  .btn {
      display: block;

      width: 100%;

      padding: 9px 12px;

      border-radius: 7px;

      border: 1px solid;

      font-family: var(--mono);

      font-size: 9px;

      letter-spacing: 1px;

      cursor: pointer;

      text-align: left;

      transition:
          transform .2s ease,
          background .2s ease,
          box-shadow .2s ease,
          border-color .2s ease;

      margin-bottom: 6px;

      font-weight: 600;

      position: relative;

      overflow: hidden;
  }


  /* efecto de brillo al pasar */

  .btn::after {
      content: "";

      position: absolute;

      top: 0;
      left: -100%;

      width: 60%;
      height: 100%;

      background:
          linear-gradient(
              90deg,
              transparent,
              rgba(255,255,255,.5),
              transparent
          );

      transition: left .45s ease;
  }

  .btn:hover::after {
      left: 130%;
  }

  .btn:hover {
      transform: translateY(-1px);
  }


  /* Verde */

  .btn-g {
      border-color: #16834a55;

      background: var(--g-soft);

      color: var(--g1);
  }

  .btn-g:hover {
      background: #e1f4e8;

      border-color: var(--g2);

      box-shadow:
          0 5px 14px rgba(22, 131, 74, .12);
  }


  /* Amarillo */

  .btn-a {
      border-color: #c9871655;

      background: var(--amber-light);

      color: var(--amber);
  }

  .btn-a:hover {
      background: #fff1cc;

      box-shadow:
          0 5px 14px rgba(201, 135, 22, .12);
  }


  /* Rojo */

  .btn-r {
      border-color: #d94a4a55;

      background: var(--red-light);

      color: var(--red);
  }

  .btn-r:hover {
      background: #ffe5e5;

      box-shadow:
          0 5px 14px rgba(217, 74, 74, .12);
  }


  /* Azul */

  .btn-b {
      border-color: #287db555;

      background: var(--blue-light);

      color: var(--blue);
  }

  .btn-b:hover {
      background: #e2f3fc;

      box-shadow:
          0 5px 14px rgba(40, 125, 181, .12);
  }


  /* Morado */

  .btn-p {
      border-color: #8055a655;

      background: var(--purple-light);

      color: var(--purple);
  }

  .btn-p:hover {
      background: #eee3f5;

      box-shadow:
          0 5px 14px rgba(128, 85, 166, .12);
  }


  /* =========================================================
    PILLS
    ========================================================= */

  .pill {
      font-size: 8px;

      padding: 3px 8px;

      border-radius: 10px;

      border: 1px solid;

      display: inline-flex;

      align-items: center;

      gap: 4px;

      font-family: var(--mono);

      background: #ffffff;

      box-shadow: var(--shadow-sm);
  }


  /* =========================================================
    STATS
    ========================================================= */

  .stat-row {
      display: flex;

      justify-content: space-between;

      padding: 6px 0;

      border-bottom: 1px solid #dfe7dc88;

      font-size: 9px;

      transition:
          background .2s ease,
          padding-left .2s ease;
  }

  .stat-row:hover {
      background: var(--g-soft);

      padding-left: 5px;
  }

  .stat-row .k {
      color: var(--label);
  }

  .stat-row .v {
      color: var(--g1);

      font-weight: 700;
  }


  /* =========================================================
    LEGEND
    ========================================================= */

  .legend-item {
      display: flex;

      align-items: center;

      gap: 6px;

      font-size: 8px;

      color: var(--label);

      margin-bottom: 5px;

      transition:
          color .2s ease,
          transform .2s ease;
  }

  .legend-item:hover {
      color: var(--text);

      transform: translateX(3px);
  }

  .legend-dot {
      width: 8px;
      height: 8px;

      border-radius: 50%;

      flex-shrink: 0;

      box-shadow:
          0 0 0 3px rgba(22, 131, 74, .06);
  }


  /* =========================================================
    INFO BOX
    ========================================================= */

  .info-box {
      background:
          linear-gradient(
              135deg,
              #f0faf3,
              #ffffff
          );

      border: 1px solid #bfe1c9;

      border-radius: 9px;

      padding: 12px 14px;

      font-size: 9px;

      color: var(--text-soft);

      line-height: 1.7;

      margin-bottom: 12px;

      box-shadow: var(--shadow-sm);

      position: relative;

      overflow: hidden;
  }


  /* Línea de crecimiento */

  .info-box::before {
      content: "";

      position: absolute;

      left: 0;
      top: 0;

      width: 3px;
      height: 100%;

      background:
          linear-gradient(
              180deg,
              var(--g2),
              #8bc99b
          );

      transform: scaleY(0);

      transform-origin: top;

      animation: growLine .7s ease forwards;
  }

  .info-box strong {
      color: var(--g1);
  }

  .info-box .title {
      font-size: 10px;

      color: var(--g1);

      font-weight: 700;

      margin-bottom: 6px;

      letter-spacing: 1px;
  }


  /* =========================================================
    LOG
    ========================================================= */

  .log {
      background:
          linear-gradient(
              135deg,
              #f7faf7,
              #ffffff
          );

      border: 1px solid var(--border);

      border-radius: 7px;

      padding: 10px;

      font-size: 9px;

      color: var(--label);

      max-height: 120px;

      overflow-y: auto;

      line-height: 1.8;

      margin-top: 10px;

      box-shadow: inset 0 1px 2px rgba(31, 55, 39, .025);
  }

  .log-line {
      color: var(--g1);

      animation: logAppear .3s ease;
  }

  .log-line.warn {
      color: var(--amber);
  }

  .log-line.err {
      color: var(--red);
  }


  /* =========================================================
    NODE TOOLTIP
    ========================================================= */

  .tooltip {
      position: fixed;

      background:
          rgba(255, 255, 255, .97);

      border: 1px solid #9fc9aa;

      border-radius: 9px;

      padding: 10px 14px;

      font-size: 9px;

      pointer-events: none;

      z-index: 999;

      max-width: 220px;

      line-height: 1.7;

      display: none;

      color: var(--text);

      box-shadow:
          0 12px 30px rgba(31, 55, 39, .15);

      backdrop-filter: blur(8px);

      animation: tooltipIn .18s ease-out;
  }

  .tooltip .tt {
      color: var(--g1);

      font-weight: 700;

      margin-bottom: 4px;
  }


  /* =========================================================
    PANEL VISIBILITY
    ========================================================= */

  .panel {
      display: none;
  }

  .panel.on {
      display: grid;

      animation: panelIn .35s ease-out;
  }

  .panel-1col {
      display: none;
  }

  .panel-1col.on {
      display: block;

      animation: panelIn .35s ease-out;
  }


  /* =========================================================
    🌱 AGRICULTURAL ANIMATIONS
    ========================================================= */


  /* Entrada general */

  @keyframes pageEnter {

      from {
          opacity: 0;

          transform:
              translateY(6px);
      }

      to {
          opacity: 1;

          transform:
              translateY(0);
      }
  }


  /* Movimiento de línea verde */

  @keyframes greenFlow {

      0% {
          background-position: 200% 0;
      }

      100% {
          background-position: -200% 0;
      }
  }


  /* Pulso de indicador */

  @keyframes pulseGreen {

      0%,
      100% {
          box-shadow:
              0 0 0 3px rgba(34, 150, 83, .10);
      }

      50% {
          box-shadow:
              0 0 0 6px rgba(34, 150, 83, .02);
      }
  }


  /* Crecimiento vertical */

  @keyframes growLine {

      from {
          transform: scaleY(0);
      }

      to {
          transform: scaleY(1);
      }
  }


  /* Entrada de panel */

  @keyframes panelIn {

      from {
          opacity: 0;

          transform:
              translateY(8px);
      }

      to {
          opacity: 1;

          transform:
              translateY(0);
      }
  }


  /* Tooltip */

  @keyframes tooltipIn {

      from {
          opacity: 0;

          transform:
              translateY(4px)
              scale(.98);
      }

      to {
          opacity: 1;

          transform:
              translateY(0)
              scale(1);
      }
  }


  /* Logs apareciendo */

  @keyframes logAppear {

      from {
          opacity: 0;

          transform:
              translateX(-5px);
      }

      to {
          opacity: 1;

          transform:
              translateX(0);
      }
  }


  /* =========================================================
    🌿 EFECTO DE CARGA
    ========================================================= */

  .loading {
      position: relative;
      overflow: hidden;
  }

  .loading::after {
      content: "";

      position: absolute;

      inset: 0;

      background:
          linear-gradient(
              90deg,
              transparent,
              rgba(34, 150, 83, .08),
              transparent
          );

      transform: translateX(-100%);

      animation: loadingSweep 1.4s infinite;
  }

  @keyframes loadingSweep {

      to {
          transform: translateX(100%);
      }
  }


  /* =========================================================
    🌾 ESTADO ACTIVO / SELECCIONADO
    ========================================================= */

  .selected {
      border-color: var(--g2) !important;

      box-shadow:
          0 0 0 3px rgba(34, 150, 83, .08),
          var(--shadow);
  }


  /* =========================================================
    📱 RESPONSIVE
    ========================================================= */

  @media (max-width: 900px) {

      .body {
          grid-template-columns: 210px 1fr;
      }

      .main {
          padding: 14px;
      }

      .hdr {
          padding: 12px 16px;
      }
  }


  @media (max-width: 700px) {

      .body {
          display: block;

          height: auto;
      }

      .sidebar {
          border-right: none;

          border-bottom: 1px solid var(--border);

          max-height: 420px;
      }

      .main {
          padding: 12px;
      }
  }


  /* =========================================================
    ♿ REDUCIR ANIMACIONES SI EL SISTEMA LO SOLICITA
    ========================================================= */

  @media (prefers-reduced-motion: reduce) {

      *,
      *::before,
      *::after {
          animation-duration: .01ms !important;
          animation-iteration-count: 1 !important;

          transition-duration: .01ms !important;

          scroll-behavior: auto !important;
      }
  }

</style>

</head>
<body>

<div class="tooltip" id="tooltip"></div>

<!-- HEADER -->
<div class="hdr">
  <div>
    <div class="logo">AGRO<em>GRAPH</em> · 🦠</div>
    <div class="logo-sub">SIMULACIÓN EPIDEMIOLÓGICA · GRAFOS SOBRE POSTGIS</div>
  </div>
  <div style="display:flex;gap:8px;flex-wrap:wrap">
    <span class="pill" style="border-color:#00ff8733;color:var(--g1)">⬤ D3.js LIVE</span>
    <span class="pill" style="border-color:#ffb70033;color:var(--amber)">⬤ FASTAPI</span>
    <span class="pill" style="border-color:#00c8ff33;color:var(--blue)">⬤ LEAFLET + POSTGIS</span>
    <span class="pill" style="border-color:#c084fc33;color:var(--purple)">⬤ BFS · MST · CENTRALIDAD</span>
  </div>
</div>

<!-- TABS -->
<div class="tabs">
  <button class="tab on"  data-tab="prop"   disabled>🦠 CONTAGIO</button>
  <button class="tab"     data-tab="map"    disabled>🗺️ MAPA SATELITAL</button>
  <button class="tab"     data-tab="cent"   disabled>📊 CENTRALIDAD</button>
  <button class="tab"     data-tab="mst"    disabled>🌐 MST SENSORES</button>
  <button class="tab"     data-tab="data"   disabled>📋 DATOS</button>
</div>

<!-- BODY -->
<div class="body">
  <div class="sidebar" id="sidebar"></div>
  <div class="main" id="main"></div>
</div>

<script>
/* ═══════════════════════════════════════════════════════════════
   CONFIGURACIÓN
═══════════════════════════════════════════════════════════════ */
const API_URL_DEFAULT = "http://localhost:8081/api/v1/graphs/simulate-contagion";

/* ═══════════════════════════════════════════════════════════════
   ESTADO GLOBAL
═══════════════════════════════════════════════════════════════ */
let STATE = {
  simulation: null,
  apiUrl: API_URL_DEFAULT,
  hasData: false,
};

/* ═══════════════════════════════════════════════════════════════
   ROUTER DE TABS
═══════════════════════════════════════════════════════════════ */
let currentTab = 'prop';
document.querySelectorAll('.tab').forEach(t => {
  t.addEventListener('click', () => {
    if (t.disabled) return;
    document.querySelectorAll('.tab').forEach(x => x.classList.remove('on'));
    t.classList.add('on');
    currentTab = t.dataset.tab;
    renderTab(currentTab);
  });
});

function renderTab(name){
  if (name === 'prop')  buildPropagation();
  if (name === 'map')   buildMap();
  if (name === 'cent')  buildCentrality();
  if (name === 'mst')   buildMST();
  if (name === 'data')  buildData();
}

/* ═══════════════════════════════════════════════════════════════
   SIDEBAR
═══════════════════════════════════════════════════════════════ */
function renderSidebar(){
  const s = STATE.simulation;

  document.getElementById('sidebar').innerHTML = `
    <div class="ctrl">
      <div class="ctrl-title">⚙️ PARÁMETROS</div>

      <div class="field">
        <label>API ENDPOINT</label>
        <input id="f-api" type="text" value="${STATE.apiUrl}"/>
        <small>URL del endpoint FastAPI</small>
      </div>

      <div class="field">
        <label>CICLO PRODUCTIVO ID</label>
        <input id="f-ciclo" type="number" value="1" min="1"/>
      </div>

      <div class="field">
        <label>ÁRBOL ORIGEN ID</label>
        <input id="f-origen" type="number" value="1" min="1"/>
        <small>Paciente cero de la simulación</small>
      </div>

      <div class="field">
        <label>DISTANCIA MÁXIMA (m)</label>
        <input id="f-dist" type="number" value="50" min="1"/>
        <small>Radio de propagación desde el origen</small>
      </div>

      <button class="btn btn-g" id="btn-run">▶ EJECUTAR SIMULACIÓN</button>
    </div>

    ${s ? `
    <div class="ctrl">
      <div class="ctrl-title">📊 RESULTADOS</div>
      ${statRow('Ciclo', s.ciclo_productivo_id)}
      ${statRow('Paciente cero', 'ID ' + s.paciente_cero_id)}
      ${statRow('Total nodos', s.total_nodos)}
      ${statRow('En riesgo', s.total_en_riesgo)}
      ${statRow('Árboles críticos', (s.arboles_criticos_ids || []).length)}
    </div>

    <div class="ctrl">
      <div class="ctrl-title">🌲 ESTADO VITAL</div>
      ${estadoVitalResumen(s.nodes)}
    </div>

    <div class="ctrl">
      <div class="ctrl-title">📋 LOG</div>
      <div class="log" id="algo-log"></div>
    </div>
    ` : ''}
  `;

  document.getElementById('btn-run').addEventListener('click', runSimulation);
}

function estadoVitalResumen(nodes){
  const counts = {};
  (nodes || []).forEach(n => {
    counts[n.estado_vital] = (counts[n.estado_vital] || 0) + 1;
  });
  const colors = {
    'excelente':'#00e676','con_estres':'#ffb700',
    'enfermo_critico':'#ff3d57','muerto':'#6b7280','erradicado':'#c084fc'
  };
  return Object.entries(counts).map(([k,v]) =>
    `<div style="display:flex;align-items:center;gap:8px;margin-bottom:6px">
       <div style="width:8px;height:8px;border-radius:50%;background:${colors[k]||'#888'};
                   box-shadow:0 0 6px ${colors[k]||'#888'}88"></div>
       <span style="font-size:9px;color:var(--text);flex:1">${k}</span>
       <span style="font-size:9px;color:${colors[k]||'#888'};font-weight:700">${v}</span>
     </div>`
  ).join('') || '<div style="font-size:9px;color:var(--label)">Sin datos</div>';
}

function statRow(k,v){ return `<div class="stat-row"><span class="k">${k}</span><span class="v">${v}</span></div>`; }

/* ═══════════════════════════════════════════════════════════════
   FETCH
═══════════════════════════════════════════════════════════════ */
async function runSimulation(){
  const btn = document.getElementById('btn-run');
  const apiUrl = document.getElementById('f-api').value.trim();
  const payload = {
    ciclo_productivo_id: parseInt(document.getElementById('f-ciclo').value, 10),
    arbol_origen_id:     parseInt(document.getElementById('f-origen').value, 10),
    max_distancia_m:     parseFloat(document.getElementById('f-dist').value)
  };
  STATE.apiUrl = apiUrl;

  btn.disabled = true;
  btn.innerHTML = '<span class="loader"></span> CALCULANDO...';

  try {
    const res = await fetch(apiUrl, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify(payload)
    });

    const text = await res.text();
    let data;
    try { data = text ? JSON.parse(text) : {}; }
    catch (e) {
      console.error('Respuesta no-JSON:', text.slice(0,500));
      throw new Error(`Respuesta inválida del servidor (HTTP ${res.status})`);
    }

    if (!res.ok) throw new Error(data.detail || data.error || `HTTP ${res.status}`);

    STATE.simulation = data;
    STATE.hasData = true;

    document.querySelectorAll('.tab').forEach(t => t.disabled = false);

    renderSidebar();
    renderTab(currentTab);

    setTimeout(() => {
      log('Simulación cargada: ' + data.total_nodos + ' nodos · ' + (data.edges||[]).length + ' aristas');
      log('Paciente cero: árbol ID ' + data.paciente_cero_id, 'warn');
      log('En riesgo: ' + data.total_en_riesgo + ' árboles', 'warn');
    }, 100);

  } catch (err) {
    console.error(err);
    alert('Error al ejecutar la simulación:\n' + err.message);
    btn.disabled = false;
    btn.innerHTML = '▶ EJECUTAR SIMULACIÓN';
  }
}

function log(msg, type){
  const l = document.getElementById('algo-log');
  if (!l) return;
  const d = document.createElement('div');
  d.className = 'log-line ' + (type || '');
  d.textContent = '› ' + msg;
  l.appendChild(d);
  l.scrollTop = l.scrollHeight;
}

/* ═══════════════════════════════════════════════════════════════
   TAB 1 — CONTAGIO (D3 force-directed con zoom/pan)
═══════════════════════════════════════════════════════════════ */
let propState = {
  sim:null, nodes:null, links:null, svg:null, g:null,
  zoom:null, animTimer:null, riskVisible:true, resizeObs:null
};

function buildPropagation(){
  if (!STATE.hasData){
    document.getElementById('main').innerHTML = emptyState('🦠','Configura los parámetros',
      'Ingresa el ciclo productivo, el árbol origen y ejecuta la simulación para visualizar la red de contagio.');
    return;
  }

  const s = STATE.simulation;

  document.getElementById('main').innerHTML = `
    <div class="info-box">
      <div class="title">🧠 GRAFO DE PROPAGACIÓN — FORCE-DIRECTED + BFS</div>
      Red construida desde <strong>arboles_red_vecindad</strong>.
      Ciclo <strong>${s.ciclo_productivo_id}</strong> · Paciente cero ID <strong>${s.paciente_cero_id}</strong>.
      Usa <strong>rueda del ratón</strong> para zoom y <strong>arrastra el fondo</strong> para desplazarte.
    </div>

    <div style="display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap">
      <button class="btn btn-r" style="width:auto;padding:8px 16px" onclick="runBFS()">▶ SIMULAR BFS</button>
      <button class="btn btn-g" style="width:auto;padding:8px 16px" onclick="resetPropGraph()">↺ RESET</button>
      <button class="btn btn-a" style="width:auto;padding:8px 16px" onclick="toggleRiskEdges()">👁 TOGGLE RIESGO</button>
      <button class="btn btn-b" style="width:auto;padding:8px 16px" onclick="fitPropGraph()">⤢ AJUSTAR</button>
      <button class="btn btn-b" style="width:auto;padding:8px 16px" onclick="runBFSAll()">⚡ BFS COMPLETO</button>
    </div>

    <div class="canvas" id="prop-canvas" style="height:600px;position:relative;overflow:hidden"></div>
  `;

  drawPropGraph();
}

function drawPropGraph(){
  const s = STATE.simulation;
  if (!s || !s.nodes) return;

  // Limpiar observers previos
  if (propState.resizeObs){ propState.resizeObs.disconnect(); propState.resizeObs = null; }
  if (propState.animTimer){ clearInterval(propState.animTimer); propState.animTimer = null; }

  const nodes = s.nodes.map(n => ({
    id: n.id,
    label: n.label || String(n.id),
    estado_vital: n.estado_vital,
    es_paciente_cero: !!n.es_paciente_cero,
    en_riesgo: !!n.en_riesgo,
    distancia: n.distancia_al_origen_m
  }));

  const links = (s.edges || []).map(e => ({
    source: e.from,
    target: e.to,
    en_camino_critico: !!e.en_camino_critico,
    distancia: e.distancia_metros,
    probabilidad: e.probabilidad_contagio_base,
    tipo: e.tipo_contacto
  }));

  const container = document.getElementById('prop-canvas');
  const W = container.clientWidth;
  const H = container.clientHeight || 600;

  d3.select('#prop-canvas').selectAll('*').remove();
  const svg = d3.select('#prop-canvas').append('svg')
    .attr('width','100%').attr('height','100%')
    .attr('viewBox', `0 0 ${W} ${H}`)
    .style('display','block');

  const defs = svg.append('defs');
  [['pz','#ef4444'],['rz','#f97316'],['ok','#00e676'],['crit','#ff3d57']].forEach(([k,col])=>{
    const g = defs.append('radialGradient').attr('id','g-'+k);
    g.append('stop').attr('offset','0%').attr('stop-color',col).attr('stop-opacity',.85);
    g.append('stop').attr('offset','100%').attr('stop-color',col).attr('stop-opacity',.15);
  });

  // Capa que se mueve con zoom/pan
  const g = svg.append('g').attr('class','zoom-layer');

  const sim = d3.forceSimulation(nodes)
    .force('link', d3.forceLink(links).id(d=>d.id).distance(80).strength(0.35))
    .force('charge', d3.forceManyBody().strength(-320).distanceMax(500))
    .force('center', d3.forceCenter(W/2, H/2))
    .force('collision', d3.forceCollide(26))
    .force('x', d3.forceX(W/2).strength(0.04))
    .force('y', d3.forceY(H/2).strength(0.04));

  const linkG = g.append('g').attr('class','links');
  const nodeG = g.append('g').attr('class','nodes');

  const link = linkG.selectAll('line').data(links).enter().append('line')
    .attr('stroke', d => d.en_camino_critico ? '#ff3d57' : '#2d4a38')
    .attr('stroke-width', d => d.en_camino_critico ? 2.5 : 1)
    .attr('stroke-opacity', d => d.en_camino_critico ? 0.9 : 0.4)
    .attr('class','prop-link')
    .attr('data-crit', d => d.en_camino_critico ? 1 : 0);

  const node = nodeG.selectAll('g').data(nodes).enter().append('g')
    .attr('class','prop-node')
    .style('cursor','pointer')
    .call(d3.drag()
      .on('start',(e,d)=>{ if(!e.active) sim.alphaTarget(.3).restart(); d.fx=d.x; d.fy=d.y; })
      .on('drag',(e,d)=>{ d.fx=e.x; d.fy=e.y; })
      .on('end',(e,d)=>{ if(!e.active) sim.alphaTarget(0); d.fx=null; d.fy=null; }));

  node.append('circle')
    .attr('class','node-circle')
    .attr('r', d => d.es_paciente_cero ? 18 : d.en_riesgo ? 14 : 11)
    .attr('fill', d => d.es_paciente_cero ? 'url(#g-pz)' :
                       d.en_riesgo        ? 'url(#g-rz)' :
                       d.estado_vital === 'enfermo_critico' ? 'url(#g-crit)' :
                       'url(#g-ok)')
    .attr('stroke', d => d.es_paciente_cero ? '#ef4444' :
                         d.en_riesgo        ? '#f97316' :
                         d.estado_vital === 'enfermo_critico' ? '#ff3d57' : '#00e676')
    .attr('stroke-width', d => d.es_paciente_cero ? 3 : 1.6);

  node.append('text')
    .attr('text-anchor','middle').attr('dy','0.35em')
    .attr('font-size','8px').attr('font-family','JetBrains Mono')
    .attr('fill','#fff').attr('font-weight','700')
    .attr('pointer-events','none')
    .text(d => (d.label || '').substring(0,6));

  // Pulso del paciente cero
  node.filter(d => d.es_paciente_cero).append('circle')
    .attr('r',18).attr('fill','none').attr('stroke','#ef4444').attr('stroke-width',2)
    .attr('opacity',.7)
    .append('animate')
      .attr('attributeName','r').attr('from',18).attr('to',34)
      .attr('dur','1.5s').attr('repeatCount','indefinite');
  node.filter(d => d.es_paciente_cero).append('circle')
    .attr('r',18).attr('fill','none').attr('stroke','#ef4444').attr('stroke-width',1)
    .attr('opacity',.4)
    .append('animate')
      .attr('attributeName','r').attr('from',18).attr('to',44)
      .attr('dur','1.5s').attr('repeatCount','indefinite');

  node.each(function(d){
    tooltip(this, `<div class='tt'>${d.label || d.id}</div>
      ID: ${d.id}<br>
      Estado: <strong>${d.estado_vital || '—'}</strong><br>
      ${d.es_paciente_cero ? '<span style="color:#ef4444">🔴 PACIENTE CERO</span><br>' : ''}
      ${d.en_riesgo ? '<span style="color:#f97316">⚠️ EN RIESGO</span><br>' : ''}
      ${d.distancia != null ? 'Distancia: ' + Number(d.distancia).toFixed(1) + ' m<br>' : ''}
    `);
  });

  sim.on('tick', () => {
    link.attr('x1',d=>d.source.x).attr('y1',d=>d.source.y)
        .attr('x2',d=>d.target.x).attr('y2',d=>d.target.y);
    node.attr('transform', d => `translate(${d.x},${d.y})`);
  });

  // ✅ ZOOM + PAN
  const zoom = d3.zoom()
    .scaleExtent([0.15, 8])
    .on('zoom', (event) => {
      g.attr('transform', event.transform);
    });

  svg.call(zoom);
  svg.on('dblclick.zoom', null);

  propState = {
    sim, nodes, links, svg, g, zoom,
    animTimer:null, riskVisible:true, resizeObs:null
  };

  // Ajustar automáticamente al terminar la simulación inicial
  setTimeout(() => fitPropGraph(700), 900);

  // Redibujar si cambia el tamaño del contenedor
  if (window.ResizeObserver){
    const ro = new ResizeObserver(() => {
      const w = container.clientWidth, h = container.clientHeight || 600;
      svg.attr('viewBox', `0 0 ${w} ${h}`);
      sim.force('center', d3.forceCenter(w/2, h/2));
      sim.force('x', d3.forceX(w/2).strength(0.04));
      sim.force('y', d3.forceY(h/2).strength(0.04));
      sim.alpha(0.3).restart();
    });
    ro.observe(container);
    propState.resizeObs = ro;
  }
}

/* Ajusta el zoom para que TODO el grafo quede visible */
function fitPropGraph(duration = 500){
  if (!propState.nodes || !propState.svg) return;
  const svg = propState.svg;
  const xs = propState.nodes.map(n => n.x).filter(Number.isFinite);
  const ys = propState.nodes.map(n => n.y).filter(Number.isFinite);
  if (!xs.length) return;

  const minX = Math.min(...xs), maxX = Math.max(...xs);
  const minY = Math.min(...ys), maxY = Math.max(...ys);
  const w = maxX - minX || 1, h = maxY - minY || 1;

  const svgW = svg.node().clientWidth;
  const svgH = svg.node().clientHeight;
  const pad = 60;
  const scale = Math.min((svgW - pad*2)/w, (svgH - pad*2)/h, 3);
  const tx = svgW/2 - scale * (minX + w/2);
  const ty = svgH/2 - scale * (minY + h/2);

  svg.transition().duration(duration).call(
    propState.zoom.transform,
    d3.zoomIdentity.translate(tx, ty).scale(scale)
  );
}

function resetPropGraph(){
  if (propState.animTimer){ clearInterval(propState.animTimer); propState.animTimer = null; }
  drawPropGraph();
}

function toggleRiskEdges(){
  propState.riskVisible = !propState.riskVisible;
  d3.selectAll('.prop-link')
    .attr('stroke-width', function(){
      const isCrit = +d3.select(this).attr('data-crit');
      return isCrit ? 2.5 : (propState.riskVisible ? 1 : 0);
    })
    .attr('stroke-opacity', function(){
      const isCrit = +d3.select(this).attr('data-crit');
      return isCrit ? 0.9 : (propState.riskVisible ? 0.4 : 0);
    });
}

/* BFS animado (paso a paso) */
function runBFS(){
  if (!propState.nodes) return;
  if (propState.animTimer) clearInterval(propState.animTimer);

  const adj = {};
  propState.nodes.forEach(n => adj[n.id] = []);
  propState.links.forEach(l => {
    const s = l.source.id ?? l.source;
    const t = l.target.id ?? l.target;
    if (adj[s] && adj[t]){ adj[s].push(t); adj[t].push(s); }
  });

  const sources = propState.nodes
    .filter(n => n.es_paciente_cero || n.estado_vital === 'enfermo_critico' || n.en_riesgo)
    .map(n => n.id);

  if (!sources.length){ log('No hay focos activos para iniciar BFS', 'warn'); return; }

  const visited = new Set(sources);
  const queue = [...sources];
  let step = 0;

  log('BFS iniciado desde: ' + sources.map(id => 'ID' + id).join(', '), 'warn');

  propState.animTimer = setInterval(() => {
    if (!queue.length){
      clearInterval(propState.animTimer);
      propState.animTimer = null;
      log('BFS completo · todos los alcanzables visitados');
      return;
    }
    const curr = queue.shift();
    step++;
    const neighbors = (adj[curr] || []).filter(n => !visited.has(n));
    neighbors.forEach(n => { visited.add(n); queue.push(n); });

    log(`Paso ${step}: ID${curr} → [${neighbors.map(n=>'ID'+n).join(', ') || 'sin vecinos nuevos'}]`);

    repaintNodes(visited, curr);
  }, 450);
}

/* BFS instantáneo (sin animación) — útil para ver TODO el alcance */
function runBFSAll(){
  if (!propState.nodes) return;
  if (propState.animTimer){ clearInterval(propState.animTimer); propState.animTimer = null; }

  const adj = {};
  propState.nodes.forEach(n => adj[n.id] = []);
  propState.links.forEach(l => {
    const s = l.source.id ?? l.source;
    const t = l.target.id ?? l.target;
    if (adj[s] && adj[t]){ adj[s].push(t); adj[t].push(s); }
  });

  const sources = propState.nodes
    .filter(n => n.es_paciente_cero || n.estado_vital === 'enfermo_critico' || n.en_riesgo)
    .map(n => n.id);

  const visited = new Set(sources);
  const queue = [...sources];
  while (queue.length){
    const curr = queue.shift();
    (adj[curr] || []).forEach(n => { if (!visited.has(n)){ visited.add(n); queue.push(n); } });
  }
  log(`BFS completo: ${visited.size} / ${propState.nodes.length} nodos alcanzables`, 'warn');
  repaintNodes(visited, null);
}

function repaintNodes(visited, currentId){
  d3.selectAll('.prop-node').each(function(d){
    const sel = d3.select(this);
    const circle = sel.select('.node-circle');
    if (d.es_paciente_cero){
      circle.attr('fill','url(#g-pz)').attr('stroke','#ef4444');
    } else if (d.estado_vital === 'enfermo_critico'){
      circle.attr('fill','url(#g-crit)').attr('stroke','#ff3d57');
    } else if (visited.has(d.id)){
      circle.attr('fill','url(#g-rz)').attr('stroke','#f97316');
    } else {
      circle.attr('fill','url(#g-ok)').attr('stroke','#00e676');
    }
    if (currentId != null && d.id === currentId){
      circle.attr('stroke','#ffffff').attr('stroke-width', 3);
    } else {
      circle.attr('stroke-width', d.es_paciente_cero ? 3 : 1.6);
    }
  });
}

/* ═══════════════════════════════════════════════════════════════
   TAB 2 — MAPA
═══════════════════════════════════════════════════════════════ */
let mapInstance = null;
let mapMarkersGroup = null;
let mapLinesGroup = null;

function buildMap(){
  if (!STATE.hasData){
    document.getElementById('main').innerHTML = emptyState('🗺️','Sin datos geográficos',
      'Ejecuta la simulación para visualizar los árboles georreferenciados.');
    return;
  }

  const s = STATE.simulation;
  const withCoords = (s.nodes || []).filter(n => n.lat != null && n.lng != null);

  document.getElementById('main').innerHTML = `
    <div class="info-box">
      <div class="title">🗺️ MAPA SATELITAL — COORDENADAS POSTGIS (SRID 4326)</div>
      ${withCoords.length} de ${(s.nodes||[]).length} árboles tienen coordenadas.
      <span style="color:#ef4444">●</span> paciente cero ·
      <span style="color:#f97316">●</span> en riesgo ·
      <span style="color:#3b82f6">●</span> sano.
    </div>
    <div class="canvas">
      <div id="leaflet-map" style="height:600px;width:100%"></div>
    </div>
  `;

  if (mapInstance){
    try { mapInstance.remove(); } catch(e){}
    mapInstance = null;
  }

  mapInstance = L.map('leaflet-map').setView([4.5709, -74.2973], 18);
  L.tileLayer('https://mt1.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
    attribution: 'Imagery &copy; Google',
    maxZoom: 22,
    maxNativeZoom: 21
  }).addTo(mapInstance);

  mapMarkersGroup = L.layerGroup().addTo(mapInstance);
  mapLinesGroup = L.layerGroup().addTo(mapInstance);

  const bounds = [];
  const coords = {};

  (s.nodes || []).forEach(n => {
    if (n.lat == null || n.lng == null) return;
    const latLng = [parseFloat(n.lat), parseFloat(n.lng)];
    bounds.push(latLng);
    coords[n.id] = latLng;

    let color = '#3b82f6', radius = 6;
    if (n.es_paciente_cero){ color = '#ef4444'; radius = 11; }
    else if (n.en_riesgo)  { color = '#f97316'; radius = 9; }
    else if (n.estado_vital === 'enfermo_critico'){ color = '#ff3d57'; radius = 10; }

    const marker = L.circleMarker(latLng, {
      radius, fillColor: color, color: '#ffffff', weight: 2,
      opacity: 1, fillOpacity: 0.9
    });

    marker.bindPopup(`
      <div style="font-family:monospace;font-size:11px;color:#111">
        <strong style="color:${color}">${n.label || ('ID '+n.id)}</strong><br>
        <strong>ID:</strong> ${n.id}<br>
        <strong>Estado:</strong> ${n.estado_vital || '—'}<br>
        ${n.distancia_al_origen_m != null ? `<strong>Distancia origen:</strong> ${Number(n.distancia_al_origen_m).toFixed(1)} m<br>` : ''}
        ${n.es_paciente_cero ? '<span style="color:#ef4444">🔴 PACIENTE CERO</span><br>' : ''}
        ${n.en_riesgo ? '<span style="color:#f97316">⚠️ EN RIESGO</span><br>' : ''}
        <small>Lat: ${latLng[0].toFixed(6)}, Lng: ${latLng[1].toFixed(6)}</small>
      </div>
    `);
    mapMarkersGroup.addLayer(marker);
  });

  (s.edges || []).forEach(e => {
    const from = coords[e.from];
    const to   = coords[e.to];
    if (!from || !to) return;

    const poly = L.polyline([from, to], {
      color: e.en_camino_critico ? '#ef4444' : '#64748b',
      weight: e.en_camino_critico ? 3 : 1,
      dashArray: e.en_camino_critico ? null : '4,4',
      opacity: e.en_camino_critico ? 0.9 : 0.3
    });

    poly.bindPopup(`
      <div style="font-family:monospace;font-size:10px;color:#111">
        <strong>${e.from} → ${e.to}</strong><br>
        ${e.distancia_metros ? 'Distancia: ' + parseFloat(e.distancia_metros).toFixed(2) + ' m<br>' : ''}
        ${e.probabilidad_contagio_base != null ? 'β contagio: ' + parseFloat(e.probabilidad_contagio_base).toFixed(4) + '<br>' : ''}
        ${e.tipo_contacto ? 'Tipo: ' + e.tipo_contacto : ''}
      </div>
    `);
    mapLinesGroup.addLayer(poly);
  });

  if (bounds.length > 0){
    mapInstance.fitBounds(bounds, { padding: [40, 40], maxZoom: 20 });
  }
  setTimeout(() => { try{ mapInstance.invalidateSize(); }catch(e){} }, 200);
}

/* ═══════════════════════════════════════════════════════════════
   TAB 3 — CENTRALIDAD (con zoom/pan)
═══════════════════════════════════════════════════════════════ */
let centState = { svg:null, g:null, zoom:null, nodes:null };

function buildCentrality(){
  if (!STATE.hasData){
    document.getElementById('main').innerHTML = emptyState('📊','Sin datos',
      'Ejecuta la simulación para calcular centralidades.');
    return;
  }

  const s = STATE.simulation;
  const nodes = s.nodes || [];
  const edges = s.edges || [];

  const n = nodes.length;
  const idx = {};
  nodes.forEach((nd,i) => idx[nd.id] = i);

  const adj = Array.from({length:n}, () => []);
  edges.forEach(e => {
    const a = idx[e.from], b = idx[e.to];
    if (a != null && b != null){ adj[a].push(b); adj[b].push(a); }
  });

  const degree = adj.map(a => a.length);
  const maxD = Math.max(...degree, 1);

  const between = new Array(n).fill(0);
  for (let src = 0; src < n; src++){
    const stack = [], pred = Array.from({length:n}, () => []);
    const sigma = new Array(n).fill(0), dist = new Array(n).fill(-1);
    sigma[src] = 1; dist[src] = 0;
    const q = [src];
    while (q.length){
      const v = q.shift();
      stack.push(v);
      adj[v].forEach(w => {
        if (dist[w] < 0){ dist[w] = dist[v] + 1; q.push(w); }
        if (dist[w] === dist[v] + 1){ sigma[w] += sigma[v]; pred[w].push(v); }
      });
    }
    const delta = new Array(n).fill(0);
    while (stack.length){
      const w = stack.pop();
      pred[w].forEach(v => { delta[v] += (sigma[v]/sigma[w]) * (1 + delta[w]); });
      if (w !== src) between[w] += delta[w];
    }
  }
  const maxB = Math.max(...between, 1);

  const closeness = new Array(n).fill(0);
  for (let src = 0; src < n; src++){
    const dist = new Array(n).fill(-1);
    dist[src] = 0;
    const q = [src];
    while (q.length){
      const v = q.shift();
      adj[v].forEach(w => { if (dist[w] < 0){ dist[w] = dist[v] + 1; q.push(w); }});
    }
    const total = dist.filter(d => d > 0).reduce((a,b) => a+b, 0);
    closeness[src] = total > 0 ? (n-1) / total : 0;
  }
  const maxC = Math.max(...closeness, 1);

  const data = nodes.map((nd, i) => ({
    ...nd,
    degree: degree[i] / maxD,
    betweenness: between[i] / maxB,
    closeness: closeness[i] / maxC,
    rawDegree: degree[i],
    rawBetweenness: between[i]
  }));

  document.getElementById('main').innerHTML = `
    <div class="info-box">
      <div class="title">📊 CENTRALIDAD — HUBS DE CONTAGIO</div>
      <strong>Betweenness</strong>: fracción de caminos más cortos que pasan por el nodo ·
      <strong>Closeness</strong>: cercanía promedio al resto ·
      <strong>Degree</strong>: conexiones directas.
    </div>

    <div style="display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap">
      <button class="btn btn-g" style="width:auto;padding:8px 16px" onclick="showCentralityView('degree')">▶ DEGREE</button>
      <button class="btn btn-a" style="width:auto;padding:8px 16px" onclick="showCentralityView('betweenness')">▶ BETWEENNESS</button>
      <button class="btn btn-b" style="width:auto;padding:8px 16px" onclick="showCentralityView('closeness')">▶ CLOSENESS</button>
      <button class="btn btn-r" style="width:auto;padding:8px 16px" onclick="fitCentGraph()">⤢ AJUSTAR</button>
    </div>

    <div style="display:grid;grid-template-columns:1fr 320px;gap:14px">
      <div class="canvas" id="cent-canvas" style="height:600px;position:relative;overflow:hidden"></div>
      <div class="card">
        <div class="ctrl-title">🏆 TOP 10 HUBS CRÍTICOS</div>
        <div id="cent-top"></div>
      </div>
    </div>
  `;

  window.__centData = data;
  showCentralityView('betweenness');
}

function showCentralityView(type){
  const data = window.__centData;
  if (!data) return;

  const container = document.getElementById('cent-canvas');
  const W = container.clientWidth;
  const H = container.clientHeight || 600;
  const pad = 60;
  const cols = Math.ceil(Math.sqrt(data.length * 1.6));
  const rows = Math.ceil(data.length / cols);
  const cw = (W - 2*pad) / Math.max(cols-1, 1);
  const rh = (H - 2*pad) / Math.max(rows-1, 1);

  const nodes = data.map((d,i) => ({
    ...d,
    px: pad + (i % cols) * cw,
    py: pad + Math.floor(i / cols) * rh
  }));

  d3.select('#cent-canvas').selectAll('*').remove();
  const svg = d3.select('#cent-canvas').append('svg')
    .attr('width','100%').attr('height','100%')
    .attr('viewBox', `0 0 ${W} ${H}`)
    .style('display','block');

  const g = svg.append('g').attr('class','zoom-layer');

  const idx = {}; data.forEach((d,i) => idx[d.id] = i);
  const edges = (STATE.simulation.edges || [])
    .map(e => ({ s: idx[e.from], t: idx[e.to], crit: !!e.en_camino_critico }))
    .filter(e => e.s != null && e.t != null);

  g.append('g').selectAll('line').data(edges).enter().append('line')
    .attr('x1', d => nodes[d.s].px).attr('y1', d => nodes[d.s].py)
    .attr('x2', d => nodes[d.t].px).attr('y2', d => nodes[d.t].py)
    .attr('stroke', d => d.crit ? '#ff3d5766' : '#16241988')
    .attr('stroke-width', d => d.crit ? 2 : 1);

  const ng = g.append('g').selectAll('g').data(nodes).enter().append('g')
    .attr('transform', d => `translate(${d.px},${d.py})`);

  ng.append('circle')
    .attr('r', d => 10 + d[type] * 22)
    .attr('fill', d => {
      const v = d[type];
      return v > 0.7 ? '#ff3d57' : v > 0.4 ? '#ffb700' : '#00e676';
    })
    .attr('fill-opacity', d => 0.25 + d[type] * 0.6)
    .attr('stroke', d => {
      const v = d[type];
      return v > 0.7 ? '#ff3d57' : v > 0.4 ? '#ffb700' : '#00e676';
    })
    .attr('stroke-width', d => 1 + d[type] * 2);

  ng.append('text').attr('text-anchor','middle').attr('dy','-0.1em')
    .attr('font-size','9px').attr('font-family','JetBrains Mono')
    .attr('fill','#fff').attr('font-weight','700').attr('pointer-events','none')
    .text(d => (d.label || d.id).substring(0,7));

  ng.append('text').attr('text-anchor','middle').attr('dy','1em')
    .attr('font-size','7px').attr('font-family','JetBrains Mono')
    .attr('fill','rgba(255,255,255,.8)').attr('pointer-events','none')
    .text(d => (d[type]*100).toFixed(0) + '%');

  ng.each(function(d){
    tooltip(this, `<div class='tt'>${d.label || ('ID '+d.id)}</div>
      <strong>${type}:</strong> ${(d[type]*100).toFixed(1)}%<br>
      Degree: ${d.rawDegree}<br>
      Betweenness: ${d.rawBetweenness}<br>
      Estado: ${d.estado_vital || '—'}<br>
      ${d[type] > 0.7 ? '<span style="color:#ff3d57">⚠️ HUB CRÍTICO</span>' : ''}
    `);
  });

  const zoom = d3.zoom().scaleExtent([0.15, 8]).on('zoom', e => g.attr('transform', e.transform));
  svg.call(zoom);
  svg.on('dblclick.zoom', null);
  centState = { svg, g, zoom, nodes };

  setTimeout(() => fitCentGraph(300), 50);

  const sorted = [...data].sort((a,b) => b[type] - a[type]).slice(0,10);
  const colType = type === 'degree' ? '#00e676' : type === 'betweenness' ? '#ffb700' : '#00c8ff';
  document.getElementById('cent-top').innerHTML = sorted.map((d,k) => `
    <div style="display:flex;justify-content:space-between;align-items:center;padding:7px 0;border-bottom:1px solid #16241922">
      <div style="display:flex;align-items:center;gap:8px">
        <span style="font-size:10px;color:var(--label);width:22px">#${k+1}</span>
        <span style="font-size:10px;color:${d[type]>0.7?'#ff3d57':'var(--text)'};font-weight:700">${d.label || d.id}</span>
      </div>
      <span style="font-size:10px;color:${colType};font-weight:700">${(d[type]*100).toFixed(1)}%</span>
    </div>
  `).join('');
}

function fitCentGraph(duration = 400){
  if (!centState.nodes || !centState.svg) return;
  const xs = centState.nodes.map(n => n.px);
  const ys = centState.nodes.map(n => n.py);
  const minX = Math.min(...xs), maxX = Math.max(...xs);
  const minY = Math.min(...ys), maxY = Math.max(...ys);
  const w = maxX - minX || 1, h = maxY - minY || 1;
  const svgW = centState.svg.node().clientWidth;
  const svgH = centState.svg.node().clientHeight;
  const pad = 60;
  const scale = Math.min((svgW - pad*2)/w, (svgH - pad*2)/h, 3);
  const tx = svgW/2 - scale * (minX + w/2);
  const ty = svgH/2 - scale * (minY + h/2);
  centState.svg.transition().duration(duration).call(
    centState.zoom.transform,
    d3.zoomIdentity.translate(tx, ty).scale(scale)
  );
}

/* ═══════════════════════════════════════════════════════════════
   TAB 4 — MST (con zoom/pan)
═══════════════════════════════════════════════════════════════ */
let mstState = { svg:null, g:null, zoom:null, nodes:null };

function buildMST(){
  if (!STATE.hasData){
    document.getElementById('main').innerHTML = emptyState('🌐','Sin datos',
      'Ejecuta la simulación para calcular el MST.');
    return;
  }

  const s = STATE.simulation;
  document.getElementById('main').innerHTML = `
    <div class="info-box">
      <div class="title">🌐 MST — RED ÓPTIMA DE SENSORES IoT</div>
      <strong>Kruskal</strong> sobre ${(s.edges||[]).length} aristas reales
      (peso = distancia en metros). Cable/radio mínimo que conecta ${s.total_nodos} árboles.
    </div>

    <div style="display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap">
      <button class="btn btn-g" style="width:auto;padding:8px 16px" onclick="runKruskal()">▶ EJECUTAR KRUSKAL</button>
      <button class="btn btn-a" style="width:auto;padding:8px 16px" onclick="runKruskalStep()">⏭ PASO A PASO</button>
      <button class="btn btn-b" style="width:auto;padding:8px 16px" onclick="showFullGraph()">👁 GRAFO COMPLETO</button>
      <button class="btn btn-b" style="width:auto;padding:8px 16px" onclick="fitMSTGraph()">⤢ AJUSTAR</button>
      <button class="btn btn-r" style="width:auto;padding:8px 16px" onclick="resetMST()">↺ RESET</button>
    </div>

    <div style="display:grid;grid-template-columns:1fr 320px;gap:14px">
      <div class="canvas" id="mst-canvas" style="height:600px;position:relative;overflow:hidden"></div>
      <div class="card">
        <div class="ctrl-title">📦 RESULTADO MST</div>
        <div id="mst-result" style="font-size:10px;color:var(--label)">Ejecuta Kruskal...</div>
      </div>
    </div>
  `;
  drawMSTGraph([]);
}

function mstLayout(){
  const s = STATE.simulation;
  const container = document.getElementById('mst-canvas');
  const W = container.clientWidth;
  const H = container.clientHeight || 600;
  const pad = 60;
  const n = s.nodes.length;
  const cols = Math.ceil(Math.sqrt(n * 1.6));
  const rows = Math.ceil(n / cols);
  const cw = (W - 2*pad) / Math.max(cols-1,1);
  const rh = (H - 2*pad) / Math.max(rows-1,1);

  const idx = {};
  s.nodes.forEach((d,i) => idx[d.id] = i);

  const nodes = s.nodes.map((d,i) => ({
    ...d,
    px: pad + (i % cols) * cw,
    py: pad + Math.floor(i / cols) * rh
  }));

  const edges = (s.edges || []).map((e,i) => ({
    id: i,
    source: idx[e.from],
    target: idx[e.to],
    weight: parseFloat(e.distancia_metros || 1),
    raw: e
  })).filter(e => e.source != null && e.target != null);

  return { nodes, edges, idx, W, H };
}

function drawMSTGraph(highlightEdges = [], allEdges = null){
  const { nodes, edges, W, H } = mstLayout();
  const edgesToDraw = allEdges || edges;

  d3.select('#mst-canvas').selectAll('*').remove();
  const svg = d3.select('#mst-canvas').append('svg')
    .attr('width','100%').attr('height','100%')
    .attr('viewBox', `0 0 ${W} ${H}`)
    .style('display','block');

  const g = svg.append('g').attr('class','zoom-layer');

  g.append('g').selectAll('line').data(edgesToDraw).enter().append('line')
    .attr('x1', d => nodes[d.source].px).attr('y1', d => nodes[d.source].py)
    .attr('x2', d => nodes[d.target].px).attr('y2', d => nodes[d.target].py)
    .attr('stroke','#162419').attr('stroke-width',1)
    .attr('stroke-opacity', 0.5);

  g.append('g').selectAll('line').data(highlightEdges).enter().append('line')
    .attr('x1', d => nodes[d.source].px).attr('y1', d => nodes[d.source].py)
    .attr('x2', d => nodes[d.target].px).attr('y2', d => nodes[d.target].py)
    .attr('stroke','#00e676').attr('stroke-width',3);

  g.append('g').selectAll('text').data(highlightEdges).enter().append('text')
    .attr('x', d => (nodes[d.source].px + nodes[d.target].px)/2)
    .attr('y', d => (nodes[d.source].py + nodes[d.target].py)/2 - 5)
    .attr('text-anchor','middle').attr('font-size','8px')
    .attr('font-family','JetBrains Mono').attr('fill','#00e676')
    .text(d => d.weight.toFixed(1) + 'm');

  const ng = g.append('g').selectAll('g').data(nodes).enter().append('g')
    .attr('transform', d => `translate(${d.px},${d.py})`);

  ng.append('circle').attr('r',14).attr('fill','#0d1510')
    .attr('stroke', d => d.es_paciente_cero ? '#ef4444' : d.en_riesgo ? '#f97316' : '#00e67666')
    .attr('stroke-width', d => d.es_paciente_cero ? 2.5 : 1.5);

  ng.append('text').attr('text-anchor','middle').attr('dy','0.35em')
    .attr('font-size','7px').attr('font-family','JetBrains Mono')
    .attr('fill','#00e676').attr('font-weight','700').attr('pointer-events','none')
    .text(d => (d.label || d.id).substring(0,5));

  ng.each(function(d){
    tooltip(this, `<div class='tt'>${d.label || ('ID '+d.id)}</div>
      Estado: ${d.estado_vital || '—'}<br>
      Lat: ${d.lat != null ? parseFloat(d.lat).toFixed(5) : '—'}<br>
      Lng: ${d.lng != null ? parseFloat(d.lng).toFixed(5) : '—'}
    `);
  });

  const zoom = d3.zoom().scaleExtent([0.15, 8]).on('zoom', e => g.attr('transform', e.transform));
  svg.call(zoom);
  svg.on('dblclick.zoom', null);
  mstState = { svg, g, zoom, nodes };
}

function fitMSTGraph(){
  if (!mstState.nodes || !mstState.svg) return;
  const xs = mstState.nodes.map(n => n.px);
  const ys = mstState.nodes.map(n => n.py);
  const minX = Math.min(...xs), maxX = Math.max(...xs);
  const minY = Math.min(...ys), maxY = Math.max(...ys);
  const w = maxX - minX || 1, h = maxY - minY || 1;
  const svgW = mstState.svg.node().clientWidth;
  const svgH = mstState.svg.node().clientHeight;
  const pad = 60;
  const scale = Math.min((svgW - pad*2)/w, (svgH - pad*2)/h, 3);
  const tx = svgW/2 - scale * (minX + w/2);
  const ty = svgH/2 - scale * (minY + h/2);
  mstState.svg.transition().duration(400).call(
    mstState.zoom.transform,
    d3.zoomIdentity.translate(tx, ty).scale(scale)
  );
}

function kruskalAlgo(){
  const { edges } = mstLayout();
  const n = STATE.simulation.nodes.length;
  const sorted = [...edges].sort((a,b) => a.weight - b.weight);
  const parent = Array.from({length:n}, (_,i) => i);
  const rank = new Array(n).fill(0);
  function find(x){ return parent[x] === x ? x : (parent[x] = find(parent[x])); }
  function union(a,b){
    const ra = find(a), rb = find(b);
    if (ra === rb) return false;
    if (rank[ra] < rank[rb]) parent[ra] = rb;
    else if (rank[ra] > rank[rb]) parent[rb] = ra;
    else { parent[rb] = ra; rank[ra]++; }
    return true;
  }
  const mst = [], rejected = [];
  sorted.forEach(e => { if (union(e.source, e.target)) mst.push(e); else rejected.push(e); });
  return { mst, rejected, totalWeight: mst.reduce((s,e) => s + e.weight, 0) };
}

function runKruskal(){
  const { mst, totalWeight } = kruskalAlgo();
  const fullWeight = mstLayout().edges.reduce((s,e) => s + e.weight, 0);
  drawMSTGraph(mst);
  const n = STATE.simulation.nodes.length;
  document.getElementById('mst-result').innerHTML = `
    <div style="color:#00e676;font-weight:700;margin-bottom:8px">✓ MST CALCULADO</div>
    ${statRow('Nodos', n)}
    ${statRow('Aristas MST', mst.length + ' / ' + mstLayout().edges.length)}
    ${statRow('Peso MST total', totalWeight.toFixed(1) + ' m')}
    ${statRow('Peso grafo completo', fullWeight.toFixed(1) + ' m')}
    ${statRow('Ahorro', (fullWeight - totalWeight).toFixed(1) + ' m (' +
      (fullWeight > 0 ? Math.round((1 - totalWeight/fullWeight)*100) : 0) + '%)')}
  `;
  log('Kruskal: ' + mst.length + ' aristas en MST · peso ' + totalWeight.toFixed(1) + 'm', 'warn');
}

let mstStepState = null;
function runKruskalStep(){
  if (!mstStepState){
    const { edges } = mstLayout();
    const n = STATE.simulation.nodes.length;
    mstStepState = {
      edges: [...edges].sort((a,b) => a.weight - b.weight),
      i: 0, mst: [],
      parent: Array.from({length:n}, (_,i) => i),
      rank: new Array(n).fill(0)
    };
    log('Kruskal paso a paso iniciado');
  }
  const st = mstStepState;
  if (st.i >= st.edges.length){
    log('MST completo');
    mstStepState = null;
    return;
  }
  const e = st.edges[st.i++];
  function find(x){ return st.parent[x] === x ? x : (st.parent[x] = find(st.parent[x])); }
  const ra = find(e.source), rb = find(e.target);
  if (ra !== rb){
    if (st.rank[ra] < st.rank[rb]) st.parent[ra] = rb;
    else if (st.rank[ra] > st.rank[rb]) st.parent[rb] = ra;
    else { st.parent[rb] = ra; st.rank[ra]++; }
    st.mst.push(e);
    log(`AÑADIR ${e.raw.from}↔${e.raw.to} (${e.weight.toFixed(1)}m) → MST`);
  } else {
    log(`RECHAZAR ${e.raw.from}↔${e.raw.to} (${e.weight.toFixed(1)}m) → CICLO`, 'warn');
  }
  drawMSTGraph(st.mst);
}

function showFullGraph(){ drawMSTGraph([], mstLayout().edges); }
function resetMST(){ mstStepState = null; drawMSTGraph([]); }

/* ═══════════════════════════════════════════════════════════════
   TAB 5 — DATOS CRUDOS
═══════════════════════════════════════════════════════════════ */
function buildData(){
  if (!STATE.hasData){
    document.getElementById('main').innerHTML = emptyState('📋','Sin datos',
      'Ejecuta la simulación para ver los datos crudos.');
    return;
  }

  const s = STATE.simulation;

  document.getElementById('main').innerHTML = `
    <div class="info-box">
      <div class="title">📋 DATOS CRUDOS DE LA SIMULACIÓN</div>
      Endpoint <code>${STATE.apiUrl}</code> · Ciclo <strong>${s.ciclo_productivo_id}</strong> ·
      Nodos <strong>${s.total_nodos}</strong> · En riesgo <strong>${s.total_en_riesgo}</strong> ·
      Críticos <strong>${(s.arboles_criticos_ids||[]).length}</strong>.
    </div>

    <div class="card">
      <div class="ctrl-title">🌲 NODOS (${s.nodes.length})</div>
      <div style="max-height:340px;overflow-y:auto">
        <table>
          <thead><tr>
            <th>ID</th><th>Label</th><th>Estado vital</th>
            <th>Lat</th><th>Lng</th>
            <th>Paciente 0</th><th>En riesgo</th><th>Distancia (m)</th>
          </tr></thead>
          <tbody>
            ${s.nodes.map(n => `<tr>
              <td>${n.id}</td>
              <td style="color:#00e676">${n.label || '—'}</td>
              <td>${n.estado_vital || '—'}</td>
              <td>${n.lat != null ? parseFloat(n.lat).toFixed(6) : '—'}</td>
              <td>${n.lng != null ? parseFloat(n.lng).toFixed(6) : '—'}</td>
              <td>${n.es_paciente_cero ? '<span style="color:#ef4444">●</span>' : ''}</td>
              <td>${n.en_riesgo ? '<span style="color:#f97316">●</span>' : ''}</td>
              <td>${n.distancia_al_origen_m != null ? parseFloat(n.distancia_al_origen_m).toFixed(2) : '—'}</td>
            </tr>`).join('')}
          </tbody>
        </table>
      </div>
    </div>

    <div class="card">
      <div class="ctrl-title">🔗 ARISTAS (${(s.edges||[]).length})</div>
      <div style="max-height:340px;overflow-y:auto">
        <table>
          <thead><tr>
            <th>From</th><th>To</th><th>Distancia (m)</th>
            <th>β contagio</th><th>Tipo contacto</th><th>Camino crítico</th>
          </tr></thead>
          <tbody>
            ${(s.edges || []).map(e => `<tr>
              <td>${e.from}</td>
              <td>${e.to}</td>
              <td>${e.distancia_metros != null ? parseFloat(e.distancia_metros).toFixed(2) : '—'}</td>
              <td>${e.probabilidad_contagio_base != null ? parseFloat(e.probabilidad_contagio_base).toFixed(4) : '—'}</td>
              <td>${e.tipo_contacto || '—'}</td>
              <td>${e.en_camino_critico ? '<span style="color:#ff3d57">✓ CRÍTICO</span>' : ''}</td>
            </tr>`).join('')}
          </tbody>
        </table>
      </div>
    </div>

    <div class="card">
      <div class="ctrl-title">📦 JSON CRUDO</div>
      <pre style="background:#0a0f0c;border:1px solid #1a2a1e;border-radius:6px;
        padding:12px;font-size:9px;color:#8aa993;max-height:280px;overflow:auto;
        font-family:monospace">${JSON.stringify(s, null, 2)}</pre>
    </div>
  `;
}

/* ═══════════════════════════════════════════════════════════════
   UTILS
═══════════════════════════════════════════════════════════════ */
function tooltip(el, html){
  el.addEventListener('mouseenter', () => {
    const t = document.getElementById('tooltip');
    if (!t) return;
    t.innerHTML = html;
    t.style.display = 'block';
  });
  el.addEventListener('mousemove', e => {
    const t = document.getElementById('tooltip');
    if (!t) return;
    const x = e.clientX + 14, y = e.clientY - 10;
    const maxX = window.innerWidth - t.offsetWidth - 10;
    const maxY = window.innerHeight - t.offsetHeight - 10;
    t.style.left = Math.min(x, maxX) + 'px';
    t.style.top  = Math.min(y, maxY) + 'px';
  });
  el.addEventListener('mouseleave', () => {
    const t = document.getElementById('tooltip');
    if (t) t.style.display = 'none';
  });
}

function emptyState(ico, title, desc){
  return `<div class="empty">
    <div class="ico">${ico}</div>
    <h3>${title}</h3>
    <p>${desc}</p>
  </div>`;
}

/* ═══════════════════════════════════════════════════════════════
   INIT
═══════════════════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
  renderSidebar();
  renderTab('prop');
});
</script>
</body>
</html>