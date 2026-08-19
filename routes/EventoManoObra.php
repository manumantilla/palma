<?php
use App\Http\Controllers\EventoManoObraController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth'])->prefix('mano-obra')->name('mano-obra.')->group(function () {

    Route::get('/', [EventoManoObraController::class, 'index'])
        ->name('index');
    Route::get('/crear', [EventoManoObraController::class, 'create'])
        ->name('create');
    Route::post('/', [EventoManoObraController::class, 'store'])
        ->name('store');
    Route::get('/{id}', [EventoManoObraController::class, 'show'])
        ->whereNumber('id')
        ->name('show');
    Route::get('/{id}/editar', [EventoManoObraController::class, 'edit'])
        ->whereNumber('id')
        ->name('edit');
    Route::put('/{id}', [EventoManoObraController::class, 'update'])
        ->whereNumber('id')
        ->name('update');
    Route::delete('/{id}', [EventoManoObraController::class, 'destroy'])
        ->whereNumber('id')
        ->name('destroy');

    // 8. LIQUIDAR PAGO SEMANAL (Sábados de Pago)
    // Procesa la liquidación masiva de un trabajador por su Cédula cambiando sus registros a 'pagado'.
    Route::post('/liquidar-semana', [EventoManoObraController::class, 'liquidarPagoSemanal'])
        ->name('liquidar-semana');

});