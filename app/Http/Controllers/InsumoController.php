<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Insumo;
use App\Models\CategoriaInsumo;
use App\Models\Proveedor;
use App\Models\UnidadMedida;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InsumoController extends Controller
{
    //
    public function index(Request $request)
    {
        $query = Insumo::with(['categoria', 'lotes']);

        // Búsqueda por nombre
        if ($request->filled('nombre')) {
            $query->where('nombre', 'like', '%' . $request->nombre . '%');
        }

        // Búsqueda por ingrediente principal (CORREGIDO)
        if ($request->filled('ingrediente_principal')) {
            $query->where('ingrediente_principal', 'like', '%' . $request->ingrediente_principal . '%');
        }

        // Filtro por categoría
        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtro por nivel de toxicidad
        if ($request->filled('nivel_toxicidad')) {
            $query->where('nivel_toxicidad', $request->nivel_toxicidad);
        }

        // Filtro por requiere refrigeración
        if ($request->filled('requiere_refrigeracion')) {
            $query->where('requiere_refrigeracion', $request->requiere_refrigeracion);
        }

        $insumos = $query->latest()->paginate(20)->withQueryString();
        
        // Para los filtros en la vista
        $categorias = CategoriaInsumo::orderBy('nombre')->get();
        
        return view('insumos.index', compact('insumos', 'categorias'));
    }


    public function create()
    {
        // 1. Traer solo los campos necesarios (id y nombre) para no saturar la memoria RAM.
        // 2. Filtrar solo por registros 'activos' para evitar que usen categorías o proveedores obsoletos.
        $categorias = CategoriaInsumo::query()
            ->select('id', 'nombre')
            ->orderBy('nombre', 'asc')
            ->get();

        $proveedores = Proveedor::query()
            ->select('id', 'nombre')
            ->orderBy('nombre', 'asc')
            ->get();

        // 3. Renderizar la vista pasando las colecciones
        return view('insumos.create', compact('categorias', 'proveedores'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'categoria_id'               => 'required|exists:categorias_insumo,id',
            'nombre'                     => 'required|string|max:255',
            'ingrediente_principal'      => 'nullable|string|max:255',
            'unidad_base'                => 'required|in:kg,l,unidad',
            'factor_conversion'          => 'required|numeric|min:0',
            'nivel_toxicidad'            => 'nullable|in:bajo,medio,alto',
            'rei_horas'                  => 'nullable|integer|min:0',
            'phi_dias'                   => 'nullable|integer|min:0',
            'clasificacion_toxicologica' => 'nullable|in:Ia,Ib,II,III,IV',
            'equipo_proteccion'          => 'nullable|string|max:255', // texto libre del formulario
            'franja_color'               => 'nullable|in:rojo,amarillo,azul,verde',
            'almacenamiento_temp_min'    => 'nullable|numeric',
            'almacenamiento_temp_max'    => 'nullable|numeric',
            'almacenamiento_humedad'     => 'nullable|string|max:20',
            'requiere_refrigeracion'     => 'boolean',
            'sensible_luz'               => 'boolean',
            'stock_minimo'               => 'nullable|numeric|min:0',
            'dias_aviso_vencimiento'     => 'required|integer|min:1',
            
            // Validación de los componentes dinámicos (Ficha Técnica)
            'componentes'                => 'nullable|array',
            'componentes.*.tipo'         => 'required_with:componentes|in:nutriente,activo,coadyuvante,carga,otros',
            'componentes.*.nombre'       => 'required_with:componentes|string|max:255',
            'componentes.*.unidad'       => 'required_with:componentes|string|max:20',
            'componentes.*.concentracion'=> 'required_with:componentes|numeric|min:0',
        ]);

        $validated = $this->normalizarUnidad($validated);

        // Usamos una transacción DB por si fallan los componentes, no quede el insumo flotando solo
        DB::transaction(function () use ($validated) {
            $insumo = Insumo::create($validated);

            // Guardar componentes si el usuario los agregó en el formulario dinámico
            if (!empty($validated['componentes'])) {
                foreach ($validated['componentes'] as $comp) {
                    $insumo->componentes()->create([
                        'tipo_componente' => $comp['tipo'],
                        'componente'      => $comp['nombre'],
                        'unidad'          => $comp['unidad'],
                        'concentracion'   => $comp['concentracion'],
                    ]);
                }
            }
        });

        return redirect()->route('insumos.index')->with('success', 'Insumo catalogado correctamente con su ficha técnica.');
    }

    public function show(Insumo $insumo)
    {
        // Carga la hoja de vida completa del insumo y el histórico de compras/lotes
        $insumo->load(['categoria', 'componentes', 'lotes.proveedor']);
        return view('insumos.show', compact('insumo'));
    }

    public function edit(Insumo $insumo)
    {
        $categorias = CategoriaInsumo::orderBy('nombre')->get();
        $insumo->load('componentes');
        return view('insumos.edit', compact('insumo', 'categorias'));
    }

    public function update(Request $request, Insumo $insumo)
    {
        $validated = $request->validate([
            'categoria_id'               => 'required|exists:categorias_insumo,id',
            'nombre'                     => 'required|string|max:255',
            'ingrediente_principal'      => 'nullable|string|max:255',
            'unidad_base'                => 'required|in:kg,l,unidad',
            'factor_conversion'          => 'required|numeric|min:0',
            'nivel_toxicidad'            => 'nullable|in:bajo,medio,alto',
            'estado'                     => 'required|in:activo,inactivo',
            'rei_horas'                  => 'nullable|integer|min:0',
            'phi_dias'                   => 'nullable|integer|min:0',
            'clasificacion_toxicologica' => 'nullable|in:Ia,Ib,II,III,IV',
            'equipo_proteccion'          => 'nullable|string|max:255',
            'franja_color'               => 'nullable|in:rojo,amarillo,azul,verde',
            'almacenamiento_temp_min'    => 'nullable|numeric',
            'almacenamiento_temp_max'    => 'nullable|numeric',
            'almacenamiento_humedad'     => 'nullable|string|max:20',
            'requiere_refrigeracion'     => 'boolean',
            'sensible_luz'               => 'boolean',
            'stock_minimo'               => 'nullable|numeric|min:0',
            'dias_aviso_vencimiento'     => 'required|integer|min:1',
            
            'componentes'                => 'nullable|array',
            'componentes.*.tipo'         => 'required_with:componentes|in:nutriente,activo,coadyuvante,carga,otros',
            'componentes.*.nombre'       => 'required_with:componentes|string|max:255',
            'componentes.*.unidad'       => 'required_with:componentes|string|max:20',
            'componentes.*.concentracion'=> 'required_with:componentes|numeric|min:0',
        ]);

        $validated = $this->normalizarUnidad($validated);

        DB::transaction(function () use ($insumo, $validated) {
            $insumo->update($validated);

            // Refrescamos los componentes: la vía más limpia en actualizaciones medianas es borrar y recrear
            $insumo->componentes()->delete();
            if (!empty($validated['componentes'])) {
                foreach ($validated['componentes'] as $comp) {
                    $insumo->componentes()->create([
                        'tipo_componente' => $comp['tipo'],
                        'componente'      => $comp['nombre'],
                        'unidad'          => $comp['unidad'],
                        'concentracion'   => $comp['concentracion'],
                    ]);
                }
            }
        });

        return redirect()->route('insumos.index')->with('success', 'Datos del insumo actualizados.');
    }

    /**
     * El formulario envía la unidad como código (kg / l / unidad), pero la tabla guarda
     * unidad_base_id -> unidades_medida, donde también vive el factor de conversión.
     */
    private function normalizarUnidad(array $validated): array
    {
        $abreviatura = Insumo::ABREVIATURA_POR_CODIGO[$validated['unidad_base']] ?? $validated['unidad_base'];
        $unidadId = UnidadMedida::where('abreviatura', $abreviatura)->value('id');

        if (! $unidadId) {
            throw ValidationException::withMessages([
                'unidad_base' => "La unidad '{$validated['unidad_base']}' no está registrada en unidades de medida.",
            ]);
        }

        $validated['unidad_base_id'] = $unidadId;
        $validated['unidad_uso_id'] ??= $unidadId;
        unset($validated['unidad_base'], $validated['factor_conversion']);

        return $validated;
    }
}
