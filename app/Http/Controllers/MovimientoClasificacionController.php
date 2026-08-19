<?php

namespace App\Http\Controllers;

use App\Models\MovimientoClasificacion;
use App\Models\Contenedor;
use App\Models\RecepcionCampo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class MovimientoClasificacionController extends Controller
{
    /**
     * Muestra la lista de movimientos con filtros.
     */
    public function index(Request $request)
    {
        try {
            $movimientos = MovimientoClasificacion::with(['operario', 'recepcionCampo', 'contenedor'])
                ->fechas($request->fecha_inicio, $request->fecha_fin)
                ->porContenedor($request->contenedor_id)
                ->porRecepcion($request->recepcion_id)
                ->porOperario($request->operario_id)
                ->orderBy('fecha_movimiento', 'desc')
                ->paginate(15);

            // Agregar estos datos para los filtros
            $contenedores = Contenedor::orderBy('codigo')->get();
            $operarios = User::where('rol', 'operario')->orderBy('name')->get();

            return view('movimientos.index', compact('movimientos', 'contenedores', 'operarios'));

        } catch (Exception $e) {
            Log::error("Error al cargar el index de Movimientos: {$e->getMessage()}");
            return back()->with('error', 'Ocurrió un error al intentar cargar la lista de movimientos.');
        }
    }

public function create(Request $request)
    {
        try {
            // Obtenemos todas las órdenes en proceso para el filtro superior
            $ordenesActivas = OrdenCosecha::where('estado', 'en_proceso')->get();
            
            $recepcionesPendientes = collect();
            $ordenSeleccionada = null;

            // Si el usuario seleccionó una orden, cargamos sus costales (recepciones) pendientes
            if ($request->has('orden_id')) {
                $ordenSeleccionada = OrdenCosecha::findOrFail($request->orden_id);
                
                $recepcionesPendientes = RecepcionCampo::whereHas('sesionCosecha', function($query) use ($ordenSeleccionada) {
                        $query->where('orden_cosecha_id', $ordenSeleccionada->id);
                    })
                    ->where('estado_clasificacion', 'pendiente')
                    ->with('trabajador') // Para mostrar quién lo recolectó
                    ->get();
            }

            // Contenedores/Tolvas disponibles para recibir fruta
            $contenedores = Contenedor::where('estado', 'abierta')->get();

            return view('movimientos.create', compact(
                'ordenesActivas', 
                'ordenSeleccionada', 
                'recepcionesPendientes', 
                'contenedores'
            ));

        } catch (Exception $e) {
            Log::error("Error al cargar create de movimientos: " . $e->getMessage());
            return redirect()->route('movimientos.index')->with('error', 'Error al cargar la interfaz de asignación.');
        }
    }

    /**
     * Procesa la asignación masiva de costales a un contenedor.
     */
    public function store(Request $request)
    {
        $request->validate([
            'contenedor_id' => 'required|exists:contenedores,id',
            'recepcion_ids' => 'required|array|min:1',
            'recepcion_ids.*' => 'exists:recepciones_campo,id'
        ], [
            'recepcion_ids.required' => 'Debes seleccionar al menos un costal de la lista.',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $contenedor = Contenedor::findOrFail($request->contenedor_id);
                $totalKilosNuevos = 0;
                $fechaHoy = now()->toDateString();
                $operarioId = auth()->id();

                // 1. Consultar recepciones aplicando bloqueo de fila en PostgreSQL
                // Esto evita que si 2 operarios dan clic al mismo tiempo, dupliquen el inventario.
                $recepciones = RecepcionCampo::whereIn('id', $request->recepcion_ids)
                    ->where('estado_clasificacion', 'pendiente')
                    ->lockForUpdate() 
                    ->get();

                if ($recepciones->isEmpty()) {
                    throw new Exception("Los costales seleccionados ya fueron procesados por otro operario.");
                }

                // 2. Iterar y crear los movimientos
                foreach ($recepciones as $recepcion) {
                    MovimientoClasificacion::create([
                        'operario_id'        => $operarioId,
                        'recepcion_campo_id' => $recepcion->id,
                        'contenedor_id'      => $contenedor->id,
                        'fecha_movimiento'   => $fechaHoy,
                        'kilos_asignados'    => $recepcion->peso_neto,
                        'observaciones'      => 'Asignación masiva desde lote'
                    ]);

                    // 3. Cambiar estado de la recepción a "en_proceso" (o clasificado)
                    $recepcion->estado_clasificacion = 'en_proceso';
                    $recepcion->save();

                    // Sumar al total de este batch
                    $totalKilosNuevos += $recepcion->peso_neto;
                }

                // 4. Actualizar el acumulado del contenedor
                $contenedor->kilos_acumulados += $totalKilosNuevos;
                $contenedor->save();
            });

            Log::info("Asignación masiva exitosa. Contenedor: {$request->contenedor_id}, Costales: " . count($request->recepcion_ids));

            // Retornamos a la misma orden para que siga asignando lo que falte
            return redirect()->route('movimientos.create', ['orden_id' => $request->orden_id])
                ->with('success', 'Costales asignados correctamente a la tolva.');

        } catch (Exception $e) {
            Log::error("Error en store masivo de movimientos: " . $e->getMessage());
            return back()->withInput()->with('error', 'No se pudo completar la asignación: ' . $e->getMessage());
        }
    }

    /**
     * Muestra el formulario para editar un movimiento.
     */
    public function edit(string $id)
    {
        try {
            $movimiento = MovimientoClasificacion::findOrFail($id);
            $operarios = User::all();
            $recepciones = RecepcionCampo::all();
            $contenedores = Contenedor::where('estado', 'abierta')
                ->orWhere('id', $movimiento->contenedor_id) // Incluir el actual aunque esté cerrado
                ->get();

            return view('movimientos.edit', compact('movimiento', 'operarios', 'recepciones', 'contenedores'));

        } catch (Exception $e) {
            Log::error("Error al cargar edición del movimiento {$id}: {$e->getMessage()}");
            return redirect()->route('movimientos.index')
                ->with('error', 'No se encontró el movimiento o hubo un error al cargar.');
        }
    }

    /**
     * Actualiza el movimiento y re-calcula los saldos si los kilos cambiaron.
     */
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'operario_id'        => 'nullable|exists:users,id',
            'recepcion_campo_id' => 'required|exists:recepciones_campo,id',
            'contenedor_id'      => 'required|exists:contenedores,id',
            'fecha_movimiento'   => 'required|date',
            'kilos_asignados'    => 'required|numeric|min:0.1',
            'observaciones'      => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($validatedData, $id) {
                $movimiento = MovimientoClasificacion::findOrFail($id);
                
                // Calculamos la diferencia de kilos para ajustar el contenedor
                $diferenciaKilos = $validatedData['kilos_asignados'] - $movimiento->kilos_asignados;

                // 1. Actualizamos el movimiento
                $movimiento->update($validatedData);

                // 2. Si los kilos cambiaron, ajustamos el contenedor
                if ($diferenciaKilos != 0) {
                    $contenedor = Contenedor::findOrFail($validatedData['contenedor_id']);
                    $contenedor->kilos_acumulados += $diferenciaKilos;
                    $contenedor->save();
                }

                // Nota: Si permites cambiar de Contenedor A a Contenedor B en el edit, 
                // la lógica de la transacción debería restar los kilos originales del Contenedor A 
                // y sumar los nuevos kilos al Contenedor B. Por simplicidad, aquí asumimos que es el mismo contenedor.
            });

            Log::info("Movimiento {$id} actualizado con éxito.");
            
            return redirect()->route('movimientos.index')
                ->with('success', 'El movimiento fue actualizado correctamente.');

        } catch (Exception $e) {
            Log::error("Error al actualizar movimiento {$id}: {$e->getMessage()}", [
                'input' => $request->except(['_token'])
            ]);

            return back()->withInput()
                ->with('error', 'Ocurrió un error al actualizar: ' . $e->getMessage());
        }
    }

    /**
     * Elimina (Soft Delete) el movimiento y revierte los kilos del contenedor.
     */
    public function destroy(string $id)
    {
        try {
            DB::transaction(function () use ($id) {
                $movimiento = MovimientoClasificacion::findOrFail($id);

                // 1. Revertimos los kilos del contenedor (como si el movimiento nunca hubiera pasado)
                $contenedor = Contenedor::findOrFail($movimiento->contenedor_id);
                $contenedor->kilos_acumulados -= $movimiento->kilos_asignados;
                $contenedor->save();

                // 2. Ejecutamos el Soft Delete
                $movimiento->delete();
            });

            Log::info("Movimiento {$id} eliminado lógicamente y kilos revertidos.");

            return redirect()->route('movimientos.index')
                ->with('success', 'El registro fue eliminado y los kilos fueron descontados del contenedor.');

        } catch (Exception $e) {
            Log::error("Error al eliminar movimiento {$id}: {$e->getMessage()}");
            return back()->with('error', 'Ocurrió un error al intentar eliminar el registro.');
        }
    }
}