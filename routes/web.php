<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArbolApiController;

Route::get('/', function () {
    return view('welcome');
});



    Route::get('/ciclos/{ciclo}/grafo', [ArbolApiController::class, 'index'])->name('grafo');

use App\Http\Controllers\CultivoController;
Route::resource('cultivos', CultivoController::class);

use App\Http\Controllers\SesionCosechaController;
Route::resource('sesiones-cosecha', SesionCosechaController::class);

Route::get('/ciclos/{ciclo}/grafo', [ArbolApiController::class, 'index'])->name('grafo');
use App\Http\Controllers\FenologiaEtapaController;
Route::resource('fenologia-etapas', FenologiaEtapaController::class)->except(['index', 'show']);

use App\Http\Controllers\TipoEventoController;

// --- RUTAS DE TIPOS DE EVENTO ---
Route::get('/tipos-evento', [TipoEventoController::class, 'index'])->name('tipos-evento.index');
Route::get('/tipos-evento/crear', [TipoEventoController::class, 'create'])->name('tipos-evento.create');
Route::post('/tipos-evento', [TipoEventoController::class, 'store'])->name('tipos-evento.store');
Route::get('/tipos-evento/{tipoEvento}/editar', [TipoEventoController::class, 'edit'])->name('tipos-evento.edit');
Route::put('/tipos-evento/{tipoEvento}', [TipoEventoController::class, 'update'])->name('tipos-evento.update');
Route::delete('/tipos-evento/{tipoEvento}', [TipoEventoController::class, 'destroy'])->name('tipos-evento.destroy');

use App\Http\Controllers\TrabajadorController;

// --- RUTAS DE TRABAJADORES ---
Route::get('/trabajadores', [TrabajadorController::class, 'index'])->name('trabajadores.index');
Route::get('/trabajadores/crear', [TrabajadorController::class, 'create'])->name('trabajadores.create');
Route::post('/trabajadores', [TrabajadorController::class, 'store'])->name('trabajadores.store');
Route::get('/trabajadores/{trabajador}', [TrabajadorController::class, 'show'])->name('trabajadores.show');
Route::get('/trabajadores/{trabajador}/editar', [TrabajadorController::class, 'edit'])->name('trabajadores.edit');
Route::put('/trabajadores/{trabajador}', [TrabajadorController::class, 'update'])->name('trabajadores.update');
Route::delete('/trabajadores/{trabajador}', [TrabajadorController::class, 'destroy'])->name('trabajadores.destroy');

use App\Http\Controllers\ClienteController;

// --- RUTAS DE CLIENTES ---
Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
Route::get('/clientes/crear', [ClienteController::class, 'create'])->name('clientes.create');
Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');
Route::get('/clientes/{cliente}', [ClienteController::class, 'show'])->name('clientes.show');
Route::get('/clientes/{cliente}/editar', [ClienteController::class, 'edit'])->name('clientes.edit');
Route::put('/clientes/{cliente}', [ClienteController::class, 'update'])->name('clientes.update');
Route::delete('/clientes/{cliente}', [ClienteController::class, 'destroy'])->name('clientes.destroy');

use App\Http\Controllers\GastoController;

// --- RUTAS DE GASTOS POLIMÓRFICOS ---
Route::get('/gastos', [GastoController::class, 'index'])->name('gastos.index');
Route::post('/gastos', [GastoController::class, 'store'])->name('gastos.store');
Route::get('/gastos/{gasto}/editar', [GastoController::class, 'edit'])->name('gastos.edit');
Route::put('/gastos/{gasto}', [GastoController::class, 'update'])->name('gastos.update');
Route::delete('/gastos/{gasto}', [GastoController::class, 'destroy'])->name('gastos.destroy');

use App\Http\Controllers\BitacoraController;
Route::resource('bitacoras', BitacoraController::class);

use App\Http\Controllers\CicloProductivoController;

