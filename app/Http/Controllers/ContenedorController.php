<?php
namespace App\Http\Controllers;

use App\Models\Contenedor;
use App\Models\SesionCosecha;
use App\Models\OrdenCosecha;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Exception;

class ContenedorController extends Controller
{
    /**
     * Display a listing of the resource with filters applied.
     */
    public function index(Request $request)
    {
        try {
            // El Scope Filtrar aplica las condiciones desde el Query String
            $contenedores = Contenedor::with(['sesionCosecha', 'cliente', 'ordenPedido'])
                ->filtrar($request->all())
                ->latest()
                ->paginate(15)
                ->appends($request->all());

            return view('contenedores.index', compact('contenedores'));

        } catch (Exception $e) {
            Log::error("Error al listar contenedores: {$e->getMessage()}", [
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Ocurrió un error al cargar el listado de contenedores.');
        }
    }


public function create(string $sesion_cosecha_id)
{
    try {
        $clientes = User::all(); // ALGO TRANSITORIO, HASTA DEFINIR USUARIOS Y SUS ROLES     
        
        $sesion = SesionCosecha::findOrFail($sesion_cosecha_id);
        
        // CORRECCIÓN AQUÍ: Usamos la llave foránea 'sesion_cosecha_id', no 'id'
        
        return view('contenedores.create', compact('clientes', 'sesion'));

    } catch (Exception $e) {
        Log::error("Error al cargar formulario de creación: {$e->getMessage()}");

        return redirect()->route('contenedores.index')
            ->with('error', 'No se pudieron cargar los datos necesarios para crear el contenedor.');
    }
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'sesion_id'        => 'required|exists:sesiones_cosecha,id',
            'cliente_id'       => 'nullable|exists:users,id',
            'orden_pedido_id'  => 'nullable|exists:ordenes_cosecha,id',
            'nombre'           => 'required|string|max:255',
            'estado'           => ['required', Rule::in(['abierta', 'cerrada', 'despachada'])],
            'variedad'         => 'nullable|string|max:100',
            'calidad'          => ['nullable', Rule::in(['extra', 'primera', 'segunda', 'industria', 'descarte'])],
            'tipo_destino'     => ['required', Rule::in(['exportacion', 'mercado_local', 'industria', 'consumo_interno', 'descarte'])],
            'calibre_talla'    => ['nullable', Rule::in(['pequeño', 'mediano', 'grande', 'jumbo'])],
            'peso_tara'        => 'required|numeric|min:0',
            'kilos_acumulados' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::transaction(function () use ($validatedData) {
                // El modelo calcula automáticamente el 'peso_total' y asigna el UUID en sus eventos de boot
                Contenedor::create($validatedData);
            });

            return redirect()->route('contenedores.index')
                ->with('success', 'El contenedor fue registrado correctamente.');

        } catch (Exception $e) {
            Log::error("Error al almacenar contenedor: {$e->getMessage()}", [
                'input' => $request->except(['_token'])
            ]);

            return back()->withInput()
                ->with('error', 'Ocurrió un error al guardar el contenedor: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $contenedor = Contenedor::with([
                'sesionCosecha', 
                'cliente', 
                'ordenPedido', 
                'movimientosClasificacion.operario', 
                'mermas'
            ])->findOrFail($id);

            return view('contenedores.show', compact('contenedor'));

        } catch (Exception $e) {
            Log::error("Error al visualizar contenedor {$id}: {$e->getMessage()}");

            return redirect()->route('contenedores.index')
                ->with('error', 'El contenedor solicitado no existe o no se pudo cargar.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $contenedor = Contenedor::findOrFail($id);
            $sesiones = SesionCosecha::all();
            $clientes = User::where('role', 'cliente')->get();
            $ordenes = OrdenCosecha::all();

            return view('contenedores.edit', compact('contenedor', 'sesiones', 'clientes', 'ordenes'));

        } catch (Exception $e) {
            Log::error("Error al cargar edición del contenedor {$id}: {$e->getMessage()}");

            return redirect()->route('contenedores.index')
                ->with('error', 'No se encontró el contenedor que intenta editar.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'sesion_id'        => 'required|exists:sesiones_cosecha,id',
            'cliente_id'       => 'nullable|exists:users,id',
            'orden_pedido_id'  => 'nullable|exists:ordenes_cosecha,id',
            'nombre'           => 'required|string|max:255',
            'estado'           => ['required', Rule::in(['abierta', 'cerrada', 'despachada'])],
            'variedad'         => 'nullable|string|max:100',
            'calidad'          => ['nullable', Rule::in(['extra', 'primera', 'segunda', 'industria', 'descarte'])],
            'tipo_destino'     => ['required', Rule::in(['exportacion', 'mercado_local', 'industria', 'consumo_interno', 'descarte'])],
            'calibre_talla'    => ['nullable', Rule::in(['pequeño', 'mediano', 'grande', 'jumbo'])],
            'peso_tara'        => 'required|numeric|min:0',
            'kilos_acumulados' => 'required|numeric|min:0',
            'kilos_merma_acumulada' => 'required|numeric|min:0',
        ]);

        try {
            DB::transaction(function () use ($validatedData, $id) {
                $contenedor = Contenedor::findOrFail($id);
                
                // Actualizamos marca de tiempo cliente si viene desde un entorno offline resincronizado
                $validatedData['client_updated_at'] = now();

                $contenedor->update($validatedData);
            });

            return redirect()->route('contenedores.index')
                ->with('success', 'El contenedor fue actualizado con éxito.');

        } catch (Exception $e) {
            Log::error("Error al actualizar contenedor {$id}: {$e->getMessage()}", [
                'input' => $request->except(['_token', '_method'])
            ]);

            return back()->withInput()
                ->with('error', 'Error al intentar actualizar los datos del contenedor.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::transaction(function () use ($id) {
                $contenedor = Contenedor::findOrFail($id);

                // Regla de Negocio Poscosecha: No permitir eliminar si ya tiene despachos o mermas asociadas
                if ($contenedor->estado === 'despachada') {
                    throw new Exception("No es posible eliminar un contenedor que ya ha sido despachado.");
                }

                if ($contenedor->mermas()->count() > 0) {
                    throw new Exception("No se puede eliminar el contenedor porque registra mermas asociadas.");
                }

                $contenedor->delete();
            });

            return redirect()->route('contenedores.index')
                ->with('success', 'Contenedor eliminado del registro correctamente.');

        } catch (Exception $e) {
            Log::error("Error al eliminar contenedor {$id}: {$e->getMessage()}");

            return back()->with('error', $e->getMessage());
        }
    }
}