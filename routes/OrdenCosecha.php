<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SesionCosechaController;

// Listado general de sesiones
Route::get('/sesiones-cosecha', [SesionCosechaController::class, 'index'])->name('sesiones_cosecha.index');

// Flujo de Apertura (Dependen de una Orden de Cosecha activa)
Route::get('/ordenes-cosecha/{ordenCosecha}/sesiones/create', [SesionCosechaController::class, 'create'])->name('sesiones_cosecha.create');
Route::post('/ordenes-cosecha/{ordenCosecha}/sesiones', [SesionCosechaController::class, 'store'])->name('sesiones_cosecha.store');

// Flujo de gestión individual (Operan directo sobre el UUID de la sesión)
Route::get('/sesiones-cosecha/{sesionCosecha}', [SesionCosechaController::class, 'show'])->name('sesiones_cosecha.show');
Route::get('/sesiones-cosecha/{sesionCosecha}/edit', [SesionCosechaController::class, 'edit'])->name('sesiones_cosecha.edit');
Route::put('/sesiones-cosecha/{sesionCosecha}', [SesionCosechaController::class, 'update'])->name('sesiones_cosecha.update');
Route::delete('/sesiones-cosecha/{sesionCosecha}', [SesionCosechaController::class, 'destroy'])->name('sesiones_cosecha.destroy');