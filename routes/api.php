<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArbolApiController;
use App\Http\Controllers\ArbolGrafoController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/grafo/geojson/{ciclo_productivo_id}', [ArbolApiController::class, 'getGrafoGeoJson'])
    ->name('grafo.geojson');

    // NUEVAS RUTAS

Route::prefix('grafo')->group(function () {
    Route::get('/nodos', [ArbolGrafoController::class, 'nodos']);
    Route::get('/clusters', [ArbolGrafoController::class, 'clusters']);
    Route::get('/arboles/{id}', [ArbolGrafoController::class, 'detalle']);
});
// routes/api.php
Route::get('/grafo/extent', [ArbolGrafoController::class, 'extent']);