<x-app-layout>
<div class="min-h-screen bg-gradient-to-br from-green-50 via-lime-50 to-amber-50 py-8 relative overflow-hidden">

    <!-- Decoración de fondo: hojas flotantes -->
    <div class="pointer-events-none absolute inset-0 overflow-hidden">
        <svg class="absolute -top-10 -left-10 w-40 h-40 text-green-200 opacity-40 animate-float-slow" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17 8C8 10 5.9 16.17 3.82 21.34l1.89.66.95-2.3c.48.17.98.3 1.34.3C19 20 22 3 22 3c-1 2-8 2.25-13 3.25S2 11.5 2 13.5s1.75 3.75 1.75 3.75C7 8 17 8 17 8z"/>
        </svg>
        <svg class="absolute top-1/3 -right-12 w-52 h-52 text-lime-300 opacity-30 animate-float-slower" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17 8C8 10 5.9 16.17 3.82 21.34l1.89.66.95-2.3c.48.17.98.3 1.34.3C19 20 22 3 22 3c-1 2-8 2.25-13 3.25S2 11.5 2 13.5s1.75 3.75 1.75 3.75C7 8 17 8 17 8z"/>
        </svg>
        <svg class="absolute bottom-0 left-1/4 w-32 h-32 text-amber-200 opacity-30 animate-float-slow" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17 8C8 10 5.9 16.17 3.82 21.34l1.89.66.95-2.3c.48.17.98.3 1.34.3C19 20 22 3 22 3c-1 2-8 2.25-13 3.25S2 11.5 2 13.5s1.75 3.75 1.75 3.75C7 8 17 8 17 8z"/>
        </svg>
    </div>

    <div class="container mx-auto px-4 relative z-10">

        <!-- Header -->
        <div class="flex items-center mb-8 animate-fade-in-down">
            <a href="{{ route('contenedores.index') }}"
               class="text-green-700 hover:text-green-900 mr-4 transition-all duration-300 hover:-translate-x-1 hover:scale-110">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h1 class="text-3xl font-bold text-green-800 flex items-center">
                <span class="relative mr-3">
                    <svg class="w-9 h-9 text-green-600 animate-grow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </span>
                Nuevo Contenedor
                <span class="ml-3 text-xl">🥑</span>
            </h1>
        </div>

        <!-- Formulario -->
        <div class="bg-white/90 backdrop-blur rounded-2xl shadow-xl hover:shadow-2xl transition-shadow duration-500 p-8 max-w-4xl mx-auto border-l-4 border-green-600 animate-fade-in-up">

            <!-- Franja decorativa superior tipo "cosecha" -->
            <div class="flex items-center gap-2 mb-6 text-green-700/70 text-xs font-semibold uppercase tracking-wider">
                <div class="h-px flex-1 bg-gradient-to-r from-transparent via-green-300 to-transparent"></div>
                <span>Registro de cosecha {{ $sesion->codigo ?? ('Sesión #'.$sesion->id) }}</span>
                <div class="h-px flex-1 bg-gradient-to-r from-transparent via-green-300 to-transparent"></div>
            </div>

            <form action="{{ route('contenedores.store') }}" method="POST" class="space-y-6">
                @csrf

                <input type="hidden" name="sesion_id" value="{{$sesion->id}}">
                <!-- Campo Nombre (NUEVO) -->
            <div class="mb-6 field-group">
                <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">
                    Nombre del Contenedor *
                </label>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}"
                    class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 transition-colors duration-200 @error('nombre') border-red-500 @enderror"
                    placeholder="Ej: Tolva Extra Grande #1">
                @error('nombre')
                    <p class="text-red-500 text-xs mt-1 animate-shake">{{ $message }}</p>
                @enderror
            </div>
                <!-- Estado y Variedad -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="field-group">
                        <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">
                            Estado *
                        </label>
                        <select name="estado" id="estado"
                                class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 transition-colors duration-200 @error('estado') border-red-500 @enderror">
                            <option value="">Seleccionar estado</option>
                            <option value="abierta" {{ old('estado') == 'abierta' ? 'selected' : '' }}>🟢 Abierta</option>
                            <option value="cerrada" {{ old('estado') == 'cerrada' ? 'selected' : '' }}>🟡 Cerrada</option>
                            <option value="despachada" {{ old('estado') == 'despachada' ? 'selected' : '' }}>🚚 Despachada</option>
                        </select>
                        @error('estado')
                            <p class="text-red-500 text-xs mt-1 animate-shake">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field-group">
                        <label for="variedad" class="block text-sm font-medium text-gray-700 mb-1">
                            Variedad
                        </label>
                        <input type="text" name="variedad" id="variedad" value="{{ old('variedad') }}"
                               class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 transition-colors duration-200 @error('variedad') border-red-500 @enderror"
                               placeholder="Ej: Hass, Fuerte, Bacon">
                        @error('variedad')
                            <p class="text-red-500 text-xs mt-1 animate-shake">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Calidad y Destino -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="field-group">
                        <label for="calidad" class="block text-sm font-medium text-gray-700 mb-1">
                            Calidad
                        </label>
                        <select name="calidad" id="calidad"
                                class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 transition-colors duration-200 @error('calidad') border-red-500 @enderror">
                            <option value="">Seleccionar calidad</option>
                            <option value="extra" {{ old('calidad') == 'extra' ? 'selected' : '' }}>Extra</option>
                            <option value="primera" {{ old('calidad') == 'primera' ? 'selected' : '' }}>Primera</option>
                            <option value="segunda" {{ old('calidad') == 'segunda' ? 'selected' : '' }}>Segunda</option>
                            <option value="industria" {{ old('calidad') == 'industria' ? 'selected' : '' }}>Industria</option>
                            <option value="descarte" {{ old('calidad') == 'descarte' ? 'selected' : '' }}>Descarte</option>
                        </select>
                        @error('calidad')
                            <p class="text-red-500 text-xs mt-1 animate-shake">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field-group">
                        <label for="tipo_destino" class="block text-sm font-medium text-gray-700 mb-1">
                            Tipo de Destino *
                        </label>
                        <select name="tipo_destino" id="tipo_destino"
                                class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 transition-colors duration-200 @error('tipo_destino') border-red-500 @enderror">
                            <option value="">Seleccionar destino</option>
                            <option value="exportacion" {{ old('tipo_destino') == 'exportacion' ? 'selected' : '' }}>🌍 Exportación</option>
                            <option value="mercado_local" {{ old('tipo_destino') == 'mercado_local' ? 'selected' : '' }}>🏪 Mercado Local</option>
                            <option value="industria" {{ old('tipo_destino') == 'industria' ? 'selected' : '' }}>🏭 Industria</option>
                            <option value="consumo_interno" {{ old('tipo_destino') == 'consumo_interno' ? 'selected' : '' }}>🏠 Consumo Interno</option>
                            <option value="descarte" {{ old('tipo_destino') == 'descarte' ? 'selected' : '' }}>🗑️ Descarte</option>
                        </select>
                        @error('tipo_destino')
                            <p class="text-red-500 text-xs mt-1 animate-shake">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Calibre y Pesos -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="field-group">
                        <label for="calibre_talla" class="block text-sm font-medium text-gray-700 mb-1">
                            Calibre / Talla
                        </label>
                        <select name="calibre_talla" id="calibre_talla"
                                class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 transition-colors duration-200 @error('calibre_talla') border-red-500 @enderror">
                            <option value="">Seleccionar talla</option>
                            <option value="pequeño" {{ old('calibre_talla') == 'pequeño' ? 'selected' : '' }}>Pequeño</option>
                            <option value="mediano" {{ old('calibre_talla') == 'mediano' ? 'selected' : '' }}>Mediano</option>
                            <option value="grande" {{ old('calibre_talla') == 'grande' ? 'selected' : '' }}>Grande</option>
                            <option value="jumbo" {{ old('calibre_talla') == 'jumbo' ? 'selected' : '' }}>Jumbo</option>
                        </select>
                        @error('calibre_talla')
                            <p class="text-red-500 text-xs mt-1 animate-shake">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field-group">
                        <label for="peso_tara" class="block text-sm font-medium text-gray-700 mb-1">
                            Peso Tara (kg) *
                        </label>
                        <input type="number" step="0.01" name="peso_tara" id="peso_tara" value="{{ old('peso_tara') }}"
                               class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 transition-colors duration-200 @error('peso_tara') border-red-500 @enderror"
                               placeholder="0.00">
                        @error('peso_tara')
                            <p class="text-red-500 text-xs mt-1 animate-shake">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field-group">
                        <label for="kilos_acumulados" class="block text-sm font-medium text-gray-700 mb-1">
                            Kilos Acumulados
                        </label>
                        <input type="number" step="0.01" name="kilos_acumulados" id="kilos_acumulados"
                               value="{{ old('kilos_acumulados', 0) }}"
                               class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 transition-colors duration-200 @error('kilos_acumulados') border-red-500 @enderror"
                               placeholder="0.00">
                        @error('kilos_acumulados')
                            <p class="text-red-500 text-xs mt-1 animate-shake">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('contenedores.index') }}"
                       class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold rounded-lg transition-all duration-200 hover:scale-105 active:scale-95">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="group relative px-8 py-2 bg-green-700 hover:bg-green-800 text-white font-bold rounded-lg shadow-lg transition-all duration-300 transform hover:scale-105 active:scale-95 overflow-hidden">
                        <span class="absolute inset-0 bg-white/20 -translate-x-full group-hover:translate-x-full transition-transform duration-700 ease-out"></span>
                        <svg class="w-5 h-5 inline mr-2 relative" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span class="relative">Guardar Contenedor</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    @keyframes fade-in-down {
        0%   { opacity: 0; transform: translateY(-16px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    @keyframes fade-in-up {
        0%   { opacity: 0; transform: translateY(20px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    @keyframes grow {
        0%, 100% { transform: scale(1); }
        50%      { transform: scale(1.15); }
    }
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        20%      { transform: translateX(-4px); }
        40%      { transform: translateX(4px); }
        60%      { transform: translateX(-3px); }
        80%      { transform: translateX(3px); }
    }
    @keyframes float-slow {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        50%      { transform: translateY(-18px) rotate(6deg); }
    }
    @keyframes float-slower {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        50%      { transform: translateY(-12px) rotate(-5deg); }
    }

    .animate-fade-in-down { animation: fade-in-down .5s ease-out both; }
    .animate-fade-in-up   { animation: fade-in-up .6s ease-out .1s both; }
    .animate-grow         { animation: grow 2.4s ease-in-out infinite; }
    .animate-shake        { animation: shake .4s ease-in-out; }
    .animate-float-slow   { animation: float-slow 8s ease-in-out infinite; }
    .animate-float-slower { animation: float-slower 11s ease-in-out infinite; }

    .field-group { animation: fade-in-up .5s ease-out both; }
    .field-group:nth-of-type(2) { animation-delay: .05s; }
</style>
</x-app-layout>