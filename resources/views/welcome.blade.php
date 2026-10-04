<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PALMA · Sistema Agrícola</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,800" rel="stylesheet" />

    <style>
        :root{
            --verde-900:#0f3d1e;
            --verde-700:#1b7a3a;
            --verde-500:#2eaf5c;
            --verde-300:#7ddc9a;
            --amarillo:#ffd23f;
            --amarillo-soft:#ffe89a;
            --crema:#f6fbf5;
        }
        *{box-sizing:border-box;margin:0;padding:0;}
        html,body{font-family:'Figtree',sans-serif;color:#0f3d1e;overflow-x:hidden;}
        body{
            background: linear-gradient(135deg, #f6fbf5 0%, #eaf6ea 50%, #fffbe6 100%);
            background-size: 200% 200%;
            animation: bgShift 18s ease-in-out infinite;
            min-height:100vh;
        }
        @keyframes bgShift{
            0%,100%{background-position:0% 50%;}
            50%{background-position:100% 50%;}
        }

        /* Hojas decorativas flotantes */
        .leaf{
            position:fixed;
            top:-60px;
            font-size:28px;
            opacity:.35;
            animation: fall linear infinite;
            pointer-events:none;
            z-index:0;
        }
        @keyframes fall{
            0%{transform: translateY(-10vh) rotate(0deg);}
            100%{transform: translateY(110vh) rotate(360deg);}
        }

        /* Navbar */
        nav{
            position:relative;z-index:5;
            display:flex;justify-content:space-between;align-items:center;
            padding:1.2rem 3rem;
            backdrop-filter: blur(8px);
            background: rgba(255,255,255,.7);
            border-bottom:2px solid rgba(46,175,92,.15);
        }
        .brand{display:flex;align-items:center;gap:.7rem;font-weight:800;font-size:1.4rem;color:var(--verde-900);}
        .brand .logo{
            width:42px;height:42px;border-radius:50%;
            background: radial-gradient(circle at 30% 30%, var(--amarillo), var(--verde-500));
            display:grid;place-items:center;font-size:1.3rem;
            box-shadow: 0 6px 16px rgba(46,175,92,.35);
            animation: pulseLogo 3s ease-in-out infinite;
        }
        @keyframes pulseLogo{
            0%,100%{transform:scale(1);}
            50%{transform:scale(1.08);}
        }
        nav .actions a{
            text-decoration:none;padding:.6rem 1.2rem;border-radius:999px;
            font-weight:600;font-size:.9rem;transition:.3s;
        }
        nav .actions a.login{color:var(--verde-700);}
        nav .actions a.login:hover{background:rgba(46,175,92,.1);}
        nav .actions a.register{
            background:linear-gradient(135deg,var(--verde-500),var(--verde-700));
            color:#fff;box-shadow:0 6px 18px rgba(46,175,92,.4);
        }
        nav .actions a.register:hover{transform:translateY(-2px);box-shadow:0 10px 24px rgba(46,175,92,.5);}

        /* Hero */
        header.hero{
            position:relative;z-index:2;
            text-align:center;padding:5rem 1.5rem 3rem;
            max-width:1100px;margin:0 auto;
        }
        .badge{
            display:inline-flex;align-items:center;gap:.5rem;
            background:rgba(255,210,63,.25);
            color:var(--verde-900);
            border:1px solid rgba(46,175,92,.3);
            padding:.45rem 1rem;border-radius:999px;
            font-size:.8rem;font-weight:600;letter-spacing:.5px;
            animation: fadeUp .8s ease both;
        }
        h1{
            font-size: clamp(2rem, 5vw, 3.6rem);
            font-weight:800;line-height:1.1;margin:1.2rem 0 1rem;
            color:var(--verde-900);
            animation: fadeUp 1s ease both .1s;
        }
        h1 span{
            background:linear-gradient(135deg,var(--verde-500),var(--amarillo));
            -webkit-background-clip:text;background-clip:text;color:transparent;
        }
        p.lead{
            font-size:1.15rem;max-width:640px;margin:0 auto;color:#3f5a48;
            animation: fadeUp 1s ease both .2s;
        }
        .cta{
            margin-top:2rem;display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;
            animation: fadeUp 1s ease both .3s;
        }
        .cta a{
            text-decoration:none;padding:.9rem 1.8rem;border-radius:14px;
            font-weight:700;font-size:.95rem;transition:.3s;display:inline-flex;align-items:center;gap:.5rem;
        }
        .cta .primary{
            background:linear-gradient(135deg,var(--verde-500),var(--verde-700));
            color:#fff;box-shadow:0 10px 25px rgba(46,175,92,.4);
        }
        .cta .primary:hover{transform:translateY(-3px) scale(1.02);box-shadow:0 15px 30px rgba(46,175,92,.55);}
        .cta .secondary{
            background:#fff;color:var(--verde-700);
            border:2px solid rgba(46,175,92,.35);
        }
        .cta .secondary:hover{background:var(--amarillo-soft);border-color:var(--amarillo);}

        @keyframes fadeUp{
            from{opacity:0;transform:translateY(25px);}
            to{opacity:1;transform:translateY(0);}
        }

        /* Stats */
        .stats{
            position:relative;z-index:2;
            display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
            gap:1.2rem;max-width:1000px;margin:2rem auto 0;padding:0 1.5rem;
        }
        .stat{
            background:rgba(255,255,255,.75);
            border:1px solid rgba(46,175,92,.15);
            border-radius:18px;padding:1.3rem;text-align:center;
            backdrop-filter:blur(6px);
            transition:.35s;
            animation: fadeUp .8s ease both;
        }
        .stat:nth-child(1){animation-delay:.35s;}
        .stat:nth-child(2){animation-delay:.45s;}
        .stat:nth-child(3){animation-delay:.55s;}
        .stat:nth-child(4){animation-delay:.65s;}
        .stat:hover{transform:translateY(-6px);box-shadow:0 14px 30px rgba(46,175,92,.2);}
        .stat .num{font-size:1.8rem;font-weight:800;color:var(--verde-700);}
        .stat .lbl{font-size:.85rem;color:#4a6a54;margin-top:.25rem;font-weight:500;}

        /* Módulos */
        section.modules{
            position:relative;z-index:2;
            max-width:1200px;margin:5rem auto;padding:0 1.5rem;
        }
        .section-title{
            text-align:center;margin-bottom:3rem;
        }
        .section-title h2{
            font-size:2rem;font-weight:800;color:var(--verde-900);
        }
        .section-title p{color:#557a60;margin-top:.5rem;}
        .section-title .line{
            width:70px;height:5px;border-radius:99px;margin:1rem auto 0;
            background:linear-gradient(90deg,var(--verde-500),var(--amarillo));
        }

        .category{margin-bottom:3rem;animation: fadeUp 1s ease both;}
        .category h3{
            display:flex;align-items:center;gap:.7rem;
            font-size:1.15rem;font-weight:700;color:var(--verde-700);
            margin-bottom:1.2rem;
        }
        .category h3 .dot{
            width:10px;height:10px;border-radius:50%;
            background:var(--amarillo);
            box-shadow:0 0 0 4px rgba(255,210,63,.25);
        }

        .grid{
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(230px,1fr));
            gap:1.2rem;
        }

        .card{
            background:#fff;
            border-radius:20px;
            padding:1.4rem;
            border:1px solid rgba(46,175,92,.12);
            position:relative;
            overflow:hidden;
            transition: all .4s cubic-bezier(.2,.8,.2,1);
            cursor:pointer;
            text-decoration:none;color:inherit;
            display:block;
        }
        .card::before{
            content:'';position:absolute;inset:0;
            background:linear-gradient(135deg,transparent 40%, rgba(255,210,63,.15));
            opacity:0;transition:.4s;
        }
        .card:hover{
            transform:translateY(-8px);
            box-shadow:0 20px 40px rgba(27,122,58,.18);
            border-color:var(--verde-300);
        }
        .card:hover::before{opacity:1;}
        .card .icon{
            width:52px;height:52px;border-radius:14px;
            display:grid;place-items:center;font-size:1.5rem;
            background:linear-gradient(135deg, rgba(46,175,92,.15), rgba(255,210,63,.25));
            margin-bottom:1rem;
            transition:.4s;
        }
        .card:hover .icon{
            transform:rotate(-6deg) scale(1.1);
            background:linear-gradient(135deg, var(--verde-500), var(--amarillo));
        }
        .card h4{font-size:1rem;font-weight:700;color:var(--verde-900);margin-bottom:.35rem;}
        .card p{font-size:.85rem;color:#5c7a66;line-height:1.45;}

        /* Footer */
        footer{
            position:relative;z-index:2;
            text-align:center;padding:3rem 1.5rem;
            color:#4a6a54;font-size:.9rem;
            border-top:1px solid rgba(46,175,92,.15);
            background:rgba(255,255,255,.6);
            backdrop-filter:blur(6px);
        }
        footer strong{color:var(--verde-700);}
        footer .unab{
            display:inline-block;margin-top:.4rem;
            color:var(--verde-900);font-weight:600;
        }

        /* Scroll reveal simple con :target? no, usamos IntersectionObserver JS */
        .reveal{opacity:0;transform:translateY(30px);transition:all .8s ease;}
        .reveal.visible{opacity:1;transform:none;}

        @media (max-width:640px){
            nav{padding:1rem 1.2rem;}
            nav .actions a span.hide-sm{display:none;}
        }
    </style>
</head>
<body>

    <!-- Hojas animadas de fondo -->
    <div class="leaf" style="left:5%;  animation-duration:14s; animation-delay:0s;">🌿</div>
    <div class="leaf" style="left:20%; animation-duration:18s; animation-delay:2s;">🍃</div>
    <div class="leaf" style="left:38%; animation-duration:16s; animation-delay:5s;">🌱</div>
    <div class="leaf" style="left:55%; animation-duration:20s; animation-delay:1s;">🌾</div>
    <div class="leaf" style="left:72%; animation-duration:15s; animation-delay:3s;">🍃</div>
    <div class="leaf" style="left:88%; animation-duration:17s; animation-delay:6s;">🌿</div>

    <!-- Navbar -->
    <nav>
        <div class="brand">
            <div class="logo">🌴</div>
            <span>PALMA</span>
        </div>
        <div class="actions">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="register">Ir al Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="login">Iniciar Sesión</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="register">Registrarse</a>
                    @endif
                @endauth
            @endif
        </div>
    </nav>

    <!-- Hero -->
    <header class="hero">
        <span class="badge">🌱 Proyecto de Grado · Ingeniería de Sistemas · UNAB</span>
        <h1>Sistema Integral de <span>Gestión Agrícola</span> para Cultivos Perennes y Temporales</h1>
        <p class="lead">
            Administra lotes, árboles, ciclos productivos, insumos, cosechas y trazabilidad fitosanitaria
            desde una sola plataforma. Diseñado para optimizar cada etapa del cultivo.
        </p>

        <div class="cta">
            @auth
                <a href="{{ url('/dashboard') }}" class="primary">🚜 Ir al Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="primary">🚜 Comenzar ahora</a>
                <a href="#modulos" class="secondary">📋 Ver módulos</a>
            @endauth
        </div>

        <!-- Estadísticas -->
        <div class="stats">
            <div class="stat"><div class="num">37+</div><div class="lbl">Controladores activos</div></div>
            <div class="stat"><div class="num">9</div><div class="lbl">Categorías funcionales</div></div>
            <div class="stat"><div class="num">100%</div><div class="lbl">Trazabilidad</div></div>
            <div class="stat"><div class="num">24/7</div><div class="lbl">Disponibilidad</div></div>
        </div>
    </header>

    <!-- Módulos -->
    <section class="modules" id="modulos">
        <div class="section-title reveal">
            <h2>Módulos del Sistema</h2>
            <p>Herramientas especializadas para cada proceso del cultivo</p>
            <div class="line"></div>
        </div>

        <!-- Cultivo y Terreno -->
        <div class="category reveal">
            <h3><span class="dot"></span> Cultivo y Terreno</h3>
            <div class="grid">
                <div class="card">
                    <div class="icon">🌾</div>
                    <h4>Cultivos</h4>
                    <p>Registro y seguimiento de cultivos activos.</p>
                </div>
                <div class="card">
                    <div class="icon">🗺️</div>
                    <h4>Lotes</h4>
                    <p>Gestión de lotes, zonas de manejo y riego.</p>
                </div>
                <div class="card">
                    <div class="icon">🌴</div>
                    <h4>Árboles</h4>
                    <p>Inventario por árbol, grafo y métricas.</p>
                </div>
                <div class="card">
                    <div class="icon">🧪</div>
                    <h4>Analítica de Suelo</h4>
                    <p>Análisis y monitoreo de suelos por lote.</p>
                </div>
            </div>
        </div>

        <!-- Ciclo Productivo -->
        <div class="category reveal">
            <h3><span class="dot"></span> Ciclo Productivo</h3>
            <div class="grid">
                <div class="card">
                    <div class="icon">🔄</div>
                    <h4>Ciclos Productivos</h4>
                    <p>Control de ciclos y su historial por etapa.</p>
                </div>
                <div class="card">
                    <div class="icon">🌱</div>
                    <h4>Fenología</h4>
                    <p>Etapas fenológicas y recomendaciones técnicas.</p>
                </div>
                <div class="card">
                    <div class="icon">📅</div>
                    <h4>Eventos</h4>
                    <p>Registro de eventos, tipos y labores de campo.</p>
                </div>
                <div class="card">
                    <div class="icon">👷</div>
                    <h4>Mano de Obra</h4>
                    <p>Asignación de trabajadores a eventos.</p>
                </div>
            </div>
        </div>

        <!-- Insumos y Recursos -->
        <div class="category reveal">
            <h3><span class="dot"></span> Insumos y Recursos</h3>
            <div class="grid">
                <div class="card">
                    <div class="icon">📦</div>
                    <h4>Insumos</h4>
                    <p>Categorías, stock y movimientos de insumos.</p>
                </div>
                <div class="card">
                    <div class="icon">🧰</div>
                    <h4>Contenedores</h4>
                    <p>Administración de contenedores y almacenamiento.</p>
                </div>
                <div class="card">
                    <div class="icon">💧</div>
                    <h4>Sistemas de Riego</h4>
                    <p>Configuración de riego por lote.</p>
                </div>
                <div class="card">
                    <div class="icon">🧾</div>
                    <h4>Gastos</h4>
                    <p>Control financiero de la operación agrícola.</p>
                </div>
            </div>
        </div>

        <!-- Cosecha y Comercial -->
        <div class="category reveal">
            <h3><span class="dot"></span> Cosecha y Comercial</h3>
            <div class="grid">
                <div class="card">
                    <div class="icon">📋</div>
                    <h4>Órdenes de Cosecha</h4>
                    <p>Planificación y ejecución de cosechas.</p>
                </div>
                <div class="card">
                    <div class="icon">🚚</div>
                    <h4>Recepción en Campo</h4>
                    <p>Registro de recepciones y sesiones de cosecha.</p>
                </div>
                <div class="card">
                    <div class="icon">📉</div>
                    <h4>Mermas</h4>
                    <p>Control y análisis de pérdidas.</p>
                </div>
                <div class="card">
                    <div class="icon">💰</div>
                    <h4>Ventas y Rendimiento</h4>
                    <p>Rendimiento por venta y clasificación de movimientos.</p>
                </div>
            </div>
        </div>

        <!-- Actores y Trazabilidad -->
        <div class="category reveal">
            <h3><span class="dot"></span> Actores y Trazabilidad</h3>
            <div class="grid">
                <div class="card">
                    <div class="icon">👥</div>
                    <h4>Trabajadores</h4>
                    <p>Administración del personal de campo.</p>
                </div>
                <div class="card">
                    <div class="icon">🤝</div>
                    <h4>Clientes y Proveedores</h4>
                    <p>Gestión comercial de aliados.</p>
                </div>
                <div class="card">
                    <div class="icon">🩺</div>
                    <h4>Historial Fitosanitario</h4>
                    <p>Trazabilidad sanitaria por árbol.</p>
                </div>
                <div class="card">
                    <div class="icon">📖</div>
                    <h4>Bitácora</h4>
                    <p>Registro cronológico de todas las operaciones.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div>Proyecto desarrollado por <strong>Estudiante de Ingeniería de Sistemas</strong></div>
        <div class="unab">🎓 Universidad Autónoma de Bucaramanga — UNAB</div>
        <div style="margin-top:1rem;font-size:.8rem;opacity:.75;">© {{ date('Y') }} PALMA · Sistema Agrícola</div>
    </footer>

    <script>
        // Scroll reveal
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, { threshold: 0.12 });

        document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(link => {
            link.addEventListener('click', e => {
                const target = document.querySelector(link.getAttribute('href'));
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
    </script>
</body>
</html>