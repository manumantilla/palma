<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CicloProductivo;
class ArbolApiController extends Controller
{
    public function getGrafoGeoJson(Request $request, $ciclo_productivo_id)
    {
        try {

            $swLat = $request->input('sw_lat', -90);
            $swLng = $request->input('sw_lng', -180);
            $neLat = $request->input('ne_lat', 90);
            $neLng = $request->input('ne_lng', 180);
            $filtro = $request->input('filtro', 'todos');

            $query = DB::table('arboles')
                ->where('ciclo_productivo_id', $ciclo_productivo_id)
                ->selectRaw("
                    id,
                    codigo_unico,
                    estado_vital,
                    fila_indice,
                    posicion_indice,
                    ST_X(coordenada_precision) as lng,
                    ST_Y(coordenada_precision) as lat
                ");

            if ($filtro === 'enfermos') {
                $query->whereIn('estado_vital', [
                    'enfermo_critico',
                    'muerto'
                ]);
            }

            if ($filtro === 'estres') {
                $query->where(
                    'estado_vital',
                    'con_estres'
                );
            }

            if ($filtro === 'sanos') {
                $query->whereIn(
                    'estado_vital',
                    ['sano','excelente']
                );
            }

            $arboles = $query->get();

            return response()->json([
                'type' => 'FeatureCollection',
                'features' => $arboles->map(function ($arbol) {

                    return [
                        'type'=>'Feature',
                        'geometry'=>[
                            'type'=>'Point',
                            'coordinates'=>[
                                (float)$arbol->lng,
                                (float)$arbol->lat
                            ]
                        ],
                        'properties'=>[
                            'id'=>$arbol->id,
                            'codigo'=>$arbol->codigo_unico,
                            'estado'=>$arbol->estado_vital,
                            'fila_pos'=>"F{$arbol->fila_indice}-P{$arbol->posicion_indice}",
                        ]
                    ];

                })
            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'error'=>$e->getMessage(),
                'line'=>$e->getLine(),
                'file'=>$e->getFile()
            ],500);

        }
    }

    public function index(CicloProductivo $ciclo)
    {
        
        return view('grafo.index', compact('ciclo'));
    }
}