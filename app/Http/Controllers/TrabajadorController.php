<?php

namespace App\Http\Controllers;

use App\Models\Trabajador;
use App\Models\User;
use Illuminate\Http\Request;

class TrabajadorController extends Controller
{
    public function index(Request $request)
    {
        // Consulta base
        $query = Trabajador::query();
        
        // Filtro por búsqueda general (documento, nombres, apellidos, cargo)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('numero_documento', 'LIKE', "%{$search}%")
                  ->orWhere('nombres', 'LIKE', "%{$search}%")
                  ->orWhere('apellidos', 'LIKE', "%{$search}%")
                  ->orWhere('cargo', 'LIKE', "%{$search}%");
            });
        }
        
        // Filtro por tipo de documento
        if ($request->filled('tipo_documento')) {
            $query->where('tipo_documento', $request->tipo_documento);
        }
        
        // Filtro por cargo
        if ($request->filled('cargo')) {
            $query->where('cargo', 'LIKE', "%{$request->cargo}%");
        }
        
        // Filtro por tipo de contrato
        if ($request->filled('tipo_contrato')) {
            $query->where('tipo_contrato', $request->tipo_contrato);
        }
        
        // Filtro por estado (activo/inactivo)
        if ($request->filled('activo')) {
            $query->where('activo', $request->activo);
        }
        
        // Filtro por rango de fechas de ingreso
        if ($request->filled('fecha_ingreso_desde')) {
            $query->whereDate('fecha_ingreso', '>=', $request->fecha_ingreso_desde);
        }
        
        if ($request->filled('fecha_ingreso_hasta')) {
            $query->whereDate('fecha_ingreso', '<=', $request->fecha_ingreso_hasta);
        }
        
        // Filtro por salario mínimo y máximo
        if ($request->filled('salario_min')) {
            $query->where('salario_base', '>=', $request->salario_min);
        }
        
        if ($request->filled('salario_max')) {
            $query->where('salario_base', '<=', $request->salario_max);
        }
        
        // Ordenamiento
        $sortField = $request->get('sort', 'id');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);
        
        // Paginación
        $trabajadores = $query->paginate(15)->withQueryString();
        
        // Datos para los filtros (selects)
        $tiposDocumento = ['CC', 'CE', 'NIT', 'PPT'];
        $tiposContrato = ['indefinido', 'fijo', 'por_labores', 'aprendizaje'];
        $cargosUnicos = Trabajador::select('cargo')->distinct()->pluck('cargo');
        
        return view('trabajadores.index', compact(
            'trabajadores', 
            'tiposDocumento', 
            'tiposContrato',
            'cargosUnicos'
        ));
    }

    public function create()
    {
        // Traemos los usuarios para poder vincularlos opcionalmente con Jetstream
        $usuarios = User::all();
        return view('trabajadores.form', compact('usuarios'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'             => 'nullable|exists:users,id',
            'tipo_documento'      => 'required|in:CC,CE,NIT,PPT',
            'numero_documento'    => 'required|string|unique:trabajadores,numero_documento|max:255',
            'nombres'             => 'required|string|max:255',
            'apellidos'           => 'required|string|max:255',
            'fecha_nacimiento'    => 'nullable|date',
            'genero'              => 'nullable|in:M,F,Otro',
            'cargo'               => 'required|string|max:255',
            'fecha_ingreso'       => 'required|date',
            'fecha_retiro'        => 'nullable|date|after_or_equal:fecha_ingreso',
            'tipo_contrato'       => 'required|in:indefinido,fijo,por_labores,aprendizaje',
            'salario_base'        => 'nullable|numeric|min:0',
            'forma_pago'          => 'required|in:jornal,destajo,mixto',
            'banco_numero_cuenta' => 'nullable|string',
            'eps'                 => 'nullable|string|max:255',
            'arl'                 => 'nullable|string|max:255',
            'afp'                 => 'nullable|string|max:255',
            'habilidades_input'   => 'nullable|string', // Procesado manual
        ]);

        $validated['activo'] = $request->has('activo');

        // Convertir la cadena de habilidades (separada por comas) en un array para el campo JSON
        if ($request->filled('habilidades_input')) {
            $validated['habilidades'] = array_map('trim', explode(',', $request->input('habilidades_input')));
        } else {
            $validated['habilidades'] = null;
        }

        Trabajador::create($validated);

        return redirect()->route('trabajadores.index')
            ->with('success', 'Trabajador registrado exitosamente.');
    }

    public function show(Trabajador $trabajador)
    {
        return view('trabajadores.show', compact('trabajador'));
    }

    public function edit(Trabajador $trabajador)
    {
        $usuarios = User::all();
        return view('trabajadores.form', compact('trabajador', 'usuarios'));
    }

    public function update(Request $request, Trabajador $trabajador)
    {
        $validated = $request->validate([
            'user_id'             => 'nullable|exists:users,id',
            'tipo_documento'      => 'required|in:CC,CE,NIT,PPT',
            'numero_documento'    => 'required|string|max:255|unique:trabajadores,numero_documento,' . $trabajador->id,
            'nombres'             => 'required|string|max:255',
            'apellidos'           => 'required|string|max:255',
            'fecha_nacimiento'    => 'nullable|date',
            'genero'              => 'nullable|in:M,F,Otro',
            'cargo'               => 'required|string|max:255',
            'fecha_ingreso'       => 'required|date',
            'fecha_retiro'        => 'nullable|date|after_or_equal:fecha_ingreso',
            'tipo_contrato'       => 'required|in:indefinido,fijo,por_labores,aprendizaje',
            'salario_base'        => 'nullable|numeric|min:0',
            'forma_pago'          => 'required|in:jornal,destajo,mixto',
            'banco_numero_cuenta' => 'nullable|string',
            'eps'                 => 'nullable|string|max:255',
            'arl'                 => 'nullable|string|max:255',
            'afp'                 => 'nullable|string|max:255',
            'habilidades_input'   => 'nullable|string',
        ]);

        $validated['activo'] = $request->has('activo');

        if ($request->filled('habilidades_input')) {
            $validated['habilidades'] = array_map('trim', explode(',', $request->input('habilidades_input')));
        } else {
            $validated['habilidades'] = null;
        }

        $trabajador->update($validated);

        return redirect()->route('trabajadores.index')
            ->with('success', 'Datos del trabajador actualizados.');
    }

    public function destroy(Trabajador $trabajador)
    {
        $trabajador->delete();
        return redirect()->route('trabajadores.index')
            ->with('success', 'Trabajador eliminado correctamente.');
    }
}