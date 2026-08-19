<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MovimientoClasificacionController;
Route::middleware(['auth'])->group(function () {

    Route::resource('movimientos', MovimientoClasificacionController::class)
         ->parameters(['movimientos' => 'id']); 
    
});