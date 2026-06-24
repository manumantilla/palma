<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">
                        {{ isset($trabajador) ? '✏️ Editar Trabajador' : '➕ Nuevo Trabajador' }}
                    </h2>
                    
                    <form action="{{ isset($trabajador) ? route('trabajadores.update', $trabajador) : route('trabajadores.store') }}" 
                          method="POST">
                        @csrf
                        @if(isset($trabajador))
                            @method('PUT')
                        @endif
                        
                        <!-- Datos Personales -->
                        <div class="bg-gray-50 rounded-lg p-6 mb-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">📋 Datos Personales</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Usuario Asociado</label>
                                    <select name="user_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('user_id') border-red-500 @enderror">
                                        <option value="">Seleccione un usuario...</option>
                                        @foreach($usuarios as $usuario)
                                            <option value="{{ $usuario->id }}" {{ old('user_id', $trabajador->user_id ?? '') == $usuario->id ? 'selected' : '' }}>
                                                {{ $usuario->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Tipo Documento *</label>
                                    <select name="tipo_documento" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('tipo_documento') border-red-500 @enderror">
                                        <option value="CC" {{ old('tipo_documento', $trabajador->tipo_documento ?? 'CC') == 'CC' ? 'selected' : '' }}>CC - Cédula</option>
                                        <option value="CE" {{ old('tipo_documento', $trabajador->tipo_documento ?? '') == 'CE' ? 'selected' : '' }}>CE - Cédula Extranjería</option>
                                        <option value="NIT" {{ old('tipo_documento', $trabajador->tipo_documento ?? '') == 'NIT' ? 'selected' : '' }}>NIT</option>
                                        <option value="PPT" {{ old('tipo_documento', $trabajador->tipo_documento ?? '') == 'PPT' ? 'selected' : '' }}>PPT - Pasaporte</option>
                                    </select>
                                    @error('tipo_documento') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Número Documento *</label>
                                    <input type="text" 
                                           name="numero_documento" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('numero_documento') border-red-500 @enderror"
                                           value="{{ old('numero_documento', $trabajador->numero_documento ?? '') }}">
                                    @error('numero_documento') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Nombres *</label>
                                    <input type="text" 
                                           name="nombres" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nombres') border-red-500 @enderror"
                                           value="{{ old('nombres', $trabajador->nombres ?? '') }}">
                                    @error('nombres') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Apellidos *</label>
                                    <input type="text" 
                                           name="apellidos" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('apellidos') border-red-500 @enderror"
                                           value="{{ old('apellidos', $trabajador->apellidos ?? '') }}">
                                    @error('apellidos') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Fecha Nacimiento</label>
                                    <input type="date" 
                                           name="fecha_nacimiento" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('fecha_nacimiento') border-red-500 @enderror"
                                           value="{{ old('fecha_nacimiento', isset($trabajador) && $trabajador->fecha_nacimiento ? $trabajador->fecha_nacimiento->format('Y-m-d') : '') }}">
                                    @error('fecha_nacimiento') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Género</label>
                                    <select name="genero" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('genero') border-red-500 @enderror">
                                        <option value="">Seleccione...</option>
                                        <option value="M" {{ old('genero', $trabajador->genero ?? '') == 'M' ? 'selected' : '' }}>Masculino</option>
                                        <option value="F" {{ old('genero', $trabajador->genero ?? '') == 'F' ? 'selected' : '' }}>Femenino</option>
                                        <option value="Otro" {{ old('genero', $trabajador->genero ?? '') == 'Otro' ? 'selected' : '' }}>Otro</option>
                                    </select>
                                    @error('genero') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- Información Laboral -->
                        <div class="bg-gray-50 rounded-lg p-6 mb-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">💼 Información Laboral</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Cargo *</label>
                                    <input type="text" 
                                           name="cargo" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('cargo') border-red-500 @enderror"
                                           value="{{ old('cargo', $trabajador->cargo ?? '') }}">
                                    @error('cargo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Tipo Contrato *</label>
                                    <select name="tipo_contrato" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('tipo_contrato') border-red-500 @enderror">
                                        <option value="indefinido" {{ old('tipo_contrato', $trabajador->tipo_contrato ?? '') == 'indefinido' ? 'selected' : '' }}>Indefinido</option>
                                        <option value="fijo" {{ old('tipo_contrato', $trabajador->tipo_contrato ?? '') == 'fijo' ? 'selected' : '' }}>Fijo</option>
                                        <option value="por_labores" {{ old('tipo_contrato', $trabajador->tipo_contrato ?? '') == 'por_labores' ? 'selected' : '' }}>Por Labores</option>
                                        <option value="aprendizaje" {{ old('tipo_contrato', $trabajador->tipo_contrato ?? '') == 'aprendizaje' ? 'selected' : '' }}>Aprendizaje</option>
                                    </select>
                                    @error('tipo_contrato') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Fecha Ingreso *</label>
                                    <input type="date" 
                                           name="fecha_ingreso" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('fecha_ingreso') border-red-500 @enderror"
                                           value="{{ old('fecha_ingreso', isset($trabajador) && $trabajador->fecha_ingreso ? $trabajador->fecha_ingreso->format('Y-m-d') : date('Y-m-d')) }}">
                                    @error('fecha_ingreso') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Fecha Retiro</label>
                                    <input type="date" 
                                           name="fecha_retiro" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('fecha_retiro') border-red-500 @enderror"
                                           value="{{ old('fecha_retiro', isset($trabajador) && $trabajador->fecha_retiro ? $trabajador->fecha_retiro->format('Y-m-d') : '') }}">
                                    @error('fecha_retiro') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Forma de Pago *</label>
                                    <select name="forma_pago" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('forma_pago') border-red-500 @enderror">
                                        <option value="jornal" {{ old('forma_pago', $trabajador->forma_pago ?? '') == 'jornal' ? 'selected' : '' }}>Jornal</option>
                                        <option value="destajo" {{ old('forma_pago', $trabajador->forma_pago ?? '') == 'destajo' ? 'selected' : '' }}>Destajo</option>
                                        <option value="mixto" {{ old('forma_pago', $trabajador->forma_pago ?? '') == 'mixto' ? 'selected' : '' }}>Mixto</option>
                                    </select>
                                    @error('forma_pago') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Salario Base ($)</label>
                                    <input type="number" 
                                           step="1000" 
                                           name="salario_base" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('salario_base') border-red-500 @enderror"
                                           value="{{ old('salario_base', $trabajador->salario_base ?? '') }}">
                                    @error('salario_base') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Banco y Número de Cuenta</label>
                                    <input type="text" 
                                           name="banco_numero_cuenta" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('banco_numero_cuenta') border-red-500 @enderror"
                                           placeholder="Ej: Bancolombia - 123456789"
                                           value="{{ old('banco_numero_cuenta', $trabajador->banco_numero_cuenta ?? '') }}">
                                    @error('banco_numero_cuenta') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                
                                <div class="flex items-center space-x-4">
                                    <label class="flex items-center">
                                        <input type="checkbox" 
                                               name="activo" 
                                               value="1"
                                               {{ old('activo', isset($trabajador) ? $trabajador->activo : true) ? 'checked' : '' }}
                                               class="form-checkbox h-5 w-5 text-blue-600">
                                        <span class="ml-2 text-gray-700">Trabajador Activo</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Seguridad Social -->
                        <div class="bg-gray-50 rounded-lg p-6 mb-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">🏥 Seguridad Social</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">EPS</label>
                                    <input type="text" 
                                           name="eps" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                           placeholder="Ej: Sura, Sanitas"
                                           value="{{ old('eps', $trabajador->eps ?? '') }}">
                                </div>
                                
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">ARL</label>
                                    <input type="text" 
                                           name="arl" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                           placeholder="Ej: Positiva, Sura"
                                           value="{{ old('arl', $trabajador->arl ?? '') }}">
                                </div>
                                
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">AFP</label>
                                    <input type="text" 
                                           name="afp" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                           placeholder="Ej: Porvenir, Colpensiones"
                                           value="{{ old('afp', $trabajador->afp ?? '') }}">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Habilidades -->
                        <div class="bg-gray-50 rounded-lg p-6 mb-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">🎯 Habilidades</h3>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Habilidades (separadas por comas)</label>
                                <textarea 
                                    name="habilidades_input" 
                                    rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('habilidades_input') border-red-500 @enderror"
                                    placeholder="Ej: PHP, Laravel, JavaScript, MySQL, Trabajo en equipo">{{ 
                                    old('habilidades_input', 
                                        isset($trabajador) && $trabajador->habilidades 
                                            ? implode(', ', $trabajador->habilidades) 
                                            : '') 
                                }}</textarea>
                                @error('habilidades_input') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                <p class="text-gray-500 text-xs mt-1">Ingrese las habilidades separadas por comas. Ejemplo: "PHP, Laravel, Base de datos"</p>
                            </div>
                        </div>
                        
                        <!-- Botones -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('trabajadores.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                                <i class="fas fa-times mr-2"></i> Cancelar
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                                <i class="fas fa-save mr-2"></i> {{ isset($trabajador) ? 'Actualizar' : 'Guardar' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        const fechaIngreso = document.querySelector('input[name="fecha_ingreso"]');
        const fechaRetiro = document.querySelector('input[name="fecha_retiro"]');
        
        if(fechaIngreso && fechaRetiro) {
            fechaIngreso.addEventListener('change', function() {
                if(fechaRetiro.value && fechaRetiro.value < this.value) {
                    alert('La fecha de retiro no puede ser anterior a la fecha de ingreso');
                    fechaRetiro.value = '';
                }
            });
            
            fechaRetiro.addEventListener('change', function() {
                if(this.value && this.value < fechaIngreso.value) {
                    alert('La fecha de retiro no puede ser anterior a la fecha de ingreso');
                    this.value = '';
                }
            });
        }
    </script>
    @endpush
</x-app-layout>