<x-app-layout>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">
                        {{ isset($categorias_insumo) ? '✏️ Editar Categoría' : '➕ Nueva Categoría' }}
                    </h2>
                    
                    <form action="{{ isset($categorias_insumo) ? route('categorias-insumo.update', $categorias_insumo) : route('categorias-insumo.store') }}" 
                          method="POST">
                        @csrf
                        @if(isset($categorias_insumo))
                            @method('PUT')
                        @endif
                        
                        <div class="space-y-6">
                            <!-- Nombre -->
                            <div>
                                <label for="nombre" class="block text-gray-700 text-sm font-bold mb-2">
                                    Nombre de la Categoría *
                                </label>
                                <input type="text" 
                                       id="nombre"
                                       name="nombre" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nombre') border-red-500 @enderror"
                                       placeholder="Ej: Medicamentos, Equipos de protección, Limpieza..."
                                       value="{{ old('nombre', $categorias_insumo->nombre ?? '') }}"
                                       required>
                                @error('nombre')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Maneja Vencimiento -->
                            <div class="flex items-center justify-between">
                                <div>
                                    <label for="maneja_vencimiento" class="text-gray-700 text-sm font-bold">
                                        ¿Maneja fecha de vencimiento?
                                    </label>
                                    <p class="text-gray-500 text-xs mt-1">
                                        Marque esta opción si los insumos de esta categoría tienen fecha de expiración.
                                    </p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" 
                                           id="maneja_vencimiento"
                                           name="maneja_vencimiento" 
                                           value="1"
                                           class="sr-only peer"
                                           {{ old('maneja_vencimiento', isset($categorias_insumo) ? $categorias_insumo->maneja_vencimiento : false) ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                            
                            <!-- Maneja Toxicidad -->
                            <div class="flex items-center justify-between">
                                <div>
                                    <label for="maneja_toxicidad" class="text-gray-700 text-sm font-bold">
                                        ¿Maneja nivel de toxicidad?
                                    </label>
                                    <p class="text-gray-500 text-xs mt-1">
                                        Marque esta opción si los insumos de esta categoría requieren control de toxicidad.
                                    </p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" 
                                           id="maneja_toxicidad"
                                           name="maneja_toxicidad" 
                                           value="1"
                                           class="sr-only peer"
                                           {{ old('maneja_toxicidad', isset($categorias_insumo) ? $categorias_insumo->maneja_toxicidad : false) ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-yellow-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-yellow-600"></div>
                                </label>
                            </div>
                            
                            <!-- Botones -->
                            <div class="flex justify-end space-x-3 pt-4">
                                <a href="{{ route('categorias-insumo.index') }}" 
                                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                                    <i class="fas fa-times mr-2"></i> Cancelar
                                </a>
                                <button type="submit" 
                                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                                    <i class="fas fa-save mr-2"></i> 
                                    {{ isset($categorias_insumo) ? 'Actualizar' : 'Guardar' }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>