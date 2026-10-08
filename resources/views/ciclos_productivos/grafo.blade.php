<x-app-layout>
<style>
    /* ══════════════════════════════════════════════════════════════
    GRAFO AGRÍCOLA · TEMA "CAMPO FRESCO" (CLARO Y MODERNO)
    ══════════════════════════════════════════════════════════════ */

    .graph-shell{
    --leaf:#16a34a;
    --leaf-soft:#22c55e;
    --lime:#84cc16;
    position:relative;
    border:1px solid rgba(22,163,74,.16);
    border-radius:1.5rem;
    overflow:hidden;
    background:
        radial-gradient(900px 420px at 12% -10%, rgba(132,204,22,.16), transparent 62%),
        radial-gradient(900px 420px at 92% 112%, rgba(34,197,94,.14), transparent 62%),
        linear-gradient(160deg,#ffffff 0%,#f6fbf4 55%,#eef8ee 100%);
    box-shadow:
        0 24px 60px -30px rgba(22,101,52,.28),
        0 4px 16px -8px rgba(22,101,52,.10),
        inset 0 1px 0 rgba(255,255,255,.9);
    }

    /* ── HEADER ── */
    .graph-head{
    position:relative; z-index:30;
    display:flex; flex-direction:column; gap:12px;
    padding:16px 20px;
    border-bottom:1px solid rgba(22,163,74,.14);
    background:linear-gradient(180deg, rgba(255,255,255,.85), rgba(255,255,255,.35));
    backdrop-filter:blur(6px);
    }
    @media (min-width:640px){
    .graph-head{flex-direction:row; align-items:center; justify-content:space-between;}
    }
    .graph-icon{
    display:flex; align-items:center; justify-content:center;
    height:32px; width:32px; border-radius:10px; font-size:14px;
    background:linear-gradient(145deg,#e9f9e6,#d5f2d3);
    box-shadow:inset 0 0 0 1px rgba(22,163,74,.22), 0 4px 10px -4px rgba(22,101,52,.25);
    }
    .graph-title{font-weight:800; color:#14532d; letter-spacing:.2px;}
    .graph-sub{margin-top:4px; font-size:11px; color:rgba(20,83,45,.55);}

    .graph-live{
    display:inline-flex; align-items:center; gap:6px;
    font-family:ui-monospace,SFMono-Regular,Menlo,monospace;
    font-size:9px; letter-spacing:2px; font-weight:700;
    color:#15803d; padding:3px 9px; border-radius:999px;
    border:1px solid rgba(22,163,74,.28);
    background:linear-gradient(145deg,#eaf9e8,#dcf3dc);
    box-shadow:0 2px 8px -3px rgba(22,101,52,.30);
    }
    .graph-live-dot{
    width:6px; height:6px; border-radius:50%;
    background:var(--leaf-soft); box-shadow:0 0 0 3px rgba(34,197,94,.18);
    animation:liveBlink 1.6s ease-in-out infinite;
    }
    @keyframes liveBlink{0%,100%{opacity:1}50%{opacity:.35}}

    .graph-legend{
    display:flex; align-items:center; gap:18px;
    font-family:ui-monospace,SFMono-Regular,Menlo,monospace;
    font-size:9px; letter-spacing:2px; text-transform:uppercase;
    color:rgba(20,83,45,.55);
    }
    .graph-legend-node{
    width:9px; height:9px; border-radius:50%;
    background:radial-gradient(circle at 35% 30%, #f0fff0, #16a34a);
    box-shadow:0 0 0 3px rgba(22,163,74,.14);
    }
    .graph-legend-edge{
    width:20px; height:2px; border-radius:2px;
    background:linear-gradient(90deg,#16a34a,#84cc16);
    box-shadow:0 1px 3px rgba(22,101,52,.35);
    }

    /* ── CANVAS ── */
    .graph-canvas{
    position:relative; overflow:hidden;
    background:
        radial-gradient(ellipse at 50% 30%, #ffffff 0%, #f4fbf2 45%, #eaf6e8 100%);
    border-bottom:1px solid rgba(22,163,74,.10);
    }

    .graph-grid{
    position:absolute; inset:0; z-index:0; pointer-events:none;
    background-image:
        linear-gradient(rgba(22,163,74,.09) 1px, transparent 1px),
        linear-gradient(90deg, rgba(22,163,74,.09) 1px, transparent 1px),
        radial-gradient(rgba(22,163,74,.18) 1px, transparent 1px);
    background-size:44px 44px, 44px 44px, 22px 22px;
    -webkit-mask-image:radial-gradient(ellipse at center, #000 30%, transparent 88%);
            mask-image:radial-gradient(ellipse at center, #000 30%, transparent 88%);
    animation:gridDrift 18s linear infinite;
    }
    @keyframes gridDrift{to{background-position:44px 44px,44px 44px,22px 22px}}

    .graph-glow{
    position:absolute; inset:0; z-index:1; pointer-events:none;
    background:
        radial-gradient(circle at 50% 40%, rgba(132,204,22,.18), transparent 60%),
        radial-gradient(circle at 78% 82%, rgba(34,197,94,.14), transparent 55%);
    animation:glowPulse 7s ease-in-out infinite;
    }
    @keyframes glowPulse{
    0%,100%{opacity:.6; transform:scale(1)}
    50%    {opacity:1;  transform:scale(1.05)}
    }

    /* Sustituye el "scanline" neón por un barrido de luz solar muy sutil */
    .graph-scan{
    position:absolute; inset:0; z-index:11; pointer-events:none;
    background:linear-gradient(115deg,
        transparent 0%,
        rgba(255,255,255,.55) 45%,
        rgba(190,242,180,.35) 50%,
        transparent 62%);
    opacity:.5;
    animation:scanShift 12s ease-in-out infinite;
    }
    @keyframes scanShift{
    0%  {transform:translateX(-35%)}
    100%{transform:translateX(35%)}
    }

    .graph-canvas::before,
    .graph-canvas::after{
    content:''; position:absolute; width:26px; height:26px; z-index:20;
    pointer-events:none; border:0 solid rgba(22,163,74,.35);
    }
    .graph-canvas::before{top:14px; left:14px; border-width:2px 0 0 2px; border-radius:8px 0 0 0;}
    .graph-canvas::after{bottom:14px; right:14px; border-width:0 2px 2px 0; border-radius:0 0 8px 0;}

    .graph-cy{position:relative; z-index:10; height:520px; width:100%;}
    @media (min-width:640px){.graph-cy{height:600px;}}

    /* ── FOOTER ── */
    .graph-foot{
    position:relative; z-index:30;
    display:flex; flex-direction:column; gap:8px;
    padding:12px 20px;
    border-top:1px solid rgba(22,163,74,.14);
    background:linear-gradient(0deg, rgba(255,255,255,.85), rgba(255,255,255,.30));
    }
    @media (min-width:640px){
    .graph-foot{flex-direction:row; align-items:center; justify-content:space-between;}
    }
    .graph-hint{
    font-family:ui-monospace,SFMono-Regular,Menlo,monospace;
    font-size:10px; letter-spacing:.5px; color:rgba(20,83,45,.50);
    }
    .graph-badge{
    font-family:ui-monospace,SFMono-Regular,Menlo,monospace;
    font-size:10px; letter-spacing:1px; color:#15803d;
    padding:4px 12px; border-radius:8px;
    background:linear-gradient(145deg,#eaf9e8,#dcf3dc);
    border:1px solid rgba(22,163,74,.24);
    box-shadow:0 3px 10px -4px rgba(22,101,52,.28), inset 0 1px 0 rgba(255,255,255,.8);
    }

    @media (prefers-reduced-motion: reduce){
    .graph-grid,.graph-glow,.graph-scan,.graph-live-dot{animation:none!important}
    }
</style>
<div class="min-h-screen bg-slate-50 py-6">

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        {{-- ========================================================= --}}
        {{-- HEADER --}}
        {{-- ========================================================= --}}

        <div class="mb-6">

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                <div class="flex items-start gap-4">

                    {{-- Icono --}}
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl
                                bg-gradient-to-br from-emerald-500 to-green-700
                                text-2xl text-white shadow-lg shadow-emerald-200
                                transition duration-300 hover:scale-105">
                        🌳
                    </div>

                    <div>

                        <div class="flex flex-wrap items-center gap-2">

                            <h1 class="text-2xl font-extrabold tracking-tight text-slate-800 sm:text-3xl">
                                Grafo agrícola
                            </h1>

                            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700">
                                CICLO #{{ $cicloProductivo->id }}
                            </span>

                        </div>

                        <p class="mt-1 text-sm text-slate-500">
                            Análisis de conectividad, producción y rutas óptimas del cultivo.
                        </p>

                    </div>

                </div>


                {{-- Estado --}}
                <div class="flex items-center gap-3 rounded-2xl border border-slate-200
                            bg-white px-4 py-3 shadow-sm">

                    <span class="relative flex h-3 w-3">

                        <span class="absolute inline-flex h-full w-full
                                     animate-ping rounded-full bg-emerald-400 opacity-75"></span>

                        <span class="relative inline-flex h-3 w-3 rounded-full bg-emerald-500"></span>

                    </span>

                    <div>

                        <p class="text-xs font-medium text-slate-400">
                            SISTEMA
                        </p>

                        <p class="text-sm font-bold text-emerald-700">
                            Operativo
                        </p>

                    </div>

                    <span id="loading-spinner"
                          class="ml-2 hidden text-slate-400">

                        <svg class="h-4 w-4 animate-spin"
                             xmlns="http://www.w3.org/2000/svg"
                             fill="none"
                             viewBox="0 0 24 24">

                            <circle class="opacity-25"
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke="currentColor"
                                    stroke-width="4">
                            </circle>

                            <path class="opacity-75"
                                  fill="currentColor"
                                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z">
                            </path>

                        </svg>

                    </span>

                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- MÉTRICAS --}}
        {{-- ========================================================= --}}

        <div id="estadisticas"
             class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">


            {{-- Árboles --}}
            <div class="group rounded-2xl border border-slate-200 bg-white p-5
                        shadow-sm transition-all duration-300
                        hover:-translate-y-1 hover:shadow-lg">

                <div class="flex items-start justify-between">

                    <div>

                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                            Total árboles
                        </p>

                        <p id="total-arboles"
                           class="mt-2 text-3xl font-black text-slate-800">
                            -
                        </p>

                    </div>

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl
                                bg-emerald-50 text-xl
                                transition duration-300 group-hover:scale-110">
                        🌳
                    </div>

                </div>

                <div class="mt-4 h-1.5 overflow-hidden rounded-full bg-slate-100">

                    <div class="h-full w-2/3 rounded-full bg-emerald-500
                                transition-all duration-700">
                    </div>

                </div>

            </div>


            {{-- Producción --}}
            <div class="group rounded-2xl border border-slate-200 bg-white p-5
                        shadow-sm transition-all duration-300
                        hover:-translate-y-1 hover:shadow-lg">

                <div class="flex items-start justify-between">

                    <div>

                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                            Producción total
                        </p>

                        <p id="produccion-total"
                           class="mt-2 text-3xl font-black text-slate-800">
                            -
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            kilogramos
                        </p>

                    </div>

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl
                                bg-green-50 text-xl
                                transition duration-300 group-hover:scale-110">
                        📦
                    </div>

                </div>

            </div>


            {{-- Promedio --}}
            <div class="group rounded-2xl border border-slate-200 bg-white p-5
                        shadow-sm transition-all duration-300
                        hover:-translate-y-1 hover:shadow-lg">

                <div class="flex items-start justify-between">

                    <div>

                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                            Producción promedio
                        </p>

                        <p id="produccion-promedio"
                           class="mt-2 text-3xl font-black text-slate-800">
                            -
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            kg / árbol
                        </p>

                    </div>

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl
                                bg-amber-50 text-xl
                                transition duration-300 group-hover:scale-110">
                        ⚖️
                    </div>

                </div>

            </div>


            {{-- Conexiones --}}
            <div class="group rounded-2xl border border-slate-200 bg-white p-5
                        shadow-sm transition-all duration-300
                        hover:-translate-y-1 hover:shadow-lg">

                <div class="flex items-start justify-between">

                    <div>

                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                            Conexiones
                        </p>

                        <p id="total-conexiones"
                           class="mt-2 text-3xl font-black text-slate-800">
                            -
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            relaciones en red
                        </p>

                    </div>

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl
                                bg-blue-50 text-xl
                                transition duration-300 group-hover:scale-110">
                        🔗
                    </div>

                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- ESTADOS --}}
        {{-- ========================================================= --}}

        <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">

            <div class="mb-3 flex items-center justify-between">

                <div>

                    <h3 class="text-sm font-bold text-slate-800">
                        Estado del cultivo
                    </h3>

                    <p class="text-xs text-slate-400">
                        Distribución de árboles dentro del ciclo
                    </p>

                </div>

                <span id="node-count"
                      class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">
                    0 nodos
                </span>

            </div>

            <div id="desglose-estados"
                 class="flex flex-wrap gap-2">
            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- GRAFO --}}
        {{-- ========================================================= --}}

        <div class="graph-shell mb-6">

            {{-- Header --}}
            <div class="graph-head">

                <div>
                    <div class="flex flex-wrap items-center gap-2">

                        <span class="graph-icon">🕸️</span>

                        <h2 class="graph-title">Red de árboles</h2>

                        <span class="graph-live">
                            <span class="graph-live-dot"></span>LIVE
                        </span>

                    </div>

                    <p class="graph-sub">
                        Visualización de la estructura espacial del cultivo
                    </p>
                </div>

                <div class="graph-legend">
                    <span class="flex items-center gap-2">
                        <span class="graph-legend-node"></span>Nodo
                    </span>
                    <span class="flex items-center gap-2">
                        <span class="graph-legend-edge"></span>Conexión
                    </span>
                </div>

            </div>

            {{-- Canvas --}}
            <div class="graph-canvas">

                <div class="graph-grid"></div>
                <div class="graph-glow"></div>
                <div class="graph-scan"></div>

                <div id="cy" class="graph-cy"></div>

            </div>

            {{-- Footer --}}
            <div class="graph-foot">

                <span class="graph-hint">
                    ⟡ Arrastra los nodos para explorar la red · Usa la rueda para zoom
                </span>

                <span id="zoom-level" class="graph-badge">Zoom: 1.00x</span>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- CONTROLES --}}
        {{-- ========================================================= --}}

        <div class="mb-6 flex flex-wrap items-center gap-2">

            <button id="zoom-in"
                    class="group inline-flex items-center gap-2 rounded-xl
                           border border-slate-200 bg-white px-4 py-2.5
                           text-sm font-semibold text-slate-700 shadow-sm
                           transition-all duration-200
                           hover:-translate-y-0.5 hover:border-emerald-300
                           hover:bg-emerald-50 hover:text-emerald-700
                           active:scale-95">

                <span class="transition-transform group-hover:scale-125">
                    +
                </span>

                Zoom

            </button>


            <button id="zoom-out"
                    class="group inline-flex items-center gap-2 rounded-xl
                           border border-slate-200 bg-white px-4 py-2.5
                           text-sm font-semibold text-slate-700 shadow-sm
                           transition-all duration-200
                           hover:-translate-y-0.5 hover:border-emerald-300
                           hover:bg-emerald-50 hover:text-emerald-700
                           active:scale-95">

                <span>
                    −
                </span>

                Alejar

            </button>


            <button id="fit-view"
                    class="inline-flex items-center gap-2 rounded-xl
                           border border-slate-200 bg-white px-4 py-2.5
                           text-sm font-semibold text-slate-700 shadow-sm
                           transition-all duration-200
                           hover:-translate-y-0.5 hover:border-emerald-300
                           hover:bg-emerald-50 hover:text-emerald-700
                           active:scale-95">

                ⛶

                Ajustar vista

            </button>


            <button id="load-more"
                    class="inline-flex items-center gap-2 rounded-xl
                           bg-emerald-600 px-5 py-2.5
                           text-sm font-bold text-white shadow-md
                           shadow-emerald-200
                           transition-all duration-200
                           hover:-translate-y-0.5 hover:bg-emerald-700
                           hover:shadow-lg active:scale-95">

                <span class="text-lg leading-none">
                    +
                </span>

                Cargar más árboles

            </button>


            <span id="load-status"
                  class="ml-auto text-xs font-medium text-slate-400">
                0 de X cargados
            </span>

        </div>


        {{-- ========================================================= --}}
        {{-- DIJKSTRA --}}
        {{-- ========================================================= --}}

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">


            {{-- Panel algoritmo --}}
            <div class="overflow-hidden rounded-3xl border border-slate-200
                        bg-white shadow-lg">

                <div class="border-b border-slate-100 bg-gradient-to-br
                            from-emerald-600 to-green-700 p-5 text-white">

                    <div class="flex items-center gap-3">

                        <div class="flex h-11 w-11 items-center justify-center
                                    rounded-xl bg-white/15 text-xl">
                            🧭
                        </div>

                        <div>

                            <p class="text-xs font-semibold uppercase tracking-wider text-emerald-100">
                                Optimización
                            </p>

                            <h2 class="text-lg font-black">
                                Ruta óptima
                            </h2>

                        </div>

                    </div>

                    <p class="mt-3 text-xs leading-relaxed text-emerald-50">
                        Encuentra el camino de menor costo entre dos árboles
                        utilizando el algoritmo de Dijkstra.
                    </p>

                </div>


                <div class="space-y-5 p-5">


                    {{-- Origen --}}
                    <div>

                        <label for="dij-src"
                               class="mb-2 flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-slate-500">

                            <span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>

                            Origen

                        </label>

                        <select id="dij-src"
                                class="block w-full rounded-xl border-slate-200
                                       bg-slate-50 px-4 py-3 text-sm font-medium
                                       text-slate-700 shadow-sm
                                       transition focus:border-emerald-500
                                       focus:bg-white focus:ring-emerald-500">
                        </select>

                    </div>


                    {{-- Flecha --}}
                    <div class="flex justify-center">

                        <div class="flex h-8 w-8 items-center justify-center
                                    rounded-full bg-slate-100 text-slate-400">
                            ↓
                        </div>

                    </div>


                    {{-- Destino --}}
                    <div>

                        <label for="dij-dst"
                               class="mb-2 flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-slate-500">

                            <span class="h-2.5 w-2.5 rounded-full bg-red-500"></span>

                            Destino

                        </label>

                        <select id="dij-dst"
                                class="block w-full rounded-xl border-slate-200
                                       bg-slate-50 px-4 py-3 text-sm font-medium
                                       text-slate-700 shadow-sm
                                       transition focus:border-emerald-500
                                       focus:bg-white focus:ring-emerald-500">
                        </select>

                    </div>


                    {{-- Botones --}}
                    <div class="grid grid-cols-2 gap-2">

                        <button id="btn-dijkstra"
                                class="rounded-xl bg-emerald-600 px-3 py-3
                                       text-xs font-black text-white shadow-sm
                                       transition-all duration-200
                                       hover:-translate-y-0.5 hover:bg-emerald-700
                                       active:scale-95">

                            ▶ Ejecutar Dijkstra

                        </button>


                        <button id="btn-allpaths"
                                class="rounded-xl bg-blue-600 px-3 py-3
                                       text-xs font-black text-white shadow-sm
                                       transition-all duration-200
                                       hover:-translate-y-0.5 hover:bg-blue-700
                                       active:scale-95">

                            🗺 Todas

                        </button>


                        <button id="btn-tsp"
                                class="rounded-xl bg-violet-600 px-3 py-3
                                       text-xs font-black text-white shadow-sm
                                       transition-all duration-200
                                       hover:-translate-y-0.5 hover:bg-violet-700
                                       active:scale-95">

                            🔄 Ruta completa

                        </button>


                        <button id="btn-clear"
                                class="rounded-xl border border-slate-200
                                       bg-slate-50 px-3 py-3
                                       text-xs font-bold text-slate-600
                                       transition-all duration-200
                                       hover:bg-slate-100
                                       active:scale-95">

                            ✕ Limpiar

                        </button>

                    </div>

                </div>

            </div>


            {{-- ===================================================== --}}
            {{-- RESULTADO --}}
            {{-- ===================================================== --}}

            <div class="overflow-hidden rounded-3xl border border-slate-200
                        bg-white shadow-lg lg:col-span-2">

                <div class="flex items-center justify-between
                            border-b border-slate-100 px-5 py-4">

                    <div class="flex items-center gap-3">

                        <div class="flex h-10 w-10 items-center justify-center
                                    rounded-xl bg-blue-50 text-lg">
                            📊
                        </div>

                        <div>

                            <h2 class="font-bold text-slate-800">
                                Resultado del análisis
                            </h2>

                            <p class="text-xs text-slate-400">
                                Información generada por el algoritmo
                            </p>

                        </div>

                    </div>

                    <span class="rounded-full bg-slate-100 px-3 py-1
                                 text-xs font-semibold text-slate-500">
                        Dijkstra
                    </span>

                </div>


                <div id="dij-result"
                     class="min-h-[280px] p-6">

                    <div class="flex min-h-[240px] flex-col items-center
                                justify-center text-center">

                        <div class="mb-4 flex h-16 w-16 items-center justify-center
                                    rounded-2xl bg-slate-100 text-3xl
                                    animate-pulse">
                            🧭
                        </div>

                        <h3 class="font-bold text-slate-700">
                            Esperando análisis
                        </h3>

                        <p class="mt-1 max-w-sm text-sm text-slate-400">
                            Selecciona un origen y un destino para calcular
                            la ruta óptima dentro del cultivo.
                        </p>

                    </div>

                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- LOG --}}
        {{-- ========================================================= --}}

        <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200
                    bg-white shadow-sm">

            <details>

                <summary class="flex cursor-pointer items-center justify-between
                                px-5 py-4 text-sm font-bold text-slate-700
                                transition hover:bg-slate-50">

                    <span class="flex items-center gap-2">
                        <span class="flex h-8 w-8 items-center justify-center
                                     rounded-lg bg-slate-100">
                            📋
                        </span>

                        Registro de ejecución
                    </span>

                    <span class="text-xs font-normal text-slate-400">
                        Mostrar / ocultar
                    </span>

                </summary>


                <div class="border-t border-slate-100 bg-slate-950 p-4">

                    <div id="algo-log"
                         class="max-h-52 overflow-y-auto space-y-1
                                font-mono text-xs leading-relaxed text-slate-400">

                        <div>
                            <span class="text-emerald-400">
                                [SYSTEM]
                            </span>

                            Esperando acciones...
                        </div>

                    </div>

                </div>

            </details>

        </div>

    </div>

