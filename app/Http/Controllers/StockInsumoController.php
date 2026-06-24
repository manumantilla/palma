<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockInsumo;

class StockInsumoController extends Controller
{
    //
    public function index(Request $request)
    {
        $query = StockInsumo::with('insumo');

        if($request->filled('insumo_nombre')){
            $query->where('insumo_nombre','like','%'.$request->insumo_nombre.'%');
        }

        


    }

    public function store(Request $request, Insumo $insumo)
    {
        $request->validate([
            'cantidad_disponible' => 'decimal',
        ]);

        //Registrar en insumo_stock 
        StockInsumo::create([
            ''
        ]);


        //Registrar movimiento entrada en movimientos_stock
    }
}
