<?php

namespace App\Http\Controllers;

use App\Models\SesionCosecha;
use App\Models\OrdenCosecha;
use App\Models\User;
use App\Models\EventoCampo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SesionCosechaController extends Controller
{
    /**
     * R - READ (Index): Listar con filtros aplicados
     */
    public function index(Request $request)
    {
        $sesiones = SesionCosecha::with(['ordenCosecha', 'eventoCampo', 'responsable'])
            ->filter($request->all())
            ->orderBy('fecha', 'desc')
            ->paginate(15)
            ->withQueryString();

        $responsables = User::orderBy('name')->get();

        return view('sesiones_cosecha.index', compact('sesiones', 'responsables'));
    }

    /**
     * C - CREATE: Formulario de creación amarrado a la Orden
     */
    public function create(OrdenCosecha $ordenCosecha)
    {
        $responsables = User::orderBy('name')->get();
        $eventosCampo = EventoCampo::whereIn('estado', ['Pendiente','En Proceso'])->orderBy('id', 'desc')->get(); 

        return view('sesiones_cosecha.create', compact('ordenCosecha', 'responsables', 'eventosCampo'));
    }

    /**
     * C - STORE: Guardar en la Base de Datos central
     */
    public function store(Request $request, OrdenCosecha $ordenCosecha)
    {
        $data = $request->validate([
            'evento_campo_id'     => 'required|exists:eventos_campo,id',
            'responsable_id'      => 'required|exists:users,id',
            'fecha'               => 'required|date',
            'meta_kg_dia'         => 'nullable|numeric|min:0',
            'numero_recolectores' => 'nullable|integer|min:0',
            'hora_inicio'         => 'nullable|date_format:H:i',
        ]);

        // Inyectamos la orden del binding y metadatos online
        $data['orden_cosecha_id'] = $ordenCosecha->id;
        $data['estado'] = 'abierta';
        $data['synced_at'] = now(); // Nació en la web, ya está sincronizado

        // El Trait HasUuids del modelo generará el ID UUID automáticamente aquí
        $sesion = SesionCosecha::create($data);

        return redirect()
            ->route('sesiones-cosecha.show', $sesion->id)
            ->with('success', 'Sesión de cosecha abierta correctamente.');
    }

    /**
     * R - READ (Show): Ver detalle de una sesión específica por UUID
     */
    public function show(SesionCosecha $sesionCosecha)
    {
        // Cargamos relaciones y pesajes asociados si los necesitas en la vista
        $sesionCosecha->load(['ordenCosecha', 'eventoCampo', 'responsable']);
        
        return view('sesiones_cosecha.show', compact('sesionCosecha'));
    }

    /**
     * U - UPDATE (Edit): Formulario de edición
     */
    public function edit(SesionCosecha $sesionCosecha)
    {
        $responsables = User::orderBy('name')->get();
        $eventosCampo = EventoCampo::orderBy('id', 'desc')->get();

        return view('sesiones_cosecha.edit', compact('sesionCosecha', 'responsables', 'eventosCampo'));
    }

    /**
     * U - UPDATE (Update): Procesar los cambios en el servidor
     */
    public function update(Request $request, SesionCosecha $sesionCosecha)
    {
        $data = $request->validate([
            'evento_campo_id'     => 'required|exists:eventos_campo,id',
            'responsable_id'      => 'required|exists:users,id',
            'fecha'               => 'required|date',
            'estado'              => 'required|in:abierta,cerrada',
            'meta_kg_dia'         => 'nullable|numeric|min:0',
            'numero_recolectores' => 'nullable|integer|min:0',
            'hora_inicio'         => 'nullable|date_format:H:i:s,H:i',
            'hora_fin'            => 'required_if:estado,cerrada|nullable|date_format:H:i:s,H:i',
        ]);

        // Como se editó en entorno web, actualizamos marcas de sincronización
        $data['synced_at'] = now();

        $sesionCosecha->update($data);

        return redirect()
            ->route('sesiones_cosecha.show', $sesionCosecha->id)
            ->with('success', 'Sesión de cosecha actualizada correctamente.');
    }


    public function destroy(SesionCosecha $sesionCosecha)
    {
        // Protección: Si la sesión ya tiene kilos recolectados, es mejor no borrarla directamente
        if ($sesionCosecha->total_recolectado_kg > 0) {
            return redirect()
                ->back()
                ->with('error', 'No se puede eliminar una sesión que ya registra kilos recolectados.');
        }

        $sesionCosecha->delete();

        return redirect()
            ->route('sesiones_cosecha.index')
            ->with('success', 'La sesión de cosecha fue eliminada.');
    }
}