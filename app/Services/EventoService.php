<?php

namespace App\Services;

use App\Exceptions\EventoException;
use App\Models\Arbol;
use App\Models\CategoriaGasto;
use App\Models\CicloEtapaHistorial;
use App\Models\CicloProductivo;
use App\Models\EventoCampo;
use App\Models\EventoManoObra;
use App\Models\Gasto;
use App\Models\GastoPago;
use App\Models\LoteZonaManejo;
use App\Models\RecepcionCampo;
use App\Models\SesionCosecha;
use App\Models\Trabajador;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use MatanYadaev\EloquentSpatial\Objects\Point;

/**
 * Dueño de las labores de campo: crea eventos congruentes con su ciclo, registra la mano de obra
 * con su costo y controla los estados. Los insumos van por AplicacionInsumoService.
 *
 * Mano de obra = solo valor pagado (en el campo casi todo es jornal informal).
 */
class EventoService
{
    /** Estados con los que se puede crear una labor; Completado y Cancelado tienen su propio método. */
    private const ESTADOS_INICIALES = ['Pendiente', 'En Proceso'];

    // ================================================================
    //  EVENTOS
    // ================================================================

    /**
     * $datos: tipo_evento_id, fecha_programada y opcionales ciclo_productivo_id, lote_id, zona_id,
     * ciclo_etapa_id, estado, fecha_limite, hora_inicio, hora_fin, latitud, longitud, observaciones,
     * origen, origen_id, numero_repeticion. $alcance 'arbol' asigna los árboles vivos del lote o zona.
     */
    public function crear(array $datos, ?string $alcance = null): EventoCampo
    {
        $estado = $datos['estado'] ?? 'Pendiente';
        if (! in_array($estado, self::ESTADOS_INICIALES, true)) {
            throw new EventoException('Una labor nueva arranca Pendiente o En Proceso; se completa o cancela después.');
        }

        $datos = $this->ubicacionCongruente($datos);

        return DB::transaction(function () use ($datos, $alcance, $estado) {
            $evento = EventoCampo::create([
                'ciclo_productivo_id' => $datos['ciclo_productivo_id'] ?? null,
                'lote_id'             => $datos['lote_id'] ?? null,
                'zona_id'             => $datos['zona_id'] ?? null,
                'ciclo_etapa_id'      => $datos['ciclo_etapa_id'] ?? null,
                'tipo_evento_id'      => $datos['tipo_evento_id'],
                'origen'              => $datos['origen'] ?? 'manual',
                'origen_id'           => $datos['origen_id'] ?? null,
                'numero_repeticion'   => $datos['numero_repeticion'] ?? 1,
                'fecha_programada'    => $datos['fecha_programada'],
                'fecha_limite'        => $datos['fecha_limite'] ?? null,
                'hora_inicio'         => $datos['hora_inicio'] ?? null,
                'hora_fin'            => $datos['hora_fin'] ?? null,
                'coordenada_gps'      => $this->punto($datos),
                'estado'              => $estado,
                'observaciones'       => $datos['observaciones'] ?? null,
            ]);

            if ($alcance === 'arbol') {
                $this->asignarArboles($evento);
            }

            return $evento;
        });
    }

    /** Edita datos de programación y ubicación. El estado se cambia con completar() / cancelar(). */
    public function actualizar(EventoCampo $evento, array $datos): EventoCampo
    {
        if (in_array($evento->estado, ['Completado', 'Cancelado'], true)) {
            throw new EventoException("La labor está {$evento->estado} y ya no se edita.");
        }

        $datos = $this->ubicacionCongruente($datos + $evento->only(['ciclo_productivo_id', 'lote_id', 'zona_id', 'ciclo_etapa_id']));

        $evento->update([
            'ciclo_productivo_id' => $datos['ciclo_productivo_id'] ?? null,
            'lote_id'             => $datos['lote_id'] ?? null,
            'zona_id'             => $datos['zona_id'] ?? null,
            'ciclo_etapa_id'      => $datos['ciclo_etapa_id'] ?? null,
            'tipo_evento_id'      => $datos['tipo_evento_id'] ?? $evento->tipo_evento_id,
            'fecha_programada'    => $datos['fecha_programada'] ?? $evento->fecha_programada,
            'fecha_limite'        => array_key_exists('fecha_limite', $datos) ? $datos['fecha_limite'] : $evento->fecha_limite,
            'hora_inicio'         => $datos['hora_inicio'] ?? $evento->hora_inicio,
            'hora_fin'            => $datos['hora_fin'] ?? $evento->hora_fin,
            'coordenada_gps'      => $this->punto($datos) ?? $evento->coordenada_gps,
            'observaciones'       => $datos['observaciones'] ?? $evento->observaciones,
        ]);

        return $evento;
    }

