<?php

namespace App\Services;

use App\Exceptions\InventarioException;
use App\Models\Insumo;
use App\Models\LoteInsumo;
use App\Models\MovimientoStock;
use App\Models\UnidadMedida;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Único dueño del kardex. Todo lo que sube o baja la cantidad de un lote pasa por aquí:
 * compras, saldos iniciales, aplicaciones en campo, devoluciones y ajustes de conteo.
 *
 * Reglas que garantiza:
 *  - El lote se bloquea (SELECT ... FOR UPDATE) antes de leer su saldo.
 *  - Ningún saldo queda negativo.
 *  - Lo vencido, en cuarentena o que todavía no había entrado a bodega no sale para el campo. AUNQUE ESTA REGLA DEBERIA PODERSE ROMPER DESDE CONFIGURACIONES
 *  - Cada movimiento queda valorizado con el costo del lote (costo específico por lote).
 *  - Cantidades en la unidad base del insumo; las demás se convierten con unidades_medida.
 */
class InventarioService
{
    private const ESCALA = 4; // decimales de cantidad, igual que las columnas (14,4)

    /** Entradas que crean un lote nuevo. Las demás entradas (devolución, sobrante) vuelven a un lote existente. */
    private const ENTRADAS_DE_LOTE_NUEVO = ['entrada_compra', 'entrada_saldo_inicial'];

    /** Salidas que se permiten aunque el lote esté vencido o en cuarentena (es justamente para sacarlo). */
    private const SALIDAS_DE_DESCARTE = ['salida_devolucion_prov', 'ajuste_salida_vencido', 'ajuste_salida_merma', 'ajuste_salida_hurto'];

    /**
     * Crea el lote en cero y registra su entrada. El saldo lo pone el movimiento, no el formulario.
     *
     * $datos: insumo_id, cantidad (unidad base), costo_unitario, fecha_ingreso, codigo_lote,
     *         y opcionales proveedor_id, compra_id, compra_item_id, fecha_vencimiento, estado.
     */
    public function ingresarLote(array $datos, string $tipo, ?Model $origen = null, ?string $observacion = null, ?int $userId = null): LoteInsumo
    {
        if (! in_array($tipo, self::ENTRADAS_DE_LOTE_NUEVO, true)) {
            throw new InventarioException("El tipo {$tipo} no crea lotes; use una entrada de compra o de saldo inicial.");
        }

        $insumo = Insumo::with('categoria')->findOrFail($datos['insumo_id']);

        if ($insumo->categoria?->maneja_vencimiento && empty($datos['fecha_vencimiento'])) {
            throw new InventarioException("{$insumo->nombre} maneja vencimiento: la fecha de vencimiento del lote es obligatoria.");
        }

        return DB::transaction(function () use ($datos, $tipo, $origen, $observacion, $userId, $insumo) {
            $lote = LoteInsumo::create([
                'insumo_id'         => $insumo->id,
                'proveedor_id'      => $datos['proveedor_id'] ?? null,
                'compra_id'         => $datos['compra_id'] ?? null,
                'compra_item_id'    => $datos['compra_item_id'] ?? null,
                'codigo_lote'       => $datos['codigo_lote'],
                'fecha_vencimiento' => $datos['fecha_vencimiento'] ?? null,
                'fecha_ingreso'     => $datos['fecha_ingreso'],
                'cantidad_inicial'  => $datos['cantidad'],
                'cantidad_actual'   => 0,
                'unidad_id'         => $insumo->unidad_base_id,
                'costo_unitario'    => $datos['costo_unitario'],
                'estado'            => $datos['estado'] ?? 'activo',
            ]);

            $this->mover($lote, $tipo, $datos['cantidad'], $origen ?? $lote, $observacion, $datos['fecha_ingreso'], $userId);

            return $lote->refresh();
        });
    }

