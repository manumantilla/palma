<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LotesZonasManejoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // Creamos dos zonas de manejo de ejemplo para el lote_id = 1
        // Utilizando polígonos alrededor de las coordenadas de tus árboles de Lulo (6.911088, -72.842190)
        
        $zonas = [
            [
                'lote_id'        => 1,
                'nombre_zona'    => 'Zona Norte - Alta Productividad',
                'codigo_zona'    => 'Z-NTE-01',
                'area_hectareas' => 2.5000,
                // Polígono en PostGIS: POLYGON((lon1 lat1, lon2 lat2, lon3 lat3, lon4 lat4, lon1 lat1))
                // Importante: El primer y último punto deben ser exactamente iguales para cerrar el polígono.
                'geometria_zona' => DB::raw("ST_GeomFromText('POLYGON((
                    -72.843000 6.913000, 
                    -72.841000 6.913000, 
                    -72.841000 6.911500, 
                    -72.843000 6.911500, 
                    -72.843000 6.913000
                ))', 4326)"),
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
            [
                'lote_id'        => 1,
                'nombre_zona'    => 'Zona Sur - Desarrollo',
                'codigo_zona'    => 'Z-SUR-02',
                'area_hectareas' => 1.8000,
                'geometria_zona' => DB::raw("ST_GeomFromText('POLYGON((
                    -72.843000 6.911499, 
                    -72.841000 6.911499, 
                    -72.841000 6.910000, 
                    -72.843000 6.910000, 
                    -72.843000 6.911499
                ))', 4326)"),
                'created_at'     => $now,
                'updated_at'     => $now,
            ]
        ];

        DB::table('lotes_zonas_manejo')->insert($zonas);
    }
}