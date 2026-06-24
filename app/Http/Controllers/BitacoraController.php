<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BitacoraController extends Controller
{
    public function index(Request $request)
    {
        $query = Bitacora::with('user');

        // Búsqueda por título
        if ($request->filled('titulo')) {
            $query->where('titulo', 'like', '%' . $request->titulo . '%');
        }

        // Búsqueda por fecha (created_at)
        if ($request->filled('fecha')) {
            $query->whereDate('created_at', $request->fecha);
        }

        // Búsqueda por quien registró (nombre de usuario)
        if ($request->filled('registrado_por')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->registrado_por . '%');
            });
        }

        $bitacoras = $query->latest()->paginate(15);

        return view('bitacoras.index', compact('bitacoras'));
    }

    public function create()
    {
        return view('bitacoras.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bitacorable_type' => 'required|string',
            'bitacorable_id' => 'required|integer',
            'tipo' => 'required|in:observacion,alerta,incidente,decision,condicion_clima,visita_tecnica',
            'prioridad' => 'required|in:baja,media,alta,critica',
            'titulo' => 'nullable|string|max:255',
            'contenido' => 'required|string',
            'estado' => 'required|in:abierto,en_proceso,resuelto',
            'archivo_adjunto' => 'nullable|string|max:255',
        ]);

        $validated['user_id'] = auth()->id();

        $result = Bitacora::crearBitacora($validated);

        if ($result['success']) {
            return redirect()->route('bitacoras.index')->with('success', $result['message']);
        } else {
            return back()->withErrors(['error' => $result['message']])->withInput();
        }
    }

    public function show(Bitacora $bitacora)
    {
        $bitacora->load('user', 'bitacorable');
        return view('bitacoras.show', compact('bitacora'));
    }

    public function edit(Bitacora $bitacora)
    {
        return view('bitacoras.edit', compact('bitacora'));
    }

    public function update(Request $request, Bitacora $bitacora)
    {
        $validated = $request->validate([
            'bitacorable_type' => 'required|string',
            'bitacorable_id' => 'required|integer',
            'tipo' => 'required|in:observacion,alerta,incidente,decision,condicion_clima,visita_tecnica',
            'prioridad' => 'required|in:baja,media,alta,critica',
            'titulo' => 'nullable|string|max:255',
            'contenido' => 'required|string',
            'estado' => 'required|in:abierto,en_proceso,resuelto',
            'archivo_adjunto' => 'nullable|string|max:255',
        ]);

        $result = $bitacora->actualizarBitacora($validated);

        if ($result['success']) {
            return redirect()->route('bitacoras.index')->with('success', $result['message']);
        } else {
            return back()->withErrors(['error' => $result['message']])->withInput();
        }
    }

    public function destroy(Bitacora $bitacora)
    {
        $result = $bitacora->eliminarBitacora();

        if ($result['success']) {
            return redirect()->route('bitacoras.index')->with('success', $result['message']);
        } else {
            return back()->withErrors(['error' => $result['message']]);
        }
    }
}