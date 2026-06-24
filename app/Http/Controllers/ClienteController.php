<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::all();
        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'razon_social'                 => 'required|string|max:255',
            'nombre_comercial'             => 'nullable|string|max:255',
            'tipo_persona'                 => 'required|in:natural,juridica',
            'nit'                          => 'required|string|unique:clientes,nit|max:255',
            'dv'                           => 'nullable|string|max:1',
            'contacto_nombre'              => 'nullable|string|max:255',
            'contacto_telefono'            => 'nullable|string|max:255',
            'contacto_email'               => 'nullable|email|max:255',
            'direccion_fiscal'             => 'required|string',
            'municipio'                    => 'required|string|max:255',
            'cupo_credito'                 => 'nullable|numeric|min:0',
            'saldo_actual'                 => 'nullable|numeric|min:0',
            'dias_credito'                 => 'nullable|integer|min:0',
            'estado_cuenta'                => 'required|in:activo,suspendido,mora,castigado',
            'categoria_cliente'            => 'required|in:mayorista,minorista,exportacion,industrial',
            'email_recepcion_facturas'     => 'nullable|email|max:255',
            'codigo_postal'                => 'nullable|string|max:10',
            'cultivos_interes_input'       => 'nullable|string',
        ]);

        $validated['autoriza_factura_electronica'] = $request->has('autoriza_factura_electronica');

        // Procesar cultivos de interés desde texto por comas a un array JSON
        if ($request->filled('cultivos_interes_input')) {
            $validated['cultivos_interes'] = array_map('trim', explode(',', $request->input('cultivos_interes_input')));
        } else {
            $validated['cultivos_interes'] = null;
        }

        Cliente::create($validated);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente registrado exitosamente en el sistema.');
    }

    public function show(Cliente $cliente)
    {
        return view('clientes.show', compact('cliente'));
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.form', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $validated = $request->validate([
            'razon_social'                 => 'required|string|max:255',
            'nombre_comercial'             => 'nullable|string|max:255',
            'tipo_persona'                 => 'required|in:natural,juridica',
            'nit'                          => 'required|string|max:255|unique:clientes,nit,' . $cliente->id,
            'dv'                           => 'nullable|string|max:1',
            'contacto_nombre'              => 'nullable|string|max:255',
            'contacto_telefono'            => 'nullable|string|max:255',
            'contacto_email'               => 'nullable|email|max:255',
            'direccion_fiscal'             => 'required|string',
            'municipio'                    => 'required|string|max:255',
            'cupo_credito'                 => 'nullable|numeric|min:0',
            'saldo_actual'                 => 'nullable|numeric|min:0',
            'dias_credito'                 => 'nullable|integer|min:0',
            'estado_cuenta'                => 'required|in:activo,suspendido,mora,castigado',
            'categoria_cliente'            => 'required|in:mayorista,minorista,exportacion,industrial',
            'email_recepcion_facturas'     => 'nullable|email|max:255',
            'codigo_postal'                => 'nullable|string|max:10',
            'cultivos_interes_input'       => 'nullable|string',
        ]);

        $validated['autoriza_factura_electronica'] = $request->has('autoriza_factura_electronica');

        if ($request->filled('cultivos_interes_input')) {
            $validated['cultivos_interes'] = array_map('trim', explode(',', $request->input('cultivos_interes_input')));
        } else {
            $validated['cultivos_interes'] = null;
        }

        $cliente->update($validated);

        return redirect()->route('clientes.index')
            ->with('success', 'Ficha del cliente actualizada.');
    }

    public function destroy(Cliente $cliente)
    {
        // Al usar SoftDeletes, esto cambia la columna deleted_at sin eliminar físicamente el registro.
        $cliente->delete();

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente enviado a la papelera (Inactivo).');
    }
}