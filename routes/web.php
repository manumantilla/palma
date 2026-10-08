<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ArbolApiController;
use App\Http\Controllers\CultivoController;
use App\Http\Controllers\RecepcionCampoController;
use App\Http\Controllers\SesionCosechaController;
use App\Http\Controllers\FenologiaEtapaController;
use App\Http\Controllers\TipoEventoController;
use App\Http\Controllers\CicloEtapaHistorialController;
use App\Http\Controllers\FenologiaRecomendacionController;
use App\Http\Controllers\TrabajadorController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\GastoController;
use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\CicloProductivoController;
use App\Http\Controllers\CategoriaInsumoController;
use App\Http\Controllers\InsumoController;
use App\Http\Controllers\LoteInsumoController;
use App\Http\Controllers\EventoInsumoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\LoteController;
use App\Http\Controllers\OrdenCosechaController;
use App\Http\Controllers\ArbolController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\ArbolGrafoController;
use App\Http\Controllers\LoteZonaManejoController;
use App\Http\Controllers\CompraController;

// --- RUTAS PÚBLICAS ---
Route::get('/', function () {
    return view('welcome');
});

// --- RUTAS PROTEGIDAS (Requieren Autenticación) ---
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    
    // Dashboard Principal
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Catálogos y Recursos Básicos
    Route::resource('cultivos', CultivoController::class);
    Route::resource('clientes', ClienteController::class);
    Route::resource('trabajadores', TrabajadorController::class);
    Route::resource('bitacoras', BitacoraController::class);
    
    // Ciclos y Tipos de Evento
    Route::resource('ciclos-productivos', CicloProductivoController::class)
        ->except(['edit', 'destroy'])
        ->parameters([
            'ciclos-productivos' => 'cicloProductivo',
        ]);
    Route::get('ciclos-productivos/{cicloProductivo}/mapa', [CicloProductivoController::class, 'vistaMapa'])
    ->name('ciclos-productivos.mapa');
    //Get JSON
    Route::get('ciclos-productivos/{cicloProductivo}/grafo-estadisticas', [CicloProductivoController::class, 'obtenerGrafoYEstadisticas'])
    ->name('ciclos-productivos.grafo-estadisticas');

    //REGISTRA ETAPAS FENOLOGICAS A UN CULTIVO
    Route::post('/ciclo-etapas/inicializar-plan', [CicloEtapaHistorialController::class, 'inicializarPlanFenologico'])
        ->name('ciclo-etapas.inicializar-plan');
        
    Route::post('/ciclo-etapas/{id}/iniciar', [CicloEtapaHistorialController::class, 'iniciarEtapa'])
        ->name('ciclo-etapas.iniciar');
    Route::post('/ciclo-etapas/{id}/completar_etapa', [CicloEtapaHistorialController::class, 'completar'])
        ->name('ciclo-etapas.completar');
    Route::post('/ciclo-etapas/{id}/omitirEtapa', [CicloEtapaHistorialController::class, 'omitirEtapa'])
        ->name('ciclo-etapas.omitir');
        
        
    
    Route::resource('tipos-evento', TipoEventoController::class);
    
    // Insumos
    Route::resource('categorias-insumo', CategoriaInsumoController::class);
    Route::resource('insumos', InsumoController::class);
    Route::resource('lotes-insumo', LoteInsumoController::class);
    Route::resource('lote-zonas-manejo', LoteZonaManejoController::class);
    Route::get('/{evento}/insumos/create', [EventoInsumoController::class, 'create'])->name('eventos_campo.insumos.create');
    // POST /eventos-campo/{evento}/insumos
    Route::post('/{evento}/insumos', [EventoInsumoController::class, 'store'])
        ->name('eventos_campo.insumos.store');
    // Gastos
    Route::resource('gastos', GastoController::class)->except(['create', 'show']);

    // Cosecha
    Route::resource('sesiones-cosecha', SesionCosechaController::class);
    Route::resource('ordenes_cosecha', OrdenCosechaController::class)->except('create');    
    Route::get('/ordenes-cosecha/crear/{ciclo}', [OrdenCosechaController::class, 'create'])->name('ordenes_cosecha.create');
    
    Route::prefix('cosecha')->name('cosecha.')->group(function () {
        Route::get('/sesiones/{sesion}/preparar-offline', [RecepcionCampoController::class, 'prepararOffline'])->name('preparar_offline');
        Route::post('/recepcion/store', [RecepcionCampoController::class, 'store'])->name('store');
    });

    Route::get('/recepcion/{sesionCosecha}/create', [RecepcionCampoController::class, 'create'])->name('cosecha.create');

    // Lotes GIS
    Route::resource('lotes-gis', LoteController::class)->except(['edit', 'update', 'destroy']);
    Route::post('/lotes-gis/{lote_gis}/zonas', [LoteController::class, 'storeZona'])->name('lotes-gis.zonas.store');
    Route::post('/lotes-gis/{lote_gis}/analiticas', [LoteController::class, 'storeAnalitica'])->name('lotes-gis.analiticas.store');
    Route::post('/lotes-gis/{lote_gis}/riegos', [LoteController::class, 'storeRiego'])->name('lotes-gis.riegos.store');

    // Árboles
    Route::resource('arboles', ArbolController::class)->parameters([
        'arboles' => 'arbol'
    ])->except(['edit', 'update', 'destroy']);
    Route::post('/arboles/{arbol}/metricas', [ArbolController::class, 'storeMetrica'])->name('arboles.metricas.store');
    Route::post('/arboles/{arbol}/fitosanitario', [ArbolController::class, 'storeFitosanitario'])->name('arboles.fitosanitario.store');
    
    // Grafos de Árboles
    Route::get('/grafo', [ArbolGrafoController::class, 'index'])->name('grafo.index');
    Route::get('/ciclos/{ciclo}/grafo', [ArbolApiController::class, 'index'])->name('grafo.ciclo');

    //IMPORTANTE
    Route::get('/arboles/grafo/dashboard', [ArbolGrafoController::class, 'dashboard'])->name('arboles.grafo.dashboard');
    Route::post('/arboles/grafo/simular', [ArbolGrafoController::class, 'simular'])->name('arboles.grafo.simular');
    // Eventos de Campo
    Route::get('/eventos-campo', [EventoController::class, 'index'])->name('eventos_campo.index');
    Route::get('/eventos-campo/nuevo-general', [EventoController::class, 'createGeneral'])->name('eventos_campo.create_general');
    Route::post('/eventos-campo/general', [EventoController::class, 'store'])->name('eventos_campo.store_general');
    Route::get('/eventos-campo/{evento}', [EventoController::class, 'show'])->name('eventos_campo.show');
    
    Route::get('/ciclos/{ciclo}/nuevo-evento', [EventoController::class, 'createConCultivo'])->name('eventos_campo.create_cultivo');
    Route::post('/ciclos/{ciclo}/evento', [EventoController::class, 'storeWithCultivo'])->name('eventos_campo.store_cultivo');


    Route::prefix('fenologia-etapa')->name('fenologia-etapa.')->group(function () {
        Route::get('/lista', [FenologiaEtapaController::class, 'index'])->name('index');
        Route::get('/crear', [FenologiaEtapaController::class, 'create'])->name('create');
        Route::post('/guardar', [FenologiaEtapaController::class, 'store'])->name('store');
        Route::get('/detalle/{id}', [FenologiaEtapaController::class, 'show'])->name('show');
        Route::get('/editar/{id}', [FenologiaEtapaController::class, 'edit'])->name('edit');
        Route::put('/actualizar/{id}', [FenologiaEtapaController::class, 'update'])->name('update');
        Route::delete('/eliminar/{id}', [FenologiaEtapaController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('ciclo-etapa-historial')->name('ciclo-etapas.')->group(function () {
        Route::get('/lista', [CicloEtapaHistorialController::class, 'index'])->name('index');
        Route::get('/crear', [CicloEtapaHistorialController::class, 'create'])->name('create');
        Route::post('/guardar', [CicloEtapaHistorialController::class, 'store'])->name('store');
        Route::get('/detalle/{id}', [CicloEtapaHistorialController::class, 'show'])->name('show');
        Route::get('/editar/{id}', [CicloEtapaHistorialController::class, 'edit'])->name('edit');
        Route::put('/actualizar/{id}', [CicloEtapaHistorialController::class, 'update'])->name('update');
        Route::delete('/eliminar/{id}', [CicloEtapaHistorialController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('fenologia-recomendacion')->name('fenologia-recomendacion.')->group(function () {
        Route::get('/lista', [FenologiaRecomendacionController::class, 'index'])->name('index');
        Route::get('/crear', [FenologiaRecomendacionController::class, 'create'])->name('create');
        Route::post('/guardar', [FenologiaRecomendacionController::class, 'store'])->name('store');
        Route::get('/detalle/{id}', [FenologiaRecomendacionController::class, 'show'])->name('show');
        Route::get('/editar/{id}', [FenologiaRecomendacionController::class, 'edit'])->name('edit');
        Route::put('/actualizar/{id}', [FenologiaRecomendacionController::class, 'update'])->name('update');
        Route::delete('/eliminar/{id}', [FenologiaRecomendacionController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('proveedor')->name('proveedor.')->group(function () {
        Route::get('/lista', [ProveedorController::class, 'index'])->name('index');
        Route::get('/crear', [ProveedorController::class, 'create'])->name('create');
        Route::post('/guardar', [ProveedorController::class, 'store'])->name('store');
        Route::get('/detalle/{id}', [ProveedorController::class, 'show'])->name('show');
        Route::get('/editar/{id}', [ProveedorController::class, 'edit'])->name('edit');
        Route::put('/actualizar/{id}', [ProveedorController::class, 'update'])->name('update');
        Route::delete('/eliminar/{id}', [ProveedorController::class, 'destroy'])->name('destroy');
    });
    Route::prefix('compras')->name('compras.')->group(function () {
        Route::get('/lista', [CompraController::class, 'index'])->name('index');
        Route::get('/crear', [CompraController::class, 'create'])->name('create');
        Route::post('/guardar', [CompraController::class, 'store'])->name('store');
        Route::get('/detalle/{id}', [CompraController::class, 'show'])->name('show');
        Route::get('/editar/{id}', [CompraController::class, 'edit'])->name('edit');
        Route::put('/actualizar/{id}', [CompraController::class, 'update'])->name('update');
        Route::delete('/eliminar/{id}', [CompraController::class, 'destroy'])->name('destroy');
    });

});