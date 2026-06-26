<?php

namespace App\Http\Controllers;

use App\Models\SesionCosecha;
use App\Models\User;
use Illuminate\Http\Request;

class SesionCosechaController extends Controller
{
    /**
     * Listar las sesiones de cosecha con filtros aplicados.
     */
    public function index(Request $request)
    {
        // 1. Recorremos el query string y aplicamos el scope, cargando relaciones para evitar consultas N+1
        $sesiones = SesionCosecha::with(['ordenCosecha', 'eventoCampo', 'responsable'])
            ->filter($request->all()) 
            ->orderBy('fecha', 'desc')
            ->paginate(15)
            ->withQueryString(); // Importante para que la paginación no pierda los filtros aplicados

        // 2. Opcional: Catálogos para llenar los select de filtros en la vista de búsqueda
        $responsables = User::orderBy('name')->get();

        return view('sesiones_cosecha.index', compact('sesiones', 'responsables'));
    }
}