</div>



{{-- 1. Cytoscape Base --}}
<script src="https://unpkg.com/cytoscape@3.28.1/dist/cytoscape.min.js"></script>

{{-- 2. Dependencias requeridas por cose-bilkent --}}
<script src="https://unpkg.com/layout-base/layout-base.js"></script>
<script src="https://unpkg.com/cose-base/cose-base.js"></script>

{{-- 3. Extensión cose-bilkent --}}
<script src="https://unpkg.com/cytoscape-cose-bilkent@4.1.0/cytoscape-cose-bilkent.js"></script>
 
<script>
    (function() {
        // Esperar a que Cytoscape y la extensión estén cargadas
        if (typeof cytoscape === 'undefined') {
            console.error('Cytoscape no se cargó correctamente.');
            return;
        }

        // Registrar la extensión cose-bilkent si está disponible
        if (typeof cytoscapeCoseBilkent !== 'undefined') {
            cytoscape.use(cytoscapeCoseBilkent);
            console.log('✅ Extensión cose-bilkent registrada.');
        } else {
            console.warn('⚠️ Extensión cose-bilkent no encontrada. Se usará layout "grid" como fallback.');
        }

        // --- Resto del código ---
        document.addEventListener('DOMContentLoaded', function() {
            // ... (todo el código anterior, pero ahora con layout condicional)
        });
    })();