    /**
     * Cierra la labor. La fecha de ejecución es la que se indique o, si no, el último día trabajado.
     * Una labor que lleva mano de obra no se cierra sin jornales: quedaría con costo cero.
     */
    public function completar(EventoCampo $evento, $fechaEjecucion = null): EventoCampo
    {
        if ($evento->estado === 'Cancelado') {
            throw new EventoException('La labor está cancelada; no se puede completar.');
        }

        $ultimoDia = $evento->eventoManoObra()->max('fecha_labor');

        if ($evento->tipoEvento->consume_mano_obra && $ultimoDia === null) {
            throw new EventoException("Registre al menos un jornal antes de completar la labor de {$evento->tipoEvento->nombre}.");
        }

        $fecha = Carbon::parse($fechaEjecucion ?? $ultimoDia ?? now());
        if ($fecha->isFuture()) {
            throw new EventoException('La fecha de ejecución no puede ser futura.');
        }

        $evento->update(['estado' => 'Completado', 'fecha_ejecucion' => $fecha]);

        return $evento;
    }

    /** Solo se cancela lo que no consumió nada; si ya tiene insumos o jornales, se completa. */
    public function cancelar(EventoCampo $evento, string $motivo): EventoCampo
    {
        if ($evento->estado === 'Completado') {
            throw new EventoException('La labor ya está completada; no se puede cancelar.');
        }

        if ($evento->eventoManoObra()->exists() || $evento->eventoInsumos()->exists()) {
            throw new EventoException('La labor ya tiene insumos o jornales registrados: complétela en vez de cancelarla.');
        }

        $nota = trim(($evento->observaciones ? $evento->observaciones . "\n" : '') . 'Cancelado: ' . $motivo);
        $evento->update(['estado' => 'Cancelado', 'observaciones' => $nota]);

        return $evento;
    }

    // ================================================================
    //  MANO DE OBRA
    // ================================================================

    /**
     * $datos: tipo_labor, fecha_labor, cantidad, valor_unitario y opcionales trabajador_id,
     * nombre_trabajador, cedula, costo_total (valor a pagar ajustado a mano), unidad_destajo,
     * area_ejecutada_ha, arboles_atendidos, observaciones.
     */
    public function registrarManoObra(EventoCampo $evento, array $datos, ?int $userId = null): EventoManoObra
    {
        $this->validarEventoParaManoObra($evento);

        if (! empty($datos['sesion_id'])
            && (int) SesionCosecha::whereKey($datos['sesion_id'])->value('evento_campo_id') !== (int) $evento->id) {
            throw new EventoException('La sesión de cosecha pertenece a otra labor.');
        }

        return DB::transaction(function () use ($evento, $datos, $userId) {
            $registro = EventoManoObra::create(['evento_campo_id' => $evento->id, 'estado_pago' => 'pendiente']
                + $this->datosManoObra($datos));

            $this->causarCosto($evento, $registro, $userId ?? Auth::id());

            if ($evento->estado === 'Pendiente') {
                $evento->update(['estado' => 'En Proceso']);
            }

            return $registro;
        });
    }

    /**
     * Destajo de cosecha. El evento sale de la sesión y la cantidad, por defecto, de lo que pesó
     * el trabajador en esa sesión. El pesaje por sí solo nunca genera pago: solo este registro.
     */
    public function registrarDestajoCosecha(SesionCosecha $sesion, array $datos, ?int $userId = null): EventoManoObra
    {
        if (empty($datos['trabajador_id'])) {
            throw new EventoException('El destajo de cosecha necesita el trabajador registrado que hizo los pesajes.');
        }

        $existe = EventoManoObra::where('sesion_id', $sesion->id)
            ->where('trabajador_id', $datos['trabajador_id'])
            ->where('tipo_labor', 'destajo')
            ->exists();
        if ($existe) {
            throw new EventoException('Este trabajador ya tiene su destajo registrado en esta sesión; edite ese registro.');
        }

        $pesado = $this->kilosPesados($sesion, (int) $datos['trabajador_id']);

        return $this->registrarManoObra($sesion->eventoCampo, [
            'tipo_labor'     => 'destajo',
            'unidad_destajo' => 'kg',
            'cantidad'       => $datos['cantidad'] ?? $pesado['kilos'],
            'fecha_labor'    => $datos['fecha_labor'] ?? $sesion->fecha,
            'sesion_id'      => $sesion->id,
            'kilos_pesados'  => $pesado['kilos'],
        ] + $datos, $userId);
    }

