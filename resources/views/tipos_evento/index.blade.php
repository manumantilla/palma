<x-app-layout>
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Configuración de Labores y Eventos</h1>
                <p class="mt-2 text-sm text-gray-500">Administra las reglas de negocio, lógica financiera y restricciones fitosanitarias para cada actividad en campo.</p>
            </div>
            <div>
                <a href="{{ route('tipos-evento.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Configurar Nueva Labor
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-r-lg shadow-sm flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-emerald-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-left">
                    <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left">Labor / Actividad</th>
                            <th scope="col" class="px-6 py-4">Categoría</th>
                            <th scope="col" class="px-4 py-4">Mano de Obra</th>
                            <th scope="col" class="px-4 py-4">Insumos</th>
                            <th scope="col" class="px-4 py-4">Inventario</th>
                            <th scope="col" class="px-4 py-4">Flujo Caja</th>
                            <th scope="col" class="px-6 py-4 text-left">Restricciones de Seguridad</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white text-sm">
                        @forelse($tipos as $tipo)
                            <tr class="hover:bg-gray-50 transition-colors">
                                
                                <td class="px-6 py-4 whitespace-nowrap font-semibold text-gray-900">
                                    {{ $tipo->nombre }}
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                        @if($tipo->categoria === 'Fitosanitario') bg-red-50 text-red-700 border border-red-200
                                        @elseif($tipo->categoria === 'Fertilización') bg-blue-50 text-blue-700 border border-blue-200
                                        @elseif($tipo->categoria === 'Cosecha') bg-amber-50 text-amber-700 border border-amber-200
                                        @elseif($tipo->categoria === 'Logística') bg-purple-50 text-purple-700 border border-purple-200
                                        @else bg-gray-50 text-gray-700 border border-gray-200 @endif">
                                        {{ $tipo->categoria }}
                                    </span>
                                </td>

                                <td class="px-4 py-4 whitespace-nowrap text-center">
                                    <span class="inline-block p-1.5 rounded-md text-xs font-medium {{ $tipo->consume_mano_obra ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-400' }}">
                                        {{ $tipo->consume_mano_obra ? 'Jornales' : 'No aplica' }}
                                    </span>
                                </td>

                                <td class="px-4 py-4 whitespace-nowrap text-center">
                                    <span class="inline-block p-1.5 rounded-md text-xs font-medium {{ $tipo->consume_insumos ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-400' }}">
                                        {{ $tipo->consume_insumos ? 'Aplica' : 'No' }}
                                    </span>
                                </td>

                                <td class="px-4 py-4 whitespace-nowrap text-center">
                                    <span class="inline-block p-1.5 rounded-md text-xs font-medium {{ $tipo->genera_movimiento_stock ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-400' }}">
                                        {{ $tipo->genera_movimiento_stock ? 'Kárdex 📦' : 'No' }}
                                    </span>
                                </td>

                                <td class="px-4 py-4 whitespace-nowrap text-center">
                                    <span class="inline-block p-1.5 rounded-md text-xs font-semibold {{ $tipo->genera_income ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-50 text-rose-700' }}">
                                        {{ $tipo->genera_ingreso ? '+ Ingreso' : '- Costo OPEX' }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-xs text-gray-600">
                                    @if($tipo->categoria === 'Fitosanitario')
                                        <div class="space-y-1">
                                            <p class="flex items-center">
                                                <span class="w-2 h-2 rounded-full bg-amber-500 mr-2"></span>
                                                Reingreso: <strong class="ml-1 text-gray-900">{{ $tipo->periodo_reingreso_horas ?? 'N/A' }}h</strong>
                                            </p>
                                            <p class="flex items-center">
                                                <span class="w-2 h-2 rounded-full bg-red-500 mr-2"></span>
                                                Carencia: <strong class="ml-1 text-gray-900">{{ $tipo->periodo_carencia_dias ?? 'N/A' }} días</strong>
                                            </p>
                                        </div>
                                    @else
                                        <span class="text-gray-400 italic">Libre de restricciones</span>
                                    @endif
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="h-12 w-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                        <p class="text-base font-semibold text-gray-700">No hay labores configuradas</p>
                                        <p class="text-sm text-gray-400 max-w-xs mt-1">Crea el primer tipo de evento para estructurar las tareas del campo.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-8 bg-blue-50 border border-blue-100 rounded-xl p-5 shadow-sm">
            <h4 class="text-sm font-bold text-blue-900 uppercase tracking-wider mb-2">💡 Nota de Arquitectura de Software</h4>
            <p class="text-xs text-blue-700 leading-relaxed">
                Esta matriz funciona como el <strong>Metamodelo de Reglas</strong> del ERP. Los controladores de eventos de campo leen este catálogo de manera polimórfica para inyectar dinámicamente las compuertas lógicas en tiempo de ejecución. Esto mitiga el acoplamiento duro de datos y permite una sincronización <em>offline-first</em> consistente al estandarizar los payloads transaccionales.
            </p>
        </div>

    </div>
</x-app-layout>