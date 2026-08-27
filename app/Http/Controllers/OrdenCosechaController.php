<?php

namespace App\Http\Controllers;

use App\Models\SesionCosecha;
use App\Models\CicloProductivo;
use App\Models\LoteZonaManejo;
use App\Models\OrdenCosecha;
use App\Models\EventoCampo;
use App\Models\User;
use App\Models\Lote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrdenCosechaController extends Controller
{
    public function index(Request $request)
    {
        $query = OrdenCosecha::with([
            'cliente:id,name',
            'responsable:id,name',
            //Cargamos relacon nombre del cultivo
            'cicloProductivo:id',
            'loteCultivo:id,nombre_lote',
            'loteZona:id,nombre_zona'
        ]);

        // Filtro por Estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtro por Lote de Cultivo
        if ($request->filled('lote_cultivo_id')) {
            $query->where('lote_cultivo_id', $request->lote_cultivo_id);
        }

        // Filtro por Ciclo Productivo
        if ($request->filled('ciclo_productivo_id')) {
            $query->where('ciclo_productivo_id', $request->ciclo_productivo_id);
        }

        // Búsqueda por palabra clave (Variedad o Notas)
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('variedad_requerida', 'ILIKE', "%{$buscar}%")
                  ->orWhere('notas', 'ILIKE', "%{$buscar}%");
            });
        }

        // Filtro por Rango de Fechas (Fecha Programada)
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_programada', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_programada', '<=', $request->fecha_hasta);
        }

        // Ordenamiento por fecha más reciente
        $ordenes = $query->orderBy('fecha_programada', 'desc')
            ->paginate(12)
            ->withQueryString(); // Conserva los filtros en los enlaces de paginación

        // Catálogos para poblar los selects del filtro
        $lotes = Lote::select('id', 'nombre_lote')->get();
        $ciclos = CicloProductivo::where('estado', '!=', 'concluido')->select('id')->get();

        return view('ordenes_cosecha.index', compact('ordenes', 'lotes', 'ciclos'));
    }

    public function create(CicloProductivo $ciclo)
    {
        $clientes = User::orderBy('name')->get(); 
        $responsables = User::orderBy('name')->get();
        $loteId = $ciclo->lote_id;
        // 2. Evaluamos si el lote existe para evitar el error "Property of non-object"
        // y añadimos ->get() para traer la lista de zonas reales a la vista
        $zonas = $loteId 
            ? LoteZonaManejo::where('lote_id', $loteId)->get() 
            : collect(); // Si no hay lote, mandamos una colección vacía para que la vista no falle
        $lote = $ciclo->lote; 
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
            'lote_zona_id'            => 'nullable|exists:lotes_zonas_manejo,id',
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
        $ciclos = CicloProductivo::orderBy('nombre_campana')->get();
        $lotes = Lote::orderBy('nombre_lote')->get();
        $zonas = LoteZonaManejo::orderBy('nombre_zona')->get();

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