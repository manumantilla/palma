<?php

namespace App\Http\Controllers;

use App\Models\Contenedor;
use App\Models\SesionCosecha;
use App\Models\OrdenCosecha;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class ContenedorController extends Controller
{
    /**
     * Listar contenedores con filtros del Request (Retorna Vista Blade)
     */
    public function index(Request $request)
    {
        try {
            $contenedores = Contenedor::with(['sesionCosecha', 'cliente', 'ordenPedido'])
                ->filtrar($request->all())
                ->orderBy('created_at', 'desc')
                ->paginate(15)
                ->withQueryString();

            return view('contenedores.index', compact('contenedores'));
            
        } catch (Exception $e) {
            Log::error("Error en ContenedorController@index: " . $e->getMessage());
            abort(500, 'Error al cargar el inventario de contenedores.');
        }
    }

    /**
     * Mostrar formulario para crear un contenedor nuevo en la mesa de clasificación
     */
    public function create()
    {
        $sesionesActivas = SesionCosecha::where('estado', 'abierta')->get();
        $ordenesConfirmadas = OrdenCosecha::whereIn('estado', ['confirmada', 'en_proceso'])->get();
        $clientes = User::all(); // Filtrar por rol cliente si aplica en tu app

        return view('contenedores.create', compact('sesionesActivas', 'ordenesConfirmadas', 'clientes'));
    }

    /**
     * Registrar/Abrir un nuevo contenedor (Tolva, Palet, Canastilla Macro)
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'sesion_id' => 'required|exists:sesiones_cosecha,id',
                'cliente_id' => 'nullable|exists:users,id',
                'orden_pedido_id' => 'nullable|exists:ordenes_cosecha,id',
                'nombre' => 'required|string|max:255',
                'tipo_destino' => 'required|in:exportacion,mercado_local,industria,consumo_interno,descarte',
                'variedad' => 'nullable|string|max:100',
                'calidad' => 'nullable|in:extra,primera,segunda,industria,descarte',
                'calibre_talla' => 'nullable|in:pequeño,mediano,grande,jumbo',
                'peso_tara' => 'required|numeric|min:0',
            ]);

            // Al crearse por primera vez, inicia vacío con los valores por defecto
            $data['kilos_acumulados'] = 0;
            $data['peso_total'] = $data['peso_tara'];
            $data['estado'] = 'abierta';

            Contenedor::create($data);

            return redirect()->route('contenedores.index')
                ->with('success', 'Contenedor/Tolva asignado y abierto en la línea de clasificación.');

        } catch (Exception $e) {
            Log::error("Error en ContenedorController@store: " . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'No se pudo dar de alta el contenedor. Revisa los datos.');
        }
    }

    /**
     * Ver el detalle de un contenedor (Ideal para ver qué costales aportaron a esta tolva)
     */
    public function show($id)
    {
        try {
            // Traemos el contenedor y cargamos los movimientos de clasificación junto con el costal y el obrero de origen
            $contenedor = Contenedor::with([
                'sesionCosecha', 
                'cliente', 
                'ordenPedido', 
                'movimientosClasificacion.recepcionCampo.trabajador'
            ])->findOrFail($id);

            return view('contenedores.show', compact('contenedor'));

        } catch (Exception $e) {
            return redirect()->route('contenedores.index')
                ->with('error', 'El contenedor solicitado no existe o fue removido.');
        }
    }

    /**
     * Actualizar pesos o despachar el contenedor (Modificaciones directas del Frontend)
     */
    public function update(Request $request, $id)
    {
        try {
            $contenedor = Contenedor::findOrFail($id);

            $data = $request->validate([
                'estado' => 'nullable|in:abierta,cerrada,despachada',
                'calidad' => 'nullable|in:extra,primera,segunda,industria,descarte',
                'calibre_talla' => 'nullable|in:pequeño,mediano,grande,jumbo',
                'kilos_acumulados' => 'nullable|numeric|min:0',
                'peso_tara' => 'nullable|numeric|min:0',
                'peso_total' => 'nullable|numeric|min:0' // Recibido directamente calculado/forzado del frontend
            ]);

            $contenedor->update($data);

            // Lógica de negocio complementaria: Si el contenedor se despacha y está atado a una orden,
            // sumamos estos kilos directamente a la cantidad recolectada de la orden de cosecha macro.
            if (($data['estado'] ?? null) === 'despachada' && $contenedor->orden_pedido_id && $contenedor->kilos_acumulados > 0) {
                $orden = $contenedor->ordenPedido;
                $orden->increment('cantidad_recolectada_kg', $contenedor->kilos_acumulados);
                
                // Si con esto la orden cumple la meta, se podría cambiar su estado a completada de forma automática
                if ($orden->cantidad_recolectada_kg >= $orden->cantidad_planificada_kg) {
                    $orden->update(['estado' => 'completada']);
                }
            }

            return redirect()->route('contenedores.show', $contenedor->id)
                ->with('success', 'Datos del contenedor y estado de báscula actualizados con éxito.');

        } catch (Exception $e) {
            Log::error("Error en ContenedorController@update en ID {$id}: " . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al procesar la actualización del contenedor en báscula.');
        }
    }
}