</script>

<script>
    // Código completo (igual que antes, pero con layout condicional)
    document.addEventListener('DOMContentLoaded', function() {
        const CICLO_ID = {{ $cicloProductivo->id }};
        const API_URL = '{{ route("ciclos-productivos.grafo-estadisticas", $cicloProductivo) }}';
        const CHUNK_SIZE = 150;

        let allNodes = [], allEdges = [], loadedNodes = [], loadedEdges = [];
        let cy = null, currentPath = null;

        const cyContainer = document.getElementById('cy');
        const loadingSpinner = document.getElementById('loading-spinner');
        const nodeCountSpan = document.getElementById('node-count');
        const loadStatusSpan = document.getElementById('load-status');
        const zoomLevelSpan = document.getElementById('zoom-level');
        const logDiv = document.getElementById('algo-log');
        const resultDiv = document.getElementById('dij-result');

        function clearLog() { logDiv.innerHTML = '<div>📋 Log de ejecución</div>'; }
        function appendLog(msg, cls = '') {
            const p = document.createElement('div');
            p.className = cls || 'text-gray-400';
            p.textContent = msg;
            logDiv.appendChild(p);
            logDiv.scrollTop = logDiv.scrollHeight;
        }
        function statRow(label, value) {
            return `<div class="stat-row"><span>${label}</span><span class="font-mono">${value}</span></div>`;
        }
        function setResult(html) { resultDiv.innerHTML = html; }

        const estadoColors = {
            'excelente': '#22c55e', 'bueno': '#3b82f6', 'regular': '#f59e0b',
            'malo': '#ef4444', 'muerto': '#1f2937'
        };

        function initCytoscape() {
            // Determinar qué layout usar
            let layoutName = 'cose-bilkent';
            // Verificar si la extensión está registrada
            try {
                // Intentar crear una instancia temporal para probar
                const testCy = cytoscape({
                    container: document.createElement('div'),
                    elements: [],
                    layout: { name: 'cose-bilkent' }
                });
                testCy.destroy();
            } catch (e) {
                console.warn('Layout cose-bilkent no disponible, usando "grid".');
                layoutName = 'grid';
            }

            const cyInstance = cytoscape({
                container: cyContainer,
                style: [
                    {
                        selector: 'node',
                        style: {
                            'background-color': function(ele) {
                                const estado = ele.data('estado') || 'regular';
                                return estadoColors[estado] || '#9ca3af';
                            },
                            'label': 'data(label)',
                            'width': 32,
                            'height': 32,
                            'font-size': '8px',
                            'text-valign': 'bottom',
                            'text-halign': 'center',
                            'color': '#e0f0e0',
                            'text-outline-width': 2,
                            'text-outline-color': '#0d1510',
                            'border-width': 1.5,
                            'border-color': '#162419',
                        }
                    },
                    {
                        selector: 'edge',
                        style: {
                            'width': 1.5,
                            'line-color': '#2d4a38',
                            'target-arrow-color': '#2d4a38',
                            'target-arrow-shape': 'none',
                            'curve-style': 'bezier',
                            'opacity': 0.6,
                            'label': function(ele) {
                                return ele.data('distancia') ? ele.data('distancia').toFixed(0) + 'm' : '';
                            },
                            'font-size': '7px',
                            'text-rotation': 'autorotate',
                            'text-margin-y': -6,
                            'color': '#4a6a4a',
                            'text-outline-width': 1,
                            'text-outline-color': '#0d1510',
                        }
                    },
                    {
                        selector: '.ruta-dijkstra',
                        style: {
                            'line-color': '#00e676',
                            'target-arrow-color': '#00e676',
                            'width': 4,
                            'opacity': 1,
                            'background-color': '#00e676',
                            'border-color': '#00e676',
                            'border-width': 2,
                        }
                    },
                    {
                        selector: '.nodo-origen',
                        style: { 'border-color': '#00c8ff', 'border-width': 3 }
                    },
                    {
                        selector: '.nodo-destino',
                        style: { 'border-color': '#c084fc', 'border-width': 3 }
                    }
                ],
                layout: {
                    name: layoutName,
                    idealEdgeLength: 120,
                    nodeRepulsion: 8000,
                    gravity: 0.25,
                    numIter: 500,
                    animate: true,
                    animationDuration: 500,
                    fit: true,
                    padding: 30,
                },
                wheelSensitivity: 0.5,
                minZoom: 0.1,
                maxZoom: 4.0,
            });

            cyInstance.on('zoom', function() {
                zoomLevelSpan.textContent = `Zoom: ${cyInstance.zoom().toFixed(2)}x`;
            });

            cyInstance.on('mouseover', 'node', function(evt) {
                const node = evt.target;
                cyContainer.title = `${node.data('label')} · Estado: ${node.data('estado')} · Producción: ${node.data('produccion')||0} kg`;
            });
            cyInstance.on('mouseout', 'node', function() {
                cyContainer.title = '';
            });

            return cyInstance;
        }

        async function fetchData() {
            loadingSpinner.classList.remove('hidden');
            try {
                const response = await fetch(API_URL);
                const data = await response.json();
                if (!data.success) throw new Error(data.message || 'Error al cargar datos');

                const { leaflet, estadisticas, grafo_dijkstra } = data;
                const markers = leaflet.marcadores;
                const aristas = grafo_dijkstra.aristas;

                if (markers.length === 0) throw new Error('No hay árboles para mostrar');

                allNodes = markers.map(m => ({
                    id: m.id.toString(),
                    label: m.codigo,
                    estado: m.estado_vital,
                    produccion: m.produccion_kg,
                    lat: m.lat,
                    lng: m.lng,
                }));

                allEdges = aristas.map(e => ({
                    id: `${e.origen}-${e.destino}`,
                    source: e.origen.toString(),
                    target: e.destino.toString(),
                    distancia: parseFloat(e.peso_distancia),
                    contagio: parseFloat(e.peso_contagio),
                    tipo: e.tipo_contacto,
                }));

                document.getElementById('total-arboles').textContent = estadisticas.total_arboles;
                document.getElementById('produccion-total').textContent = estadisticas.produccion_total_kg;
                document.getElementById('produccion-promedio').textContent = estadisticas.produccion_promedio_kg;
                document.getElementById('total-conexiones').textContent = estadisticas.total_conexiones_red;

                const desgloseDiv = document.getElementById('desglose-estados');
                desgloseDiv.innerHTML = '';
                for (const [estado, count] of Object.entries(estadisticas.desglose_estado_vital)) {
                    const badge = document.createElement('span');
                    badge.className = 'px-3 py-1 rounded-full text-xs font-semibold text-white';
                    badge.style.backgroundColor = estadoColors[estado] || '#9ca3af';
                    badge.textContent = `${estado}: ${count}`;
                    desgloseDiv.appendChild(badge);
                }
                const saludable = document.createElement('span');
                saludable.className = 'px-3 py-1 text-xs text-gray-700 dark:text-gray-300';
                saludable.textContent = `✅ Saludables (excelente): ${estadisticas.porcentaje_saludables}%`;
                desgloseDiv.appendChild(saludable);

                if (!cy) cy = initCytoscape();
                loadChunk(0);
                populateSelects();
                updateCounters();

                loadingSpinner.classList.add('hidden');
                return true;
            } catch (error) {
                loadingSpinner.classList.add('hidden');
                console.error(error);
                cyContainer.innerHTML = `<div class="text-red-500 p-4">${error.message}</div>`;
                return false;
            }
        }

        function loadChunk(startIndex) {
            const endIndex = Math.min(startIndex + CHUNK_SIZE, allNodes.length);
            const chunkNodes = allNodes.slice(startIndex, endIndex);
            const chunkNodeIds = new Set(chunkNodes.map(n => n.id));
            const loadedNodeIds = new Set(loadedNodes.map(n => n.id));

            const chunkEdges = allEdges.filter(e => {
                const sourceIn = chunkNodeIds.has(e.source);
                const targetIn = chunkNodeIds.has(e.target);
                const sourceLoaded = loadedNodeIds.has(e.source);
                const targetLoaded = loadedNodeIds.has(e.target);
                return (sourceIn && targetIn) || (sourceIn && targetLoaded) || (targetIn && sourceLoaded);
            });

            const nodesToAdd = chunkNodes.map(n => ({
                group: 'nodes',
                data: { id: n.id, label: n.label, estado: n.estado, produccion: n.produccion, lat: n.lat, lng: n.lng },
                style: { 'background-color': estadoColors[n.estado] || '#9ca3af' }
            }));

            const edgesToAdd = chunkEdges.map(e => ({
                group: 'edges',
                data: { id: e.id, source: e.source, target: e.target, distancia: e.distancia, contagio: e.contagio, tipo: e.tipo }
            }));

            cy.batch(() => {
                cy.add(nodesToAdd);
                cy.add(edgesToAdd);
            });

            loadedNodes = loadedNodes.concat(chunkNodes);
            const existingEdgeIds = new Set(loadedEdges.map(e => e.id));
            const newEdges = chunkEdges.filter(e => !existingEdgeIds.has(e.id));
            loadedEdges = loadedEdges.concat(newEdges);

            // Re-layout (incremental no es trivial, se vuelve a ejecutar)
            const layout = cy.layout({
                name: 'cose-bilkent',
                idealEdgeLength: 120,
                nodeRepulsion: 8000,
                gravity: 0.25,
                numIter: 300,
                animate: true,
                animationDuration: 400,
                fit: false,
            });
            layout.run();

            updateCounters();
            return chunkNodes.length;
        }

        function loadMore() {
            if (loadedNodes.length >= allNodes.length) {
                appendLog('✅ Todos los árboles ya están cargados.', 'text-green-400');
                return;
            }
            const added = loadChunk(loadedNodes.length);
            appendLog(`📦 Cargados ${added} nuevos árboles (total: ${loadedNodes.length}/${allNodes.length})`, 'text-blue-300');
        }

        function updateCounters() {
            nodeCountSpan.textContent = `${loadedNodes.length} nodos`;
            loadStatusSpan.textContent = `(${loadedNodes.length} de ${allNodes.length} cargados)`;
            populateSelects();
        }

        function populateSelects() {
            const srcSelect = document.getElementById('dij-src');
            const dstSelect = document.getElementById('dij-dst');
            const srcVal = srcSelect.value;
            const dstVal = dstSelect.value;
            srcSelect.innerHTML = '';
            dstSelect.innerHTML = '';
            loadedNodes.forEach((n, i) => {
                const opt1 = document.createElement('option');
                opt1.value = n.id;
                opt1.textContent = `${n.label} (${n.estado})`;
                srcSelect.appendChild(opt1);
                const opt2 = document.createElement('option');
                opt2.value = n.id;
                opt2.textContent = `${n.label} (${n.estado})`;
                dstSelect.appendChild(opt2);
            });
            if (srcVal && srcSelect.querySelector(`option[value="${srcVal}"]`)) srcSelect.value = srcVal;
            else if (loadedNodes.length > 0) srcSelect.value = loadedNodes[0].id;
            if (dstVal && dstSelect.querySelector(`option[value="${dstVal}"]`)) dstSelect.value = dstVal;
            else if (loadedNodes.length > 1) dstSelect.value = loadedNodes[1].id;
            else if (loadedNodes.length > 0) dstSelect.value = loadedNodes[0].id;
        }

        function clearPath() {
            if (currentPath) { cy.remove(currentPath); currentPath = null; }
            cy.elements('.ruta-dijkstra').removeClass('ruta-dijkstra');
            cy.elements('.nodo-origen').removeClass('nodo-origen');
            cy.elements('.nodo-destino').removeClass('nodo-destino');
            setResult('Ejecuta el algoritmo para ver el resultado...');
        }

        function runDijkstra() {
            const srcId = document.getElementById('dij-src').value;
            const dstId = document.getElementById('dij-dst').value;
            if (!srcId || !dstId || srcId === dstId) {
                appendLog('⚠️ Selecciona origen y destino diferentes.', 'text-yellow-400');
                return;
            }
            clearLog();
            appendLog(`🔍 Dijkstra desde ${srcId} → ${dstId}`);
            const srcNode = cy.getElementById(srcId);
            const dstNode = cy.getElementById(dstId);
            if (!srcNode || !dstNode || srcNode.length === 0 || dstNode.length === 0) {
                appendLog('⚠️ Uno de los nodos no está cargado.', 'text-yellow-400');
                return;
            }
            try {
                const dijkstra = cy.elements().dijkstra({
                    root: srcNode,
                    weight: edge => {
                        const d = edge.data('distancia');
                        if (d === undefined || d === null || isNaN(d) || d <= 0) {
                            console.warn('⚠️ Arista con distancia inválida:', edge.id(), edge.data('distancia'));
                            return 999999; // penaliza en vez de premiar
                        }
                        return d;
                    },
                    directed: false,
                });
                const path = dijkstra.pathTo(dstNode);
                const distance = dijkstra.distanceTo(dstNode);
                if (!path || path.length === 0 || distance === Infinity) {
                    appendLog('❌ No se encontró ruta.', 'text-red-400');
                    setResult(`<div class="text-red-400 font-bold">❌ No hay ruta disponible</div>`);
                    return;
                }
                clearPath();
                path.addClass('ruta-dijkstra');
                srcNode.addClass('nodo-origen');
                dstNode.addClass('nodo-destino');
                currentPath = path;
                const pathLabels = path.nodes().map(n => n.data('label')).join(' → ');
                setResult(`
                    <div class="text-green-400 font-bold mb-1">✅ RUTA ENCONTRADA</div>
                    ${statRow('Distancia total', distance.toFixed(2) + ' m')}
                    ${statRow('Nodos en ruta', path.length)}
                    ${statRow('Ruta', pathLabels)}
                    ${statRow('Tiempo estimado (60m/min)', Math.round(distance / 60 * 2) + ' min')}
                `);
                appendLog(`✅ Ruta: ${pathLabels} (${distance.toFixed(2)} m)`, 'text-green-400');
                cy.fit(path, 50);
            } catch (e) {
                appendLog(`❌ Error: ${e.message}`, 'text-red-400');
            }
        }

        function runAllPaths() {
            const srcId = document.getElementById('dij-src').value;
            if (!srcId) { appendLog('⚠️ Selecciona origen.', 'text-yellow-400'); return; }
            clearLog();
            appendLog(`🗺 Calculando distancias desde ${srcId}...`);
            const srcNode = cy.getElementById(srcId);
            if (!srcNode || srcNode.length === 0) { appendLog('⚠️ Nodo no cargado.', 'text-yellow-400'); return; }
            try {
                const dijkstra = cy.elements().dijkstra({
                    root: srcNode,
                    weight: edge => edge.data('distancia') || 1,
                    directed: false,
                });
                const nodes = cy.nodes();
                let results = [];
                nodes.forEach(node => {
                    const d = dijkstra.distanceTo(node);
                    results.push({ id: node.id(), label: node.data('label'), distance: d });
                    appendLog(`  ${node.data('label')}: ${d === Infinity ? '∞' : d.toFixed(2) + ' m'}`);
                });
                const reachable = results.filter(r => r.distance !== Infinity).length;
                setResult(`
                    <div class="text-blue-400 font-bold mb-1">📊 DISTANCIAS DESDE ${srcNode.data('label')}</div>
                    ${statRow('Nodos alcanzables', `${reachable}/${results.length}`)}
                    ${statRow('Distancia máxima', Math.max(...results.filter(r => r.distance !== Infinity).map(r => r.distance)).toFixed(2) + ' m')}
                `);
                cy.nodes().forEach(node => {
                    const d = dijkstra.distanceTo(node);
                    let color = '#162419';
                    if (d === Infinity) color = '#ff3d57';
                    else if (d < 50) color = '#00e676';
                    else if (d < 100) color = '#ffb700';
                    else color = '#c084fc';
                    node.style('border-color', color);
                    node.style('border-width', 2);
                });
            } catch (e) {
                appendLog(`❌ Error: ${e.message}`, 'text-red-400');
            }
        }

        function runTSP() {
            // Implementación simplificada (igual que antes)
            const srcId = document.getElementById('dij-src').value;
            if (!srcId) { appendLog('⚠️ Selecciona origen.', 'text-yellow-400'); return; }
            clearLog();
            appendLog(`🔄 Ruta de cosecha (vecino más cercano) desde ${srcId}...`);
            const srcNode = cy.getElementById(srcId);
            if (!srcNode || srcNode.length === 0) { appendLog('⚠️ Nodo no cargado.', 'text-yellow-400'); return; }
            try {
                const nodes = cy.nodes();
                const n = nodes.length;
                if (n < 2) { appendLog('⚠️ Se necesitan al menos 2 nodos.', 'text-yellow-400'); return; }
                const nodeIds = nodes.map(n => n.id());
                const distMatrix = {};
                nodeIds.forEach(id1 => {
                    distMatrix[id1] = {};
                    const d = cy.elements().dijkstra({
                        root: cy.getElementById(id1),
                        weight: edge => edge.data('distancia') || 1,
                        directed: false,
                    });
                    nodeIds.forEach(id2 => {
                        distMatrix[id1][id2] = d.distanceTo(cy.getElementById(id2));
                    });
                });
                let current = srcId;
                const visited = new Set([current]);
                const path = [current];
                let totalDist = 0;
                while (visited.size < n) {
                    let nearest = null, minDist = Infinity;
                    for (const id of nodeIds) {
                        if (!visited.has(id)) {
                            const d = distMatrix[current][id];
                            if (d !== Infinity && d < minDist) { minDist = d; nearest = id; }
                        }
                    }
                    if (nearest === null) break;
                    visited.add(nearest);
                    path.push(nearest);
                    totalDist += minDist;
                    current = nearest;
                }
                const pathLabels = path.map(id => cy.getElementById(id).data('label')).join(' → ');
                setResult(`
                    <div class="text-purple-400 font-bold mb-1">🔄 RUTA DE COSECHA (Vecino más cercano)</div>
                    ${statRow('Distancia total', totalDist.toFixed(2) + ' m')}
                    ${statRow('Nodos visitados', path.length)}
                    ${statRow('Secuencia', pathLabels)}
                `);
                appendLog(`✅ Ruta: ${pathLabels} (${totalDist.toFixed(2)} m)`, 'text-purple-400');
                clearPath();
                path.forEach(id => cy.getElementById(id).addClass('ruta-dijkstra'));
                // Resaltar aristas (opcional)
                for (let i = 0; i < path.length - 1; i++) {
                    const edge = cy.edges().filter(e => {
                        return (e.data('source') === path[i] && e.data('target') === path[i+1]) ||
                                (e.data('source') === path[i+1] && e.data('target') === path[i]);
                    });
                    if (edge.length > 0) edge.addClass('ruta-dijkstra');
                }
                cy.fit(path.map(id => cy.getElementById(id)), 50);
            } catch (e) {
                appendLog(`❌ Error: ${e.message}`, 'text-red-400');
            }
        }

        function setupEventListeners() {
            document.getElementById('zoom-in').addEventListener('click', () => {
                const z = cy.zoom();
                cy.zoom(Math.min(z * 1.3, cy.maxZoom()));
            });
            document.getElementById('zoom-out').addEventListener('click', () => {
                const z = cy.zoom();
                cy.zoom(Math.max(z / 1.3, cy.minZoom()));
            });
            document.getElementById('fit-view').addEventListener('click', () => cy.fit(undefined, 30));
            document.getElementById('load-more').addEventListener('click', loadMore);
            document.getElementById('btn-dijkstra').addEventListener('click', runDijkstra);
            document.getElementById('btn-allpaths').addEventListener('click', runAllPaths);
            document.getElementById('btn-tsp').addEventListener('click', runTSP);
            document.getElementById('btn-clear').addEventListener('click', () => {
                clearPath();
                clearLog();
                appendLog('🧹 Ruta limpiada.', 'text-gray-400');
                cy.nodes().forEach(node => {
                    node.style('border-color', '#162419');
                    node.style('border-width', 1.5);
                });
            });
        }

        // Inicialización
        fetchData().then(() => {
            setupEventListeners();
            appendLog('🚀 Sistema listo. Usa los controles.', 'text-green-300');
        });
    });
</script>

</x-app-layout>