    /** Solo lo pendiente de pago. Cambiar de evento no se permite: se elimina y se registra de nuevo. */
    public function actualizarManoObra(EventoManoObra $registro, array $datos): EventoManoObra
    {
        if ($registro->estado_pago === 'pagado') {
            throw new EventoException('Este registro ya fue pagado y no se modifica.');
        }

        return DB::transaction(function () use ($registro, $datos) {
            // La sesión y lo que marcó la báscula no se cambian desde un formulario
            $datos = $this->datosManoObra(array_merge($datos, [
                'sesion_id'     => $registro->sesion_id,
                'kilos_pesados' => $registro->kilos_pesados,
            ]));
            $registro->update($datos);

            $registro->gasto?->update([
                'base_gravable'     => $registro->costo_total,
                'fecha'             => $registro->fecha_labor,
                'trabajador_id'     => $registro->trabajador_id,
                'tercero_nombre'    => $registro->nombre_trabajador,
                'tercero_documento' => $registro->cedula,
                'concepto'          => $this->concepto($registro),
            ]);

            return $registro;
        });
    }

    /** Elimina un registro pendiente. Su gasto no se borra: queda anulado para la auditoría. */
    public function eliminarManoObra(EventoManoObra $registro, ?int $userId = null): void
    {
        if ($registro->estado_pago === 'pagado') {
            throw new EventoException('No es posible eliminar un registro que ya fue pagado.');
        }

        DB::transaction(function () use ($registro, $userId) {
            $registro->gasto?->update([
                'estado'           => 'anulado',
                'motivo_anulacion' => 'Se eliminó el registro de mano de obra',
                'anulado_por'      => $userId ?? Auth::id(),
                'anulado_at'       => now(),
            ]);
            $registro->delete();
        });
    }

    /**
     * Paga lo pendiente de un trabajador en un rango de días trabajados.
     * Marca los registros como pagados y abona el pago en su gasto (la cuenta por pagar queda saldada).
     *
     * @return array{registros: int, total: float}
     */
    public function liquidar(?int $trabajadorId, ?string $documento, $desde, $hasta, $fechaPago, ?int $userId = null): array
    {
        if ($trabajadorId === null && blank($documento)) {
            throw new EventoException('Indique el trabajador o su documento.');
        }

        $userId ??= Auth::id();

        return DB::transaction(function () use ($trabajadorId, $documento, $desde, $hasta, $fechaPago, $userId) {
            $pendientes = EventoManoObra::pendientesDePago()
                ->where(fn ($q) => $q
                    ->when($trabajadorId, fn ($q) => $q->orWhere('trabajador_id', $trabajadorId))
                    ->when(filled($documento), fn ($q) => $q->orWhere('cedula', $documento)))
                ->enRangoFechas(Carbon::parse($desde)->toDateString(), Carbon::parse($hasta)->toDateString())
                ->lockForUpdate()
                ->with('gasto')
                ->get();

            if ($pendientes->isEmpty()) {
                throw new EventoException('No hay jornales pendientes de pago para ese trabajador en esas fechas.');
            }

            foreach ($pendientes as $registro) {
                $registro->update(['estado_pago' => 'pagado', 'fecha_pago' => $fechaPago]);

                if ($gasto = $registro->gasto) {
                    GastoPago::create([
                        'gasto_id'   => $gasto->id,
                        'monto'      => $registro->costo_total,
                        'fecha_pago' => $fechaPago,
                        'metodo'     => 'efectivo',
                        'user_id'    => $userId,
                    ]);
                    $gasto->update(['valor_pagado' => $registro->costo_total, 'estado' => 'pagado']);
                }
            }

            return ['registros' => $pendientes->count(), 'total' => round((float) $pendientes->sum('costo_total'), 2)];
        });
    }

