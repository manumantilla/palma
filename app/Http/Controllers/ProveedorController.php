<?php
namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Exception;

class ProveedorController extends Controller
{
    public function index()
    {
        try {
            $proveedores = Proveedor::orderBy('nombre', 'asc')->get();
            return view('proveedor.index', compact('proveedores'));
        } catch (Exception $e) {
            Log::error('Error al listar proveedores: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'Ocurrió un error al cargar los proveedores.');
        }
    }

    public function create()
    {
        return view('proveedor.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nombre' => 'required|string|max:255',
                'nit' => 'required|string|unique:proveedores,nit|max:50',
                'categoria_principal' => 'nullable|string|max:100',
                'contacto_principal' => 'nullable|string|max:255',
                'telefono' => 'nullable|string|max:50',
                'email' => 'nullable|email|unique:proveedores,email|max:255',
                'direccion' => 'nullable|string|max:255',
                'dias_plazo' => 'nullable|integer|min:0',
                'limite_credito' => 'nullable|numeric|min:0',
                'banco_1' => 'nullable|string|max:100',
                'cuenta_bancaria_1' => 'nullable|string|max:100',
                'banco_2' => 'nullable|string|max:100',
                'cuenta_bancaria_2' => 'nullable|string|max:100',
                'notas' => 'nullable|string',
            ]);

            // Manejo de checkboxes
            $validated['activo'] = $request->has('activo');
            $validated['tiene_credito'] = $request->has('tiene_credito');
            $validated['dias_plazo'] = $validated['dias_plazo'] ?? 0;

            $proveedor = Proveedor::create($validated);
            Log::info("Proveedor creado exitosamente: ID {$proveedor->id} - {$proveedor->nombre}");

            return redirect()->route('proveedor.index')->with('success', 'Proveedor registrado correctamente.');
        } catch (Exception $e) {
            Log::error('Error al crear proveedor: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'No se pudo registrar el proveedor. Verifique los datos.');
        }
    }

    public function show($id)
    {
        try {
            $proveedor = Proveedor::with('lotesInsumos')->findOrFail($id);
            return view('proveedor.show', compact('proveedor'));
        } catch (Exception $e) {
            Log::error("Error al buscar proveedor ID {$id}: " . $e->getMessage());
            return redirect()->route('proveedor.index')->with('error', 'Proveedor no encontrado.');
        }
    }

    public function edit($id)
    {
        try {
            $proveedor = Proveedor::findOrFail($id);
            return view('proveedor.edit', compact('proveedor'));
        } catch (Exception $e) {
            Log::error("Error al buscar proveedor para edición ID {$id}: " . $e->getMessage());
            return redirect()->route('proveedor.index')->with('error', 'Proveedor no encontrado.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'nombre' => 'required|string|max:255',
                'nit' => ['required', 'string', 'max:50', Rule::unique('proveedores')->ignore($id)],
                'categoria_principal' => 'nullable|string|max:100',
                'contacto_principal' => 'nullable|string|max:255',
                'telefono' => 'nullable|string|max:50',
                'email' => ['nullable', 'email', 'max:255', Rule::unique('proveedores')->ignore($id)],
                'direccion' => 'nullable|string|max:255',
                'dias_plazo' => 'nullable|integer|min:0',
                'limite_credito' => 'nullable|numeric|min:0',
                'banco_1' => 'nullable|string|max:100',
                'cuenta_bancaria_1' => 'nullable|string|max:100',
                'banco_2' => 'nullable|string|max:100',
                'cuenta_bancaria_2' => 'nullable|string|max:100',
                'notas' => 'nullable|string',
            ]);

            $validated['activo'] = $request->has('activo');
            $validated['tiene_credito'] = $request->has('tiene_credito');
            $validated['dias_plazo'] = $validated['dias_plazo'] ?? 0;

            $proveedor = Proveedor::findOrFail($id);
            $proveedor->update($validated);
            
            Log::info("Proveedor actualizado exitosamente: ID {$proveedor->id}");

            return redirect()->route('proveedor.index')->with('success', 'Proveedor actualizado correctamente.');
        } catch (Exception $e) {
            Log::error("Error al actualizar proveedor ID {$id}: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'No se pudo actualizar el proveedor.');
        }
    }

    public function destroy($id)
    {
        try {
            $proveedor = Proveedor::findOrFail($id);
            $proveedor->delete(); // Elimina usando SoftDeletes
            
            Log::info("Proveedor eliminado (SoftDelete): ID {$id}");

            return redirect()->route('proveedor.index')->with('success', 'Proveedor eliminado correctamente.');
        } catch (Exception $e) {
            Log::error("Error al eliminar proveedor ID {$id}: " . $e->getMessage());
            return redirect()->route('proveedor.index')->with('error', 'No se pudo eliminar el proveedor.');
        }
    }
}