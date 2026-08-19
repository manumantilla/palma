<?php
namespace App\Http\Controllers;

use App\Models\EventoManoObra;
use App\Models\EventoCampo;
use App\Models\SesionCosecha;
use App\Models\Trabajador;
use App\Models\CicloProductivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class EventoManoObraController extends Controller
{

    public function index(Request $request)
    {
        try {
            $query = EventoManoObra::with(['eventoCampo.cicloProductivo', 'sesionCosecha', 'trabajador']);

            // 1. Filtro por Búsqueda (Trabajador o Cédula)
            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('nombre_trabajador', 'LIKE', "%{$search}%")
                      ->orWhere('cedula', 'LIKE', "%{$search}%")
                      ->orWhereHas('trabajador', function ($t) use ($search) {
                          $t->where('nombre', 'LIKE', "%{$search}%")
                            ->orWhere('apellido', 'LIKE', "%{$search}%");
                      });
                });
            }

            // 2. Filtro por Estado de Pago
            if ($request->filled('estado_pago')) {
                $query->where('estado_pago', $request->input('estado_pago'));
            }

            // 3. Filtro por Tipo de Labor
            if ($request->filled('tipo_labor')) {
                $query->porTipoLabor($request->input('tipo_labor'));
            }

            // 4. Filtro por Ciclo Productivo
            if ($request->filled('ciclo_productivo_id')) {
                $query->porCicloProductivo($request->input('ciclo_productivo_id'));
            }

            // 5. Filtro por Rango de Fechas
            if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
                $query->enRangoFechas($request->input('fecha_inicio'), $request->input('fecha_fin'));
            }

            // Ordenamiento por defecto: Más recientes primero
            $registros = $query->latest()->paginate(15)->appends($request->all());

            // Datos auxiliares para dropdowns de filtros en la vista
            $ciclos = CicloProductivo::select('id', 'nombre')->get();

            return view('mano_obra.index', compact('registros', 'ciclos'));

        } catch (Exception $e) {
            Log::error("Error al consultar el listado de mano de obra: {$e->getMessage()}", [
                'request' => $request->all(),
                'trace'   => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Ocurrió un error al cargar el registro de mano de obra.');
        }
    }

    public function create()
    {
        try {
            $eventos = EventoCampo::select('id', 'tipo_evento_id', 'fecha_programada')->latest()->get();
            $sesiones = SesionCosecha::where('estado', 'abierta')->get();
            $trabajadores = Trabajador::select('id', 'nombre', 'apellido', 'cedula')->get();

            return view('mano_obra.create', compact('eventos', 'sesiones', 'trabajadores'));

        } catch (Exception $e) {
            Log::error("Error al cargar formulario de mano de obra: {$e->getMessage()}");
            return redirect()->route('mano-obra.index')->with('error', 'No se pudo abrir el formulario de registro.');
        }
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'evento_campo_id'   => 'nullable|exists:eventos_campo,id',
            'sesion_id'          => 'nullable|exists:sesiones_cosecha,id',
            'trabajador_id'     => 'nullable|exists:trabajadores,id',
            'nombre_trabajador' => 'required_without:trabajador_id|nullable|string|max:255',
            'cedula'            => 'required|string|max:20',
            'tipo_labor'        => 'required|in:jornal_dia_completo,jornal_medio_dia,hora_extra,destajo',
            'cantidad'          => 'required|numeric|min:0.1',
            'unidad_destajo'    => 'nullable|required_if:tipo_labor,destajo|string|max:30',
            'valor_unitario'    => 'required|numeric|min:0',
            'observaciones'     => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($validated, &$registro) {
                // Si el trabajador viene de la tabla trabajadores, autocompletamos el nombre
                if (!empty($validated['trabajador_id'])) {
                    $trabajador = Trabajador::find($validated['trabajador_id']);
                    $validated['nombre_trabajador'] = $trabajador->nombre . ' ' . $trabajador->apellido;
                    $validated['cedula'] = $trabajador->cedula ?? $validated['cedula'];
                }

                // El costo_total se calcula automáticamente en el evento 'saving' del modelo
                $registro = EventoManoObra::create($validated);
            });

            Log::info("Registro de mano de obra #{$registro->id} creado exitosamente.", [
                'usuario_id' => auth()->id(),
                'registro'   => $registro->toArray()
            ]);

            return redirect()->route('mano-obra.index')
                ->with('success', 'Registro de mano de obra guardado correctamente.');

        } catch (Exception $e) {
            Log::error("Error al guardar registro de mano de obra: {$e->getMessage()}", [
                'input' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'No se pudo guardar el registro de mano de obra.');
        }
    }

    public function show($id)
    {
        try {
            $registro = EventoManoObra::with(['eventoCampo.cicloProductivo', 'sesionCosecha', 'trabajador'])
                ->findOrFail($id);

            return view('mano_obra.show', compact('registro'));

        } catch (Exception $e) {
            Log::error("Error al mostrar mano de obra #{$id}: {$e->getMessage()}");
            return redirect()->route('mano-obra.index')->with('error', 'El registro solicitado no existe.');
        }
    }


    public function edit($id)
    {
        try {
            $registro = EventoManoObra::findOrFail($id);

            if ($registro->estado_pago === 'pagado') {
                return redirect()->route('mano-obra.index')
                    ->with('error', 'No se puede editar un registro de mano de obra que ya fue PAGADO.');
            }

            $eventos = EventoCampo::select('id', 'tipo_evento_id', 'fecha_programada')->latest()->get();
            $sesiones = SesionCosecha::where('estado', 'abierta')->get();
            $trabajadores = Trabajador::select('id', 'nombre', 'apellido', 'cedula')->get();

            return view('mano_obra.edit', compact('registro', 'eventos', 'sesiones', 'trabajadores'));

        } catch (Exception $e) {
            Log::error("Error al cargar edición de mano de obra #{$id}: {$e->getMessage()}");
            return redirect()->route('mano-obra.index')->with('error', 'No se pudo cargar el registro para edición.');
        }
    }


    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'evento_campo_id'   => 'nullable|exists:eventos_campo,id',
            'sesion_id'          => 'nullable|exists:sesiones_cosecha,id',
            'trabajador_id'     => 'nullable|exists:trabajadores,id',
            'nombre_trabajador' => 'required_without:trabajador_id|nullable|string|max:255',
            'cedula'            => 'required|string|max:20',
            'tipo_labor'        => 'required|in:jornal_dia_completo,jornal_medio_dia,hora_extra,destajo',
            'cantidad'          => 'required|numeric|min:0.1',
            'unidad_destajo'    => 'nullable|required_if:tipo_labor,destajo|string|max:30',
            'valor_unitario'    => 'required|numeric|min:0',
            'observaciones'     => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($validated, $id) {
                $registro = EventoManoObra::findOrFail($id);

                if ($registro->estado_pago === 'pagado') {
                    throw new Exception("Intento de modificación sobre un registro liquidado.");
                }

                if (!empty($validated['trabajador_id'])) {
                    $trabajador = Trabajador::find($validated['trabajador_id']);
                    $validated['nombre_trabajador'] = $trabajador->nombre . ' ' . $trabajador->apellido;
                    $validated['cedula'] = $trabajador->cedula ?? $validated['cedula'];
                }

                $registro->update($validated);
            });

            Log::info("Mano de obra #{$id} actualizada correctamente por usuario #".auth()->id());

            return redirect()->route('mano-obra.index')
                ->with('success', 'Registro de mano de obra actualizado correctamente.');

        } catch (Exception $e) {
            Log::error("Error al actualizar mano de obra #{$id}: {$e->getMessage()}", [
                'input' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Ocurrió un error al actualizar el registro.');
        }
    }

    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $registro = EventoManoObra::findOrFail($id);

                if ($registro->estado_pago === 'pagado') {
                    throw new Exception("No es posible eliminar un registro que ya fue pagado.");
                }

                $registro->delete();
            });

            Log::info("Mano de obra #{$id} eliminada por usuario #".auth()->id());

            return redirect()->route('mano-obra.index')
                ->with('success', 'El registro de mano de obra fue eliminado con éxito.');

        } catch (Exception $e) {
            Log::error("Error al eliminar mano de obra #{$id}: {$e->getMessage()}");

            return redirect()->route('mano-obra.index')
                ->with('error', 'No se pudo eliminar el registro. ' . $e->getMessage());
        }
    }

    /**
     * Método Especial: Liquidación masiva de la semana para un trabajador (Sábados de Pago).
     */
    public function liquidarPagoSemanal(Request $request)
    {
        $request->validate([
            'cedula'       => 'required|string',
            'fecha_pago'   => 'required|date',
        ]);

        try {
            $totalLiquidado = 0;
            $cantidadRegistros = 0;

            DB::transaction(function () use ($request, &$totalLiquidado, &$cantidadRegistros) {
                $pendientes = EventoManoObra::delTrabajador($request->input('cedula'))
                    ->pendientesDePago()
                    ->get();

                if ($pendientes->isEmpty()) {
                    throw new Exception("No hay registros pendientes de pago para el trabajador indicado.");
                }

                $cantidadRegistros = $pendientes->count();
                $totalLiquidado = $pendientes->sum('costo_total');

                // Actualización masiva del estado de pago
                EventoManoObra::whereIn('id', $pendientes->pluck('id'))
                    ->update([
                        'estado_pago' => 'pagado',
                        'fecha_pago'  => $request->input('fecha_pago'),
                    ]);
            });

            Log::info("Liquidación exitosa para trabajador CC: {$request->input('cedula')}. Total: ${$totalLiquidado}");

            return redirect()->route('mano-obra.index')
                ->with('success', "Liquidación completada. Se pagaron {$cantidadRegistros} labor(es) por un total de $" . number_format($totalLiquidado, 2) . " COP.");

        } catch (Exception $e) {
            Log::error("Error en liquidación masiva de pago: {$e->getMessage()}", [
                'request' => $request->all()
            ]);

            return redirect()->back()
                ->with('error', 'Error al procesar la liquidación: ' . $e->getMessage());
        }
    }
}