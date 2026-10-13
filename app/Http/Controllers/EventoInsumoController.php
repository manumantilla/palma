<?php

namespace App\Http\Controllers;

use App\Exceptions\InventarioException;
use App\Models\EventoCampo;
use App\Models\Insumo;
use App\Models\UnidadMedida;
use App\Services\AplicacionInsumoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventoInsumoController extends Controller
{
    /** Código que manda el formulario => abreviatura en unidades_medida. */
    private const UNIDADES_FORMULARIO = ['kg' => 'kg', 'g' => 'g', 'litros' => 'L', 'ml' => 'mL', 'unidades' => 'u'];

    /**
     * Muestra la vista para registrar el consumo de insumos de un evento
     */
    public function create(EventoCampo $evento)
    {
        // Solo lotes que de verdad pueden salir al campo: activos, con saldo y sin vencer, en orden FEFO
        $hoy = today();
        $insumosConLotes = Insumo::whereHas('lotes', fn ($q) => $q->where('estado', 'activo')->where('cantidad_actual', '>', 0))
            ->with(['lotes' => fn ($q) => $q->where('estado', 'activo')
                ->where('cantidad_actual', '>', 0)
                ->where(fn ($v) => $v->whereNull('fecha_vencimiento')->orWhereDate('fecha_vencimiento', '>=', $hoy))
                ->orderByRaw('fecha_vencimiento ASC NULLS LAST')
                ->select('id', 'insumo_id', 'codigo_lote', 'cantidad_actual', 'costo_unitario', 'fecha_vencimiento')])
            ->get();

        return view('eventos_insumos.create', compact('evento', 'insumosConLotes'));
    }

    /**
     * Registra los insumos aplicados. Si no se eligen lotes, el kardex los toma por FEFO.
     */
    public function store(Request $request, EventoCampo $evento, AplicacionInsumoService $aplicaciones)
    {
        $request->validate([
            'insumos' => 'required|array|min:1',
            'insumos.*.insumo_id' => 'required|exists:insumos,id',
            'insumos.*.metodo_aplicacion' => 'required|in:terrestre,foliar,dron,fertirriego,drench',
            'insumos.*.unidad_medida' => 'required|in:' . implode(',', array_keys(self::UNIDADES_FORMULARIO)),
            'insumos.*.area_aplicada' => 'nullable|numeric|min:0',
            'insumos.*.observaciones' => 'nullable|string',

            // Lotes opcionales: si vienen, se respetan; si no, FEFO
            'insumos.*.lotes' => 'nullable|array',
            'insumos.*.lotes.*.lote_insumo_id' => 'required|exists:lotes_insumos,id',
            'insumos.*.lotes.*.cantidad' => 'required|numeric|min:0.0001',
            'insumos.*.cantidad' => 'required_without:insumos.*.lotes|nullable|numeric|min:0.0001',
        ]);

        $unidades = UnidadMedida::whereIn('abreviatura', self::UNIDADES_FORMULARIO)->pluck('id', 'abreviatura');

        try {
            DB::transaction(function () use ($request, $evento, $aplicaciones, $unidades) {
                foreach ($request->insumos as $insumoData) {
                    // Un mismo lote puede aparecer dos veces en el formulario; se suma
                    $lotes = [];
                    foreach ($insumoData['lotes'] ?? [] as $loteReq) {
                        $lotes[$loteReq['lote_insumo_id']] = ($lotes[$loteReq['lote_insumo_id']] ?? 0) + $loteReq['cantidad'];
                    }

                    $aplicaciones->registrar($evento, [
                        'insumo_id'         => $insumoData['insumo_id'],
                        'cantidad'          => $lotes !== [] ? array_sum($lotes) : $insumoData['cantidad'],
                        'unidad_id'         => $unidades[self::UNIDADES_FORMULARIO[$insumoData['unidad_medida']]],
                        'metodo_aplicacion' => $insumoData['metodo_aplicacion'],
                        'area_aplicada'     => $insumoData['area_aplicada'] ?? null,
                        'observaciones'     => $insumoData['observaciones'] ?? null,
                        'lotes'             => $lotes,
                    ]);
                }

                if ($evento->estado === 'Pendiente') {
                    $evento->update(['estado' => 'En Proceso']);
                }
            });
        } catch (InventarioException $e) {
            return redirect()->back()->withInput()->with('error', 'No se registró la aplicación: ' . $e->getMessage());
        }

        return redirect()->route('eventos_campo.show', $evento->id)
            ->with('success', 'Insumos registrados y descontados del kardex.');
    }
}
