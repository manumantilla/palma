<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ArbolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $arboles = [];
        
        // Coordenadas base proporcionadas
        $baseLat = 6.911088;
        $baseLon = -72.842190;
        
        // Aproximadamente 3 metros de distancia entre plantas en grados decimales
        $espaciado = 0.000027; 
        
        $totalArboles = 3000;
        $filas = 60; // 60 filas
        $columnas = 50; // 50 árboles por fila = 3000 árboles
        
        $contador = 0;
        $fechaSiembra = Carbon::now()->subMonths(6)->format('Y-m-d');
        $now = Carbon::now();

        for ($f = 1; $f <= $filas; $f++) {
            for ($p = 1; $p <= $columnas; $p++) {
                if ($contador >= $totalArboles) {
                    break 2;
                }

                // Generar un patrón de cuadrícula simulando la plantación
                $lat = $baseLat + ($f * $espaciado);
                $lon = $baseLon + ($p * $espaciado);
                
                // PostGIS estándar: ST_GeomFromText('POINT(Longitud Latitud)', SRID)
                $puntoGeom = DB::raw("ST_GeomFromText('POINT({$lon} {$lat})', 4326)");
                
                // Altitud aleatoria razonable para clima de Lulo (ej. 1800 - 2000 msnm)
                $altitud = rand(1800, 2000) + (rand(0, 99) / 100);

                $arboles[] = [
                    'ciclo_productivo_id'      => 1,
                    'lote_id'                  => 1,
                    'lote_zona_manejo_id'      => 1,
                    'codigo_unico'             => "LULO-F" . str_pad($f, 2, '0', STR_PAD_LEFT) . "-P" . str_pad($p, 2, '0', STR_PAD_LEFT),
                    'fila_indice'              => $f,
                    'posicion_indice'          => $p,
                    'altitud'                  => $altitud,
                    'estado_vital'             => 'excelente',
                    'etapa_biologica'          => 'establecimiento',
                    'fecha_baja_muerte'        => null,
                    'motivo_baja'              => null,
                    'coordenada_precision'     => $puntoGeom,
                    'altitud_ortometrica_msnm' => $altitud,
                    'fecha_siembra'            => $fechaSiembra,
                    'fecha_primera_cosecha'    => null,
                    'variedad'                 => 'Lulo de Castilla',
                    'fecha_muerte'             => null,
                    'causa_muerte'             => null,
                    'es_reemplazo'             => false,
                    'fecha_reemplazo'          => null,
                    'produccion_acumulada_kg'  => 0,
                    'ciclos_productivos_count' => 0,
                    'observaciones'            => 'Siembra inicial masiva automatizada',
                    'created_at'               => $now,
                    'updated_at'               => $now,
                ];

                $contador++;
            }
        }

        // Insertar en bloques (chunks) de 500 registros para optimizar memoria y rendimiento en la BD
        $chunks = array_chunk($arboles, 500);
        foreach ($chunks as $chunk) {
            DB::table('arboles')->insert($chunk);
        }
    }
}