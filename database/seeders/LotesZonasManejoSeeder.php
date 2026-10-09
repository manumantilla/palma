<?php

namespace Database\Seeders;

use App\Models\LoteZonaManejo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LotesZonasManejoSeeder extends Seeder
{
    use CreaLoteDemo;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $loteId = $this->loteDemo()->id;

        // Dos zonas de manejo de ejemplo dentro del lote demo.
        // POLYGON((lon lat, ...)): el primer y último punto deben ser iguales para cerrar el polígono.
        $zonas = [
            'Z-NTE-01' => [
                'nombre_zona'    => 'Zona Norte - Alta Productividad',
                'area_hectareas' => 2.5000,
                'geometria_zona' => DB::raw("ST_GeomFromText('POLYGON((
                    -72.843000 6.913000,
                    -72.841000 6.913000,
                    -72.841000 6.911500,
                    -72.843000 6.911500,
                    -72.843000 6.913000
                ))', 4326)"),
            ],
            'Z-SUR-02' => [
                'nombre_zona'    => 'Zona Sur - Desarrollo',
                'area_hectareas' => 1.8000,
                'geometria_zona' => DB::raw("ST_GeomFromText('POLYGON((
                    -72.843000 6.911499,
                    -72.841000 6.911499,
                    -72.841000 6.910000,
                    -72.843000 6.910000,
                    -72.843000 6.911499
                ))', 4326)"),
            ],
        ];

        foreach ($zonas as $codigo => $datos) {
            LoteZonaManejo::firstOrCreate(['codigo_zona' => $codigo], $datos + ['lote_id' => $loteId]);
        }
    }
}
