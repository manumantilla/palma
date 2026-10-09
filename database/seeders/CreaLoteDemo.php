<?php

namespace Database\Seeders;

use App\Models\CicloProductivo;
use App\Models\Cultivo;
use App\Models\Finca;
use App\Models\Lote;
use MatanYadaev\EloquentSpatial\Objects\MultiPolygon;

/**
 * Lote y ciclo de demostración compartidos por los seeders de zonas y árboles.
 * Se crean solo si no existen, así los seeders funcionan sobre una BD recién migrada.
 */
trait CreaLoteDemo
{
    protected function loteDemo(): Lote
    {
        $finca = Finca::firstOrCreate(['nombre' => 'Finca El Manzano'], ['ubicacion' => 'Camara']);

        return Lote::firstOrCreate(['codigo_lote' => 'LT-DEMO-01'], [
            'finca_id' => $finca->id,
            'nombre_lote' => 'Lote Principal',
            'area_hectareas_declaradas' => 4.5,
            'altitud_mediana_msnm' => 1850,
            'pendiente_promedio_porcentaje' => 18,
            'pendiente_terreno' => 'escarpado',
            'tipo_suelo' => 'franco_arcilloso',
            'ph_suelo' => 5.6,
            // Mismo polígono irregular en ladera que usa ArbolesSeeder (lng lat)
            'geometria_gps' => MultiPolygon::fromWkt('MULTIPOLYGON(((
                -72.843100 6.912100, -72.842100 6.912250, -72.841400 6.912050, -72.841150 6.911500,
                -72.841300 6.910700, -72.841800 6.910050, -72.842500 6.909800, -72.843200 6.910000,
                -72.843500 6.910600, -72.843400 6.911400, -72.843100 6.912100)))', 4326),
        ]);
    }

    protected function cicloDemo(Lote $lote): CicloProductivo
    {
        $cultivo = Cultivo::firstOrCreate(
            ['nombre_cultivo' => 'Aguacate Hass'],
            ['tipo' => 'perenne', 'nombre_cientifico' => 'Persea americana', 'descripcion' => 'Aguacate Hass para exportación.']
        );

        return CicloProductivo::firstOrCreate(
            ['lote_id' => $lote->id, 'nombre_campana' => 'Aguacate Hass 2026'],
            [
                'cultivo_id' => $cultivo->id,
                'estado' => 'activo',
                'tipo' => 'perenne',
                'fecha_inicio' => now()->subMonths(6)->toDateString(),
                'fecha_estimada_cosecha' => now()->addYears(2)->toDateString(),
                'fecha_estimada_fin_cosecha' => now()->addYears(20)->toDateString(),
                'modalidad_siembra' => 'arbol_injertado',
                'arreglo_espacial' => 'rectangulo',
                'distancia_entre_hileras_metros' => 6,
                'distancia_entre_plantas_metros' => 5,
            ]
        );
    }
}