Route::get('/ciclos-productivos', [CicloProductivoController::class, 'index'])->name('ciclos-productivos.index');
Route::get('/ciclos-productivos/crear', [CicloProductivoController::class, 'create'])->name('ciclos-productivos.create');
Route::post('/ciclos-productivos', [CicloProductivoController::class, 'store'])->name('ciclos-productivos.store');
Route::get('/ciclos-productivos/{cicloProductivo}', [CicloProductivoController::class, 'show'])->name('ciclos-productivos.show');
Route::put('/ciclos-productivos/{cicloProductivo}', [CicloProductivoController::class, 'update'])->name('ciclos-productivos.update');

use App\Http\Controllers\CategoriaInsumoController;
Route::resource('categorias-insumo', CategoriaInsumoController::class);

use App\Http\Controllers\InsumoController;
Route::resource('insumos', InsumoController::class);

use App\Http\Controllers\LoteInsumoController;
Route::resource('lotes-insumo', LoteInsumoController::class);

use App\Http\Controllers\LoteController;

use App\Http\Controllers\OrdenCosechaController;
Route::resource('ordenes_cosecha', OrdenCosechaController::class)->except('create');
Route::get('/ordenes-cosecha/crear/{lote}', [OrdenCosechaController::class, 'create'])->name('ordenes_cosecha.create');  


Route::middleware(['auth:sanctum', config('jetstream::auth_session'), 'verified'])->group(function () {
    // CRUD Principal del Lote Maestro
    Route::get('/lotes-gis', [LoteController::class, 'index'])->name('lotes-gis.index');
    Route::get('/lotes-gis/crear', [LoteController::class, 'create'])->name('lotes-gis.create');
    Route::post('/lotes-gis', [LoteController::class, 'store'])->name('lotes-gis.store');
    Route::get('/lotes-gis/{lote_gis}', [LoteController::class, 'show'])->name('lotes-gis.show');

    // Rutas Scoped hijas para agregar sub-registros desde el Show Dashboard
    Route::post('/lotes-gis/{lote_gis}/zonas', [LoteController::class, 'storeZona'])->name('lotes-gis.zonas.store');
    Route::post('/lotes-gis/{lote_gis}/analiticas', [LoteController::class, 'storeAnalitica'])->name('lotes-gis.analiticas.store');
    Route::post('/lotes-gis/{lote_gis}/riegos', [LoteController::class, 'storeRiego'])->name('lotes-gis.riegos.store');
});
use App\Http\Controllers\ArbolController;

Route::middleware(['auth:sanctum', config('jetstream::auth_session'), 'verified'])->group(function () {
    
    // CRUD Maestro de Árboles (Index incluye la lógica de la barra de filtros)
    Route::get('/arboles', [ArbolController::class, 'index'])->name('arboles.index');
    Route::get('/arboles/crear', [ArbolController::class, 'create'])->name('arboles.create');
    Route::post('/arboles', [ArbolController::class, 'store'])->name('arboles.store');
    Route::get('/arboles/{arbol}', [ArbolController::class, 'show'])->name('arboles.show');

    // Rutas Hijas (Scoped) gatilladas desde modales o pestañas en el dashboard del árbol
    Route::post('/arboles/{arbol}/metricas', [ArbolController::class, 'storeMetrica'])->name('arboles.metricas.store');
    Route::post('/arboles/{arbol}/fitosanitario', [ArbolController::class, 'storeFitosanitario'])->name('arboles.fitosanitario.store');
});

use App\Http\Controllers\EventoController;

// Rutas para Evento General (Sin Cultivo)
Route::get('/eventos-campo/nuevo-general', [EventoController::class, 'createGeneral'])->name('eventos_campo.create_general');
Route::post('/eventos-campo/general', [EventoController::class, 'store'])->name('eventos_campo.store_general');
Route::get('/eventos-campo', [EventoController::class, 'index'])->name('eventos_campo.index');
// Rutas para Evento con Cultivo (Pasando el ID del ciclo en la URL)
Route::get('/ciclos/{ciclo}/nuevo-evento', [EventoController::class, 'createConCultivo'])->name('eventos_campo.create_cultivo');
Route::post('/ciclos/{ciclo}/evento', [EventoController::class, 'storeWithCultivo'])->name('eventos_campo.store_cultivo');



use App\Http\Controllers\ArbolGrafoController;

Route::get('/grafo', [ArbolGrafoController::class, 'index'])->name('grafo.index');


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