    /**
     * Registra un movimiento sobre un lote y actualiza su saldo. Es el único método que escribe cantidad_actual.
     *
     * @param  float|string  $cantidad  positiva y en la unidad del lote; el tipo de movimiento da el signo
     */
    public function mover(
        LoteInsumo|int $lote,
        string $tipo,
        float|string $cantidad,
        Model $origen,
        ?string $observacion = null,
        $fecha = null,
        ?int $userId = null,
    ): MovimientoStock {
        $esEntrada = MovimientoStock::esEntrada($tipo);
        if (! $esEntrada && ! in_array($tipo, MovimientoStock::SALIDAS, true)) {
            throw new InventarioException("Tipo de movimiento desconocido: {$tipo}.");
        }

        $cantidad = round((float) $cantidad, self::ESCALA);
        if ($cantidad <= 0) {
            throw new InventarioException('La cantidad del movimiento debe ser mayor que cero.');
        }

        $userId ??= Auth::id();
        if ($userId === null) {
            throw new InventarioException('Todo movimiento de kardex necesita el usuario que lo registra.');
        }

        $fecha = Carbon::parse($fecha ?? now())->startOfDay();

        return DB::transaction(function () use ($lote, $tipo, $cantidad, $origen, $observacion, $fecha, $userId, $esEntrada) {
            // Bloqueo de fila: si dos personas sacan del mismo lote a la vez, la segunda espera
            // a que la primera termine y lee el saldo ya descontado.
            $lote = LoteInsumo::whereKey($lote instanceof LoteInsumo ? $lote->getKey() : $lote)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $esEntrada) {
                $this->validarSalida($lote, $tipo, $fecha);
            }

            $saldo = round((float) $lote->cantidad_actual + ($esEntrada ? $cantidad : -$cantidad), self::ESCALA);

            if ($saldo < 0) {
                throw new InventarioException(sprintf(
                    'Stock insuficiente en el lote %s: hay %s y se intentan sacar %s.',
                    $lote->codigo_lote, $this->fmt($lote->cantidad_actual), $this->fmt($cantidad)
                ));
            }

            $lote->cantidad_actual = $saldo;
            if ($saldo == 0 && $lote->estado === 'activo') {
                $lote->estado = 'agotado';
            } elseif ($saldo > 0 && $lote->estado === 'agotado') {
                $lote->estado = 'activo'; // devolución de campo a un lote que se había acabado
            }
            $lote->save();

            $costo = (float) $lote->costo_unitario;

            return MovimientoStock::create([
                'lote_insumo_id'      => $lote->id,
                'tipo_movimiento'     => $tipo,
                'cantidad'            => $cantidad,
                'costo_unitario'      => $costo,
                'valor_total'         => round($cantidad * $costo, 2),
                'stock_resultante'    => $saldo,
                'saldo_valor'         => round($saldo * $costo, 2),
                'movimientoable_type' => $origen->getMorphClass(),
                'movimientoable_id'   => $origen->getKey(),
                'fecha_movimiento'    => $fecha->toDateString(),
                'observacion'         => $observacion,
                'user_id'             => $userId,
            ]);
        });
    }

    /**
     * Decide de qué lotes sale una cantidad y los deja bloqueados hasta el final de la transacción.
     * No mueve nada: devuelve el reparto para que quien llama cree sus registros y luego llame a mover().
     *
     * @param  array<int, float>  $manual  [lote_id => cantidad en unidad base]; vacío = FEFO automático
     * @return list<array{lote: LoteInsumo, cantidad: float}>
     */
    public function repartirSalida(Insumo $insumo, float $cantidadBase, $fecha, array $manual = []): array
    {
        if (DB::transactionLevel() === 0) {
            throw new InventarioException('repartirSalida() debe llamarse dentro de una transacción para que el bloqueo sirva.');
        }

        $fecha = Carbon::parse($fecha)->startOfDay();

        return $manual === []
            ? $this->repartirFefo($insumo, round($cantidadBase, self::ESCALA), $fecha)
            : $this->repartirManual($insumo, $manual, $fecha);
    }

    /**
     * Convierte entre unidades del mismo tipo (masa con masa, volumen con volumen).
     * Ej.: 500 mL a L = 500 × 0.001 / 1 = 0.5
     */
    public function convertir(float|string $cantidad, UnidadMedida $de, UnidadMedida $a): float
    {
        if ($de->is($a)) {
            return round((float) $cantidad, self::ESCALA);
        }

        if ($de->tipo !== $a->tipo) {
            throw new InventarioException(
                "No se puede convertir {$de->nombre} a {$a->nombre}: una es {$de->tipo} y la otra {$a->tipo}."
            );
        }

        return round((float) $cantidad * (float) $de->factor_conversion / (float) $a->factor_conversion, self::ESCALA);
    }

    /** Lo que de verdad se puede llevar al campo hoy (activo, con saldo y sin vencer). */
    public function disponibleParaCampo(int $insumoId, $fecha = null): float
    {
        return round((float) LoteInsumo::disponiblesFefo($insumoId, Carbon::parse($fecha ?? now()))->sum('cantidad_actual'), self::ESCALA);
    }

    /**
     * Costo promedio ponderado del saldo en bodega. El kardex costea por lote (más exacto);
     * este promedio es para reportes y para valorizar el inventario total del insumo.
     */
    public function costoPromedio(int $insumoId): ?float
    {
        $fila = LoteInsumo::where('insumo_id', $insumoId)
            ->where('cantidad_actual', '>', 0)
            ->selectRaw('SUM(cantidad_actual * costo_unitario) AS valor, SUM(cantidad_actual) AS cantidad')
            ->first();

        return (float) $fila->cantidad > 0 ? round($fila->valor / $fila->cantidad, 4) : null;
    }

    /** Estados que puede poner una persona. 'agotado' lo pone solo el kardex cuando el saldo llega a cero. */
    public function cambiarEstado(LoteInsumo $lote, string $estado): LoteInsumo
    {
        if (! in_array($estado, ['activo', 'cuarentena', 'vencido'], true)) {
            throw new InventarioException("El estado {$estado} no se asigna a mano.");
        }

        if ($estado === 'activo' && $lote->vencidoAl(today())) {
            throw new InventarioException("El lote {$lote->codigo_lote} venció el {$lote->fecha_vencimiento->format('d/m/Y')}; no puede volver a activo.");
        }

        if ($estado === 'activo' && (float) $lote->cantidad_actual == 0) {
            $estado = 'agotado';
        }

        $lote->update(['estado' => $estado]);

        return $lote;
    }

    // ------------------------------------------------------------------

    private function validarSalida(LoteInsumo $lote, string $tipo, Carbon $fecha): void
    {
        if ($lote->fecha_ingreso && $fecha->lt($lote->fecha_ingreso)) {
            throw new InventarioException(sprintf(
                'El lote %s entró a bodega el %s; no puede tener salidas del %s.',
                $lote->codigo_lote, $lote->fecha_ingreso->format('d/m/Y'), $fecha->format('d/m/Y')
            ));
        }

        if (in_array($tipo, self::SALIDAS_DE_DESCARTE, true)) {
            return;
        }

        if ($lote->estado === 'cuarentena') {
            throw new InventarioException("El lote {$lote->codigo_lote} está en cuarentena y no puede salir para el campo.");
        }

        if ($lote->estado === 'vencido' || $lote->vencidoAl($fecha)) {
            throw new InventarioException("El lote {$lote->codigo_lote} está vencido y no puede aplicarse.");
        }
    }

    /** First Expired, First Out: primero lo que vence antes; lo que no vence va al final. */
    private function repartirFefo(Insumo $insumo, float $cantidad, Carbon $fecha): array
    {
        $lotes = LoteInsumo::disponiblesFefo($insumo->id, $fecha)
            ->whereDate('fecha_ingreso', '<=', $fecha)
            ->lockForUpdate()
            ->get();

        $disponible = round((float) $lotes->sum('cantidad_actual'), self::ESCALA);
        if ($disponible < $cantidad) {
            throw new InventarioException(sprintf(
                'No hay suficiente %s en bodega: se necesitan %s y hay %s disponibles.',
                $insumo->nombre, $this->fmt($cantidad), $this->fmt($disponible)
            ));
        }

        $reparto = [];
        $pendiente = $cantidad;
        foreach ($lotes as $lote) {
            $toma = min($pendiente, (float) $lote->cantidad_actual);
            $reparto[] = ['lote' => $lote, 'cantidad' => round($toma, self::ESCALA)];
            $pendiente = round($pendiente - $toma, self::ESCALA);
            if ($pendiente <= 0) {
                break;
            }
        }

        return $reparto;
    }

    /** El usuario eligió los lotes (ej. el almacenista entregó la caneca que tenía a la mano). Se valida igual. */
    private function repartirManual(Insumo $insumo, array $manual, Carbon $fecha): array
    {
        // Se bloquean en orden de id para que dos transacciones no se crucen (deadlock)
        $lotes = LoteInsumo::whereIn('id', array_keys($manual))->orderBy('id')->lockForUpdate()->get()->keyBy('id');

        $reparto = [];
        foreach ($manual as $loteId => $cantidad) {
            $lote = $lotes->get($loteId) ?? throw new InventarioException("El lote {$loteId} no existe.");

            if ((int) $lote->insumo_id !== (int) $insumo->id) {
                throw new InventarioException("El lote {$lote->codigo_lote} no es de {$insumo->nombre}.");
            }

            $this->validarSalida($lote, 'salida_aplicacion', $fecha);

            $reparto[] = ['lote' => $lote, 'cantidad' => round((float) $cantidad, self::ESCALA)];
        }

        return $reparto;
    }

    private function fmt(float|string $cantidad): string
    {
        return rtrim(rtrim(number_format((float) $cantidad, self::ESCALA, ',', '.'), '0'), ',');
    }
}
