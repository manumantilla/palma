<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ERP - Gestión agrícola para Colombia</title>
    <meta name="description" content="Software de gestión agrícola para cultivos colombianos: lotes, árboles, fenología, cosecha, inventario y finanzas.">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800" rel="stylesheet" />

    <style>
        :root{
            --fondo:#fbfaf6;
            --blanco:#ffffff;
            --texto:#16261b;
            --suave:#4f6155;
            --tenue:#7d8b81;
            --linea:#e4e7dd;
            --verde:#1f7a3f;
            --verde-claro:#e8f3e6;
            --verde-vivo:#3fa34d;
            --amarillo:#f2c230;
            --alerta:#e2502c;
        }
        *{box-sizing:border-box;margin:0;padding:0;}
        html{scroll-behavior:smooth;}
        body{
            font-family:'Figtree', system-ui, sans-serif;
            background:var(--fondo);
            color:var(--texto);
            overflow-x:hidden;
            -webkit-font-smoothing:antialiased;
        }
        a{color:inherit;text-decoration:none;}
        ::selection{background:var(--verde);color:#fff;}

        /* ============ NAV ============ */
        nav{
            position:fixed;inset:0 0 auto 0;z-index:30;
            display:flex;justify-content:space-between;align-items:center;
            padding:1rem clamp(1rem,4vw,3rem);
            transition:background .35s, box-shadow .35s;
        }
        nav.solid{
            background:rgba(251,250,246,.9);
            backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);
            box-shadow:0 1px 0 var(--linea);
        }
        .brand{display:flex;align-items:center;gap:.6rem;font-weight:800;font-size:1.2rem;letter-spacing:-.01em;}
        .brand svg{width:32px;height:32px;}
        .actions{display:flex;gap:.6rem;align-items:center;}
        .btn{
            display:inline-flex;align-items:center;gap:.5rem;
            padding:.7rem 1.25rem;border-radius:10px;
            font-weight:600;font-size:.92rem;
            transition:transform .25s, box-shadow .25s, background .25s, border-color .25s;
            white-space:nowrap;
        }
        .btn-linea{background:var(--blanco);border:1px solid var(--linea);color:var(--texto);}
        .btn-linea:hover{border-color:var(--verde);color:var(--verde);}
        .btn-verde{background:var(--verde);color:#fff;box-shadow:0 6px 18px -6px rgba(31,122,63,.6);}
        .btn-verde:hover{transform:translateY(-2px);box-shadow:0 12px 26px -8px rgba(31,122,63,.65);}
        .btn svg{width:16px;height:16px;transition:transform .25s;}
        .btn:hover svg{transform:translateX(3px);}

        /* ============ HERO ============ */
        .hero{position:relative;height:100svh;min-height:660px;overflow:hidden;background:#e9f1ec;}
        #campo{position:absolute;inset:0;width:100%;height:100%;display:block;}
        .hero::before{
            content:'';position:absolute;inset:0;z-index:1;pointer-events:none;
            background:linear-gradient(90deg, rgba(251,250,246,.94) 0%, rgba(251,250,246,.78) 34%, rgba(251,250,246,0) 64%);
        }
        .hero::after{
            content:'';position:absolute;inset:auto 0 0 0;height:22%;z-index:1;
            background:linear-gradient(to top, var(--fondo), rgba(251,250,246,0));
            pointer-events:none;
        }
        .hero-content{
            position:absolute;z-index:5;left:clamp(1.2rem,5vw,4.5rem);top:50%;transform:translateY(-44%);
            max-width:min(640px,90vw);
        }
        .etiqueta{
            display:inline-flex;align-items:center;gap:.55rem;
            font-size:.85rem;font-weight:600;color:var(--verde);
            background:var(--verde-claro);border:1px solid #cfe5cc;
            padding:.4rem .85rem;border-radius:999px;
            opacity:0;animation:aparecer .8s ease forwards .2s;
        }
        .bandera{display:inline-flex;width:18px;height:12px;border-radius:2px;overflow:hidden;flex-direction:column;box-shadow:0 0 0 1px rgba(0,0,0,.06);}
        .bandera i{display:block;}
        .bandera i:nth-child(1){flex:2;background:#fcd116;}
        .bandera i:nth-child(2){flex:1;background:#003893;}
        .bandera i:nth-child(3){flex:1;background:#ce1126;}
        h1{
            font-weight:800;
            font-size:clamp(2.5rem,5.6vw,4.6rem);
            line-height:1.04;letter-spacing:-.035em;
            margin:1.1rem 0 1.2rem;
        }
        h1 .w{display:inline-block;overflow:hidden;vertical-align:bottom;padding-bottom:.06em;}
        h1 .w > span{display:inline-block;transform:translateY(100%);opacity:0;animation:subir .9s cubic-bezier(.2,.9,.2,1) forwards;}
        h1 .marca{color:var(--verde);}
        .lead{
            font-size:clamp(1.02rem,1.4vw,1.15rem);line-height:1.65;color:var(--suave);max-width:540px;
            opacity:0;animation:aparecer .9s ease forwards 1.1s;
        }
        .cta{display:flex;gap:.7rem;flex-wrap:wrap;margin-top:1.9rem;opacity:0;animation:aparecer .9s ease forwards 1.3s;}
        .cta .btn{padding:.9rem 1.5rem;font-size:.97rem;}

        /* Panel de datos */
        .hud{
            position:absolute;z-index:5;top:clamp(5.5rem,14vh,8rem);right:clamp(1rem,4vw,3rem);
            width:280px;
            background:rgba(255,255,255,.86);backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);
            border:1px solid rgba(228,231,221,.9);border-radius:16px;
            box-shadow:0 20px 50px -24px rgba(22,38,27,.35);
            padding:1rem 1.1rem;font-size:.85rem;
            opacity:0;animation:aparecer .9s ease forwards 1.6s;
        }
        .hud-top{display:flex;justify-content:space-between;align-items:center;margin-bottom:.6rem;}
        .hud-top strong{font-size:.9rem;}
        .hud-top span{font-size:.72rem;font-weight:600;color:var(--tenue);background:#f1f3ec;padding:.2rem .5rem;border-radius:6px;}
        .hud-row{display:flex;justify-content:space-between;align-items:baseline;padding:.45rem 0;border-top:1px solid #eef0e8;}
        .hud-row span{color:var(--suave);}
        .hud-row b{font-weight:700;font-variant-numeric:tabular-nums;}
        .hud-row b.ok{color:var(--verde);}
        .hud-row b.alerta{color:var(--alerta);}
        .hud-leyenda{display:flex;gap:.9rem;margin-top:.7rem;color:var(--tenue);font-size:.75rem;}
        .hud-leyenda i{display:inline-block;width:8px;height:8px;border-radius:50%;margin-right:.3rem;}

        /* ============ SECCIONES ============ */
        section{position:relative;padding:clamp(4.5rem,11vh,7.5rem) clamp(1.2rem,5vw,4.5rem);}
        .contenedor{max-width:1220px;margin:0 auto;}
        .pre{font-size:.9rem;font-weight:700;color:var(--verde);}
        h2{
            font-weight:800;font-size:clamp(2rem,4vw,3.1rem);line-height:1.08;letter-spacing:-.03em;
            margin:.7rem 0 1rem;max-width:20ch;
        }
        .sub{color:var(--suave);font-size:1.06rem;line-height:1.65;max-width:600px;}

        /* Cultivos */
        .cultivos{border-block:1px solid var(--linea);background:var(--blanco);padding:1.1rem 0;overflow:hidden;white-space:nowrap;}
        .cultivos-track{display:inline-flex;gap:.7rem;animation:desliza 45s linear infinite;}
        .chip{
            display:inline-flex;align-items:center;gap:.5rem;
            padding:.5rem 1rem;border-radius:999px;background:var(--fondo);border:1px solid var(--linea);
            font-weight:600;font-size:.92rem;color:var(--suave);
        }
        .chip i{width:8px;height:8px;border-radius:50%;}

        /* Ciclo fenológico */
        .ciclo{display:grid;grid-template-columns:1fr 1.05fr;gap:clamp(2rem,6vw,5rem);align-items:center;}
        .tabs{display:inline-flex;gap:.3rem;background:#f0f2ea;padding:.3rem;border-radius:12px;margin-top:1.5rem;}
        .tab{
            border:0;background:transparent;font:inherit;font-weight:600;font-size:.92rem;color:var(--suave);
            padding:.55rem 1.05rem;border-radius:9px;cursor:pointer;transition:background .25s,color .25s,box-shadow .25s;
        }
        .tab.on{background:var(--blanco);color:var(--texto);box-shadow:0 2px 8px -3px rgba(22,38,27,.25);}
        .reloj{position:relative;width:min(440px,86vw);aspect-ratio:1;margin:0 auto;}
        .reloj svg{width:100%;height:100%;overflow:visible;}
        .reloj-centro{position:absolute;inset:0;display:grid;place-content:center;text-align:center;pointer-events:none;}
        .reloj-cultivo{font-size:.9rem;font-weight:600;color:var(--tenue);}
        .reloj-dia{font-size:clamp(3rem,7vw,4.4rem);font-weight:800;line-height:1.05;letter-spacing:-.04em;font-variant-numeric:tabular-nums;}
        .reloj-dia small{font-size:.3em;font-weight:600;color:var(--tenue);margin-left:.2em;letter-spacing:0;}
        .reloj-fase{font-size:1rem;font-weight:700;margin-top:.3rem;transition:color .3s;}
        .reloj-bbch{font-size:.8rem;color:var(--tenue);margin-top:.2rem;}
        .fases{list-style:none;margin-top:1.6rem;display:grid;gap:.4rem;}
        .fases li{
            display:grid;grid-template-columns:auto 1fr auto;gap:.9rem;align-items:center;
            padding:.85rem 1rem;border-radius:12px;border:1px solid transparent;
            transition:background .35s,border-color .35s;
        }
        .fases li .pt{width:10px;height:10px;border-radius:50%;}
        .fases li h4{font-weight:700;font-size:.98rem;}
        .fases li p{color:var(--tenue);font-size:.86rem;margin-top:.1rem;line-height:1.45;}
        .fases li .d{font-size:.85rem;font-weight:600;color:var(--tenue);font-variant-numeric:tabular-nums;}
        .fases li.on{background:var(--blanco);border-color:var(--linea);box-shadow:0 8px 24px -16px rgba(22,38,27,.3);}
        .nota{margin-top:1.3rem;font-size:.93rem;color:var(--suave);line-height:1.6;padding:.9rem 1rem;background:#fff8e2;border-radius:10px;border:1px solid #f4e3a8;}

        /* Colombia */
        .colombia{background:var(--blanco);border-block:1px solid var(--linea);}
        .rejilla{display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-top:3rem;}
        .tarjeta{
            padding:1.6rem;border-radius:16px;border:1px solid var(--linea);background:var(--fondo);
            transition:transform .3s, box-shadow .3s, border-color .3s;
        }
        .tarjeta:hover{transform:translateY(-4px);border-color:#cfe5cc;box-shadow:0 18px 40px -24px rgba(22,38,27,.35);}
        .tarjeta .ico{width:44px;height:44px;border-radius:11px;display:grid;place-items:center;background:var(--verde-claro);color:var(--verde);margin-bottom:1rem;}
        .tarjeta .ico svg{width:22px;height:22px;}
        .tarjeta h3{font-size:1.08rem;font-weight:700;}
        .tarjeta p{color:var(--suave);font-size:.93rem;line-height:1.55;margin-top:.4rem;}

        /* Módulos */
        .mod-head{display:flex;justify-content:space-between;align-items:flex-end;gap:2rem;flex-wrap:wrap;margin-bottom:2.6rem;}
        .categorias{display:grid;gap:1rem;}
        .categoria{display:grid;grid-template-columns:230px 1fr;gap:1rem;align-items:start;}
        .categoria h3{font-size:1.1rem;font-weight:700;padding-top:1.1rem;display:flex;gap:.6rem;align-items:center;}
        .categoria h3 span{display:grid;place-items:center;width:26px;height:26px;border-radius:7px;background:var(--verde-claro);color:var(--verde);font-size:.8rem;font-weight:800;}
        .modulos{display:grid;grid-template-columns:repeat(4,1fr);gap:.7rem;}
        .modulo{
            position:relative;padding:1.1rem 1.15rem;background:var(--blanco);border:1px solid var(--linea);border-radius:12px;
            transition:border-color .25s, box-shadow .25s, transform .25s;
        }
        .modulo:hover{border-color:#bcdcb8;transform:translateY(-2px);box-shadow:0 12px 26px -18px rgba(22,38,27,.4);}
        .modulo h4{font-weight:700;font-size:.97rem;}
        .modulo p{color:var(--tenue);font-size:.87rem;line-height:1.45;margin-top:.3rem;}

        /* Cifras */
        .cifras{display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;}
        .cifra{padding:1.6rem;border-radius:16px;background:var(--blanco);border:1px solid var(--linea);}
        .cifra .num{font-size:clamp(2.4rem,4.5vw,3.4rem);font-weight:800;letter-spacing:-.04em;line-height:1;color:var(--verde);font-variant-numeric:tabular-nums;}
        .cifra .lbl{color:var(--suave);font-size:.95rem;margin-top:.55rem;font-weight:500;}

        /* Cierre */
        .cierre{text-align:center;}
        .cierre-caja{
            max-width:1220px;margin:0 auto;border-radius:24px;padding:clamp(3rem,7vw,5rem) 1.5rem;
            background:linear-gradient(135deg,#1f7a3f 0%,#2b8f4a 55%,#3fa34d 100%);color:#fff;position:relative;overflow:hidden;
        }
        .cierre-caja::before{
            content:'';position:absolute;inset:0;opacity:.18;
            background-image:repeating-linear-gradient(115deg, rgba(255,255,255,.5) 0 2px, transparent 2px 26px);
            mask-image:linear-gradient(to bottom, transparent, #000 60%);
        }
        .cierre h2{color:#fff;margin:0 auto 1rem;max-width:22ch;}
        .cierre .sub{color:rgba(255,255,255,.86);margin:0 auto 2rem;}
        .cierre .btn{background:#fff;color:var(--verde);padding:1rem 1.7rem;position:relative;}
        .cierre .btn:hover{transform:translateY(-2px);box-shadow:0 14px 30px -10px rgba(0,0,0,.35);}

        footer{
            padding:2.2rem clamp(1.2rem,5vw,4.5rem);
            display:flex;justify-content:space-between;align-items:center;gap:1.2rem;flex-wrap:wrap;
            color:var(--tenue);font-size:.9rem;border-top:1px solid var(--linea);
        }
        footer strong{color:var(--texto);font-weight:600;}

        .reveal{opacity:0;transform:translateY(28px);transition:opacity .8s ease, transform .8s cubic-bezier(.2,.8,.2,1);}
        .reveal.visible{opacity:1;transform:none;}
        .reveal.d1{transition-delay:.08s}.reveal.d2{transition-delay:.16s}.reveal.d3{transition-delay:.24s}

        @keyframes subir{to{transform:none;opacity:1;}}
        @keyframes aparecer{from{opacity:0;transform:translateY(14px);}to{opacity:1;transform:none;}}
        @keyframes desliza{to{transform:translateX(-50%);}}

        @media (max-width:1100px){
            .hud{display:none;}
        }
        @media (max-width:1024px){
            .ciclo{grid-template-columns:1fr;}
            .rejilla{grid-template-columns:repeat(2,1fr);}
            .categoria{grid-template-columns:1fr;}
            .categoria h3{padding-top:0;}
            .modulos{grid-template-columns:repeat(2,1fr);}
            .cifras{grid-template-columns:repeat(2,1fr);}
        }
        @media (max-width:720px){
            .hud{display:none;}
            .actions .btn-linea{display:none;}
            .hero::before{background:linear-gradient(180deg, rgba(251,250,246,.6), rgba(251,250,246,.92) 55%);}
            .rejilla,.modulos{grid-template-columns:1fr;}
        }
        @media (prefers-reduced-motion:reduce){
            *,*::before,*::after{animation-duration:.01ms!important;animation-delay:0s!important;transition-duration:.01ms!important;}
        }
    </style>
</head>
<body>

    <!-- ============ NAV ============ -->
    <nav id="nav">
        <a href="#" class="brand" aria-label="PALMA inicio">
            <svg viewBox="0 0 32 32" fill="none" aria-hidden="true">
                <rect width="32" height="32" rx="9" fill="#1f7a3f"/>
                <path d="M16 25V14" stroke="#fff" stroke-width="2" stroke-linecap="round"/>
                <path d="M16 15c0-4.5 3-7.5 7.5-7.5 0 4.5-3 7.5-7.5 7.5Z" fill="#f2c230"/>
                <path d="M16 18.5c0-3.6-2.4-6-6-6 0 3.6 2.4 6 6 6Z" fill="#fff"/>
            </svg>
            PALMA
        </a>
        <div class="actions">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn btn-verde">Ir al dashboard
                        <svg viewBox="0 0 16 16" fill="none"><path d="M3 8h10m-4-4 4 4-4 4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-linea">Iniciar sesión</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-verde">Registrarse</a>
                    @endif
                @endauth
            @endif
        </div>
    </nav>

    <!-- ============ HERO ============ -->
    <header class="hero">
        <canvas id="campo" aria-hidden="true"></canvas>

        <div class="hud" aria-hidden="true">
            <div class="hud-top"><strong>Recorrido de la finca</strong><span>Simulación</span></div>
            <div class="hud-row"><span>Cultivos en vista</span><b id="hud-cultivos">0</b></div>
            <div class="hud-row"><span>Plantas analizadas</span><b id="hud-vista">0</b></div>
            <div class="hud-row"><span>NDVI promedio</span><b class="ok" id="hud-ndvi">0.00</b></div>
            <div class="hud-row"><span>Plantas con estrés</span><b id="hud-estres">0</b></div>
            <div class="hud-row"><span>Alertas fitosanitarias</span><b class="alerta" id="hud-alertas">0</b></div>
            <div class="hud-leyenda">
                <span><i style="background:#3fa34d"></i>Sana</span>
                <span><i style="background:#f2a516"></i>Estrés</span>
                <span><i style="background:#e2502c"></i>Riesgo</span>
            </div>
        </div>

        <div class="hero-content">
            <span class="etiqueta"><span class="bandera"><i></i><i></i><i></i></span>Hecho para el campo colombiano</span>
            <h1 id="titulo">Tu finca, lote por lote, en una sola <span class="marca">plataforma.</span></h1>
            <p class="lead">
                Café, cacao, palma, plátano, aguacate, maíz, arroz y más. Controla lotes, labores,
                fenología, cosecha, inventario y finanzas, con trazabilidad desde la planta hasta la venta.
            </p>
            <div class="cta">
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn btn-verde">Ir al dashboard
                        <svg viewBox="0 0 16 16" fill="none"><path d="M3 8h10m-4-4 4 4-4 4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-verde">Comenzar ahora
                        <svg viewBox="0 0 16 16" fill="none"><path d="M3 8h10m-4-4 4 4-4 4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                @endauth
                <a href="#modulos" class="btn btn-linea">Ver módulos</a>
            </div>
        </div>
    </header>

    <!-- ============ CULTIVOS ============ -->
    <div class="cultivos" aria-label="Cultivos soportados">
        <div class="cultivos-track">
            @php
                $chips = [
                    ['Café', '#7a4a2a'], ['Cacao', '#8a5a3c'], ['Palma de aceite', '#e2502c'], ['Plátano', '#d4b21c'],
                    ['Banano', '#e8c83a'], ['Aguacate Hass', '#3d6b2c'], ['Maíz', '#e0b43a'], ['Arroz', '#9cc75a'],
                    ['Frijol', '#9b3b2c'], ['Papa', '#b08a5a'], ['Caña panelera', '#6fa34a'], ['Lulo', '#f0a020'],
                    ['Mora', '#6b2347'], ['Cítricos', '#f29a1c'], ['Mango', '#f2b230'], ['Piña', '#d9b02a'],
                ];
            @endphp
            @for ($i = 0; $i < 2; $i++)
                @foreach ($chips as [$nombre, $color])
                    <span class="chip"><i style="background:{{ $color }}"></i>{{ $nombre }}</span>
                @endforeach
            @endfor
        </div>
    </div>

    <!-- ============ CICLO FENOLÓGICO ============ -->
    <section id="ciclo">
        <div class="contenedor ciclo">
            <div class="reveal">
                <div class="reloj">
                    <svg viewBox="0 0 400 400" id="reloj-svg" aria-label="Ciclo fenológico del cultivo">
                        <circle cx="200" cy="200" r="150" fill="none" stroke="#eef0e8" stroke-width="26"/>
                        <g id="arcos" transform="rotate(-90 200 200)"></g>
                        <g id="ticks"></g>
                        <g id="marcador">
                            <circle cx="200" cy="50" r="12" fill="#fff" stroke="#16261b" stroke-width="3"/>
                        </g>
                    </svg>
                    <div class="reloj-centro">
                        <div class="reloj-cultivo" id="reloj-cultivo">Café</div>
                        <div class="reloj-dia"><span id="dia">0</span><small>días</small></div>
                        <div class="reloj-fase" id="fase-nombre">Floración</div>
                        <div class="reloj-bbch" id="fase-bbch">BBCH 60–69</div>
                    </div>
                </div>
            </div>

            <div>
                <span class="pre reveal">Fenología</span>
                <h2 class="reveal d1">Cada cultivo tiene su propio reloj.</h2>
                <p class="sub reveal d2">
                    PALMA conoce las etapas de cada cultivo, ajusta las fechas según la altitud y el clima de tu lote,
                    y programa las labores en el momento justo.
                </p>
                <div class="tabs reveal d2" role="tablist" id="tabs"></div>
                <ul class="fases reveal d3" id="fases"></ul>
                <p class="nota reveal d3" id="nota"></p>
            </div>
        </div>
    </section>

    <!-- ============ COLOMBIA ============ -->
    <section class="colombia">
        <div class="contenedor">
            <span class="pre reveal">Pensado para Colombia</span>
            <h2 class="reveal d1">Habla el idioma del campo y de la DIAN.</h2>
            <p class="sub reveal d2">No es un software extranjero traducido. Está construido sobre cómo se produce, se vende y se declara en Colombia.</p>

            <div class="rejilla">
                <div class="tarjeta reveal">
                    <div class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 4 6v6c0 5 3.4 8 8 9 4.6-1 8-4 8-9V6l-8-3Z"/><path d="m9 12 2 2 4-4"/></svg></div>
                    <h3>Registro ICA</h3>
                    <p>Insumos con registro ICA, periodos de carencia y reingreso, y predios listos para certificación.</p>
                </div>
                <div class="tarjeta reveal d1">
                    <div class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M7 3h7l5 5v13H7z"/><path d="M14 3v5h5M10 13h6M10 17h6"/></svg></div>
                    <h3>Facturación electrónica DIAN</h3>
                    <p>Factura electrónica con CUFE y documento soporte para las compras a campesinos que no facturan.</p>
                </div>
                <div class="tarjeta reveal d2">
                    <div class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/></svg></div>
                    <h3>PUC y retenciones</h3>
                    <p>Gastos clasificados con el PUC colombiano, retención en la fuente y ReteICA calculadas con la UVT del año.</p>
                </div>
                <div class="tarjeta reveal">
                    <div class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v10M9.5 9.5c0-1 1-1.8 2.5-1.8s2.5.7 2.5 1.8-1 1.6-2.5 1.9-2.5.9-2.5 2 1 1.8 2.5 1.8 2.5-.8 2.5-1.8"/></svg></div>
                    <h3>Fondos parafiscales</h3>
                    <p>Cuotas de fomento descontadas en cada liquidación: palmero, cafetero, cacaotero, cerealista y los demás.</p>
                </div>
                <div class="tarjeta reveal d1">
                    <div class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m3 20 6-11 4 6 3-4 5 9z"/><circle cx="17" cy="6" r="2"/></svg></div>
                    <h3>Pisos térmicos</h3>
                    <p>El mismo cultivo no crece igual a 500 que a 2.000 msnm. Las etapas se ajustan con grados-día según la altitud del lote.</p>
                </div>
                <div class="tarjeta reveal d2">
                    <div class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 9a15 15 0 0 1 20 0M5 12.5a10 10 0 0 1 14 0M8.5 16a5 5 0 0 1 7 0"/><path d="m3 3 18 18"/></svg></div>
                    <h3>Funciona en la vereda</h3>
                    <p>Pesaje de cosecha y labores se registran sin señal en el celular y se sincronizan al llegar a la cobertura.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ MÓDULOS ============ -->
    <section id="modulos">
        <div class="contenedor">
            <div class="mod-head">
                <div>
                    <span class="pre reveal">Módulos del sistema</span>
                    <h2 class="reveal d1">Todo el campo, conectado.</h2>
                </div>
                <p class="sub reveal d2">Una aplicación en campo descuenta el inventario, carga el costo al ciclo y queda en la trazabilidad de cada planta.</p>
            </div>

            @php
                $categorias = [
                    ['Cultivo y terreno', [
                        ['Cultivos', 'Cultivos, variedades y su plan fenológico.'],
                        ['Lotes', 'Polígonos GPS, zonas de manejo y riego.'],
                        ['Plantas y árboles', 'Inventario individual, métricas y vecindad.'],
                        ['Análisis de suelo', 'pH, materia orgánica, CIC y textura.'],
                    ]],
                    ['Ciclo productivo', [
                        ['Ciclos productivos', 'Campañas por lote con plan contra real.'],
                        ['Fenología', 'Etapas BBCH y recomendaciones técnicas.'],
                        ['Labores de campo', 'Programadas y ejecutadas, con GPS.'],
                        ['Mano de obra', 'Jornales y destajo por trabajador.'],
                    ]],
                    ['Insumos y recursos', [
                        ['Insumos', 'Stock por lote de compra y vencimientos.'],
                        ['Compras', 'Proveedores, facturas, retenciones y pagos.'],
                        ['Maquinaria', 'Horómetro, combustible y mantenimientos.'],
                        ['Riego', 'Tanques, sensores y eficiencia hídrica.'],
                    ]],
                    ['Cosecha y comercial', [
                        ['Órdenes de cosecha', 'Planeación por cliente, lote y zona.'],
                        ['Recepción en campo', 'Pesaje por trabajador, sin señal.'],
                        ['Despachos', 'Remisiones, carta porte y destino.'],
                        ['Liquidaciones', 'Venta neta, cartera y pagos.'],
                    ]],
                    ['Finanzas y trazabilidad', [
                        ['Gastos', 'Costos por ciclo y etapa, con PUC.'],
                        ['Activos', 'Cultivos en formación y depreciación.'],
                        ['Sanidad vegetal', 'Historial por planta y riesgo de contagio.'],
                        ['Bitácora', 'Observaciones, alertas y visitas técnicas.'],
                    ]],
                ];
            @endphp

            <div class="categorias">
                @foreach ($categorias as $idx => [$nombre, $modulos])
                    <div class="categoria reveal">
                        <h3><span>{{ $idx + 1 }}</span>{{ $nombre }}</h3>
                        <div class="modulos">
                            @foreach ($modulos as [$titulo, $desc])
                                <div class="modulo">
                                    <h4>{{ $titulo }}</h4>
                                    <p>{{ $desc }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ============ CIFRAS ============ -->
    <section style="padding-top:0">
        <div class="contenedor cifras">
            <div class="cifra reveal"><div class="num"><span data-contar="37">0</span>+</div><div class="lbl">Controladores activos</div></div>
            <div class="cifra reveal d1"><div class="num"><span data-contar="9">0</span></div><div class="lbl">Categorías funcionales</div></div>
            <div class="cifra reveal d2"><div class="num"><span data-contar="100">0</span>%</div><div class="lbl">Trazabilidad</div></div>
            <div class="cifra reveal d3"><div class="num">24/7</div><div class="lbl">Disponibilidad</div></div>
        </div>
    </section>

    <!-- ============ CIERRE ============ -->
    <section class="cierre" style="padding-top:0">
        <div class="cierre-caja reveal">
            <h2>Del lote a la venta, todo bajo control.</h2>
            <p class="sub">Registra tu primer lote, crea el ciclo del cultivo y deja que el sistema programe el resto.</p>
            @auth
                <a href="{{ url('/dashboard') }}" class="btn">Ir al dashboard
                    <svg viewBox="0 0 16 16" fill="none"><path d="M3 8h10m-4-4 4 4-4 4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
            @else
                <a href="{{ route('login') }}" class="btn">Comenzar ahora
                    <svg viewBox="0 0 16 16" fill="none"><path d="M3 8h10m-4-4 4 4-4 4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
            @endauth
        </div>
    </section>

    <footer>
        <div>Proyecto de grado · <strong>Ingeniería de Sistemas</strong> · Universidad Autónoma de Bucaramanga — UNAB</div>
        <div>© {{ date('Y') }} PALMA · Gestión agrícola</div>
    </footer>

    <script>
    (() => {
        const reducido = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        /* ---------- Título palabra por palabra ---------- */
        const titulo = document.getElementById('titulo');
        const nodos = Array.from(titulo.childNodes);
        titulo.textContent = '';
        let n = 0;
        nodos.forEach(nodo => {
            const clase = nodo.nodeType === 1 ? nodo.className : '';
            nodo.textContent.split(/\s+/).filter(Boolean).forEach(palabra => {
                const w = document.createElement('span');
                w.className = 'w';
                const inner = document.createElement('span');
                inner.textContent = palabra;
                if (clase) inner.classList.add(clase);
                inner.style.animationDelay = (0.3 + n * 0.07) + 's';
                w.appendChild(inner);
                titulo.appendChild(w);
                titulo.appendChild(document.createTextNode(' '));
                n++;
            });
        });

        const nav = document.getElementById('nav');
        const navScroll = () => nav.classList.toggle('solid', window.scrollY > 40);
        window.addEventListener('scroll', navScroll, { passive: true });
        navScroll();

        /* =====================================================================
           RECORRIDO EN DRON SOBRE UNA FINCA COLOMBIANA (canvas)
           Mosaico de lotes con distintos cultivos, cordillera al fondo,
           escaneo NDVI y grafo de contagio entre plantas en riesgo.
           ===================================================================== */
        const cv = document.getElementById('campo');
        const ctx = cv.getContext('2d');
        let W = 0, H = 0;
        const ajustar = () => {
            const dpr = Math.min(window.devicePixelRatio || 1, 2);
            W = cv.clientWidth; H = cv.clientHeight;
            cv.width = W * dpr; cv.height = H * dpr;
            ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        };
        ajustar();
        window.addEventListener('resize', ajustar);

        const hash = (a, b) => {
            let h = (a * 374761393 + b * 668265263) | 0;
            h = Math.imul(h ^ (h >>> 13), 1274126177);
            return ((h ^ (h >>> 16)) >>> 0) / 4294967295;
        };
        const clamp = (x, a, b) => Math.max(a, Math.min(b, x));
        const mezclar = (a, b, t) => [a[0] + (b[0] - a[0]) * t, a[1] + (b[1] - a[1]) * t, a[2] + (b[2] - a[2]) * t];
        const rgb = c => 'rgb(' + (c[0] | 0) + ',' + (c[1] | 0) + ',' + (c[2] | 0) + ')';
        const easeBack = t => { const c = 1.5; return 1 + (c + 1) * Math.pow(t - 1, 3) + c * Math.pow(t - 1, 2); };

        const NIEBLA = [222, 234, 229];
        const DORMIDO = [150, 166, 150];
        const ESTRES = [242, 165, 22];
        const RIESGO = [226, 80, 44];
        const LUZ = [214, 246, 170];

        const CULTIVOS = [
            { nombre: 'Café',          tipo: 'arbusto', esp: 3.2, alto: 1.9, peso: 4, color: [44, 112, 52],  suelo: [176, 196, 150], deptos: ['Huila', 'Antioquia', 'Caldas', 'Tolima', 'Nariño', 'Cauca'] },
            { nombre: 'Palma de aceite', tipo: 'palma', esp: 9,   alto: 7.5, peso: 2, color: [64, 142, 70],  suelo: [192, 202, 160], deptos: ['Meta', 'Cesar', 'Magdalena', 'Santander'] },
            { nombre: 'Plátano',       tipo: 'platano', esp: 4.2, alto: 3.6, peso: 2, color: [92, 166, 72],  suelo: [184, 202, 150], deptos: ['Quindío', 'Antioquia', 'Meta', 'Córdoba'] },
            { nombre: 'Cacao',         tipo: 'arbol',   esp: 4.5, alto: 3.4, peso: 2, color: [50, 118, 60],  suelo: [170, 188, 140], deptos: ['Santander', 'Arauca', 'Huila', 'Antioquia'] },
            { nombre: 'Aguacate Hass', tipo: 'arbol',   esp: 7,   alto: 5.5, peso: 2, color: [38, 100, 54],  suelo: [178, 194, 148], deptos: ['Antioquia', 'Caldas', 'Tolima', 'Risaralda'] },
            { nombre: 'Maíz',          tipo: 'surco',   esp: 2.8, alto: 2.3, peso: 2, color: [128, 176, 66], suelo: [214, 206, 160], deptos: ['Córdoba', 'Tolima', 'Meta', 'Valle'] },
            { nombre: 'Arroz',         tipo: 'arroz',   esp: 0,   alto: 0,   peso: 1, color: [150, 204, 86], suelo: [150, 204, 96], deptos: ['Casanare', 'Tolima', 'Meta', 'Huila'] },
        ];
        const PESO_TOTAL = CULTIVOS.reduce((a, c) => a + c.peso, 0);
        const cultivoDe = (bx, bz) => {
            let v = hash(bx * 7 + 3, bz * 13 + 1) * PESO_TOTAL;
            for (const c of CULTIVOS) { if ((v -= c.peso) < 0) return c; }
            return CULTIVOS[0];
        };

        const LOTE_X = 58, LOTE_Z = 50, CAMINO = 6;
        const PASO_X = LOTE_X + CAMINO, PASO_Z = LOTE_Z + CAMINO;
        const ALTURA_CAM = 34, VEL = 6, PROF = 300, CERCA = 22;

        // Cordillera: tres capas de montañas
        const ridge = (x, semilla, escala) => {
            let y = 0;
            for (let k = 1; k <= 4; k++) y += Math.sin(x * 0.0021 * k * escala + semilla * k * 1.7) / k;
            return y;
        };

        let raton = 0, ratonSuave = 0;
        window.addEventListener('pointermove', e => { raton = (e.clientX / window.innerWidth - 0.5) * 2; }, { passive: true });

        const hud = {
            cultivos: document.getElementById('hud-cultivos'),
            vista: document.getElementById('hud-vista'),
            ndvi: document.getElementById('hud-ndvi'),
            estres: document.getElementById('hud-estres'),
            alertas: document.getElementById('hud-alertas'),
        };
        let ultimoHud = 0;
        const inicio = performance.now();

        /* --- Dibujo de cada tipo de planta (x, suelo en px; s = px por metro) --- */
        const dibujarPlanta = (tipo, x, y, s, alto, esp, color, giro, t) => {
            const c = rgb(color);
            if (tipo === 'palma') {
                const top = y - alto * s;
                ctx.strokeStyle = 'rgb(118,104,78)';
                ctx.lineWidth = Math.max(0.7, 0.55 * s);
                ctx.beginPath(); ctx.moveTo(x, y); ctx.lineTo(x, top); ctx.stroke();
                const L = 4.8 * s, brisa = Math.sin(t * 1.3 + giro * 9) * 0.08;
                ctx.strokeStyle = c; ctx.lineWidth = Math.max(0.7, 0.45 * s);
                ctx.beginPath();
                for (let i = 0; i < 9; i++) {
                    const th = giro + i / 9 * Math.PI * 2 + brisa;
                    const dx = Math.cos(th) * L, dy = Math.sin(th) * L * 0.2;
                    ctx.moveTo(x, top);
                    ctx.quadraticCurveTo(x + dx * 0.55, top - L * 0.3 + dy, x + dx, top + L * 0.38 + dy);
                }
                ctx.stroke();
            } else if (tipo === 'arbol') {
                const top = y - alto * s;
                ctx.strokeStyle = 'rgb(118,96,70)';
                ctx.lineWidth = Math.max(0.6, 0.4 * s);
                ctx.beginPath(); ctx.moveTo(x, y); ctx.lineTo(x, top + esp * 0.2 * s); ctx.stroke();
                const r = esp * 0.36 * s;
                ctx.fillStyle = c;
                ctx.beginPath(); ctx.ellipse(x, top + r * 0.3, r, r * 0.82, 0, 0, 7); ctx.fill();
                ctx.fillStyle = 'rgba(255,255,255,.18)';
                ctx.beginPath(); ctx.ellipse(x - r * 0.3, top, r * 0.45, r * 0.35, 0, 0, 7); ctx.fill();
            } else if (tipo === 'arbusto') {
                const rx = 0.85 * s, ry = alto * 0.55 * s;
                ctx.fillStyle = c;
                ctx.beginPath(); ctx.ellipse(x, y - ry, rx, ry, 0, 0, 7); ctx.fill();
                ctx.fillStyle = 'rgba(255,255,255,.16)';
                ctx.beginPath(); ctx.ellipse(x - rx * 0.3, y - ry * 1.3, rx * 0.4, ry * 0.35, 0, 0, 7); ctx.fill();
            } else if (tipo === 'platano') {
                const top = y - alto * 0.62 * s;
                ctx.strokeStyle = 'rgb(126,150,86)';
                ctx.lineWidth = Math.max(0.7, 0.35 * s);
                ctx.beginPath(); ctx.moveTo(x, y); ctx.lineTo(x, top); ctx.stroke();
                ctx.strokeStyle = c; ctx.lineCap = 'round';
                ctx.lineWidth = Math.max(1, 0.55 * s);
                const L = 2.4 * s, brisa = Math.sin(t * 1.6 + giro * 7) * 0.1;
                ctx.beginPath();
                for (let i = 0; i < 6; i++) {
                    const th = giro + i / 6 * Math.PI * 2 + brisa;
                    const dx = Math.cos(th) * L;
                    ctx.moveTo(x, top);
                    ctx.quadraticCurveTo(x + dx * 0.6, top - L * 0.55, x + dx, top - L * 0.1 + Math.sin(th) * L * 0.2);
                }
                ctx.stroke();
                ctx.lineCap = 'butt';
            } else if (tipo === 'surco') {
                const top = y - alto * s;
                ctx.strokeStyle = c;
                ctx.lineWidth = Math.max(0.6, 0.18 * s);
                ctx.beginPath();
                ctx.moveTo(x, y); ctx.lineTo(x, top);
                for (let k = 1; k <= 3; k++) {
                    const yy = y - alto * s * k / 4, lado = k % 2 ? 1 : -1;
                    ctx.moveTo(x, yy); ctx.quadraticCurveTo(x + lado * 0.5 * s, yy - 0.3 * s, x + lado * 0.8 * s, yy + 0.1 * s);
                }
                ctx.stroke();
                ctx.fillStyle = 'rgb(222,186,82)';
                ctx.fillRect(x - 0.12 * s, top - 0.3 * s, 0.24 * s, 0.35 * s);
            }
        };

        const dibujar = (ahora) => {
            const t = reducido ? 10 : (ahora - inicio) / 1000;
            ratonSuave += (raton - ratonSuave) * 0.04;
            const horizonte = H * 0.32;
            const f = H * 1.0;
            const camZ = t * VEL;
            const camX = Math.sin(t * 0.05) * 30 + ratonSuave * 14;
            const pX = (x, dz) => W / 2 + (x - camX) * f / dz;
            const pY = (h, dz) => horizonte + (ALTURA_CAM - h) * f / dz;

            // Cielo de día
            const cielo = ctx.createLinearGradient(0, 0, 0, horizonte);
            cielo.addColorStop(0, '#bcdcec');
            cielo.addColorStop(1, '#eaf2ee');
            ctx.fillStyle = cielo;
            ctx.fillRect(0, 0, W, horizonte + 2);
            const sol = ctx.createRadialGradient(W * 0.78, horizonte * 0.35, 0, W * 0.78, horizonte * 0.35, W * 0.35);
            sol.addColorStop(0, 'rgba(255,246,214,.9)');
            sol.addColorStop(1, 'rgba(255,246,214,0)');
            ctx.fillStyle = sol;
            ctx.fillRect(0, 0, W, horizonte);

            // Cordillera (tres capas con parallax)
            [[0.55, '#b7cdd2', 1.0, 0.05], [0.4, '#a3c0b4', 1.6, 0.1], [0.24, '#8fb39b', 2.3, 0.18]].forEach(([alturaRel, color, escala, par], i) => {
                ctx.fillStyle = color;
                ctx.beginPath();
                ctx.moveTo(0, horizonte + 1);
                for (let x = 0; x <= W + 10; x += 10) {
                    const wx = x + camX * par * 6;
                    const y = horizonte - (alturaRel * horizonte) * (0.55 + 0.45 * ridge(wx, i + 1, escala) * 0.6);
                    ctx.lineTo(x, y);
                }
                ctx.lineTo(W, horizonte + 1);
                ctx.closePath();
                ctx.fill();
            });

            // Suelo base (caminos entre lotes)
            const base = ctx.createLinearGradient(0, horizonte, 0, H);
            base.addColorStop(0, '#dfe7dc');
            base.addColorStop(1, '#e6dcc4');
            ctx.fillStyle = base;
            ctx.fillRect(0, horizonte, W, H - horizonte);

            // Onda de escaneo NDVI
            const ciclo = reducido ? 0.65 : ((t * 0.13) % 1.12);
            const scanDz = CERCA + ciclo * PROF;

            const bz0 = Math.floor((camZ + CERCA) / PASO_Z);
            const bz1 = Math.floor((camZ + PROF) / PASO_Z);
            const riesgos = [], etiquetas = [];
            const cultivosVista = new Set();
            let vista = 0, sumaNdvi = 0, nNdvi = 0, estres = 0;

            for (let bz = bz1; bz >= bz0; bz--) {
                const z0 = bz * PASO_Z, z1 = z0 + LOTE_Z;
                const dzLejos = z1 - camZ;
                if (dzLejos < CERCA) continue;
                const mitad = (W / 2) / f * dzLejos + LOTE_X;
                const bx0 = Math.floor((camX - mitad) / PASO_X);
                const bx1 = Math.floor((camX + mitad) / PASO_X);

                // ordenar lotes de afuera hacia el centro para un solapamiento correcto
                const lotes = [];
                for (let bx = bx0; bx <= bx1; bx++) lotes.push(bx);
                lotes.sort((a, b) => Math.abs((b + 0.5) * PASO_X - camX) - Math.abs((a + 0.5) * PASO_X - camX));

                for (const bx of lotes) {
                    const cult = cultivoDe(bx, bz);
                    const x0 = bx * PASO_X, x1 = x0 + LOTE_X;
                    const zc0 = Math.max(z0, camZ + CERCA);
                    const dzA = zc0 - camZ, dzB = z1 - camZ;
                    const nieblaLote = clamp(1 - (dzA + dzB) / 2 / PROF, 0, 1);

                    // parcela
                    const suelo = mezclar(NIEBLA, cult.suelo, 0.35 + 0.65 * nieblaLote);
                    ctx.fillStyle = rgb(suelo);
                    ctx.beginPath();
                    ctx.moveTo(pX(x0, dzB), pY(0, dzB)); ctx.lineTo(pX(x1, dzB), pY(0, dzB));
                    ctx.lineTo(pX(x1, dzA), pY(0, dzA)); ctx.lineTo(pX(x0, dzA), pY(0, dzA));
                    ctx.closePath(); ctx.fill();

                    if (cult.tipo === 'arroz') {
                        // arrozal: franjas de agua y verde que brillan con el escaneo
                        const revelado = dzA < scanDz;
                        for (let zz = z1; zz > zc0; zz -= 5) {
                            const dz = zz - camZ;
                            const yy = pY(0, dz);
                            ctx.strokeStyle = revelado || dz < scanDz ? 'rgba(255,255,255,.35)' : 'rgba(255,255,255,.18)';
                            ctx.lineWidth = 1;
                            ctx.beginPath(); ctx.moveTo(pX(x0, dz), yy); ctx.lineTo(pX(x1, dz), yy); ctx.stroke();
                        }
                        cultivosVista.add(cult.nombre);
                    } else {
                        // plantas, de atrás hacia adelante
                        const esp = cult.esp;
                        const filas = Math.floor(LOTE_Z / (cult.tipo === 'palma' ? esp * 0.866 : esp));
                        const pasoFila = LOTE_Z / filas;
                        for (let iz = filas - 1; iz >= 0; iz--) {
                            const z = z0 + (iz + 0.5) * pasoFila;
                            const dz = z - camZ;
                            if (dz < CERCA || dz > PROF) continue;
                            const s0 = f / dz;
                            const densa = esp < 5;
                            if (densa && s0 < 0.9) continue;     // muy lejos: basta con la parcela
                            const niebla = clamp(1 - dz / PROF, 0, 1) * clamp((dz - CERCA) / 30, 0, 1);
                            const crecer = reducido ? 1 : easeBack(clamp((t - 0.2 - (dz / PROF) * 1.3) / 1.0, 0, 1));
                            const s = s0 * crecer;
                            const y = pY(0, dz);
                            const off = (cult.tipo === 'palma' && iz % 2) ? esp / 2 : 0;
                            const revelada = dz < scanDz;
                            const enBanda = Math.abs(dz - scanDz) < 6;
                            const columnas = Math.floor(LOTE_X / esp);
                            for (let ix = 0; ix < columnas; ix++) {
                                const wx = x0 + (ix + 0.5) * esp + off;
                                if (wx > x1) continue;
                                const x = pX(wx, dz);
                                if (x < -40 || x > W + 40) continue;
                                const v = hash(bx * 1000 + ix, bz * 1000 + iz);
                                const est = v < 0.025 ? 2 : (v < 0.11 ? 1 : 0);
                                const ndvi = 0.64 + hash(ix + bx * 31, iz + bz * 17) * 0.26;
                                let color;
                                if (enBanda) color = LUZ;
                                else if (!revelada) color = DORMIDO;
                                else if (est === 2) color = RIESGO;
                                else if (est === 1) color = ESTRES;
                                else color = mezclar(cult.color, [120, 196, 92], (ndvi - 0.64) / 0.26 * 0.45);
                                color = mezclar(NIEBLA, color, 0.3 + 0.7 * niebla);

                                if (s < 1.4) {
                                    ctx.fillStyle = rgb(color);
                                    ctx.fillRect(x - 0.9, y - cult.alto * s - 0.9, 1.8, 1.8);
                                } else {
                                    dibujarPlanta(cult.tipo, x, y, s, cult.alto, esp, color, v * 6.28, t);
                                }
                                if (revelada) {
                                    vista++;
                                    cultivosVista.add(cult.nombre);
                                    if (est === 1) estres++;
                                    if (est === 0) { sumaNdvi += ndvi; nNdvi++; }
                                    if (est === 2 && dz > CERCA + 10 && s > 2) riesgos.push({ bx, bz, ix, iz, x, y: y - cult.alto * s, s, dz, cult, off, pasoFila });
                                }
                            }
                        }
                    }

                    // Etiqueta del lote (solo algunos, a media distancia)
                    const zMed = (zc0 + z1) / 2, dzMed = zMed - camZ;
                    const xm = pX((x0 + x1) / 2, dzMed);
                    if (dzMed > 60 && dzMed < 190 && hash(bx, bz * 3) > 0.45 && xm > W * 0.45 && xm < W - 40) {
                        etiquetas.push({ x: xm, y: pY(cult.alto + 4, dzMed), texto: cult.nombre + ' · ' + cult.deptos[Math.floor(hash(bz, bx) * cult.deptos.length)], a: clamp(Math.min(dzMed - 60, 190 - dzMed) / 25, 0, 1) });
                    }
                }
            }

            // Grafo de contagio entre plantas vecinas del mismo lote
            riesgos.forEach(p => {
                const pulso = (Math.sin(t * 4 + p.ix) + 1) / 2;
                const alfa = clamp(1 - p.dz / PROF, 0, 1);
                ctx.strokeStyle = 'rgba(226,80,44,' + (0.35 + 0.5 * pulso) * alfa + ')';
                ctx.lineWidth = Math.max(0.8, p.s * 0.16);
                ctx.setLineDash([4, 4]);
                ctx.lineDashOffset = -t * 16;
                [[1, 0], [-1, 0], [0, 1], [0, -1], [1, 1], [-1, -1]].forEach(([di, dj]) => {
                    const wx = p.bx * PASO_X + (p.ix + di + 0.5) * p.cult.esp + p.off;
                    const dz2 = p.bz * PASO_Z + (p.iz + dj + 0.5) * p.pasoFila - camZ;
                    if (dz2 < CERCA) return;
                    ctx.beginPath(); ctx.moveTo(p.x, p.y);
                    ctx.lineTo(pX(wx, dz2), pY(p.cult.alto, dz2)); ctx.stroke();
                });
                ctx.setLineDash([]);
                ctx.strokeStyle = 'rgba(226,80,44,' + (0.9 - pulso * 0.7) * alfa + ')';
                ctx.lineWidth = 1.4;
                ctx.beginPath(); ctx.arc(p.x, p.y, p.s * (1.2 + pulso * 2), 0, 7); ctx.stroke();
            });

            // Línea de escaneo
            const yScan = pY(0, scanDz);
            if (yScan < H) {
                const g = ctx.createLinearGradient(0, yScan - 20, 0, yScan + 20);
                g.addColorStop(0, 'rgba(63,163,77,0)');
                g.addColorStop(0.5, 'rgba(63,163,77,.18)');
                g.addColorStop(1, 'rgba(63,163,77,0)');
                ctx.fillStyle = g;
                ctx.fillRect(0, yScan - 20, W, 40);
                ctx.strokeStyle = 'rgba(31,122,63,.75)';
                ctx.lineWidth = 1.3;
                ctx.beginPath(); ctx.moveTo(0, yScan); ctx.lineTo(W, yScan); ctx.stroke();
            }

            // Etiquetas de lote
            ctx.font = '600 12px Figtree, system-ui, sans-serif';
            etiquetas.slice(0, 5).forEach(e => {
                const ancho = ctx.measureText(e.texto).width + 20;
                ctx.globalAlpha = e.a;
                ctx.strokeStyle = 'rgba(22,38,27,.35)';
                ctx.lineWidth = 1;
                ctx.beginPath(); ctx.moveTo(e.x, e.y); ctx.lineTo(e.x, e.y - 18); ctx.stroke();
                ctx.fillStyle = 'rgba(255,255,255,.95)';
                ctx.shadowColor = 'rgba(22,38,27,.18)'; ctx.shadowBlur = 10; ctx.shadowOffsetY = 3;
                ctx.beginPath(); ctx.roundRect(e.x - ancho / 2, e.y - 44, ancho, 26, 8); ctx.fill();
                ctx.shadowColor = 'transparent';
                ctx.fillStyle = '#16261b';
                ctx.textAlign = 'center'; ctx.textBaseline = 'middle';
                ctx.fillText(e.texto, e.x, e.y - 31);
                ctx.fillStyle = '#1f7a3f';
                ctx.beginPath(); ctx.arc(e.x, e.y, 3, 0, 7); ctx.fill();
                ctx.globalAlpha = 1;
            });

            if (ahora - ultimoHud > 250) {
                ultimoHud = ahora;
                hud.cultivos.textContent = cultivosVista.size;
                hud.vista.textContent = vista.toLocaleString('es-CO');
                hud.ndvi.textContent = nNdvi ? (sumaNdvi / nNdvi).toFixed(2) : '0.00';
                hud.estres.textContent = estres.toLocaleString('es-CO');
                hud.alertas.textContent = riesgos.length;
            }
        };

        let corriendo = false, visibleHero = true;
        const bucle = (ahora) => { if (!corriendo) return; dibujar(ahora); requestAnimationFrame(bucle); };
        const arrancar = () => {
            if (reducido) { dibujar(performance.now()); return; }
            if (!corriendo && visibleHero && !document.hidden) { corriendo = true; requestAnimationFrame(bucle); }
        };
        const parar = () => { corriendo = false; };
        new IntersectionObserver(([e]) => { visibleHero = e.isIntersecting; visibleHero ? arrancar() : parar(); }).observe(cv);
        document.addEventListener('visibilitychange', () => document.hidden ? parar() : arrancar());
        window.addEventListener('resize', () => { if (reducido) dibujar(performance.now()); });
        arrancar();

        /* =====================================================================
           RELOJ FENOLÓGICO (café, maíz, palma)
           Duraciones de referencia aproximadas.
           ===================================================================== */
        const RELOJES = {
            'Café': {
                nota: 'En la zona andina hay dos temporadas de lluvia, por eso el café da dos cosechas al año: la principal y la mitaca.',
                fases: [
                    { nombre: 'Floración',           dias: 15, bbch: 'BBCH 60–69', color: '#f2c230', desc: 'Las flores abren pocos días después de las lluvias.' },
                    { nombre: 'Fruto verde',         dias: 75, bbch: 'BBCH 71–74', color: '#9cc75a', desc: 'Crecimiento del fruto; etapa sensible a la sequía.' },
                    { nombre: 'Llenado del grano',   dias: 70, bbch: 'BBCH 75–79', color: '#3fa34d', desc: 'Se forma el grano; clave para fertilizar y vigilar la broca.' },
                    { nombre: 'Maduración y cosecha', dias: 60, bbch: 'BBCH 81–89', color: '#c0392b', desc: 'El fruto pasa a cereza y se recoge en pases sucesivos.' },
                ],
            },
            'Maíz': {
                nota: 'En clima cálido el ciclo dura unos 4 meses; en clima frío puede pasar de 6. Por eso PALMA calcula las etapas con grados-día.',
                fases: [
                    { nombre: 'Emergencia',            dias: 7,  bbch: 'BBCH 00–09', color: '#c9d77a', desc: 'Germinación y salida de la plántula.' },
                    { nombre: 'Desarrollo vegetativo', dias: 45, bbch: 'BBCH 10–39', color: '#3fa34d', desc: 'Formación de hojas y tallo; fertilización nitrogenada.' },
                    { nombre: 'Floración',             dias: 20, bbch: 'BBCH 51–69', color: '#f2c230', desc: 'Espigamiento y polinización: etapa más crítica.' },
                    { nombre: 'Llenado de grano',      dias: 35, bbch: 'BBCH 71–79', color: '#e0a030', desc: 'El grano acumula almidón.' },
                    { nombre: 'Madurez y cosecha',     dias: 20, bbch: 'BBCH 81–99', color: '#a0703a', desc: 'Secado del grano en campo y recolección.' },
                ],
            },
            'Palma de aceite': {
                nota: 'Una palma adulta abre un nuevo racimo cada dos semanas, así que en el mismo lote siempre hay racimos en todas las etapas.',
                fases: [
                    { nombre: 'Antesis',               dias: 7,   bbch: 'BBCH 61–69', color: '#f2c230', desc: 'Flores receptivas; en híbridos OxG la polinización es asistida.' },
                    { nombre: 'Desarrollo del racimo', dias: 120, bbch: 'BBCH 71–79', color: '#3fa34d', desc: 'Llenado del fruto y acumulación de aceite.' },
                    { nombre: 'Maduración',            dias: 40,  bbch: 'BBCH 80–88', color: '#f08a24', desc: 'El fruto cambia de color.' },
                    { nombre: 'Cosecha',               dias: 12,  bbch: 'BBCH 89',    color: '#c0392b', desc: 'Corte del racimo y despacho a la planta extractora.' },
                ],
            },
        };
        const NS = 'http://www.w3.org/2000/svg';
        const R = 150, CIRC = 2 * Math.PI * R, HUECO = 5;
        const arcos = document.getElementById('arcos');
        const ticks = document.getElementById('ticks');
        const lista = document.getElementById('fases');
        const tabs = document.getElementById('tabs');
        const nota = document.getElementById('nota');
        const marcador = document.getElementById('marcador');
        const diaEl = document.getElementById('dia');
        const faseEl = document.getElementById('fase-nombre');
        const bbchEl = document.getElementById('fase-bbch');
        const cultivoEl = document.getElementById('reloj-cultivo');

        for (let i = 0; i < 60; i++) {
            const a = i / 60 * Math.PI * 2, largo = i % 5 === 0 ? 9 : 4;
            const l = document.createElementNS(NS, 'line');
            l.setAttribute('x1', 200 + Math.cos(a) * 176); l.setAttribute('y1', 200 + Math.sin(a) * 176);
            l.setAttribute('x2', 200 + Math.cos(a) * (176 + largo)); l.setAttribute('y2', 200 + Math.sin(a) * (176 + largo));
            l.setAttribute('stroke', i % 5 === 0 ? '#b8c1b3' : '#dde2d6');
            ticks.appendChild(l);
        }

        const nombres = Object.keys(RELOJES);
        let actual = null, total = 0, faseActual = -1, diaBase = 0, tBase = performance.now(), manual = false;

        const cargar = (nombre) => {
            actual = RELOJES[nombre];
            total = actual.fases.reduce((a, f) => a + f.dias, 0);
            arcos.innerHTML = ''; lista.innerHTML = '';
            let acumulado = 0;
            actual.fases.forEach(f => {
                const largo = f.dias / total * CIRC;
                const arco = document.createElementNS(NS, 'circle');
                arco.setAttribute('cx', 200); arco.setAttribute('cy', 200); arco.setAttribute('r', R);
                arco.setAttribute('fill', 'none'); arco.setAttribute('stroke', f.color);
                arco.setAttribute('stroke-width', 26);
                arco.setAttribute('stroke-dasharray', Math.max(largo - HUECO, 1) + ' ' + CIRC);
                arco.setAttribute('stroke-dashoffset', -acumulado);
                arco.setAttribute('opacity', '.3');
                arco.style.transition = 'opacity .35s';
                arcos.appendChild(arco);
                f.arco = arco;
                f.inicio = acumulado / CIRC * total;
                acumulado += largo;

                const li = document.createElement('li');
                li.innerHTML = '<span class="pt"></span><div><h4></h4><p></p></div><span class="d"></span>';
                li.querySelector('.pt').style.background = f.color;
                li.querySelector('h4').textContent = f.nombre;
                li.querySelector('p').textContent = f.desc;
                li.querySelector('.d').textContent = '~' + f.dias + ' días';
                lista.appendChild(li);
                f.li = li;
            });
            nota.textContent = actual.nota;
            cultivoEl.textContent = nombre;
            tabs.querySelectorAll('.tab').forEach(b => b.classList.toggle('on', b.dataset.cultivo === nombre));
            faseActual = -1; tBase = performance.now();
        };

        nombres.forEach(nombre => {
            const b = document.createElement('button');
            b.className = 'tab'; b.type = 'button'; b.textContent = nombre; b.dataset.cultivo = nombre;
            b.setAttribute('role', 'tab');
            b.addEventListener('click', () => { manual = true; cargar(nombre); });
            tabs.appendChild(b);
        });
        cargar(nombres[0]);

        let relojVisible = false;
        new IntersectionObserver(([e]) => { relojVisible = e.isIntersecting; }).observe(document.getElementById('reloj-svg'));
        const VUELTA_SEG = 14;
        const reloj = (ahora) => {
            if (relojVisible || faseActual === -1) {
                const progreso = reducido ? 0.3 : (ahora - tBase) / 1000 / VUELTA_SEG;
                if (progreso >= 1 && !manual) {
                    cargar(nombres[(nombres.indexOf(cultivoEl.textContent) + 1) % nombres.length]);
                } else {
                    const dia = (progreso % 1) * total;
                    marcador.setAttribute('transform', 'rotate(' + (dia / total * 360) + ' 200 200)');
                    diaEl.textContent = Math.floor(dia);
                    const idx = actual.fases.findIndex(f => dia >= f.inicio && dia < f.inicio + f.dias);
                    if (idx !== faseActual && idx >= 0) {
                        faseActual = idx;
                        actual.fases.forEach((f, i) => {
                            f.li.classList.toggle('on', i === idx);
                            f.arco.setAttribute('opacity', i === idx ? '1' : '.3');
                        });
                        faseEl.textContent = actual.fases[idx].nombre;
                        faseEl.style.color = actual.fases[idx].color;
                        bbchEl.textContent = actual.fases[idx].bbch;
                    }
                }
            }
            if (!reducido) requestAnimationFrame(reloj);
        };
        requestAnimationFrame(reloj);

        /* ---------- Reveal + contadores ---------- */
        const contar = el => {
            const meta = +el.dataset.contar, t0 = performance.now();
            const paso = ahora => {
                const p = Math.min((ahora - t0) / 1500, 1);
                el.textContent = Math.round(meta * (1 - Math.pow(1 - p, 4)));
                if (p < 1) requestAnimationFrame(paso);
            };
            requestAnimationFrame(paso);
        };
        const obs = new IntersectionObserver(entradas => {
            entradas.forEach(e => {
                if (!e.isIntersecting) return;
                e.target.classList.add('visible');
                e.target.querySelectorAll('[data-contar]').forEach(contar);
                obs.unobserve(e.target);
            });
        }, { threshold: 0.15 });
        document.querySelectorAll('.reveal').forEach(el => obs.observe(el));
    })();
    </script>
</body>
</html>
