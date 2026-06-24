<?php

namespace App\Http\Controllers;

use App\Models\SesionCosecha;
use App\Models\OrdenCosecha;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class SesionCosechaController extends Controller
{
    /**
     * Listar sesiones de cosecha filtradas por Request (Retorna Vista)
     */
    public function index(Request $request)
    {
        try {
            // Recibe los filtros de la URL/Formulario y pagina los resultados
            $sesiones = SesionCosecha::with(['ordenCosecha.loteCultivo', 'responsable'])
                ->filtrar($request->all())
                ->orderBy('fecha', 'desc')
                ->paginate(15)
                ->withQueryString(); // Mantiene los filtros activos al cambiar de página

            // Retorna la vista de Blade pasando los datos
            return view('sesiones.index', compact('sesiones'));

        } catch (Exception $e) {
            Log::error("Error al listar sesiones de cosecha: " . $e->getMessage());
            
            // Aborta con un error 500 personalizado o redirige con un error
            abort(500, 'Error interno al procesar las sesiones de cosecha.');
        }
    }

    /**
     * Mostrar formulario de creación (Retorna Vista)
     */
    public function create()
    {
        // Necesitamos listar órdenes activas y usuarios para los selects del formulario
        $ordenes = OrdenCosecha::whereIn('estado', ['confirmada', 'en_proceso'])->get();
        $responsables = User::all(); // Puedes filtrarlo por roles si tienes (ej: operarios/supervisores)

        return view('sesiones.create', compact('ordenes', 'responsables'));
    }

    /**
     * Guardar la sesión de cosecha (Redirección con Flash Session)
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'orden_cosecha_id' => 'required|exists:ordenes_cosecha,id',
                'responsable_id' => 'required|exists:users,id',
                'fecha' => 'required|date',
                'meta_kg_dia' => 'nullable|numeric|min:0',
                'numero_recolectores' => 'nullable|integer|min:1',
                'hora_inicio' => 'nullable',
                'hora_fin' => 'nullable',
            ]);

            SesionCosecha::create($data);

            return redirect()->route('sesiones.index')
                ->with('success', 'La sesión de cosecha se abrió correctamente en el sistema.');

        } catch (Exception $e) {
            Log::error("Error al crear sesión de cosecha: " . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'No se pudo abrir la sesión de cosecha. Verifica los datos.');
        }
    }

    /**
     * Ver el detalle de una sesión específica (Retorna Vista)
     */
    public function show($id)
    {
        try {
            $sesion = SesionCosecha::with(['ordenCosecha', 'responsable'])->findOrFail($id);

            return view('sesiones.show', compact('sesion'));

        } catch (Exception $e) {
            return redirect()->route('sesiones.index')
                ->with('error', 'La sesión de cosecha solicitada no existe.');
        }
    }

    /**
     * Actualizar la sesión (Cierre de sesión o ingreso de kilos en báscula)
     */
    public function update(Request $request, $id)
    {
        try {
            $sesion = SesionCosecha::findOrFail($id);

            $data = $request->validate([
                'estado' => 'required|in:abierta,cerrada',
                'numero_recolectores' => 'nullable|integer|min:1',
                'hora_fin' => 'nullable',
                'total_recolectado_kg' => 'nullable|numeric|min:0'
            ]);

            $sesion->update($data);

            // Lógica de negocio opcional: Si se cierra la sesión, podríamos acumular los kilos a la orden madre
            if ($data['estado'] === 'cerrada' && $sesion->total_recolectado_kg > 0) {
                $orden = $sesion->ordenCosecha;
                $orden->increment('cantidad_recolectada_kg', $sesion->total_recolectado_kg);
            }

            return redirect()->route('sesiones.show', $sesion->id)
                ->with('success', 'Sesión de cosecha actualizada y guardada con éxito.');

        } catch (Exception $e) {
            Log::error("Error al actualizar sesión ID {$id}: " . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Ocurrió un error inesperado al intentar actualizar la sesión.');
        }
    }
}