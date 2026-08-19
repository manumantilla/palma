<x-app-layout>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Editar Movimiento #{{ $movimiento->id }}</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Kilos actuales:</strong> {{ number_format($movimiento->kilos_asignados, 2) }} kg
                        <br>
                        <small>Al modificar los kilos, el contenedor se actualizará automáticamente.</small>
                    </div>

                    <form action="{{ route('movimientos.update', $movimiento->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="fecha_movimiento" class="form-label">Fecha y Hora <span class="text-danger">*</span></label>
                            <input type="datetime-local" 
                                   class="form-control @error('fecha_movimiento') is-invalid @enderror" 
                                   id="fecha_movimiento" 
                                   name="fecha_movimiento" 
                                   value="{{ old('fecha_movimiento', \Carbon\Carbon::parse($movimiento->fecha_movimiento)->format('Y-m-d\TH:i')) }}" 
                                   required>
                            @error('fecha_movimiento')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="operario_id" class="form-label">Operario</label>
                            <select class="form-select @error('operario_id') is-invalid @enderror" 
                                    id="operario_id" 
                                    name="operario_id">
                                <option value="">Seleccionar operario</option>
                                @foreach($operarios as $operario)
                                    <option value="{{ $operario->id }}" {{ old('operario_id', $movimiento->operario_id) == $operario->id ? 'selected' : '' }}>
                                        {{ $operario->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('operario_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="recepcion_campo_id" class="form-label">Recepción de Campo <span class="text-danger">*</span></label>
                            <select class="form-select @error('recepcion_campo_id') is-invalid @enderror" 
                                    id="recepcion_campo_id" 
                                    name="recepcion_campo_id" 
                                    required>
                                <option value="">Seleccionar recepción</option>
                                @foreach($recepciones as $recepcion)
                                    <option value="{{ $recepcion->id }}" {{ old('recepcion_campo_id', $movimiento->recepcion_campo_id) == $recepcion->id ? 'selected' : '' }}>
                                        {{ $recepcion->codigo }} - {{ $recepcion->producto ?? 'Sin producto' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('recepcion_campo_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="contenedor_id" class="form-label">Contenedor Destino <span class="text-danger">*</span></label>
                            <select class="form-select @error('contenedor_id') is-invalid @enderror" 
                                    id="contenedor_id" 
                                    name="contenedor_id" 
                                    required>
                                <option value="">Seleccionar contenedor</option>
                                @foreach($contenedores as $contenedor)
                                    <option value="{{ $contenedor->id }}" {{ old('contenedor_id', $movimiento->contenedor_id) == $contenedor->id ? 'selected' : '' }}>
                                        {{ $contenedor->codigo }} 
                                        @if($contenedor->id == $movimiento->contenedor_id)
                                            (Actual - {{ number_format($contenedor->kilos_acumulados ?? 0, 2) }} kg)
                                        @else
                                            ({{ number_format($contenedor->kilos_acumulados ?? 0, 2) }} kg)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('contenedor_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Si cambias de contenedor, los kilos se transferirán automáticamente.</small>
                        </div>

                        <div class="mb-3">
                            <label for="kilos_asignados" class="form-label">Kilos Asignados <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" 
                                       step="0.01" 
                                       min="0.1" 
                                       class="form-control @error('kilos_asignados') is-invalid @enderror" 
                                       id="kilos_asignados" 
                                       name="kilos_asignados" 
                                       value="{{ old('kilos_asignados', $movimiento->kilos_asignados) }}" 
                                       placeholder="0.00" 
                                       required>
                                <span class="input-group-text">kg</span>
                            </div>
                            @error('kilos_asignados')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Diferencia: {{ number_format(old('kilos_asignados', $movimiento->kilos_asignados) - $movimiento->kilos_asignados, 2) }} kg</small>
                        </div>

                        <div class="mb-3">
                            <label for="observaciones" class="form-label">Observaciones</label>
                            <textarea class="form-control @error('observaciones') is-invalid @enderror" 
                                      id="observaciones" 
                                      name="observaciones" 
                                      rows="3">{{ old('observaciones', $movimiento->observaciones) }}</textarea>
                            @error('observaciones')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('movimientos.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Actualizar Movimiento
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>