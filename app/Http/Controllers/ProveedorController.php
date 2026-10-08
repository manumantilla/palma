<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Exception;

class ProveedorController extends Controller
{
    /**
     * Lista todos los proveedores paginados.
     */
    public function index()
    {
        $proveedores = Proveedor::latest()->paginate(10);
        return view('proveedores.index', compact('proveedores'));
    }

    /**
     * Muestra el formulario para crear un nuevo proveedor.
     */
    public function create()
    {
        return view('proveedores.create');
    }

    /**
     * Guarda un nuevo proveedor en la base de datos.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'              => ['required', 'string', 'max:255'],
            'nit'                 => ['required', 'string', 'max:255', 'unique:proveedores,nit'],
            'categoria_principal' => ['nullable', 'string', 'max:255'],
            'activo'              => ['nullable', 'boolean'],
            'contacto_principal'  => ['nullable', 'string', 'max:255'],
            'telefono'            => ['nullable', 'string', 'max:255'],
            'email'               => ['nullable', 'email', 'max:255', 'unique:proveedores,email'],
            'direccion'           => ['nullable', 'string', 'max:255'],
            'tiene_credito'       => ['nullable', 'boolean'],
            'dias_plazo'          => ['nullable', 'integer', 'min:0'],
            'limite_credito'      => ['nullable', 'numeric', 'min:0'],
            'banco_1'             => ['nullable', 'string', 'max:255'],
            'cuenta_bancaria_1'   => ['nullable', 'string', 'max:255'],
            'banco_2'             => ['nullable', 'string', 'max:255'],
            'cuenta_bancaria_2'   => ['nullable', 'string', 'max:255'],
            'notas'              => ['nullable', 'string'],
        ]);

        // Ajuste para campos tipo checkbox en formularios web
        $validated['activo'] = $request->has('activo');
        $validated['tiene_credito'] = $request->has('tiene_credito');

        DB::beginTransaction();

        try {
            $proveedor = Proveedor::create($validated);

            DB::commit();

            Log::info('Proveedor creado exitosamente.', [
                'proveedor_id' => $proveedor->id,
                'nit'          => $proveedor->nit,
                'user_id'      => auth()->id(),
            ]);

            return redirect()
                ->route('proveedores.index')
                ->with('success', 'Proveedor registrado exitosamente.');

        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Error al intentar crear un proveedor.', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->except(['_token']),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Ocurrió un error inesperado al registrar el proveedor.');
        }
    }

    /**
     * Muestra el detalle de un proveedor junto con sus compras.
     */
    public function show(Proveedor $proveedor)
    {
        $proveedor->load(['compras' => function ($query) {
            $query->latest();
        }]);

        return view('proveedores.show', compact('proveedor'));
    }

    /**
     * Muestra el formulario para editar un proveedor existente.
     */
    public function edit(Proveedor $proveedor)
    {
        return view('proveedores.edit', compact('proveedor'));
    }

    /**
     * Actualiza la información del proveedor.
     */
    public function update(Request $request, Proveedor $proveedor)
    {
        $validated = $request->validate([
            'nombre'              => ['required', 'string', 'max:255'],
            'nit'                 => ['required', 'string', 'max:255', Rule::unique('proveedores', 'nit')->ignore($proveedor->id)],
            'categoria_principal' => ['nullable', 'string', 'max:255'],
            'activo'              => ['nullable', 'boolean'],
            'contacto_principal'  => ['nullable', 'string', 'max:255'],
            'telefono'            => ['nullable', 'string', 'max:255'],
            'email'               => ['nullable', 'email', 'max:255', Rule::unique('proveedores', 'email')->ignore($proveedor->id)],
            'direccion'           => ['nullable', 'string', 'max:255'],
            'tiene_credito'       => ['nullable', 'boolean'],
            'dias_plazo'          => ['nullable', 'integer', 'min:0'],
            'limite_credito'      => ['nullable', 'numeric', 'min:0'],
            'banco_1'             => ['nullable', 'string', 'max:255'],
            'cuenta_bancaria_1'   => ['nullable', 'string', 'max:255'],
            'banco_2'             => ['nullable', 'string', 'max:255'],
            'cuenta_bancaria_2'   => ['nullable', 'string', 'max:255'],
            'notas'              => ['nullable', 'string'],
        ]);

        $validated['activo'] = $request->has('activo');
        $validated['tiene_credito'] = $request->has('tiene_credito');

        DB::beginTransaction();

        try {
            $proveedor->update($validated);

            DB::commit();

            Log::info('Proveedor actualizado exitosamente.', [
                'proveedor_id' => $proveedor->id,
                'user_id'      => auth()->id(),
            ]);

            return redirect()
                ->route('proveedores.index')
                ->with('success', 'Proveedor actualizado correctamente.');

        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Error al actualizar el proveedor.', [
                'proveedor_id' => $proveedor->id,
                'error'        => $e->getMessage(),
                'trace'        => $e->getTraceAsString(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'No se pudo actualizar el proveedor. Inténtalo nuevamente.');
        }
    }

    /**
     * Elimina suavemente (SoftDelete) al proveedor.
     */
    public function destroy(Proveedor $proveedor)
    {
        DB::beginTransaction();

        try {
            $proveedorId = $proveedor->id;
            $proveedor->delete();

            DB::commit();

            Log::info('Proveedor eliminado (soft delete).', [
                'proveedor_id' => $proveedorId,
                'user_id'      => auth()->id(),
            ]);

            return redirect()
                ->route('proveedores.index')
                ->with('success', 'Proveedor eliminado correctamente.');

        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Error al intentar eliminar el proveedor.', [
                'proveedor_id' => $proveedor->id,
                'error'        => $e->getMessage(),
                'trace'        => $e->getTraceAsString(),
            ]);

            return redirect()
                ->route('proveedores.index')
                ->with('error', 'Ocurrió un error al intentar eliminar el proveedor.');
        }
    }
}