    /**
     * Lo que pesó un trabajador en una sesión. Se muestra al registrar el destajo para que
     * el supervisor pague con base en la báscula.
     *
     * @return array{kilos: float, pesajes: int}
     */
    public function kilosPesados(SesionCosecha $sesion, int $trabajadorId): array
    {
        $fila = RecepcionCampo::where('sesion_cosecha_id', $sesion->id)
            ->where('trabajador_id', $trabajadorId)
            ->selectRaw('COALESCE(SUM(peso_neto), 0) AS kilos, COUNT(*) AS pesajes')
            ->first();

        return ['kilos' => round((float) $fila->kilos, 2), 'pesajes' => (int) $fila->pesajes];
    }

    // ================================================================
    //  INTERNOS
    // ================================================================

    /**
     * El lote debe ser el del ciclo, la zona de ese lote y la etapa de ese ciclo.
     * Si viene ciclo sin lote, el lote se toma del ciclo.
     */
    private function ubicacionCongruente(array $datos): array
    {
        $ciclo = ! empty($datos['ciclo_productivo_id']) ? CicloProductivo::findOrFail($datos['ciclo_productivo_id']) : null;

        if ($ciclo) {
            if (! empty($datos['lote_id']) && (int) $datos['lote_id'] !== (int) $ciclo->lote_id) {
                throw new EventoException('El lote no corresponde al ciclo productivo seleccionado.');
            }
            $datos['lote_id'] = $ciclo->lote_id;
        }

        if (! empty($datos['zona_id'])) {
            $zona = LoteZonaManejo::findOrFail($datos['zona_id']);
            if (empty($datos['lote_id']) || (int) $zona->lote_id !== (int) $datos['lote_id']) {
                throw new EventoException("La zona {$zona->nombre_zona} no pertenece al lote de la labor.");
            }
        }

        if (! empty($datos['ciclo_etapa_id'])) {
            $etapa = CicloEtapaHistorial::findOrFail($datos['ciclo_etapa_id']);
            if (! $ciclo || (int) $etapa->ciclo_productivo_id !== (int) $ciclo->id) {
                throw new EventoException('La etapa fenológica no pertenece al ciclo de la labor.');
            }
        }

        return $datos;
    }

    private function punto(array $datos): ?Point
    {
        if (! isset($datos['latitud'], $datos['longitud']) || $datos['latitud'] === '' || $datos['longitud'] === '') {
            return null;
        }

        return new Point((float) $datos['latitud'], (float) $datos['longitud'], 4326);
    }

    /** Árboles vivos del lote (o de la zona, si la labor tiene zona). Los muertos o erradicados no se atienden. */
    private function asignarArboles(EventoCampo $evento): void
    {
        if (! $evento->lote_id) {
            throw new EventoException('Para asignar árboles la labor necesita un lote.');
        }

        $ids = Arbol::where('lote_id', $evento->lote_id)
            ->when($evento->zona_id, fn ($q) => $q->where('lote_zona_manejo_id', $evento->zona_id))
            ->whereNotIn('estado_vital', ['muerto', 'erradicado'])
            ->pluck('id');

        if ($ids->isEmpty()) {
            throw new EventoException('No hay árboles vivos registrados en ese lote o zona.');
        }

        $ahora = now();
        DB::table('evento_arbol')->insert($ids->map(fn ($id) => [
            'evento_campo_id' => $evento->id,
            'arbol_id'        => $id,
            'novedad_arbol'   => 'ninguna',
            'created_at'      => $ahora,
            'updated_at'      => $ahora,
        ])->all());
    }

    private function validarEventoParaManoObra(EventoCampo $evento): void
    {
        if (! $evento->tipoEvento->consume_mano_obra) {
            throw new EventoException("Una labor de {$evento->tipoEvento->nombre} no lleva mano de obra.");
        }

        if ($evento->estado === 'Cancelado') {
            throw new EventoException('La labor está cancelada; no se le registran jornales.');
        }
    }

