<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrdenCosechaController;

    // Historial de recepciones
    Route::get('/recepciones', [RecepcionCampoController::class, 'index'])
        ->name('recepciones.index');

    // Obtener información de una sesión para comenzar la captura offline
    Route::get('/recepciones/create/{sesion_cosecha_id}', [RecepcionCampoController::class, 'create'])
        ->name('recepciones.create');

    // Sincronizar o guardar recepciones
    Route::post('/recepciones', [RecepcionCampoController::class, 'store'])
        ->name('recepciones.store');