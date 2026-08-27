<?php

use Illuminate\Support\Facades\Route;

// --- IMPORTACIONES DE CONTROLADORES ---
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ArbolApiController;
use App\Http\Controllers\CultivoController;
use App\Http\Controllers\RecepcionCampoController;
use App\Http\Controllers\SesionCosechaController;
use App\Http\Controllers\FenologiaEtapaController;
use App\Http\Controllers\TipoEventoController;
use App\Http\Controllers\TrabajadorController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\GastoController;
use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\CicloProductivoController;
use App\Http\Controllers\CategoriaInsumoController;
use App\Http\Controllers\InsumoController;
use App\Http\Controllers\LoteInsumoController;
use App\Http\Controllers\LoteController;
use App\Http\Controllers\OrdenCosechaController;
use App\Http\Controllers\ArbolController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\ArbolGrafoController;

use App\Http\Controllers\LoteZonaManejoController;
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
    Route::resource('fenologia-etapas', FenologiaEtapaController::class)->except(['index', 'show']);
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

    
    Route::resource('tipos-evento', TipoEventoController::class);
    
    // Insumos
    Route::resource('categorias-insumo', CategoriaInsumoController::class);
    Route::resource('insumos', InsumoController::class);
    Route::resource('lotes-insumo', LoteInsumoController::class);
    Route::resource('lote-zonas-manejo', LoteZonaManejoController::class);
    // Gastos
    Route::resource('gastos', GastoController::class)->except(['create', 'show']);

    // Cosecha
    Route::resource('sesiones-cosecha', SesionCosechaController::class);
    Route::resource('ordenes_cosecha', OrdenCosechaController::class)->except('create');    
    Route::get('/ordenes-cosecha/crear/{ciclo}', [OrdenCosechaController::class, 'create'])->name('ordenes_cosecha.create');
    
    Route::prefix('cosecha')->name('cosecha.')->group(function () {
        Route::get('/sesiones/{sesion}/preparar-offline', [RecepcionCampoController::class, 'prepararOffline'])->name('preparar_offline');
        Route::post('/recepcion/store', [RecepcionCampoController::class, 'store'])->name('recepcion.store');
    });

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

    // Eventos de Campo
    Route::get('/eventos-campo', [EventoController::class, 'index'])->name('eventos_campo.index');
    Route::get('/eventos-campo/nuevo-general', [EventoController::class, 'createGeneral'])->name('eventos_campo.create_general');
    Route::post('/eventos-campo/general', [EventoController::class, 'store'])->name('eventos_campo.store_general');
    Route::get('/eventos-campo/{evento}', [EventoController::class, 'show'])->name('eventos_campo.show');
    
    Route::get('/ciclos/{ciclo}/nuevo-evento', [EventoController::class, 'createConCultivo'])->name('eventos_campo.create_cultivo');
    Route::post('/ciclos/{ciclo}/evento', [EventoController::class, 'storeWithCultivo'])->name('eventos_campo.store_cultivo');
});