    /** Normaliza y valida un registro de mano de obra. El valor a pagar arranca en cantidad × valor. */
    private function datosManoObra(array $d): array
    {
        if (! in_array($d['tipo_labor'] ?? null, EventoManoObra::TIPOS_LABOR, true)) {
            throw new EventoException('Tipo de labor no válido.');
        }

        $fecha = Carbon::parse($d['fecha_labor'] ?? throw new EventoException('Falta el día trabajado.'));
        if ($fecha->isFuture()) {
            throw new EventoException('No se registran jornales de días que no han pasado.');
        }

        $cantidad = round((float) ($d['cantidad'] ?? 0), 2);
        $valor = round((float) ($d['valor_unitario'] ?? -1), 2);
        if ($cantidad <= 0 || $valor < 0) {
            throw new EventoException('La cantidad debe ser mayor que cero y el valor unitario no puede ser negativo.');
        }

        $costo = isset($d['costo_total']) && $d['costo_total'] !== '' ? round((float) $d['costo_total'], 2) : round($cantidad * $valor, 2);
        if ($costo < 0) {
            throw new EventoException('El valor a pagar no puede ser negativo.');
        }

        if ($d['tipo_labor'] === 'destajo' && blank($d['unidad_destajo'] ?? null)) {
            throw new EventoException('El destajo necesita su unidad (kg, racimos, árboles...).');
        }

        // Si es un trabajador registrado, el nombre y el documento salen de su ficha
        if (! empty($d['trabajador_id'])) {
            $t = Trabajador::findOrFail($d['trabajador_id']);
            $d['nombre_trabajador'] = trim($t->nombres . ' ' . $t->apellidos);
            $d['cedula'] = $t->numero_documento;
        }

        if (blank($d['nombre_trabajador'] ?? null)) {
            throw new EventoException('Indique el nombre del trabajador.');
        }

        return [
            'sesion_id'         => $d['sesion_id'] ?? null,
            'tipo_labor'        => $d['tipo_labor'],
            'trabajador_id'     => $d['trabajador_id'] ?? null,
            'nombre_trabajador' => trim($d['nombre_trabajador']),
            'cedula'            => filled($d['cedula'] ?? null) ? trim($d['cedula']) : null,
            'fecha_labor'       => $fecha->toDateString(),
            'cantidad'          => $cantidad,
            'unidad_destajo'    => $d['tipo_labor'] === 'destajo' ? $d['unidad_destajo'] : null,
            'valor_unitario'    => $valor,
            'costo_total'       => $costo,
            'kilos_pesados'     => $d['kilos_pesados'] ?? null,
            'area_ejecutada_ha' => filled($d['area_ejecutada_ha'] ?? null) ? $d['area_ejecutada_ha'] : null,
            'arboles_atendidos' => filled($d['arboles_atendidos'] ?? null) ? (int) $d['arboles_atendidos'] : null,
            'observaciones'     => $d['observaciones'] ?? null,
        ];
    }

    /**
     * El jornal se causa como costo de la labor, cargado a su lote, ciclo y etapa, y queda como
     * cuenta por pagar hasta la liquidación. Sin ciclo (una cerca, un camino) es costo indirecto.
     */
    private function causarCosto(EventoCampo $evento, EventoManoObra $registro, ?int $userId): Gasto
    {
        $categoria = CategoriaGasto::where('origen_automatico', 'mano_obra')->firstOrFail();

        return Gasto::create([
            'origen'              => 'mano_obra',
            'gastable_type'       => $registro->getMorphClass(),
            'gastable_id'         => $registro->id,
            'categoria_gasto_id'  => $categoria->id,
            'clase_costo'         => $categoria->clase_costo_default,
            'imputacion'          => $evento->ciclo_productivo_id ? $categoria->imputacion_default : 'indirecto',
            'cuenta_puc'          => $categoria->cuenta_puc,
            'lote_id'             => $evento->lote_id,
            'ciclo_productivo_id' => $evento->ciclo_productivo_id,
            'ciclo_etapa_id'      => $evento->ciclo_productivo_id ? $evento->ciclo_etapa_id : null,
            'trabajador_id'       => $registro->trabajador_id,
            'tercero_nombre'      => $registro->nombre_trabajador,
            'tercero_documento'   => $registro->cedula,
            'tipo_soporte'        => 'comprobante_interno',
            'concepto'            => $this->concepto($registro),
            'fecha'               => $registro->fecha_labor,
            'base_gravable'       => $registro->costo_total,
            'es_cuenta_por_pagar' => true,
            'estado'              => 'devengado',
            'user_id'             => $userId,
        ]);
    }

    private function concepto(EventoManoObra $registro): string
    {
        $labor = str_replace('_', ' ', $registro->tipo_labor);

        return mb_substr("Mano de obra ({$labor}): {$registro->nombre_trabajador}", 0, 255);
    }
}
