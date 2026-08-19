<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Botón Volver -->
        <div class="mb-6">
            <a href="{{ route('ordenes_cosecha.index') }}" class="inline-flex items-center gap-2 text-green-600 hover:text-green-800 transition-colors">
                <i class="fas fa-arrow-left"></i>
                <span>Volver a órdenes de cosecha</span>
            </a>
        </div>

        <!-- Encabezado estilo campo -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-8">
            <div class="flex items-center gap-3">
                <div class="bg-green-700 p-2.5 rounded-full shadow-md">
                    <i class="fas fa-seedling text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-green-800 tracking-wide" style="font-family: 'Segoe UI', 'Georgia', serif;">
                        Orden #{{ $ordenesCosecha->id }}
                    </h1>
                    <div class="flex items-center gap-3 mt-1">
                        <p class="text-sm text-amber-700 flex items-center gap-2">
                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            {{ $ordenesCosecha->cliente->name ?? 'N/A' }}
                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        </p>
                        @php
                            $estadosColores = [
                                'borrador' => 'bg-gray-100 text-gray-700 border-gray-300',
                                'confirmada' => 'bg-blue-100 text-blue-700 border-blue-300',
                                'en_proceso' => 'bg-amber-100 text-amber-700 border-amber-300',
                                'completada' => 'bg-green-100 text-green-700 border-green-300',
                                'cancelada' => 'bg-red-100 text-red-700 border-red-300'
                            ];
                            $estadosIconos = [
                                'borrador' => '📄',
                                'confirmada' => '✅',
                                'en_proceso' => '⏳',
                                'completada' => '🌾',
                                'cancelada' => '❌'
                            ];
                        @endphp
                        <span class="px-3 py-1 inline-flex items-center gap-1.5 text-xs leading-4 font-semibold rounded-full border {{ $estadosColores[$ordenesCosecha->estado] }}">
                            <span>{{ $estadosIconos[$ordenesCosecha->estado] }}</span>
                            {{ ucfirst($ordenesCosecha->estado) }}
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Botones de acción -->
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('ordenes_cosecha.edit', $ordenesCosecha) }}" class="bg-amber-600 hover:bg-amber-700 text-white px-5 py-2 rounded-full shadow-md transition-all duration-200 flex items-center gap-2 text-sm font-medium">
                    <i class="fas fa-edit"></i> Editar orden
                </a>
                <a href="{{ route('sesiones_cosecha.create', $ordenesCosecha) }}" class="bg-amber-600 hover:bg-amber-700 text-white px-5 py-2 rounded-full shadow-md transition-all duration-200 flex items-center gap-2 text-sm font-medium">
                    <i class="fas fa-edit"></i> Crear Sesion Cosecha
                </a>
                <form action="{{ route('ordenes_cosecha.destroy', $ordenesCosecha) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar esta orden?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-100 hover:bg-red-200 text-red-600 px-5 py-2 rounded-full shadow-md transition-all duration-200 flex items-center gap-2 text-sm font-medium">
                        <i class="fas fa-trash-alt"></i> Eliminar
                    </button>
                </form>
            </div>
        </div>

        <!-- Tarjetas de información principal -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-lg border border-green-100 p-4">
                <div class="flex items-center gap-3">
                    <div class="bg-green-100 p-2 rounded-lg">
                        <i class="fas fa-weight-hanging text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Solicitado</p>
                        <p class="text-lg font-bold text-green-700">{{ number_format($ordenesCosecha->cantidad_solicitada_kg ?? 0, 2) }} kg</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-lg border border-green-100 p-4">
                <div class="flex items-center gap-3">
                    <div class="bg-blue-100 p-2 rounded-lg">
                        <i class="fas fa-clipboard-list text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Planificado</p>
                        <p class="text-lg font-bold text-blue-700">{{ number_format($ordenesCosecha->cantidad_planificada_kg ?? 0, 2) }} kg</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-lg border border-green-100 p-4">
                <div class="flex items-center gap-3">
                    <div class="bg-amber-100 p-2 rounded-lg">
                        <i class="fas fa-tractor text-amber-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Recolectado</p>
                        <p class="text-lg font-bold text-amber-700">{{ number_format($ordenesCosecha->cantidad_recolectada_kg ?? 0, 2) }} kg</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-lg border border-green-100 p-4">
                <div class="flex items-center gap-3">
                    <div class="bg-purple-100 p-2 rounded-lg">
                        <i class="fas fa-percent text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Progreso</p>
                        @php
                            $progreso = $ordenesCosecha->cantidad_solicitada_kg > 0 ? 
                                min(($ordenesCosecha->cantidad_recolectada_kg / $ordenesCosecha->cantidad_solicitada_kg) * 100, 100) : 0;
                        @endphp
                        <p class="text-lg font-bold text-purple-700">{{ number_format($progreso, 1) }}%</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfica de progreso -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-green-100/80 mb-6">
            <div class="p-6 border-b border-green-100">
                <h3 class="text-lg font-semibold text-green-800 flex items-center gap-2">
                    <i class="fas fa-chart-line text-amber-600"></i>
                    Comparativa de Kilos
                </h3>
            </div>
            <div class="p-6">
                <div class="flex items-end gap-8 h-64 justify-center">
                    <div class="flex flex-col items-center">
                        <div class="relative group">
                            <div class="w-20 bg-gradient-to-t from-blue-400 to-blue-600 rounded-t-lg transition-all duration-300" 
                                 style="height: {{ min(($ordenesCosecha->cantidad_solicitada_kg ?? 0) / 2, 200) }}px;">
                            </div>
                            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 opacity-0 group-hover:opacity-100 transition-opacity bg-gray-800 text-white text-xs rounded py-1 px-2 whitespace-nowrap">
                                {{ number_format($ordenesCosecha->cantidad_solicitada_kg ?? 0, 2) }} kg
                            </div>
                        </div>
                        <span class="text-sm font-medium text-gray-600 mt-3">Solicitado</span>
                    </div>
                    
                    <div class="flex flex-col items-center">
                        <div class="relative group">
                            <div class="w-20 bg-gradient-to-t from-green-400 to-green-600 rounded-t-lg transition-all duration-300" 
                                 style="height: {{ min(($ordenesCosecha->cantidad_planificada_kg ?? 0) / 2, 200) }}px;">
                            </div>
                            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 opacity-0 group-hover:opacity-100 transition-opacity bg-gray-800 text-white text-xs rounded py-1 px-2 whitespace-nowrap">
                                {{ number_format($ordenesCosecha->cantidad_planificada_kg ?? 0, 2) }} kg
                            </div>
                        </div>
                        <span class="text-sm font-medium text-gray-600 mt-3">Planificado</span>
                    </div>
                    
                    <div class="flex flex-col items-center">
                        <div class="relative group">
                            <div class="w-20 bg-gradient-to-t from-amber-400 to-amber-600 rounded-t-lg transition-all duration-300" 
                                 style="height: {{ min(($ordenesCosecha->cantidad_recolectada_kg ?? 0) / 2, 200) }}px;">
                            </div>
                            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 opacity-0 group-hover:opacity-100 transition-opacity bg-gray-800 text-white text-xs rounded py-1 px-2 whitespace-nowrap">
                                {{ number_format($ordenesCosecha->cantidad_recolectada_kg ?? 0, 2) }} kg
                            </div>
                        </div>
                        <span class="text-sm font-medium text-gray-600 mt-3">Recolectado</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información detallada -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Línea de tiempo de estados -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-green-100/80">
                <div class="p-6 border-b border-green-100">
                    <h3 class="text-lg font-semibold text-green-800 flex items-center gap-2">
                        <i class="fas fa-clock text-amber-600"></i>
                        Línea de Tiempo
                    </h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center justify-between gap-2">
                        @php
                            $todosEstados = ['borrador', 'confirmada', 'en_proceso', 'completada', 'cancelada'];
                            $estadoActual = $ordenesCosecha->estado;
                            $indiceActual = array_search($estadoActual, $todosEstados);
                            $esCancelada = $estadoActual == 'cancelada';
                        @endphp
                        
                        @foreach($todosEstados as $index => $estado)
                            @php
                                $isActive = $index <= $indiceActual && !$esCancelada;
                                $isCompleted = $index < $indiceActual;
                                $isCurrent = $index == $indiceActual;
                                $isCanceled = $esCancelada && $index >= $indiceActual;
                                $isCanceledPrevious = $esCancelada && $index < $indiceActual;
                            @endphp
                            
                            <div class="flex flex-col items-center flex-1">
                                @if($index > 0)
                                    <div class="w-full h-0.5 relative -mb-1">
                                        <div class="absolute top-0 left-0 w-full h-full {{ $isActive && !$esCancelada ? 'bg-green-400' : ($isCanceledPrevious ? 'bg-red-300' : 'bg-gray-300') }}"></div>
                                    </div>
                                @endif
                                
                                <div class="relative group/tooltip mt-2">
                                    <div class="w-12 h-12 rounded-full flex items-center justify-center text-lg transition-all duration-300
                                        {{ $isActive && !$esCancelada ? 'bg-gradient-to-br from-green-400 to-green-600 text-white shadow-lg' : 'bg-gray-200 text-gray-400' }}
                                        {{ $isCurrent ? 'ring-4 ring-offset-2 ring-green-400 scale-110' : '' }}
                                        {{ $isCanceled ? 'bg-red-200 text-red-500 line-through' : '' }}
                                        {{ $isCanceledPrevious ? 'bg-red-100 text-red-400' : '' }}
                                        {{ $isCompleted ? 'opacity-80' : '' }}
                                        hover:scale-110 cursor-pointer">
                                        {{ $estadosIconos[$estado] }}
                                    </div>
                                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 opacity-0 group-hover/tooltip:opacity-100 transition-opacity bg-gray-800 text-white text-xs rounded py-1 px-2 whitespace-nowrap pointer-events-none">
                                        {{ ucfirst($estado) }}
                                        @if($isCurrent) (Actual) @endif
                                    </div>
                                </div>
                                <span class="text-xs text-gray-600 mt-1">{{ ucfirst($estado) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Detalles de la orden -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-green-100/80">
                <div class="p-6 border-b border-green-100">
                    <h3 class="text-lg font-semibold text-green-800 flex items-center gap-2">
                        <i class="fas fa-info-circle text-amber-600"></i>
                        Detalles de la Orden
                    </h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-4">
                        <div class="flex items-center justify-between border-b border-green-50 pb-2">
                            <dt class="text-sm text-gray-600 flex items-center gap-2">
                                <i class="fas fa-user text-green-600"></i> Cliente
                            </dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $ordenesCosecha->cliente->name ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex items-center justify-between border-b border-green-50 pb-2">
                            <dt class="text-sm text-gray-600 flex items-center gap-2">
                                <i class="fas fa-leaf text-green-600"></i> Ciclo Productivo
                            </dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $ordenesCosecha->cicloProductivo->nombre_campana ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex items-center justify-between border-b border-green-50 pb-2">
                            <dt class="text-sm text-gray-600 flex items-center gap-2">
                                <i class="fas fa-map-marked-alt text-green-600"></i> Lote Cultivo
                            </dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $ordenesCosecha->loteCultivo->nombre ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex items-center justify-between border-b border-green-50 pb-2">
                            <dt class="text-sm text-gray-600 flex items-center gap-2">
                                <i class="fas fa-vector-square text-green-600"></i> Zona
                            </dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $ordenesCosecha->loteZona->nombre ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex items-center justify-between border-b border-green-50 pb-2">
                            <dt class="text-sm text-gray-600 flex items-center gap-2">
                                <i class="fas fa-calendar-alt text-green-600"></i> Fecha Programada
                            </dt>
                            <dd class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($ordenesCosecha->fecha_programada)->locale('es')->isoFormat('D MMMM [de] Y') }}</dd>
                        </div>
                        <div class="flex items-center justify-between border-b border-green-50 pb-2">
                            <dt class="text-sm text-gray-600 flex items-center gap-2">
                                <i class="fas fa-calendar-check text-green-600"></i> Fecha Entrega
                            </dt>
                            <dd class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($ordenesCosecha->fecha_entrega)->locale('es')->isoFormat('D MMMM [de] Y') }}</dd>
                        </div>
                        <div class="flex items-center justify-between border-b border-green-50 pb-2">
                            <dt class="text-sm text-gray-600 flex items-center gap-2">
                                <i class="fas fa-user-tie text-green-600"></i> Responsable
                            </dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $ordenesCosecha->responsable->name ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex items-center justify-between border-b border-green-50 pb-2">
                            <dt class="text-sm text-gray-600 flex items-center gap-2">
                                <i class="fas fa-seedling text-green-600"></i> Variedad
                            </dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $ordenesCosecha->variedad_requerida ?? 'N/A' }}</dd>
                        </div>
                        @if($ordenesCosecha->notas)
                        <div class="flex items-start justify-between border-b border-green-50 pb-2">
                            <dt class="text-sm text-gray-600 flex items-center gap-2">
                                <i class="fas fa-sticky-note text-green-600"></i> Notas
                            </dt>
                            <dd class="text-sm text-gray-700 max-w-xs text-right">{{ $ordenesCosecha->notas }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>

        <!-- Fechas de inicio y fin -->
        @if($ordenesCosecha->fecha_inicio || $ordenesCosecha->fecha_fin)
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-green-100/80 mb-6">
            <div class="p-6 border-b border-green-100">
                <h3 class="text-lg font-semibold text-green-800 flex items-center gap-2">
                    <i class="fas fa-calendar-alt text-amber-600"></i>
                    Cronograma de Cosecha
                </h3>
            </div>
            <div class="p-6">
                <div class="flex items-center justify-between gap-4">
                    @if($ordenesCosecha->fecha_inicio)
                    <div class="flex-1 text-center">
                        <p class="text-sm text-gray-500">Fecha de Inicio</p>
                        <p class="text-lg font-bold text-green-700">{{ \Carbon\Carbon::parse($ordenesCosecha->fecha_inicio)->locale('es')->isoFormat('D MMMM [de] Y') }}</p>
                    </div>
                    @endif
                    @if($ordenesCosecha->fecha_inicio && $ordenesCosecha->fecha_fin)
                    <div class="flex-1 text-center">
                        <div class="h-1 bg-gradient-to-r from-green-400 to-amber-400 rounded-full"></div>
                    </div>
                    @endif
                    @if($ordenesCosecha->fecha_fin)
                    <div class="flex-1 text-center">
                        <p class="text-sm text-gray-500">Fecha de Fin</p>
                        <p class="text-lg font-bold text-amber-700">{{ \Carbon\Carbon::parse($ordenesCosecha->fecha_fin)->locale('es')->isoFormat('D MMMM [de] Y') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Puntos decorativos agrícolas -->
        <div class="mt-6 flex justify-center gap-2 opacity-40">
            <span class="inline-block w-2 h-2 rounded-full bg-green-300"></span>
            <span class="inline-block w-2 h-2 rounded-full bg-amber-300"></span>
            <span class="inline-block w-2 h-2 rounded-full bg-green-300"></span>
            <span class="inline-block w-2 h-2 rounded-full bg-amber-300"></span>
            <span class="inline-block w-2 h-2 rounded-full bg-green-300"></span>
            <span class="inline-block w-2 h-2 rounded-full bg-amber-300"></span>
            <span class="inline-block w-2 h-2 rounded-full bg-green-300"></span>
            <span class="inline-block w-2 h-2 rounded-full bg-amber-300"></span>
            <span class="inline-block w-2 h-2 rounded-full bg-green-300"></span>
        </div>

    </div>
</x-app-layout>