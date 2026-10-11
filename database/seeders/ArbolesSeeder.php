<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ArbolesSeeder extends Seeder
{
    use CreaLoteDemo;

    /** Límite duro de árboles a sembrar */
    private const MAX_ARBOLES = 500;

    public function run()
    {
        $lote  = $this->loteDemo();
        $ciclo = $this->cicloDemo($lote);

        if (DB::table('arboles')->where('ciclo_productivo_id', $ciclo->id)->exists()) {
            $this->command->info('El ciclo demo ya tiene árboles; no se vuelven a sembrar.');
            return;
        }

        $faker = Faker::create('es_PE');

        // ================================================================
        // 1) CONFIGURACIÓN AGRONÓMICA
        // ================================================================
        $spacing_row_m = 6.0;   // entre filas
        $spacing_col_m = 5.0;   // entre plantas dentro de la fila

        // Centro de referencia: LookAt del KML "lote (1).kml" (Placemark LULOS)
        $centro_lat = 6.910780408880457;
        $centro_lng = -72.84217278070524;

        $lat_deg_per_m = 1.0 / 111320.0;
        $lng_deg_per_m = 1.0 / (111320.0 * cos(deg2rad($centro_lat)));

        // ================================================================
        // 2) POLÍGONO REAL DEL LOTE (KML "lote (1).kml")
        //    Formato [lat, lng]
        // ================================================================
        $polygon = [
            [6.910736123415511, -72.84292204877374],
            [6.91059505456343,  -72.84291540125042],
            [6.910479251957617, -72.8428902781054],
            [6.910386293430872, -72.8427598147199],
            [6.910414260577348, -72.84268938675797],
            [6.910414001748099, -72.84254187149287],
            [6.910463058693249, -72.84244540004308],
            [6.910453498113557, -72.84231080676619],
            [6.910462986924046, -72.84224002365781],
            [6.910481859926554, -72.84218743169839],
            [6.910514780886331, -72.84212280127996],
            [6.910528648242908, -72.8420551034217],
            [6.91053100149036,  -72.84199211503635],
            [6.910535574842885, -72.84188902722552],
            [6.910509696368684, -72.84182625626192],
            [6.910469779712209, -72.84180984973885],
            [6.910495502597566, -72.84171264629417],
            [6.910515758937382, -72.84164926983551],
            [6.910620380903547, -72.84164788493109],
            [6.910667164379478, -72.8415829940149],
            [6.910720872017689, -72.84145151322262],
            [6.910744696155308, -72.84138461884663],
            [6.910789492094547, -72.84131791863851],
            [6.910824833461641, -72.84125428307],
            [6.910859467130838, -72.84121727011711],
            [6.910900815026154, -72.84118324114985],
            [6.910986659405909, -72.84114839608492],
            [6.91106774406011,  -72.84109535221542],
            [6.911126066369856, -72.84107489323038],
            [6.911193692325262, -72.84107250776319],
            [6.911247169842676, -72.84108635672079],
            [6.911285318983489, -72.84111170510316],
            [6.911266132623192, -72.84116977853867],
            [6.911282256270367, -72.84122088960432],
            [6.911321998049919, -72.84129930722945],
            [6.911370762349438, -72.84139066056889],
            [6.911386940321822, -72.8414527517583],
            [6.911353061817737, -72.84150108011166],
            [6.911357966290878, -72.84157361438912],
            [6.911295427844906, -72.84164147757424],
            [6.911357178099627, -72.84167880561668],
            [6.911383904629393, -72.84176633752651],
            [6.911379008608436, -72.84181336926713],
            [6.911400269185758, -72.84186074345628],
            [6.911381795206759, -72.84197691761317],
            [6.911360569099736, -72.84204600551628],
            [6.911331648931936, -72.84214136355608],
            [6.91135629319316,  -72.84224231859685],
            [6.911328623292941, -72.84228455859161],
            [6.911322336353554, -72.84232817132715],
            [6.911322460582602, -72.84238071486554],
            [6.911262380808479, -72.84244253492663],
            [6.911212689105839, -72.84250164508154],
            [6.911178331142703, -72.84261428728284],
            [6.911153594317315, -72.84260035184144],
            [6.91115692145635,  -72.84261944931252],
            [6.911145559239948, -72.84264331447851],
            [6.911109663477285, -72.8426416671544],
            [6.91104815172882,  -72.84267451387726],
            [6.91096883231484,  -72.84276602166481],
            [6.910945328682527, -72.84291146876301],
            [6.910736123415511, -72.84292204877374],
        ];

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

        $lats = array_column($polygon, 0);
        $lngs = array_column($polygon, 1);
        $minLat = min($lats); $maxLat = max($lats);
        $minLng = min($lngs); $maxLng = max($lngs);

        // ================================================================
        // 2.1) ESTIMAR DENSIDAD Y AJUSTAR SPACING PARA ~MAX_ARBOLES
        //      Área aproximada por bounding box × factor de forma.
        // ================================================================
        $altura_m = ($maxLat - $minLat) / $lat_deg_per_m;
        $ancho_m  = ($maxLng - $minLng) / $lng_deg_per_m;
        $area_bbox_m2 = $altura_m * $ancho_m;

        // Factor empírico: el polígono irregular ocupa ~60% del bbox
        $area_lote_m2 = $area_bbox_m2 * 0.60;

        // Densidad objetivo: árboles / m²
        $densidad_objetivo = self::MAX_ARBOLES / max($area_lote_m2, 1);

        // Área por árbol según densidad objetivo
        $area_por_arbol = 1.0 / $densidad_objetivo;

        // Mantener proporción 6:5 del marco agronómico
        $ratio = $spacing_row_m / $spacing_col_m; // 1.2
        // área = row * col = row * (row / ratio) = row² / ratio
        $row_efectivo = sqrt($area_por_arbol * $ratio);
        $col_efectivo = $row_efectivo / $ratio;

        // Aplicar un pequeño margen (5% menos árboles) para asegurar el corte
        $row_efectivo *= 1.03;
        $col_efectivo *= 1.03;

        $spacing_deg_lat = $row_efectivo * $lat_deg_per_m;
        $spacing_deg_lng = $col_efectivo * $lng_deg_per_m;

        // ================================================================
        // 3) GENERACIÓN DE POSICIONES
        // ================================================================
        $positions = [];
        $rowIdx = 0;

        for ($lat = $maxLat; $lat >= $minLat; $lat -= $spacing_deg_lat) {
            $rowIdx++;

            $row_shift_m = $faker->randomFloat(3, -1.5, 1.5);
            $row_lat = $lat + $row_shift_m * $lat_deg_per_m;

            $lateral_offset = $faker->randomFloat(3, -1.0, 1.0) * $lng_deg_per_m;

            $colIdx = 0;
            for ($lng = $minLng + $lateral_offset; $lng <= $maxLng; $lng += $spacing_deg_lng) {
                $colIdx++;

                $jitter_lat = $faker->randomFloat(6, -0.7, 0.7) * $lat_deg_per_m;
                $jitter_lng = $faker->randomFloat(6, -0.7, 0.7) * $lng_deg_per_m;

                if ($faker->boolean(8)) {
                    $jitter_lat += $faker->randomFloat(6, -1.8, 1.8) * $lat_deg_per_m;
                    $jitter_lng += $faker->randomFloat(6, -1.8, 1.8) * $lng_deg_per_m;
                }

                $pos_lat = $row_lat + $jitter_lat;
                $pos_lng = $lng + $jitter_lng;

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

        // ================================================================
        // 3.1) CORTE DURO A MAX_ARBOLES
        //      Si sobran, se toman los primeros (distribución uniforme
        //      porque se generan fila por fila).
        // ================================================================
        if (count($positions) > self::MAX_ARBOLES) {
            $positions = array_slice($positions, 0, self::MAX_ARBOLES);
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

                $estado_vital = $faker->randomElement([
                    'excelente', 'excelente', 'excelente',
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

                $altitud            = round($faker->randomFloat(2, 2395, 2410), 2);
                $altitud_ortometrica = $faker->optional(0.8)->randomFloat(2, 2395, 2410);

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

            foreach (array_chunk($treeData, 200) as $chunk) {
                DB::table('arboles')->insert($chunk);
            }

            // ================================================================
            // 5) RECUPERAR IDs Y COORDENADAS
            // ================================================================
            $arboles = DB::table('arboles')
                ->where('lote_id', $lote->id)
                ->where('ciclo_productivo_id', $ciclo->id)
                ->select('id', 'fila_indice', 'posicion_indice')
                ->selectRaw('ST_Y(coordenada_precision::geometry) AS lat')
                ->selectRaw('ST_X(coordenada_precision::geometry) AS lng')
                ->get();

            $posToId   = [];
            $idToCoord = [];

            foreach ($arboles as $arbol) {
                $posToId[$arbol->fila_indice . ',' . $arbol->posicion_indice] = $arbol->id;
                $idToCoord[$arbol->id] = [
                    'lat' => (float) $arbol->lat,
                    'lng' => (float) $arbol->lng,
                ];
            }

            // ================================================================
            // 6) RED DE VECINDAD
            // ================================================================
            $directions = [
                [0,  1],
                [1,  1],
                [1,  0],
                [1, -1],
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
                        continue;
                    }

                    $idDestino = $posToId[$key];
                    if ($idDestino === $idOrigen) {
                        continue;
                    }

                    $pair = $idOrigen < $idDestino
                        ? [$idOrigen, $idDestino]
                        : [$idDestino, $idOrigen];
                    $pairKey = $pair[0] . '-' . $pair[1];
                    if (isset($edgeSet[$pairKey])) {
                        continue;
                    }
                    $edgeSet[$pairKey] = true;

                    $coordDestino = $idToCoord[$idDestino];
                    $dlat_m = ($coordDestino['lat'] - $coordOrigen['lat']) / $lat_deg_per_m;
                    $dlng_m = ($coordDestino['lng'] - $coordOrigen['lng']) / $lng_deg_per_m;
                    $dist = sqrt($dlat_m * $dlat_m + $dlng_m * $dlng_m);

                    // Con spacing adaptativo, el umbral sube un poco.
                    // Usamos 1.6× el spacing efectivo como corte.
                    $max_dist = max(12.0, $row_efectivo * 1.6, $col_efectivo * 1.6);
                    if ($dist > $max_dist) {
                        continue;
                    }

                    if (abs($dlat_m) < ($row_efectivo * 0.4)) {
                        $tipo = 'misma_fila';
                    } elseif (abs($dlng_m) < ($col_efectivo * 0.4)) {
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

            foreach (array_chunk($neighbors, 500) as $chunk) {
                DB::table('arboles_red_vecindad')->insert($chunk);
            }

            DB::commit();

            $this->command->info("Se crearon {$total} árboles (límite: " . self::MAX_ARBOLES . ") y " . count($neighbors) . ' relaciones de vecindad.');
            $this->command->info("Marco efectivo: " . round($row_efectivo, 2) . " m × " . round($col_efectivo, 2) . " m.");
            $this->command->info("Centro del lote: {$centro_lat}, {$centro_lng}");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error: ' . $e->getMessage());
        }
    }
}