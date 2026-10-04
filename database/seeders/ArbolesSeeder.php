<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use App\Models\Lote;
use App\Models\CicloProductivo;

class ArbolesSeeder extends Seeder
{
    public function run()
    {
        // Asegurar que existan lote y ciclo_productivo con ID = 1
        $lote = Lote::firstOrCreate(
            ['id' => 1],
            ['nombre' => 'Lote Principal', 'descripcion' => 'Lote creado por seeder']
        );
        $ciclo = CicloProductivo::firstOrCreate(
            ['id' => 1],
            ['nombre' => 'Ciclo 2026', 'fecha_inicio' => now()->subMonths(6), 'lote_id' => $lote->id]
        );

        $faker = Faker::create('es_PE');

        // Configuración de la cuadrícula
        $rows = 12;
        $cols = 20;
        $total = $rows * $cols; // 1000
        $spacing_m = 3.0; // metros entre árboles
        // Aproximación: 1 grado ≈ 111320 m (en el ecuador), ajustamos para latitud -12°
        $lat_deg_per_m = 1 / 111320;
        $lng_deg_per_m = 1 / (111320 * cos(deg2rad(-12.0)));
        $spacing_deg_lat = $spacing_m * $lat_deg_per_m;
        $spacing_deg_lng = $spacing_m * $lng_deg_per_m;

        $base_lat = -12.0;   // latitud inicial
        $base_lng = -77.0;   // longitud inicial

        $treeData = [];

        DB::beginTransaction();

        try {
            // Insertar árboles
            for ($row = 1; $row <= $rows; $row++) {
                for ($col = 1; $col <= $cols; $col++) {
                    $lat = $base_lat + ($row - 1) * $spacing_deg_lat;
                    $lng = $base_lng + ($col - 1) * $spacing_deg_lng;
                    $codigo = 'A-' . str_pad($row, 3, '0', STR_PAD_LEFT) . '-' . str_pad($col, 3, '0', STR_PAD_LEFT);

                    // Atributos aleatorios
                    $estado_vital = $faker->randomElement(['excelente', 'con_estres', 'enfermo_critico', 'muerto', 'erradicado']);
                    $etapa = $faker->randomElement(['vivero', 'establecimiento', 'desarrollo_inmaduro', 'produccion_madura', 'senescencia']);
                    $variedad = $faker->randomElement(['Hass', 'Fuerte', 'Bacon', 'Zutano', 'Ettinger']);
                    $fecha_siembra = $faker->dateTimeBetween('-5 years', '-1 month')->format('Y-m-d');
                    
                    // CORRECCIÓN: manejar null de optional()
                    $fecha_primera_cosecha_obj = $faker->optional(0.7)->dateTimeBetween($fecha_siembra, 'now');
                    $fecha_primera_cosecha = $fecha_primera_cosecha_obj ? $fecha_primera_cosecha_obj->format('Y-m-d') : null;
                    
                    $altitud = $faker->randomFloat(2, 800, 2500);
                    $altitud_ortometrica = $faker->optional(0.8)->randomFloat(2, 800, 2500);

                    $treeData[] = [
                        'ciclo_productivo_id' => $ciclo->id,
                        'lote_id' => $lote->id,
                        'lote_zona_manejo_id' => null,
                        'codigo_unico' => $codigo,
                        'fila_indice' => $row,
                        'posicion_indice' => $col,
                        'altitud' => $altitud,
                        'estado_vital' => $estado_vital,
                        'etapa_biologica' => $etapa,
                        'fecha_baja_muerte' => ($estado_vital == 'muerto' || $estado_vital == 'erradicado') ? $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d') : null,
                        'motivo_baja' => null,
                        'coordenada_precision' => DB::raw("ST_GeomFromText('POINT($lng $lat)', 4326)"),
                        'altitud_ortometrica_msnm' => $altitud_ortometrica,
                        'fecha_siembra' => $fecha_siembra,
                        'fecha_primera_cosecha' => $fecha_primera_cosecha,
                        'variedad' => $variedad,
                        'fecha_muerte' => null,
                        'causa_muerte' => null,
                        'es_reemplazo' => $faker->boolean(5),
                        'fecha_reemplazo' => null,
                        'produccion_acumulada_kg' => $faker->randomFloat(2, 0, 500),
                        'ciclos_productivos_count' => $faker->numberBetween(0, 10),
                        'observaciones' => $faker->optional(0.3)->sentence,
                        'created_at' => now(),
                        'updated_at' => now(),
                        'deleted_at' => null,
                    ];
                }
            }

            // Insertar en lote usando chunks (evitar tamaño enorme)
            $chunks = array_chunk($treeData, 200);
            foreach ($chunks as $chunk) {
                DB::table('arboles')->insert($chunk);
            }

            // Obtener los IDs y coordenadas de los árboles recién insertados
            $arboles = DB::table('arboles')
                ->where('lote_id', $lote->id)
                ->where('ciclo_productivo_id', $ciclo->id)
                ->get(['id', 'fila_indice', 'posicion_indice']);

            // Mapear posición -> id
            $posToId = [];
            foreach ($arboles as $arbol) {
                $posToId[$arbol->fila_indice . ',' . $arbol->posicion_indice] = $arbol->id;
            }

            // Crear vecindades (8 direcciones)
            $neighbors = [];
            $directions = [
                [0, 1],  // derecha
                [1, 0],  // abajo
                [1, 1],  // diagonal abajo-derecha
                [1, -1], // diagonal abajo-izquierda
                [0, -1], // izquierda (opcional, para evitar duplicados, solo usamos derecha y abajo)
                [-1, 0], // arriba (evitar duplicados)
                [-1, 1], // arriba-derecha
                [-1, -1],// arriba-izquierda
            ];

            // Para evitar duplicados, solo conectamos con vecinos que tengan fila >= actual y (si misma fila, col > actual)
            // Pero es más fácil procesar todas las direcciones y luego evitar duplicados con array_unique, pero con 1000 árboles y 8 direcciones es pequeño.
            // Usamos un set para evitar duplicados (origen < destino o similar). Asegura que solo haya un registro por par.
            $edgeSet = [];

            foreach ($arboles as $arbol) {
                $row = $arbol->fila_indice;
                $col = $arbol->posicion_indice;
                $idOrigen = $arbol->id;

                foreach ($directions as $d) {
                    $dr = $d[0];
                    $dc = $d[1];
                    $nr = $row + $dr;
                    $nc = $col + $dc;
                    if ($nr < 1 || $nr > $rows || $nc < 1 || $nc > $cols) continue;

                    $key = $posToId[$nr . ',' . $nc] ?? null;
                    if (!$key) continue;

                    $idDestino = $key;
                    // Evitar duplicados: guardar siempre (min, max) para tener un par único
                    $pair = $idOrigen < $idDestino ? [$idOrigen, $idDestino] : [$idDestino, $idOrigen];
                    $pairKey = $pair[0] . '-' . $pair[1];
                    if (isset($edgeSet[$pairKey])) continue;
                    $edgeSet[$pairKey] = true;

                    // Calcular distancia en metros
                    $dist = sqrt(pow($dr * $spacing_m, 2) + pow($dc * $spacing_m, 2));
                    // Asignar tipo de contacto según dirección
                    if ($dr == 0 && $dc != 0) {
                        $tipo = 'misma_fila';
                    } elseif ($dr != 0 && $dc == 0) {
                        $tipo = 'fila_contigua';
                    } else {
                        $tipo = $faker->randomElement(['viento_predominante', 'mecanico_herramienta']);
                    }

                    $neighbors[] = [
                        'arbol_origen_id' => $idOrigen,
                        'arbol_destino_id' => $idDestino,
                        'distancia_metros' => round($dist, 2),
                        'probabilidad_contagio_base' => $faker->randomFloat(4, 0.01, 0.2),
                        'tipo_contacto' => $tipo,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            // Insertar vecindades en chunks
            $neighborChunks = array_chunk($neighbors, 500);
            foreach ($neighborChunks as $chunk) {
                DB::table('arboles_red_vecindad')->insert($chunk);
            }

            DB::commit();

            $this->command->info("Se crearon {$total} árboles y " . count($neighbors) . " relaciones de vecindad.");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error: ' . $e->getMessage());
        }
    }
}