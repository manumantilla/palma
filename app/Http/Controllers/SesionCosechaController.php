<?php

namespace App\Http\Controllers;

use App\Models\SesionCosecha;
use App\Models\OrdenCosecha;
use App\Models\EventoCampo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SesionCosechaController extends Controller
{

    public function index(Request $request)
    {
        // 1. Filtros validados del Request
        $filters = $request->only([
            'estado',
            'responsable_id',
            'orden_cosecha_id',
            'evento_campo_id',
            'fecha_desde',
            'fecha_hasta',
        ]);

        // 2. Consulta optimizada con Eager Loading
        $sesiones = SesionCosecha::with([
            'ordenCosecha:id,variedad_requerida,lote_cultivo_id,cantidad_planificada_kg',
            'ordenCosecha.loteCultivo:id,nombre_lote',
            'eventoCampo:id,fecha_programada,prioridad',
            'responsable:id,name',
        ])
        ->withCount('recepcionesCampo') // Asume relación hasMany en el modelo
        ->filter($filters)
        ->orderBy('fecha', 'desc')
        ->orderBy('id', 'desc')
        ->paginate(12)
        ->withQueryString();

        // 3. Catálogos para los selectores de la vista
        $responsables = User::select('id', 'name')->get();
        $ordenes = OrdenCosecha::select('id', 'variedad_requerida')->get();

        return view('sesiones_cosecha.index', compact('sesiones', 'responsables', 'ordenes'));
    }
    
    public function create()
    {
        $ordenes = OrdenCosecha::where('estado', '!=', ['completada','cancelada'])->get();
        $eventos = EventoCampo::where('estado', 'activo')->get();
        $responsables = User::all();

        return view('sesiones_cosecha.create', compact('ordenes', 'eventos', 'responsables'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'orden_cosecha_id' => 'required|exists:ordenes_cosecha,id',
            'evento_campo_id' => 'required|exists:eventos_campo,id',
            'responsable_id' => 'required|exists:users,id',
            'fecha' => 'required|date',
            'estado' => 'required|in:abierta,cerrada',
            'meta_kg_dia' => 'nullable|numeric|min:0',
            'numero_recolectores' => 'nullable|integer|min:0',
            'hora_inicio' => 'nullable|date_format:H:i',
            'hora_fin' => 'nullable|date_format:H:i|after:hora_inicio',
            'total_recolectado_kg' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $sesion = SesionCosecha::create($validated);

            Log::info('Sesión de cosecha creada exitosamente', [
                'sesion_id' => $sesion->id,
                'orden_cosecha_id' => $sesion->orden_cosecha_id,
                'responsable_id' => $sesion->responsable_id,
                'fecha' => $sesion->fecha,
                'user_id' => auth()->id()
            ]);

            DB::commit();

            return redirect()
                ->route('sesiones-cosecha.index')
                ->with('success', 'Sesión de cosecha creada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al crear sesión de cosecha', [
                'error' => $e->getMessage(),
                'data' => $validated,
                'user_id' => auth()->id()
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Ocurrió un error al crear la sesión de cosecha. Por favor, intente nuevamente.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $sesion = SesionCosecha::with([
                'ordenCosecha',
                'eventoCampo',
                'responsable'
            ])->findOrFail($id);

            Log::info('Visualización de sesión de cosecha', [
                'sesion_id' => $sesion->id,
                'user_id' => auth()->id()
            ]);

            return view('sesiones-cosecha.show', compact('sesion'));

        } catch (\Exception $e) {
            Log::error('Error al mostrar sesión de cosecha', [
                'error' => $e->getMessage(),
                'sesion_id' => $id,
                'user_id' => auth()->id()
            ]);

            return redirect()
                ->route('sesiones-cosecha.index')
                ->with('error', 'La sesión de cosecha solicitada no existe o ha sido eliminada.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $sesion = SesionCosecha::findOrFail($id);
            $ordenes = OrdenCosecha::where('estado', 'activa')->get();
            $eventos = EventoCampo::where('estado', 'activo')->get();
            $responsables = User::where('role', 'responsable_campo')->get();

            return view('sesiones-cosecha.edit', compact('sesion', 'ordenes', 'eventos', 'responsables'));

        } catch (\Exception $e) {
            Log::error('Error al editar sesión de cosecha', [
                'error' => $e->getMessage(),
                'sesion_id' => $id,
                'user_id' => auth()->id()
            ]);

            return redirect()
                ->route('sesiones-cosecha.index')
                ->with('error', 'No se pudo cargar el formulario de edición.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'orden_cosecha_id' => 'required|exists:ordenes_cosecha,id',
            'evento_campo_id' => 'required|exists:eventos_campo,id',
            'responsable_id' => 'required|exists:users,id',
            'fecha' => 'required|date',
            'estado' => 'required|in:abierta,cerrada',
            'meta_kg_dia' => 'nullable|numeric|min:0',
            'numero_recolectores' => 'nullable|integer|min:0',
            'hora_inicio' => 'nullable|date_format:H:i',
            'hora_fin' => 'nullable|date_format:H:i|after:hora_inicio',
            'total_recolectado_kg' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $sesion = SesionCosecha::findOrFail($id);
            $oldData = $sesion->toArray();
            $sesion->update($validated);

            Log::info('Sesión de cosecha actualizada exitosamente', [
                'sesion_id' => $sesion->id,
                'old_data' => $oldData,
                'new_data' => $validated,
                'user_id' => auth()->id()
            ]);

            DB::commit();

            return redirect()
                ->route('sesiones-cosecha.index')
                ->with('success', 'Sesión de cosecha actualizada exitosamente.');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();

            Log::warning('Intento de actualizar sesión inexistente', [
                'sesion_id' => $id,
                'user_id' => auth()->id()
            ]);

            return redirect()
                ->route('sesiones-cosecha.index')
                ->with('error', 'La sesión de cosecha que intenta actualizar no existe.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al actualizar sesión de cosecha', [
                'error' => $e->getMessage(),
                'sesion_id' => $id,
                'data' => $validated,
                'user_id' => auth()->id()
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Ocurrió un error al actualizar la sesión de cosecha. Por favor, intente nuevamente.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $sesion = SesionCosecha::findOrFail($id);
            $sesionData = $sesion->toArray();
            $sesion->delete();

            Log::info('Sesión de cosecha eliminada exitosamente', [
                'sesion_id' => $id,
                'deleted_data' => $sesionData,
                'user_id' => auth()->id()
            ]);

            DB::commit();

            return redirect()
                ->route('sesiones-cosecha.index')
                ->with('success', 'Sesión de cosecha eliminada exitosamente.');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();

            Log::warning('Intento de eliminar sesión inexistente', [
                'sesion_id' => $id,
                'user_id' => auth()->id()
            ]);

            return redirect()
                ->route('sesiones-cosecha.index')
                ->with('error', 'La sesión de cosecha que intenta eliminar no existe.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al eliminar sesión de cosecha', [
                'error' => $e->getMessage(),
                'sesion_id' => $id,
                'user_id' => auth()->id()
            ]);

            return redirect()
                ->route('sesiones-cosecha.index')
                ->with('error', 'Ocurrió un error al eliminar la sesión de cosecha. Por favor, intente nuevamente.');
        }
    }
}