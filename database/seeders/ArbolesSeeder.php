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

    // ================================================================
    // 1) CONFIGURACIÓN AGRONÓMICA
    // ================================================================
    // Aguacate Hass en ladera (Santander, Colombia): marco 6 x 5 m
    $spacing_row_m = 6.0;   // entre filas (curvas de nivel)
    $spacing_col_m = 5.0;   // entre plantas dentro de la fila

    // Centro de referencia geográfico del lote
    $centro_lat = 6.910907;
    $centro_lng = -72.842402;

    // Conversión grados ↔ metros (a ~6.91° de latitud)
    $lat_deg_per_m = 1.0 / 111320.0;
    $lng_deg_per_m = 1.0 / (111320.0 * cos(deg2rad($centro_lat)));

    // ================================================================
    // 2) POLÍGONO IRREGULAR DEL TERRENO (~4-5 ha)
    //    Vértices [lat, lng] en sentido horario.
    //    Esquina NE cortada y borde sur curvo (lote real en ladera).
    // ================================================================
    $polygon = [
        [6.912100, -72.843100],   // NO
        [6.912250, -72.842100],   // N
        [6.912050, -72.841400],   // NE (esquina cortada)
        [6.911500, -72.841150],   // E
        [6.910700, -72.841300],   // E-SE
        [6.910050, -72.841800],   // SE
        [6.909800, -72.842500],   // S
        [6.910000, -72.843200],   // SO
        [6.910600, -72.843500],   // O
        [6.911400, -72.843400],   // NO
    ];

    // Ray casting point-in-polygon
    $pointInPolygon = function ($lat, $lng, array $poly): bool {
        $inside = false;
        $n = count($poly);
        for ($i = 0, $j = $n - 1; $i < $n; $j = $i++) {
            $xi = $poly[$i][1]; $yi = $poly[$i][0];
            $xj = $poly[$j][1]; $yj = $poly[$j][0];
            $intersect = (($yi > $lat) !== ($yj > $lat))
                && ($lng < ($xj - $xi) * ($lat - $yi) / (($yj - $yi) ?: 1e-12) + $xi);
            if ($intersect) {
                $inside = !$inside;
            }
        }
        return $inside;
    };

    // Bounding box del polígono
    $lats = array_column($polygon, 0);
    $lngs = array_column($polygon, 1);
    $minLat = min($lats); $maxLat = max($lats);
    $minLng = min($lngs); $maxLng = max($lngs);

    $spacing_deg_lat = $spacing_row_m * $lat_deg_per_m;
    $spacing_deg_lng = $spacing_col_m * $lng_deg_per_m;

    // ================================================================
    // 3) GENERACIÓN DE POSICIONES CON DISTANCIAS CASI HOMOGÉNEAS
    //    Y JITTER REALISTA
    // ================================================================
    $positions = [];
    $rowIdx = 0;

    for ($lat = $maxLat; $lat >= $minLat; $lat -= $spacing_deg_lat) {
        $rowIdx++;

        // Variación global de la fila: ±1.5 m (curvas de nivel no perfectas)
        $row_shift_m = $faker->randomFloat(3, -1.5, 1.5);
        $row_lat = $lat + $row_shift_m * $lat_deg_per_m;

        // Desplazamiento lateral de la fila (tresbolillo irregular)
        $lateral_offset = $faker->randomFloat(3, -1.0, 1.0) * $lng_deg_per_m;

        $colIdx = 0;
        for ($lng = $minLng + $lateral_offset; $lng <= $maxLng; $lng += $spacing_deg_lng) {
            $colIdx++;

            // Jitter agronómico base: ±0.7 m (replanteo GPS real)
            $jitter_lat = $faker->randomFloat(6, -0.7, 0.7) * $lat_deg_per_m;
            $jitter_lng = $faker->randomFloat(6, -0.7, 0.7) * $lng_deg_per_m;

            // 8% de árboles con desviación mayor (rocas, resiembra, huecos)
            if ($faker->boolean(8)) {
                $jitter_lat += $faker->randomFloat(6, -1.8, 1.8) * $lat_deg_per_m;
                $jitter_lng += $faker->randomFloat(6, -1.8, 1.8) * $lng_deg_per_m;
            }

            $pos_lat = $row_lat + $jitter_lat;
            $pos_lng = $lng + $jitter_lng;

            // Solo conservar los árboles dentro del polígono
            if (!$pointInPolygon($pos_lat, $pos_lng, $polygon)) {
                continue;
            }

            $positions[] = [
                'lat'  => $pos_lat,
                'lng'  => $pos_lng,
                'fila' => $rowIdx,
                'col'  => $colIdx,
            ];
        }
    }

    $total = count($positions);

    // ================================================================
    // 4) CONSTRUCCIÓN DE DATOS DE ÁRBOLES
    // ================================================================
    $treeData = [];

    DB::beginTransaction();

    try {
        foreach ($positions as $pos) {
            $fila = $pos['fila'];
            $col  = $pos['col'];
            $lat  = $pos['lat'];
            $lng  = $pos['lng'];

            $codigo = 'A-'
                . str_pad($fila, 3, '0', STR_PAD_LEFT) . '-'
                . str_pad($col, 3, '0', STR_PAD_LEFT);

            // Atributos biológicos
            $estado_vital = $faker->randomElement([
                'excelente', 'excelente', 'excelente', // mayor peso a sanos
                'con_estres', 'enfermo_critico', 'muerto', 'erradicado',
            ]);
            $etapa = $faker->randomElement([
                'vivero', 'establecimiento', 'desarrollo_inmaduro',
                'produccion_madura', 'senescencia',
            ]);
            $variedad = $faker->randomElement([
                'Hass', 'Hass', 'Hass', 'Fuerte', 'Bacon', 'Zutano', 'Ettinger',
            ]);
            $fecha_siembra = $faker->dateTimeBetween('-5 years', '-1 month')->format('Y-m-d');

            $fecha_primera_cosecha_obj = $faker->optional(0.7)->dateTimeBetween($fecha_siembra, 'now');
            $fecha_primera_cosecha = $fecha_primera_cosecha_obj
                ? $fecha_primera_cosecha_obj->format('Y-m-d')
                : null;

            $altitud = $faker->randomFloat(2, 800, 2500);
            $altitud_ortometrica = $faker->optional(0.8)->randomFloat(2, 800, 2500);

            $treeData[] = [
                'ciclo_productivo_id'       => $ciclo->id,
                'lote_id'                   => $lote->id,
                'lote_zona_manejo_id'       => null,
                'codigo_unico'              => $codigo,
                'fila_indice'               => $fila,
                'posicion_indice'           => $col,
                'altitud'                   => $altitud,
                'estado_vital'              => $estado_vital,
                'etapa_biologica'           => $etapa,
                'fecha_baja_muerte'         => ($estado_vital == 'muerto' || $estado_vital == 'erradicado')
                    ? $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d')
                    : null,
                'motivo_baja'               => null,
                'coordenada_precision'      => DB::raw("ST_GeomFromText('POINT($lng $lat)', 4326)"),
                'altitud_ortometrica_msnm'  => $altitud_ortometrica,
                'fecha_siembra'             => $fecha_siembra,
                'fecha_primera_cosecha'     => $fecha_primera_cosecha,
                'variedad'                  => $variedad,
                'fecha_muerte'              => null,
                'causa_muerte'              => null,
                'es_reemplazo'              => $faker->boolean(5),
                'fecha_reemplazo'           => null,
                'produccion_acumulada_kg'   => $faker->randomFloat(2, 0, 500),
                'ciclos_productivos_count'  => $faker->numberBetween(0, 10),
                'observaciones'             => $faker->optional(0.3)->sentence,
                'created_at'                => now(),
                'updated_at'                => now(),
                'deleted_at'                => null,
            ];
        }

        // Insertar en chunks
        foreach (array_chunk($treeData, 200) as $chunk) {
            DB::table('arboles')->insert($chunk);
        }

        // ================================================================
        // 5) RECUPERAR IDs Y COORDENADAS REALES
        // ================================================================
        $arboles = DB::table('arboles')
            ->where('lote_id', $lote->id)
            ->where('ciclo_productivo_id', $ciclo->id)
            ->select('id', 'fila_indice', 'posicion_indice')
            ->selectRaw('ST_Y(coordenada_precision::geometry) AS lat')
            ->selectRaw('ST_X(coordenada_precision::geometry) AS lng')
            ->get();

        $posToId   = []; // "fila,col" -> id
        $idToCoord = []; // id -> ['lat'=>..,'lng'=>..]

        foreach ($arboles as $arbol) {
            $posToId[$arbol->fila_indice . ',' . $arbol->posicion_indice] = $arbol->id;
            $idToCoord[$arbol->id] = [
                'lat' => (float) $arbol->lat,
                'lng' => (float) $arbol->lng,
            ];
        }

        // ================================================================
        // 6) RED DE VECINDAD (8 direcciones, sin duplicar)
        //    Solo se recorren 4 direcciones "hacia adelante".
        // ================================================================
        $directions = [
            [0,  1],   // E
            [1,  1],   // SE
            [1,  0],   // S
            [1, -1],   // SO
        ];

        $neighbors = [];
        $edgeSet   = [];

        foreach ($arboles as $arbol) {
            $row = $arbol->fila_indice;
            $col = $arbol->posicion_indice;
            $idOrigen = $arbol->id;
            $coordOrigen = $idToCoord[$idOrigen];

            foreach ($directions as [$dr, $dc]) {
                $nr = $row + $dr;
                $nc = $col + $dc;
                $key = $nr . ',' . $nc;

                if (!isset($posToId[$key])) {
                    continue; // celda vacía por polígono irregular o jitter
                }

                $idDestino = $posToId[$key];
                if ($idDestino === $idOrigen) {
                    continue;
                }

                // Dedupe por par ordenado
                $pair = $idOrigen < $idDestino
                    ? [$idOrigen, $idDestino]
                    : [$idDestino, $idOrigen];
                $pairKey = $pair[0] . '-' . $pair[1];
                if (isset($edgeSet[$pairKey])) {
                    continue;
                }
                $edgeSet[$pairKey] = true;

                // Distancia real (coordenadas con jitter)
                $coordDestino = $idToCoord[$idDestino];
                $dlat_m = ($coordDestino['lat'] - $coordOrigen['lat']) / $lat_deg_per_m;
                $dlng_m = ($coordDestino['lng'] - $coordOrigen['lng']) / $lng_deg_per_m;
                $dist = sqrt($dlat_m * $dlat_m + $dlng_m * $dlng_m);

                // Descartar relaciones anómalas (>12 m) por jitter extremo
                if ($dist > 12.0) {
                    continue;
                }

                // Tipo de contacto según geometría real
                if (abs($dlat_m) < 2.0) {
                    $tipo = 'misma_fila';
                } elseif (abs($dlng_m) < 2.0) {
                    $tipo = 'fila_contigua';
                } else {
                    $tipo = $faker->randomElement(['viento_predominante', 'mecanico_herramienta']);
                }

                $neighbors[] = [
                    'arbol_origen_id'             => $idOrigen,
                    'arbol_destino_id'            => $idDestino,
                    'distancia_metros'            => round($dist, 2),
                    'probabilidad_contagio_base'  => $faker->randomFloat(4, 0.01, 0.2),
                    'tipo_contacto'               => $tipo,
                    'created_at'                  => now(),
                    'updated_at'                  => now(),
                ];
            }
        }

        // Insertar vecindades en chunks
        foreach (array_chunk($neighbors, 500) as $chunk) {
            DB::table('arboles_red_vecindad')->insert($chunk);
        }

        DB::commit();

        $this->command->info("Se crearon {$total} árboles y " . count($neighbors) . ' relaciones de vecindad.');
        $this->command->info("Marco de plantación: {$spacing_row_m} m × {$spacing_col_m} m (aguacate Hass).");
        $this->command->info("Centro del lote: {$centro_lat}, {$centro_lng}");

    } catch (\Exception $e) {
        DB::rollBack();
        $this->command->error('Error: ' . $e->getMessage());
    }
}
}