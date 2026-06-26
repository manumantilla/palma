<?php

namespace App\Http\Controllers;

use App\Models\OrdenCosecha;
use App\Models\User;
use App\Models\CicloProductivo;
use App\Models\Lote;
use App\Models\LoteZonaManejo;
use Illuminate\Http\Request;

class OrdenCosechaController extends Controller
{
    /**
     * Listar las órdenes de cosecha paginadas.
     */
    public function index()
    {
        $ordenes = OrdenCosecha::with(['cliente', 'responsable', 'cicloProductivo', 'loteCultivo', 'loteZona'])
            ->orderBy('fecha_programada', 'desc')
            ->paginate(15);

        return view('ordenes_cosecha.index', compact('ordenes'));
    }

    /**
     * Mostrar el formulario para crear una nueva orden.
     */


public function create(CicloProductivo $ciclo)
{
    $clientes = User::orderBy('name')->get(); 
    $responsables = User::orderBy('name')->get();
    $lote = $ciclo->lote_id;
    // 2. Evaluamos si el lote existe para evitar el error "Property of non-object"
    // y añadimos ->get() para traer la lista de zonas reales a la vista
    $zonas = $lote 
        ? LoteZonaManejo::where('lote_id', $lote->id)->get() 
        : collect(); // Si no hay lote, mandamos una colección vacía para que la vista no falle

    // NOTA: Quité 'ciclos' del compact ya que pasas '$ciclo' individualmente
    return view('ordenes_cosecha.create', compact('ciclo', 'clientes', 'responsables', 'lote', 'zonas'));
}
    /**
     * Guardar una orden de cosecha en la base de datos.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'cliente_id'              => 'required|exists:users,id',
            'ciclo_productivo_id'     => 'required|exists:ciclos_productivos,id',
            'lote_cultivo_id'         => 'nullable|exists:lotes,id',
            'lote_zona_id'            => 'nullable|exists:lotes_zona_manejo,id',
            'fecha_programada'        => 'required|date',
            'fecha_entrega'           => 'required|date|after_or_equal:fecha_programada',
            'responsable_id'          => 'nullable|exists:users,id',
            'cantidad_solicitada_kg'  => 'nullable|numeric|min:0',
            'variedad_requerida'      => 'nullable|string|max:255',
            'cantidad_planificada_kg' => 'nullable|numeric|min:0',
            'cantidad_recolectada_kg' => 'nullable|numeric|min:0',
            'fecha_inicio'            => 'nullable|date',
            'fecha_fin'               => 'nullable|date|after_or_equal:fecha_inicio',
            'estado'                  => 'required|in:borrador,confirmada,en_proceso,completada,cancelada',
            'notes'                   => 'nullable|string',
        ]);

        OrdenCosecha::create($data);

        return redirect()->route('ordenes_cosecha.index')
            ->with('success', 'Orden de cosecha creada exitosamente.');
    }

    /**
     * Mostrar el detalle de una orden específica.
     */
    public function show(OrdenCosecha $ordenesCosecha) // El binding se acopla al nombre del parámetro en la ruta
    {
        $ordenesCosecha->load(['cliente', 'responsable', 'cicloProductivo', 'loteCultivo', 'loteZona']);
        return view('ordenes_cosecha.show', compact('ordenesCosecha'));
    }

    /**
     * Mostrar el formulario para editar una orden.
     */
    public function edit(OrdenCosecha $ordenesCosecha)
    {
        $clientes = User::orderBy('name')->get();
        $responsables = User::orderBy('name')->get();
        $ciclos = CicloProductivo::orderBy('nombre')->get();
        $lotes = Lote::orderBy('nombre')->get();
        $zonas = LoteZonaManejo::orderBy('nombre')->get();

        return view('ordenes_cosecha.edit', compact('ordenesCosecha', 'clientes', 'responsables', 'ciclos', 'lotes', 'zonas'));
    }

    /**
     * Actualizar una orden en la base de datos.
     */
    public function update(Request $request, OrdenCosecha $ordenesCosecha)
    {
        $data = $request->validate([
            'cliente_id'              => 'required|exists:users,id',
            'ciclo_productivo_id'     => 'required|exists:ciclos_productivos,id',
            'lote_cultivo_id'         => 'nullable|exists:lotes,id',
            'lote_zona_id'            => 'nullable|exists:lotes_zona_manejo,id',
            'fecha_programada'        => 'required|date',
            'fecha_entrega'           => 'required|date|after_or_equal:fecha_programada',
            'responsable_id'          => 'nullable|exists:users,id',
            'cantidad_solicitada_kg'  => 'nullable|numeric|min:0',
            'variedad_requerida'      => 'nullable|string|max:255',
            'cantidad_planificada_kg' => 'nullable|numeric|min:0',
            'cantidad_recolectada_kg' => 'nullable|numeric|min:0',
            'fecha_inicio'            => 'nullable|date',
            'fecha_fin'               => 'nullable|date|after_or_equal:fecha_inicio',
            'estado'                  => 'required|in:borrador,confirmada,en_proceso,completada,cancelada',
            'notes'                   => 'nullable|string',
        ]);

        $ordenesCosecha->update($data);

        return redirect()->route('ordenes_cosecha.index')
            ->with('success', 'Orden de cosecha actualizada correctamente.');
    }

    /**
     * Eliminar una orden de la base de datos.
     */
    public function destroy(OrdenCosecha $ordenesCosecha)
    {
        $ordenesCosecha->delete();

        return redirect()->route('ordenes_cosecha.index')
            ->with('success', 'Orden de cosecha eliminada correctamente.');
    }
}