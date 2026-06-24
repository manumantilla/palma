<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-800">👤 Detalles del Trabajador</h2>
                        <div class="flex space-x-2">
                            <a href="{{ route('trabajadores.edit', $trabajador) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                                <i class="fas fa-edit mr-2"></i> Editar
                            </a>
                            <a href="{{ route('trabajadores.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                                <i class="fas fa-arrow-left mr-2"></i> Volver
                            </a>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-6 mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-600 text-sm font-bold mb-1">ID:</label>
                                <p class="text-gray-800">{{ $trabajador->id }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-gray-600 text-sm font-bold mb-1">Usuario Asociado:</label>
                                <p class="text-gray-800">{{ $trabajador->user ? $trabajador->user->name : 'No asociado' }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-gray-600 text-sm font-bold mb-1">Tipo Documento:</label>
                                <p class="text-gray-800">{{ $trabajador->tipo_documento }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-gray-600 text-sm font-bold mb-1">Número Documento:</label>
                                <p class="text-gray-800">{{ $trabajador->numero_documento }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-gray-600 text-sm font-bold mb-1">Nombres:</label>
                                <p class="text-gray-800">{{ $trabajador->nombres }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-gray-600 text-sm font-bold mb-1">Apellidos:</label>
                                <p class="text-gray-800">{{ $trabajador->apellidos }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-gray-600 text-sm font-bold mb-1">Fecha Nacimiento:</label>
                                <p class="text-gray-800">{{ $trabajador->fecha_nacimiento ? \Carbon\Carbon::parse($trabajador->fecha_nacimiento)->format('d/m/Y') : 'N/A' }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-gray-600 text-sm font-bold mb-1">Género:</label>
                                <p class="text-gray-800">
                                    @if($trabajador->genero == 'M') Masculino
                                    @elseif($trabajador->genero == 'F') Femenino
                                    @elseif($trabajador->genero == 'Otro') Otro
                                    @else N/A @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">💼 Información Laboral</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-600 text-sm font-bold mb-1">Cargo:</label>
                                <p class="text-gray-800">{{ $trabajador->cargo }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-gray-600 text-sm font-bold mb-1">Tipo Contrato:</label>
                                <p class="text-gray-800">{{ ucfirst($trabajador->tipo_contrato) }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-gray-600 text-sm font-bold mb-1">Fecha Ingreso:</label>
                                <p class="text-gray-800">{{ \Carbon\Carbon::parse($trabajador->fecha_ingreso)->format('d/m/Y') }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-gray-600 text-sm font-bold mb-1">Fecha Retiro:</label>
                                <p class="text-gray-800">{{ $trabajador->fecha_retiro ? \Carbon\Carbon::parse($trabajador->fecha_retiro)->format('d/m/Y') : 'Activo' }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-gray-600 text-sm font-bold mb-1">Forma de Pago:</label>
                                <p class="text-gray-800">{{ ucfirst($trabajador->forma_pago) }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-gray-600 text-sm font-bold mb-1">Salario Base:</label>
                                <p class="text-gray-800">$ {{ number_format($trabajador->salario_base, 0, ',', '.') }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-gray-600 text-sm font-bold mb-1">Cuenta Bancaria:</label>
                                <p class="text-gray-800">{{ $trabajador->banco_numero_cuenta ?? 'No registrada' }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-gray-600 text-sm font-bold mb-1">Estado:</label>
                                <p class="text-gray-800">
                                    @if($trabajador->activo)
                                        <span class="inline-block px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded">Activo</span>
                                    @else
                                        <span class="inline-block px-2 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded">Inactivo</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">🏥 Seguridad Social</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-gray-600 text-sm font-bold mb-1">EPS:</label>
                                <p class="text-gray-800">{{ $trabajador->eps ?? 'N/A' }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-gray-600 text-sm font-bold mb-1">ARL:</label>
                                <p class="text-gray-800">{{ $trabajador->arl ?? 'N/A' }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-gray-600 text-sm font-bold mb-1">AFP:</label>
                                <p class="text-gray-800">{{ $trabajador->afp ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                    
                    @if($trabajador->habilidades)
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">🎯 Habilidades</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($trabajador->habilidades as $habilidad)
                                <span class="inline-block px-3 py-1 text-sm font-semibold text-blue-700 bg-blue-100 rounded-full">
                                    {{ $habilidad }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>