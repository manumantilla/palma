<x-app-layout>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-800">📋 Detalles de la Categoría</h2>
                        <div class="flex space-x-2">
                            <a href="{{ route('categorias-insumo.edit', $categorias_insumo) }}" 
                               class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                                <i class="fas fa-edit mr-2"></i> Editar
                            </a>
                            <a href="{{ route('categorias-insumo.index') }}" 
                               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                                <i class="fas fa-arrow-left mr-2"></i> Volver
                            </a>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-6 space-y-4">
                        <div class="border-b pb-3">
                            <label class="block text-gray-600 text-sm font-bold mb-1">ID:</label>
                            <p class="text-gray-800 text-lg">{{ $categorias_insumo->id }}</p>
                        </div>
                        
                        <div class="border-b pb-3">
                            <label class="block text-gray-600 text-sm font-bold mb-1">Nombre:</label>
                            <p class="text-gray-800 text-lg font-semibold">{{ $categorias_insumo->nombre }}</p>
                        </div>
                        
                        <div class="border-b pb-3">
                            <label class="block text-gray-600 text-sm font-bold mb-1">Maneja Vencimiento:</label>
                            <div class="mt-1">
                                @if($categorias_insumo->maneja_vencimiento)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-2"></i> Sí, maneja fechas de vencimiento
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                        <i class="fas fa-times-circle mr-2"></i> No maneja vencimiento
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="border-b pb-3">
                            <label class="block text-gray-600 text-sm font-bold mb-1">Maneja Toxicidad:</label>
                            <div class="mt-1">
                                @if($categorias_insumo->maneja_toxicidad)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-exclamation-triangle mr-2"></i> Sí, requiere control de toxicidad
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                        <i class="fas fa-check-circle mr-2"></i> No maneja toxicidad
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="border-b pb-3">
                            <label class="block text-gray-600 text-sm font-bold mb-1">Fecha de Creación:</label>
                            <p class="text-gray-800">{{ $categorias_insumo->created_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-gray-600 text-sm font-bold mb-1">Última Actualización:</label>
                            <p class="text-gray-800">{{ $categorias_insumo->updated_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                    </div>
                    
                    <!-- Información de insumos asociados (opcional) -->
                    @if($categorias_insumo->insumos && $categorias_insumo->insumos->count() > 0)
                    <div class="mt-6 bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">📦 Insumos asociados ({{ $categorias_insumo->insumos->count() }})</h3>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($categorias_insumo->insumos as $insumo)
                                <li class="text-gray-700">{{ $insumo->nombre }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>