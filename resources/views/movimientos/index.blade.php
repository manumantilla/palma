
</x-app-layout>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Movimientos de Clasificación</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('movimientos.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Movimiento
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('movimientos.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                    <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="{{ request('fecha_inicio') }}">
                </div>
                <div class="col-md-3">
                    <label for="fecha_fin" class="form-label">Fecha Fin</label>
                    <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" value="{{ request('fecha_fin') }}">
                </div>
                <div class="col-md-2">
                    <label for="contenedor_id" class="form-label">Contenedor</label>
                    <select class="form-select" id="contenedor_id" name="contenedor_id">
                        <option value="">Todos</option>
                        @foreach($contenedores ?? [] as $contenedor)
                            <option value="{{ $contenedor->id }}" {{ request('contenedor_id') == $contenedor->id ? 'selected' : '' }}>
                                {{ $contenedor->codigo }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="operario_id" class="form-label">Operario</label>
                    <select class="form-select" id="operario_id" name="operario_id">
                        <option value="">Todos</option>
                        @foreach($operarios ?? [] as $operario)
                            <option value="{{ $operario->id }}" {{ request('operario_id') == $operario->id ? 'selected' : '' }}>
                                {{ $operario->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de movimientos -->
    <div class="card">
        <div class="card-body">
            @if($movimientos->isEmpty())
                <div class="alert alert-info">
                    No hay movimientos registrados.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Fecha</th>
                                <th>Operario</th>
                                <th>Recepción Campo</th>
                                <th>Contenedor</th>
                                <th>Kilos Asignados</th>
                                <th>Observaciones</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($movimientos as $movimiento)
                                <tr>
                                    <td>{{ $movimiento->id }}</td>
                                    <td>{{ \Carbon\Carbon::parse($movimiento->fecha_movimiento)->format('d/m/Y H:i') }}</td>
                                    <td>{{ $movimiento->operario->name ?? 'Sin asignar' }}</td>
                                    <td>{{ $movimiento->recepcionCampo->codigo ?? 'N/A' }}</td>
                                    <td>{{ $movimiento->contenedor->codigo ?? 'N/A' }}</td>
                                    <td class="fw-bold">{{ number_format($movimiento->kilos_asignados, 2) }} kg</td>
                                    <td>{{ Str::limit($movimiento->observaciones, 30) }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('movimientos.edit', $movimiento->id) }}" 
                                               class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-danger" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#deleteModal{{ $movimiento->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $movimientos->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modales de confirmación para eliminar -->
@foreach($movimientos as $movimiento)
<div class="modal fade" id="deleteModal{{ $movimiento->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de eliminar el movimiento #{{ $movimiento->id }}?</p>
                <p class="text-danger">
                    <strong>Se revertirán {{ number_format($movimiento->kilos_asignados, 2) }} kg del contenedor.</strong>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form action="{{ route('movimientos.destroy', $movimiento->id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach
</x-app-layout>