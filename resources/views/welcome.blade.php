<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Palma - La Revolución del Helado Saludable</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #051a0a;
            color: #fbf9f5;
            overflow-x: hidden;
        }
        
        /* Custom Customizations outside Tailwind standard utility limits */
        .text-brand-green { color: #0b4a1b; }
        .bg-brand-green { background-color: #0b4a1b; }
        .border-brand-green { border-color: #0b4a1b; }
        
        .text-brand-lilac { color: #ccb4f5; }
        .bg-brand-lilac { background-color: #ccb4f5; }
        .border-brand-lilac { border-color: #ccb4f5; }

        /* Glassmorphism utility */
        .glass-panel {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        
        .glass-panel-active {
            background: rgba(204, 180, 245, 0.08);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(204, 180, 245, 0.2);
        }

        /* Glowing Effects */
        .glow-green {
            box-shadow: 0 0 40px rgba(11, 74, 27, 0.4);
        }
        .glow-lilac {
            box-shadow: 0 0 40px rgba(204, 180, 245, 0.3);
        }
        
        /* Hide scrollbar for clean app-like appearance */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #051a0a;
        }
        ::-webkit-scrollbar-thumb {
            background: #0b4a1b;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #ccb4f5;
        }
    </style>
</head>
<body class="relative min-h-screen selection:bg-[#ccb4f5] selection:text-[#0b4a1b]">

    <div id="mouse-glow" class="pointer-events-none fixed top-0 left-0 w-[500px] h-[500px] -translate-x-1/2 -translate-y-1/2 rounded-full bg-[radial-gradient(circle,rgba(204,180,245,0.06)_0%,rgba(0,0,0,0)_70%)] z-40 transition-transform duration-100 ease-out"></div>

    <header class="fixed top-0 left-0 w-full z-50 px-6 py-4 transition-all duration-300" id="main-nav">
        <div class="max-w-7xl mx-auto flex items-center justify-between glass-panel rounded-full px-6 py-3">
            <a href="#" class="flex items-center gap-2 group">
                <span class="w-8 h-8 rounded-full bg-gradient-to-tr from-[#0b4a1b] to-[#ccb4f5] flex items-center justify-center font-extrabold text-sm text-[#051a0a] transition-transform duration-500 group-hover:rotate-180">P</span>
                <span class="text-xl font-bold tracking-tight bg-gradient-to-r from-white to-[#ccb4f5] bg-clip-text text-transparent">palma</span>
            </a>
            
            <nav class="hidden md:flex items-center gap-8 text-sm font-medium tracking-wide text-gray-300">
                <a href="#idea" class="hover:text-[#ccb4f5] transition-colors">Nuestra Idea</a>
                <a href="#transformacion" class="hover:text-[#ccb4f5] transition-colors">Transformación</a>
                <a href="#menu" class="hover:text-[#ccb4f5] transition-colors">Menú</a>
                <a href="#experiencia" class="hover:text-[#ccb4f5] transition-colors">Comunidad</a>
                <a href="#ubicacion" class="hover:text-[#ccb4f5] transition-colors">Boutiques</a>
            </nav>
            
            <div>
                <a href="#contacto" class="relative group inline-flex items-center justify-center px-5 py-2 text-xs font-semibold uppercase tracking-wider text-[#051a0a] bg-[#ccb4f5] rounded-full overflow-hidden transition-all duration-300 hover:scale-105 active:scale-95 shadow-[0_4px_20px_rgba(204,180,245,0.3)]">
                    <span class="relative z-10">Pide Ahora</span>
                    <div class="absolute inset-0 -translate-x-full group-hover:translate-x-0 bg-white transition-transform duration-300 ease-out"></div>
                </a>
            </div>
        </div>
    </header>

    <section class="relative min-h-screen flex items-center justify-center pt-24 pb-16 px-6 overflow-hidden bg-[radial-gradient(circle_at_top_right,rgba(11,74,27,0.2),transparent_50%),radial-gradient(circle_at_bottom_left,rgba(204,180,245,0.05),transparent_50%)]">
        <div class="absolute top-1/4 left-1/10 w-96 h-96 bg-[#0b4a1b]/10 rounded-full blur-[120px] animate-pulse duration-[8000ms]"></div>
        <div class="absolute bottom-1/4 right-1/10 w-[450px] h-[450px] bg-[#ccb4f5]/5 rounded-full blur-[140px] animate-pulse duration-[10000ms]"></div>
        
        <div class="absolute top-28 left-[15%] floating-item text-3xl opacity-40 select-none">🍃</div>
        <div class="absolute bottom-32 left-[8%] floating-item text-4xl opacity-30 select-none" style="animation-delay: 2s;">🔮</div>
        <div class="absolute top-40 right-[12%] floating-item text-4xl opacity-40 select-none" style="animation-delay: 1.5s;">🫐</div>
        <div class="absolute bottom-40 right-[18%] floating-item text-3xl opacity-30 select-none" style="animation-delay: 3.5s;">🥥</div>

        <div class="max-w-7xl mx-auto w-full grid grid-cols-1 lg:grid-cols-12 gap-12 items-center relative z-10">
            <div class="lg:col-span-7 space-y-8 text-center lg:text-left">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/5 border border-white/10 text-xs font-semibold tracking-wider uppercase text-[#ccb4f5]" id="hero-tag">
                    <span class="w-2 h-2 rounded-full bg-[#ccb4f5] animate-ping"></span>
                    Estilo de vida Startup 2026
                </div>
                
                <h1 class="text-4xl sm:text-6xl lg:text-7xl font-extrabold tracking-tight leading-[1.05] text-white" id="hero-title">
                    Maria Camila <br>
                    <span class="bg-gradient-to-r from-[#ccb4f5] via-white to-[#ccb4f5] bg-clip-text text-transparent">Castrillon</span> <br>
                    <span class="text-[#ccb4f5] underline decoration-[#0b4a1b] decoration-wavy decoration-2">Ing de Mercados</span>.
                </h1>
                
                <p class="text-base sm:text-xl text-gray-300 max-w-xl mx-auto lg:mx-0 font-light leading-relaxed" id="hero-subtitle">
                    Helado de Acai el mas rico
                </p>
                
                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-4" id="hero-ctas">
                    <a href="#menu" class="w-full sm:w-auto px-8 py-4 bg-[#ccb4f5] text-[#051a0a] font-bold rounded-2xl shadow-[0_10px_30px_rgba(204,180,245,0.25)] hover:bg-white hover:scale-[1.02] active:scale-[0.98] transition-all duration-300 text-center">
                        Explorar Menú Futurista
                    </a>
                    <a href="#idea" class="w-full sm:w-auto px-8 py-4 glass-panel text-white hover:bg-white/10 font-medium rounded-2xl transition-all duration-300 text-center flex items-center justify-center gap-2">
                        Nuestra Filosofía 
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-[#ccb4f5]"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                    </a>
                </div>
            </div>

            <div class="lg:col-span-5 flex justify-center relative" id="hero-mockup">
                <div class="relative w-72 h-72 sm:w-96 sm:h-96 rounded-full bg-gradient-to-tr from-[#0b4a1b] to-[#ccb4f5]/30 p-[2px] shadow-[0_0_80px_rgba(11,74,27,0.3)] group">
                    <div class="w-full h-full bg-[#051a0a] rounded-full overflow-hidden flex items-center justify-center p-8 relative">
                        <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" class="w-full h-full drop-shadow-[0_20px_30px_rgba(204,180,245,0.4)] animate-[spin_60s_linear_infinite]">
                            <defs>
                                <linearGradient id="iceCreamGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" style="stop-color:#ccb4f5;stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:#0b4a1b;stop-opacity:1" />
                                </linearGradient>
                            </defs>
                            <path fill="url(#iceCreamGrad)" d="M44.7,-76.3C58.1,-69.3,69.2,-57.2,76.5,-42.9C83.8,-28.6,87.2,-12.1,86.2,4.1C85.2,20.3,79.8,36.2,71.1,49.2C62.4,62.2,50.3,72.4,36.2,78.2C22.1,84,6,85.5,-10.4,83.4C-26.9,81.3,-43.7,75.6,-57.2,65.3C-70.6,55,-80.6,40.1,-84.9,23.8C-89.2,7.5,-87.8,-10.2,-81.8,-25.9C-75.8,-41.6,-65.2,-55.4,-51.7,-62.6C-38.2,-69.9,-21.8,-70.6,-4.8,-62.9C12.1,-55.1,31.3,-83.2,44.7,-76.3Z" transform="translate(100 100)" />
                        </svg>
                        
                        <div class="absolute top-1/4 right-1/4 bg-white/10 backdrop-blur-md border border-white/20 text-[10px] uppercase font-bold tracking-widest px-3 py-1 rounded-full text-[#ccb4f5]">0% AZÚCAR</div>
                        <div class="absolute bottom-1/4 left-1/4 bg-white/10 backdrop-blur-md border border-white/20 text-[10px] uppercase font-bold tracking-widest px-3 py-1 rounded-full text-white">100% VEGAN</div>
                    </div>
                </div>
                <div class="absolute -inset-4 bg-gradient-to-tr from-[#0b4a1b] to-[#ccb4f5] rounded-full blur-3xl opacity-20 -z-10 animate-pulse"></div>
            </div>
        </div>
    </section>

    <section id="idea" class="py-24 px-6 max-w-7xl mx-auto border-t border-white/5 relative">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-start">
            
            <div class="lg:col-span-5 sticky top-28 space-y-6">
                <div class="text-xs font-bold uppercase tracking-widest text-[#ccb4f5] flex items-center gap-2">
                    <span class="w-6 h-[1px] bg-[#ccb4f5]"></span> El Manifiesto Palma
                </div>
                <h2 class="text-3xl sm:text-5xl font-extrabold tracking-tight leading-none text-white">
                    Redefiniendo <br>el placer culinario.
                </h2>
                <p class="text-gray-400 font-light leading-relaxed">
                    Creemos firmemente que comer bien no debe ser un acto de sacrificio ni restricción. Los helados tradicionales nos han condicionado a elegir entre sabor extraordinario o salud comprometida. Nosotros rompimos ese paradigma.
                </p>
                <div class="p-6 rounded-2xl bg-gradient-to-br from-[#0b4a1b]/40 to-transparent border border-[#0b4a1b]/50">
                    <span class="text-3xl block mb-2">⚡</span>
                    <p class="text-sm font-semibold text-[#ccb4f5]">La Premisa Fundamental:</p>
                    <p class="text-xs text-gray-300 mt-1">El helado no es un premio de fin de semana para sabotear tus hábitos; es el combustible diario de tu felicidad biológica.</p>
                </div>
            </div>

            <div class="lg:col-span-7 space-y-12 pt-4 lg:pt-0">
                <div class="glass-panel rounded-3xl p-8 space-y-4 reveal-card">
                    <div class="w-12 h-12 rounded-xl bg-red-500/10 flex items-center justify-center text-red-400 font-bold border border-red-500/20">01</div>
                    <h3 class="text-xl font-bold text-white">El Gran Problema de la Industria</h3>
                    <p class="text-gray-400 font-light text-sm leading-relaxed">
                        Los helados industriales están cargados de jarabe de maíz de alta fructosa, grasas hidrogenadas y estabilizantes químicos derivados del petróleo. Generan picos masivos de insulina, inflamación celular y un bajón energético inmediato. Te venden felicidad momentánea a cambio de tu bienestar a largo plazo.
                    </p>
                </div>

                <div class="glass-panel rounded-3xl p-8 space-y-4 reveal-card">
                    <div class="w-12 h-12 rounded-xl bg-[#ccb4f5]/10 flex items-center justify-center text-[#ccb4f5] font-bold border border-[#ccb4f5]/20">02</div>
                    <h3 class="text-xl font-bold text-white">La Alternativa Bio-Evolucionada</h3>
                    <p class="text-gray-400 font-light text-sm leading-relaxed">
                        En nuestro laboratorio gastronómico sustituimos los lácteos pesados por bases sedosas de coco joven, castañas de cajú activadas y pulpa pura de superfrutas como el Açaí de la Amazonía. Endulzamos exclusivamente con compuestos botánicos puros (monk fruit de alta pureza y eritritol orgánico) que poseen un índice glucémico CERO.
                    </p>
                </div>

                <div class="glass-panel rounded-3xl p-8 space-y-4 reveal-card">
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-400 font-bold border border-emerald-500/20">03</div>
                    <h3 class="text-xl font-bold text-white">Experiencia Premium y Conectada</h3>
                    <p class="text-gray-400 font-light text-sm leading-relaxed">
                        Cada cucharada aporta fibra prebiótica para tu microbiota, antioxidantes activos y grasas monoinsaturadas cardiosaludables. Es textura densa, cremosidad total de alta repostería y un sabor espectacular que te empodera. Volverás a sonreír compartiendo un helado sin una sola pizca de arrepentimiento.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section id="transformacion" class="py-24 px-6 bg-[linear-gradient(to_bottom,#051a0a,#0a2d13,#051a0a)] relative overflow-hidden">
        <div class="max-w-7xl mx-auto relative z-10">
            <div class="text-center max-w-2xl mx-auto mb-16 space-y-4">
                <span class="text-xs font-bold uppercase tracking-widest text-[#ccb4f5] bg-white/5 border border-white/10 px-4 py-1.5 rounded-full">Análisis Comparativo</span>
                <h2 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-white">La Transformación de la Cuchara</h2>
                <p class="text-gray-300 font-light text-sm">Mira exactamente qué pones en tu cuerpo. Dejemos atrás los paradigmas anticuados del siglo pasado.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-stretch">
                <div class="glass-panel rounded-[32px] p-8 lg:p-12 border-red-500/10 bg-red-950/5 relative overflow-hidden flex flex-col justify-between group">
                    <div class="absolute -top-12 -right-12 w-48 h-48 bg-red-600/5 rounded-full blur-3xl group-hover:scale-125 transition-transform duration-700"></div>
                    <div>
                        <div class="flex items-center justify-between mb-8">
                            <h3 class="text-xl font-bold uppercase tracking-wider text-red-400">Helado Tradicional</h3>
                            <span class="text-xs font-bold text-red-400/70 uppercase border border-red-500/20 px-3 py-1 rounded-full bg-red-500/10">Ultraprocesado</span>
                        </div>
                        <ul class="space-y-4 text-sm text-gray-400">
                            <li class="flex items-start gap-3">
                                <span class="text-red-400 mt-0.5">✕</span>
                                <div><strong class="text-gray-300">Azúcar Blanca Refinada (32g por porción):</strong> Genera picos brutales de glucosa y adicción neuroquímica.</div>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="text-red-400 mt-0.5">✕</span>
                                <div><strong class="text-gray-300">Grasas Trans e Hidrogenadas:</strong> Aceites vegetales refinados de palma o soya de baja calidad para dar volumen artificial.</div>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="text-red-400 mt-0.5">✕</span>
                                <div><strong class="text-gray-300">Químicos y Colorantes Tartrazina:</strong> Conservantes nocivos para extender la vida útil en estantes por meses.</div>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="text-red-400 mt-0.5">✕</span>
                                <div><strong class="text-gray-300">Lácteos de Ganadería Intensiva:</strong> Altos en hormonas y caseína A1, detonantes frecuentes de inflamación digestiva.</div>
                            </li>
                        </ul>
                    </div>
                    <div class="mt-8 pt-6 border-t border-white/5 flex items-center justify-between text-xs text-red-300/60 font-medium">
                        <span>Efecto adverso: Letargo y culpa post-consumo</span>
                        <span class="text-lg">❌</span>
                    </div>
                </div>

                <div class="glass-panel rounded-[32px] p-8 lg:p-12 border-[#ccb4f5]/20 bg-[#0b4a1b]/20 relative overflow-hidden flex flex-col justify-between group glow-green">
                    <div class="absolute -top-12 -right-12 w-48 h-48 bg-[#ccb4f5]/10 rounded-full blur-3xl group-hover:scale-125 transition-transform duration-700"></div>
                    <div>
                        <div class="flex items-center justify-between mb-8">
                            <h3 class="text-xl font-bold uppercase tracking-wider text-[#ccb4f5]">La Evolución Palma</h3>
                            <span class="text-xs font-bold text-[#ccb4f5] uppercase border border-[#ccb4f5]/30 px-3 py-1 rounded-full bg-[#ccb4f5]/10">100% Nutrición Bioactiva</span>
                        </div>
                        <ul class="space-y-4 text-sm text-gray-300">
                            <li class="flex items-start gap-3">
                                <span class="text-[#ccb4f5] mt-0.5">✓</span>
                                <div><strong class="text-white">Endulzado Natural Botánico:</strong> Cero azúcar. Usamos Monk Fruit puro. Amigable para diabéticos y estilo Keto.</div>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="text-[#ccb4f5] mt-0.5">✓</span>
                                <div><strong class="text-white">Grasas Saludables de Cadena Media:</strong> Extraídas de cocos jóvenes y frutos secos activados mecánicamente.</div>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="text-[#ccb4f5] mt-0.5">✓</span>
                                <div><strong class="text-white">Fruta Real Súper Concentrada:</strong> Colores intensos directo de antocianinas de açaí y clorofila silvestre.</div>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="text-[#ccb4f5] mt-0.5">✓</span>
                                <div><strong class="text-white">Prebióticos Inulina de Achicoria:</strong> Estimula selectivamente el crecimiento de bacterias benéficas en tu colon.</div>
                            </li>
                        </ul>
                    </div>
                    <div class="mt-8 pt-6 border-t border-white/5 flex items-center justify-between text-xs text-[#ccb4f5] font-medium">
                        <span>Efecto adverso: Vitalidad celular y endorfinas naturales</span>
                        <span class="text-lg">🌿✨</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="menu" class="py-24 px-6 max-w-7xl mx-auto relative">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-6">
            <div class="space-y-4">
                <span class="text-xs font-bold uppercase tracking-widest text-[#ccb4f5] flex items-center gap-2">
                    <span class="w-6 h-[1px] bg-[#ccb4f5]"></span> Alta Expresión Heladera
                </span>
                <h2 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-white">Nuestras Creaciones Lab 2026</h2>
            </div>
            <div class="flex flex-wrap gap-2 text-xs font-medium">
                <button class="px-4 py-2 rounded-full bg-[#ccb4f5] text-[#051a0a] font-bold shadow-sm transition-all duration-300">Todos</button>
                <button class="px-4 py-2 rounded-full glass-panel text-gray-300 hover:text-white transition-all duration-300">Antioxidantes</button>
                <button class="px-4 py-2 rounded-full glass-panel text-gray-300 hover:text-white transition-all duration-300">Keto-Friendly</button>
                <button class="px-4 py-2 rounded-full glass-panel text-gray-300 hover:text-white transition-all duration-300">High-Protein</button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            
            <div class="glass-panel rounded-[28px] p-5 flex flex-col justify-between group hover:border-[#ccb4f5]/40 hover:-translate-y-2 transition-all duration-300 relative overflow-hidden">
                <div>
                    <div class="w-full aspect-square bg-gradient-to-br from-[#0b4a1b] to-[#ccb4f5]/40 rounded-2xl mb-6 relative overflow-hidden flex items-center justify-center">
                        <span class="text-6xl group-hover:scale-125 transition-transform duration-500 select-none">🌴</span>
                        <div class="absolute inset-0 bg-black/10 mix-blend-overlay"></div>
                    </div>
                    <div class="flex flex-wrap gap-1 mb-3">
                        <span class="text-[9px] font-extrabold uppercase bg-emerald-950 text-emerald-400 px-2 py-0.5 rounded border border-emerald-500/20">Vegano</span>
                        <span class="text-[9px] font-extrabold uppercase bg-purple-950 text-purple-300 px-2 py-0.5 rounded border border-purple-500/20">Alto en Fruta</span>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2 group-hover:text-[#ccb4f5] transition-colors">Palma Açaí Amazonia</h3>
                    <p class="text-xs text-gray-400 font-light leading-relaxed mb-4">
                        El estandarte de la casa. Pulpa orgánica de açaí silvestre prensado en frío, remolinada con crema de almendras tostadas y nibs crujientes de cacao puro.
                    </p>
                </div>
                <div>
                    <div class="text-[11px] text-gray-400 border-b border-white/5 pb-3 mb-4">
                        <span class="text-white font-medium">Ingredientes:</span> Açaí, coco, almendra, monk fruit, maca.
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-lg font-bold text-white">$6.200 <span class="text-xs text-gray-400 font-light">/ scoop</span></span>
                        <button class="w-9 h-9 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-white hover:bg-[#ccb4f5] hover:text-[#051a0a] transition-all duration-300 font-bold">+</button>
                    </div>
                </div>
            </div>

            <div class="glass-panel rounded-[28px] p-5 flex flex-col justify-between group hover:border-[#ccb4f5]/40 hover:-translate-y-2 transition-all duration-300 relative overflow-hidden">
                <div>
                    <div class="w-full aspect-square bg-gradient-to-br from-[#0c3140] to-[#ccb4f5]/30 rounded-2xl mb-6 relative overflow-hidden flex items-center justify-center">
                        <span class="text-6xl group-hover:scale-125 transition-transform duration-500 select-none">🪻</span>
                        <div class="absolute inset-0 bg-black/10 mix-blend-overlay"></div>
                    </div>
                    <div class="flex flex-wrap gap-1 mb-3">
                        <span class="text-[9px] font-extrabold uppercase bg-emerald-950 text-emerald-400 px-2 py-0.5 rounded border border-emerald-500/20">Sin Azúcar</span>
                        <span class="text-[9px] font-extrabold uppercase bg-blue-950 text-blue-300 px-2 py-0.5 rounded border border-blue-500/20">Natural</span>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2 group-hover:text-[#ccb4f5] transition-colors">Matcha Lavender Zen</h3>
                    <p class="text-xs text-gray-400 font-light leading-relaxed mb-4">
                        Té matcha ceremonial de Kyoto infusionado con extracto botánico de lavanda orgánica. Diseñado para inducir un estado de calma atenta y enfoque cognitivo.
                    </p>
                </div>
                <div>
                    <div class="text-[11px] text-gray-400 border-b border-white/5 pb-3 mb-4">
                        <span class="text-white font-medium">Ingredientes:</span> Matcha grado Uji, lavanda, leche de cajú, eritritol.
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-lg font-bold text-white">$6.800 <span class="text-xs text-gray-400 font-light">/ scoop</span></span>
                        <button class="w-9 h-9 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-white hover:bg-[#ccb4f5] hover:text-[#051a0a] transition-all duration-300 font-bold">+</button>
                    </div>
                </div>
            </div>

            <div class="glass-panel rounded-[28px] p-5 flex flex-col justify-between group hover:border-[#ccb4f5]/40 hover:-translate-y-2 transition-all duration-300 relative overflow-hidden">
                <div>
                    <div class="w-full aspect-square bg-gradient-to-br from-[#402a0c] to-[#0b4a1b]/40 rounded-2xl mb-6 relative overflow-hidden flex items-center justify-center">
                        <span class="text-6xl group-hover:scale-125 transition-transform duration-500 select-none">🥑</span>
                        <div class="absolute inset-0 bg-black/10 mix-blend-overlay"></div>
                    </div>
                    <div class="flex flex-wrap gap-1 mb-3">
                        <span class="text-[9px] font-extrabold uppercase bg-emerald-950 text-emerald-400 px-2 py-0.5 rounded border border-emerald-500/20">Vegano</span>
                        <span class="text-[9px] font-extrabold uppercase bg-amber-950 text-amber-300 px-2 py-0.5 rounded border border-amber-500/20">Keto</span>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2 group-hover:text-[#ccb4f5] transition-colors">Velvet Choco Avocado</h3>
                    <p class="text-xs text-gray-400 font-light leading-relaxed mb-4">
                        Cacao ecuatoriano de origen al 85% fundido con la untuosidad de la palta hass madura. Una textura que imita la mejor crema pastelera francesa sin lácteos.
                    </p>
                </div>
                <div>
                    <div class="text-[11px] text-gray-400 border-b border-white/5 pb-3 mb-4">
                        <span class="text-white font-medium">Ingredientes:</span> Cacao fino de aroma, palta hass, dátiles, vainilla.
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-lg font-bold text-white">$6.400 <span class="text-xs text-gray-400 font-light">/ scoop</span></span>
                        <button class="w-9 h-9 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-white hover:bg-[#ccb4f5] hover:text-[#051a0a] transition-all duration-300 font-bold">+</button>
                    </div>
                </div>
            </div>

            <div class="glass-panel rounded-[28px] p-5 flex flex-col justify-between group hover:border-[#ccb4f5]/40 hover:-translate-y-2 transition-all duration-300 relative overflow-hidden">
                <div>
                    <div class="w-full aspect-square bg-gradient-to-br from-[#1b0b4a] to-[#ccb4f5]/40 rounded-2xl mb-6 relative overflow-hidden flex items-center justify-center">
                        <span class="text-6xl group-hover:scale-125 transition-transform duration-500 select-none">🥜</span>
                        <div class="absolute inset-0 bg-black/10 mix-blend-overlay"></div>
                    </div>
                    <div class="flex flex-wrap gap-1 mb-3">
                        <span class="text-[9px] font-extrabold uppercase bg-purple-950 text-purple-300 px-2 py-0.5 rounded border border-purple-500/20">High Protein</span>
                        <span class="text-[9px] font-extrabold uppercase bg-emerald-950 text-emerald-400 px-2 py-0.5 rounded border border-emerald-500/20">Sin Azúcar</span>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2 group-hover:text-[#ccb4f5] transition-colors">Pistachio Paradise</h3>
                    <p class="text-xs text-gray-400 font-light leading-relaxed mb-4">
                        Pistacho siciliano puro molido a piedra, potenciado con aislado de proteína vegetal premium de arveja. Un snack de recuperación post-entrenamiento de lujo.
                    </p>
                </div>
                <div>
                    <div class="text-[11px] text-gray-400 border-b border-white/5 pb-3 mb-4">
                        <span class="text-white font-medium">Ingredientes:</span> Pistacho, proteína vegetal, sal de mar, stevia pura.
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-lg font-bold text-white">$7.100 <span class="text-xs text-gray-400 font-light">/ scoop</span></span>
                        <button class="w-9 h-9 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-white hover:bg-[#ccb4f5] hover:text-[#051a0a] transition-all duration-300 font-bold">+</button>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <section id="experiencia" class="py-24 px-6 bg-[radial-gradient(circle_at_center,rgba(204,180,245,0.04),transparent_60%)]">
        <div class="max-w-7xl mx-auto">
            
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 mb-24 text-center" id="stats-container">
                <div class="glass-panel p-6 rounded-2xl">
                    <div class="text-3xl sm:text-5xl font-extrabold text-[#ccb4f5] mb-2 font-mono" data-target="100">0%</div>
                    <div class="text-xs uppercase tracking-widest text-gray-400">Azúcar Añadida</div>
                </div>
                <div class="glass-panel p-6 rounded-2xl">
                    <div class="text-3xl sm:text-5xl font-extrabold text-white mb-2 font-mono" data-target="50000">0+</div>
                    <div class="text-xs uppercase tracking-widest text-gray-400">Sonrisas Saludables</div>
                </div>
                <div class="glass-panel p-6 rounded-2xl">
                    <div class="text-3xl sm:text-5xl font-extrabold text-[#ccb4f5] mb-2 font-mono" data-target="12">0</div>
                    <div class="text-xs uppercase tracking-widest text-gray-400">Ingredientes Ancestrales</div>
                </div>
                <div class="glass-panel p-6 rounded-2xl">
                    <div class="text-3xl sm:text-5xl font-extrabold text-white mb-2 font-mono" data-target="0">0%</div>
                    <div class="text-xs uppercase tracking-widest text-gray-400">Culpa Post-Postre</div>
                </div>
            </div>

            <div class="text-center max-w-xl mx-auto mb-16 space-y-3">
                <span class="text-xs font-bold uppercase tracking-widest text-[#ccb4f5]">Comunidad Palma</span>
                <h2 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-white">Lo que dice la Tribu Consciente</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                <div class="glass-panel p-8 rounded-3xl space-y-6 relative">
                    <span class="text-5xl text-[#ccb4f5]/20 absolute top-4 right-6 font-serif">“</span>
                    <p class="text-sm text-gray-300 font-light leading-relaxed relative z-10">
                        "El de Velvet Choco cambió por completo mis meriendas. Soy atleta de alto rendimiento y encontrar algo que respete mis macros, no me inflame y se sienta como un postre de tres estrellas Michelin es un absoluto milagro."
                    </p>
                    <div class="flex items-center gap-4 border-t border-white/5 pt-4">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-[#0b4a1b] to-[#ccb4f5] flex items-center justify-center font-bold text-xs text-black">VM</div>
                        <div>
                            <h4 class="text-sm font-semibold text-white">Valeria Mendoza</h4>
                            <p class="text-[11px] text-[#ccb4f5]">Crossfit Coach & Biohacker</p>
                        </div>
                    </div>
                </div>

                <div class="glass-panel p-8 rounded-3xl space-y-6 relative border-[#ccb4f5]/20">
                    <span class="text-5xl text-[#ccb4f5]/20 absolute top-4 right-6 font-serif">“</span>
                    <p class="text-sm text-gray-300 font-light leading-relaxed relative z-10">
                        "Tengo diabetes tipo 2 y llevaba 6 años sin poder sentarme a disfrutar un helado con mis hijos los domingos. El eslogan de 'una nueva forma de compartir' es 100% real. Gracias por devolvernos esto sin alterar mis niveles."
                    </p>
                    <div class="flex items-center gap-4 border-t border-white/5 pt-4">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-[#ccb4f5] to-[#0b4a1b] flex items-center justify-center font-bold text-xs text-black">AM</div>
                        <div>
                            <h4 class="text-sm font-semibold text-white">Alejandro Martí</h4>
                            <p class="text-[11px] text-[#ccb4f5]">Emprendedor Tecnológico</p>
                        </div>
                    </div>
                </div>

                <div class="glass-panel p-8 rounded-3xl space-y-6 relative">
                    <span class="text-5xl text-[#ccb4f5]/20 absolute top-4 right-6 font-serif">“</span>
                    <p class="text-sm text-gray-300 font-light leading-relaxed relative z-10">
                        "Visualmente el empaque y el local se sienten como entrar a una tienda de Apple en el futuro, pero el sabor del Matcha Lavender es de otro planeta. Es sedoso, floral, perfectamente equilibrado. Mi adicción saludable favorita."
                    </p>
                    <div class="flex items-center gap-4 border-t border-white/5 pt-4">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-white to-[#ccb4f5] flex items-center justify-center font-bold text-xs text-black">SR</div>
                        <div>
                            <h4 class="text-sm font-semibold text-white">Sofía Rincón</h4>
                            <p class="text-[11px] text-[#ccb4f5]">Diseñadora Digital</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <section id="ubicacion" class="py-24 px-6 max-w-7xl mx-auto border-t border-white/5">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <div class="lg:col-span-5 space-y-6">
                <span class="text-xs font-bold uppercase tracking-widest text-[#ccb4f5] bg-white/5 border border-white/10 px-4 py-1.5 rounded-full inline-block">Nuestros Espacios</span>
                <h2 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-white">Visita un Oasis de Bienestar</h2>
                <p class="text-gray-400 font-light leading-relaxed">
                    Nuestras boutiques físicas están diseñadas bajo principios de arquitectura biofílica y minimalismo tecnológico. Un espacio de paz sonora, aromas naturales y estaciones de carga inalámbrica para que disfrutes tu momento.
                </p>
                
                <div class="space-y-4 pt-4 text-sm">
                    <div class="flex items-start gap-3">
                        <span class="text-xl">📍</span>
                        <div>
                            <strong class="text-white block">Palma Flagship Lab:</strong>
                            <span class="text-gray-400">Av. Las Palmas #45 - Edificio Quantum, Piso 1.</span>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="text-xl">🕒</span>
                        <div>
                            <strong class="text-white block">Horarios de Experiencia:</strong>
                            <span class="text-gray-400">Lunes a Sábado: 11:00 AM - 9:30 PM <br>Domingos de Mercado Consciente: 10:00 AM - 8:30 PM</span>
                        </div>
                    </div>
                </div>
                
                <div class="pt-2">
                    <a href="https://maps.google.com" target="_blank" class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-[#ccb4f5] hover:text-white transition-colors group">
                        Abrir en Google Maps 
                        <span class="transform group-hover:translate-x-1 transition-transform">→</span>
                    </a>
                </div>
            </div>

            <div class="lg:col-span-7 h-96 rounded-[32px] overflow-hidden glass-panel p-2 relative group shadow-2xl">
                <div class="w-full h-full rounded-[26px] bg-[#05260e] relative overflow-hidden flex items-center justify-center border border-white/5">
                    <div class="absolute inset-0 opacity-20 bg-[linear-gradient(to_right,#0b4a1b_1px,transparent_1px),linear-gradient(to_bottom,#0b4a1b_1px,transparent_1px)] bg-[size:30px_30px]"></div>
                    
                    <div class="relative z-10 flex flex-col items-center">
                        <div class="w-6 h-6 rounded-full bg-[#ccb4f5] flex items-center justify-center glow-lilac animate-bounce">
                            <div class="w-2 h-2 rounded-full bg-[#051a0a]"></div>
                        </div>
                        <div class="mt-4 px-4 py-2 glass-panel rounded-xl text-center backdrop-blur-xl">
                            <p class="text-xs font-bold text-white">Palma Flagship Store</p>
                            <p class="text-[10px] text-[#ccb4f5] font-light">Estación Saludable Activa</p>
                        </div>
                    </div>
                    
                    <div class="absolute bottom-4 left-4 text-[10px] font-mono text-gray-500 bg-black/40 px-3 py-1 rounded-md">LAT: 7.1192° N / LONG: 73.1221° W</div>
                    <div class="absolute top-4 right-4 text-[10px] font-bold uppercase text-emerald-400 bg-emerald-950/80 border border-emerald-500/30 px-2 py-0.5 rounded-full">Señal satelital premium</div>
                </div>
            </div>

        </div>
    </section>

    <section id="contacto" class="py-24 px-6 bg-[linear-gradient(to_top,#030f06,#051a0a)] border-t border-white/5 relative">
        <div class="max-w-4xl mx-auto glass-panel rounded-[40px] p-8 sm:p-12 md:p-16 relative overflow-hidden shadow-[0_30px_100px_rgba(11,74,27,0.15)]">
            <div class="absolute top-0 right-0 w-64 h-64 bg-[#ccb4f5]/5 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="text-center max-w-xl mx-auto mb-12 space-y-4">
                <h2 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-white">Forma parte de la Revolución</h2>
                <p class="text-gray-400 font-light text-sm">¿Quieres catering para un evento corporativo de bienestar, ser distribuidor o simplemente dejarnos un mensaje de amor digital?</p>
            </div>

            <form class="space-y-6" onsubmit="event.preventDefault(); alert('¡Mensaje enviado a Palma Lab! Conectando canales de felicidad.');">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-xs font-semibold tracking-wider uppercase text-gray-300">Tu Nombre</label>
                        <input type="text" placeholder="Ej. Carlos Alcaraz" required class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-3.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:border-[#ccb4f5] focus:bg-white/10 transition-all duration-300">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-semibold tracking-wider uppercase text-gray-300">Correo Corporativo / Personal</label>
                        <input type="email" placeholder="carlos@startup.com" required class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-3.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:border-[#ccb4f5] focus:bg-white/10 transition-all duration-300">
                    </div>
                </div>
                
                <div class="space-y-2">
                    <label class="text-xs font-semibold tracking-wider uppercase text-gray-300">¿Cómo deseas colaborar?</label>
                    <select class="w-full bg-[#051a0a] border border-white/10 rounded-2xl px-5 py-3.5 text-sm text-gray-300 focus:outline-none focus:border-[#ccb4f5] transition-all duration-300 appearance-none">
                        <option>Quiero hacer un pedido premium corporativo</option>
                        <option>Deseo franquiciar la marca en mi ciudad (Latam 2026)</option>
                        <option>Sugerencias o felicitaciones al equipo chef molecular</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-semibold tracking-wider uppercase text-gray-300">Tu Mensaje</label>
                    <textarea rows="4" placeholder="Cuéntanos un poco sobre tus ideas..." required class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-3.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:border-[#ccb4f5] focus:bg-white/10 transition-all duration-300 resize-none"></textarea>
                </div>

                <button type="submit" class="w-full py-4 bg-[#ccb4f5] text-[#051a0a] font-bold rounded-2xl shadow-lg hover:bg-white hover:scale-[1.01] active:scale-[0.99] transition-all duration-300 text-center uppercase tracking-widest text-xs">
                    Enviar Transmisión Digital
                </button>
            </form>

            <div class="mt-12 pt-8 border-t border-white/5 flex flex-wrap items-center justify-center gap-6 text-xs font-medium text-gray-400">
                <span class="uppercase tracking-widest text-[10px]">Canales Inmediatos:</span>
                <a href="https://instagram.com" target="_blank" class="flex items-center gap-1 hover:text-[#ccb4f5] transition-colors">
                    <span>📸</span> Instagram
                </a>
                <a href="https://whatsapp.com" target="_blank" class="flex items-center gap-1 hover:text-[#ccb4f5] transition-colors">
                    <span>💬</span> WhatsApp Directo
                </a>
                <a href="https://facebook.com" target="_blank" class="flex items-center gap-1 hover:text-[#ccb4f5] transition-colors">
                    <span>👥</span> Facebook Hub
                </a>
            </div>
        </div>
    </section>

    <footer class="py-12 px-6 border-t border-white/5 text-center text-xs text-gray-500 space-y-4">
        <p class="font-medium tracking-wide text-gray-400">© 2026 PALMA FOODS SYSTEMS INC. REVOLUCIÓN PLANETARIA SALUDABLE.</p>
        <p class="font-light max-w-md mx-auto text-gray-600">Desarrollado bajo estándares premium de experiencia de usuario web. Prohibida la reproducción de nuestra fórmula molecular sin el consentimiento de los chefs.</p>
    </footer>

    <script>
        // Check if plugins are loaded before initialization
        gsap.registerPlugin(ScrollTrigger);

        // 1. Mouse Glow Tracking Effect
        const mouseGlow = document.getElementById('mouse-glow');
        window.addEventListener('mousemove', (e) => {
            gsap.to(mouseGlow, {
                x: e.clientX,
                y: e.clientY,
                duration: 0.3,
                ease: 'power2.out'
            });
        });

        // 2. Navbar glass visual interaction on scroll
        window.addEventListener('scroll', () => {
            const nav = document.getElementById('main-nav');
            if (window.scrollY > 50) {
                nav.classList.remove('py-4');
                nav.classList.add('py-2', 'backdrop-blur-xl', 'bg-black/20');
            } else {
                nav.classList.remove('py-2', 'backdrop-blur-xl', 'bg-black/20');
                nav.classList.add('py-4');
            }
        });

        // 3. Hero Load Reveal Sequence
        window.addEventListener('DOMContentLoaded', () => {
            const tl = gsap.timeline();
            tl.from('#hero-tag', { opacity: 0, y: -20, duration: 0.6, ease: 'power3.out' })
              .from('#hero-title', { opacity: 0, y: 30, duration: 0.8, ease: 'power3.out' }, '-=0.4')
              .from('#hero-subtitle', { opacity: 0, y: 20, duration: 0.6, ease: 'power3.out' }, '-=0.5')
              .from('#hero-ctas', { opacity: 0, y: 15, duration: 0.6, ease: 'power3.out' }, '-=0.4')
              .from('#hero-mockup', { opacity: 0, scale: 0.9, duration: 1, ease: 'elastic.out(1, 0.75)' }, '-=0.6');
        });

        // 4. Scroll Reveal Card Animations (Nuestra Idea Section)
        gsap.utils.toArray('.reveal-card').forEach((card, index) => {
            gsap.from(card, {
                scrollTrigger: {
                    trigger: card,
                    start: 'top 85%',
                    toggleActions: 'play none none none'
                },
                opacity: 0,
                y: 40,
                duration: 0.8,
                ease: 'power2.out',
                delay: index * 0.1
            });
        });

        // 5. Animated Numbers Counter Effect (Experiencia Section)
        const statsSection = document.getElementById('stats-container');
        if (statsSection) {
            gsap.from(statsSection.querySelectorAll('[data-target]'), {
                scrollTrigger: {
                    trigger: statsSection,
                    start: 'top 80%',
                    toggleActions: 'play none none none'
                },
                innerText: 0,
                duration: 2,
                snap: { innerText: 1 },
                stagger: 0.2,
                ease: 'power1.out',
                onUpdate: function() {
                    // Quick fix to handle suffixes correctly during runtime values updating
                    this.targets().forEach(el => {
                        const targetVal = el.getAttribute('data-target');
                        if (targetVal === "100" || targetVal === "0") {
                            el.innerText = el.innerText + "%";
                        } else if (targetVal === "50000") {
                            el.innerText = parseInt(el.innerText).toLocaleString() + "+";
                        }
                    });
                }
            });
        }
    </script>
</body>
</html>
