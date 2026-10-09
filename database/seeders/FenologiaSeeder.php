<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Etapas fenológicas de referencia. Son valores agronómicos aproximados: ajustarlos con
 * el agrónomo o las guías de Cenipalma para la zona y el material. Los rangos min/max
 * existen para eso, y python_service los irá refinando con las duraciones reales.
 */
class FenologiaSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $palma = DB::table('cultivos')->where('nombre_cultivo', 'Palma de Aceite')->value('id');
        $maiz  = DB::table('cultivos')->where('nombre_cultivo', 'Maíz')->value('id');

        if (DB::table('fenologia_etapas')->whereIn('cultivo_id', [$palma, $maiz])->exists()) {
            $this->command?->info('La fenología de referencia ya está cargada; no se vuelve a sembrar.');
            return;
        }

        DB::table('materiales_geneticos')->insert([
            ['cultivo_id' => $palma, 'nombre' => 'E. guineensis Ténera (DxP)', 'tipo' => 'variedad', 'caracteristicas' => json_encode(['polinizacion' => 'natural']), 'created_at' => $now, 'updated_at' => $now],
            ['cultivo_id' => $palma, 'nombre' => 'Híbrido OxG',                'tipo' => 'hibrido',  'caracteristicas' => json_encode(['polinizacion' => 'asistida', 'tolerancia' => ['pudricion_cogollo']]), 'created_at' => $now, 'updated_at' => $now],
        ]);

        $base = fn (array $r) => $r + [
            'material_genetico_id' => null, 'etapa_padre_id' => null, 'descripcion' => null,
            'bbch_inicio' => null, 'bbch_fin' => null, 'duracion_dias_min' => null, 'duracion_dias_max' => null,
            'grados_dia_requeridos' => null, 'temperatura_base_c' => null, 'periodicidad_dias' => null,
            'mes_inicio_tipico' => null, 'es_cosechable' => false, 'es_critica' => false,
            'created_at' => $now, 'updated_at' => $now,
        ];

        // PALMA: fases de vida (desde germinación).
        // Si el ciclo arranca con plántula de vivero, el cronograma empieza en "Establecimiento".
        DB::table('fenologia_etapas')->insert(array_map($base, [
            ['cultivo_id' => $palma, 'tipo_fase' => 'vida', 'orden' => 1, 'nombre' => 'Previvero',                 'bbch_inicio' => 0,  'bbch_fin' => 12, 'duracion_dias_desde_inicio' => 0,    'duracion_dias_estimada' => 90,   'duracion_dias_min' => 75,  'duracion_dias_max' => 120],
            ['cultivo_id' => $palma, 'tipo_fase' => 'vida', 'orden' => 2, 'nombre' => 'Vivero',                    'bbch_inicio' => 13, 'bbch_fin' => 19, 'duracion_dias_desde_inicio' => 90,   'duracion_dias_estimada' => 270,  'duracion_dias_min' => 240, 'duracion_dias_max' => 330],
            ['cultivo_id' => $palma, 'tipo_fase' => 'vida', 'orden' => 3, 'nombre' => 'Establecimiento (inmadura)',                                    'duracion_dias_desde_inicio' => 360,  'duracion_dias_estimada' => 900,  'duracion_dias_min' => 720, 'duracion_dias_max' => 1080, 'es_critica' => true],
            ['cultivo_id' => $palma, 'tipo_fase' => 'vida', 'orden' => 4, 'nombre' => 'Producción ascendente',                                         'duracion_dias_desde_inicio' => 1260, 'duracion_dias_estimada' => 1825, 'es_cosechable' => true],
            ['cultivo_id' => $palma, 'tipo_fase' => 'vida', 'orden' => 5, 'nombre' => 'Producción estable',                                            'duracion_dias_desde_inicio' => 3085, 'duracion_dias_estimada' => 3650, 'es_cosechable' => true],
            ['cultivo_id' => $palma, 'tipo_fase' => 'vida', 'orden' => 6, 'nombre' => 'Producción declinante / renovación',                            'duracion_dias_desde_inicio' => 6735, 'duracion_dias_estimada' => 2555, 'es_cosechable' => true],
        ]));

        // PALMA: cohorte de racimo (se repite y se superpone).
        // Antesis -> cosecha ≈ 5-6 meses. Nueva cohorte ≈ cada 15 días en palma adulta.
        DB::table('fenologia_etapas')->insert(array_map($base, [
            ['cultivo_id' => $palma, 'tipo_fase' => 'repetitiva', 'orden' => 10, 'nombre' => 'Antesis',               'bbch_inicio' => 61, 'bbch_fin' => 69, 'duracion_dias_desde_inicio' => 0,   'duracion_dias_estimada' => 7,   'duracion_dias_min' => 3,   'duracion_dias_max' => 10,  'periodicidad_dias' => 15, 'es_critica' => true],
            ['cultivo_id' => $palma, 'tipo_fase' => 'repetitiva', 'orden' => 11, 'nombre' => 'Desarrollo del racimo', 'bbch_inicio' => 71, 'bbch_fin' => 79, 'duracion_dias_desde_inicio' => 7,   'duracion_dias_estimada' => 120, 'duracion_dias_min' => 100, 'duracion_dias_max' => 140, 'periodicidad_dias' => 15],
            ['cultivo_id' => $palma, 'tipo_fase' => 'repetitiva', 'orden' => 12, 'nombre' => 'Maduración',            'bbch_inicio' => 80, 'bbch_fin' => 88, 'duracion_dias_desde_inicio' => 127, 'duracion_dias_estimada' => 40,  'duracion_dias_min' => 30,  'duracion_dias_max' => 55,  'periodicidad_dias' => 15],
            ['cultivo_id' => $palma, 'tipo_fase' => 'repetitiva', 'orden' => 13, 'nombre' => 'Cosecha (ronda)',       'bbch_inicio' => 89, 'bbch_fin' => 89, 'duracion_dias_desde_inicio' => 167, 'duracion_dias_estimada' => 12,  'duracion_dias_min' => 8,   'duracion_dias_max' => 15,  'periodicidad_dias' => 15, 'es_cosechable' => true],
        ]));

        // MAÍZ: secuencial, tiempo térmico base 10 °C.
        // GDD de referencia para tierra caliente (~15 GD/día); en clima medio los días se alargan.
        DB::table('fenologia_etapas')->insert(array_map($base, [
            ['cultivo_id' => $maiz, 'tipo_fase' => 'secuencial', 'orden' => 1, 'nombre' => 'Siembra - emergencia', 'bbch_inicio' => 0,  'bbch_fin' => 9,  'duracion_dias_desde_inicio' => 0,   'duracion_dias_estimada' => 7,  'duracion_dias_min' => 5,  'duracion_dias_max' => 12, 'grados_dia_requeridos' => 105, 'temperatura_base_c' => 10],
            ['cultivo_id' => $maiz, 'tipo_fase' => 'secuencial', 'orden' => 2, 'nombre' => 'Vegetativo',           'bbch_inicio' => 10, 'bbch_fin' => 39, 'duracion_dias_desde_inicio' => 7,   'duracion_dias_estimada' => 45, 'duracion_dias_min' => 35, 'duracion_dias_max' => 60, 'grados_dia_requeridos' => 675, 'temperatura_base_c' => 10],
            ['cultivo_id' => $maiz, 'tipo_fase' => 'secuencial', 'orden' => 3, 'nombre' => 'Espigamiento',         'bbch_inicio' => 51, 'bbch_fin' => 59, 'duracion_dias_desde_inicio' => 52,  'duracion_dias_estimada' => 10, 'duracion_dias_min' => 7,  'duracion_dias_max' => 14, 'grados_dia_requeridos' => 150, 'temperatura_base_c' => 10],
            ['cultivo_id' => $maiz, 'tipo_fase' => 'secuencial', 'orden' => 4, 'nombre' => 'Floración',            'bbch_inicio' => 61, 'bbch_fin' => 69, 'duracion_dias_desde_inicio' => 62,  'duracion_dias_estimada' => 10, 'duracion_dias_min' => 7,  'duracion_dias_max' => 14, 'grados_dia_requeridos' => 150, 'temperatura_base_c' => 10, 'es_critica' => true],
            ['cultivo_id' => $maiz, 'tipo_fase' => 'secuencial', 'orden' => 5, 'nombre' => 'Llenado de grano',     'bbch_inicio' => 71, 'bbch_fin' => 79, 'duracion_dias_desde_inicio' => 72,  'duracion_dias_estimada' => 35, 'duracion_dias_min' => 28, 'duracion_dias_max' => 45, 'grados_dia_requeridos' => 525, 'temperatura_base_c' => 10, 'es_critica' => true],
            ['cultivo_id' => $maiz, 'tipo_fase' => 'secuencial', 'orden' => 6, 'nombre' => 'Maduración',           'bbch_inicio' => 81, 'bbch_fin' => 89, 'duracion_dias_desde_inicio' => 107, 'duracion_dias_estimada' => 20, 'duracion_dias_min' => 15, 'duracion_dias_max' => 30, 'grados_dia_requeridos' => 300, 'temperatura_base_c' => 10],
            ['cultivo_id' => $maiz, 'tipo_fase' => 'secuencial', 'orden' => 7, 'nombre' => 'Cosecha',              'bbch_inicio' => 99, 'bbch_fin' => 99, 'duracion_dias_desde_inicio' => 127, 'duracion_dias_estimada' => 7,  'duracion_dias_min' => 3,  'duracion_dias_max' => 15, 'es_cosechable' => true],
        ]));
    }
}
