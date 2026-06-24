<x-app-layout>
    <div class="max-w-7xl mx-auto p-6">

        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold">Lotes GIS</h1>
                <p class="text-gray-500">
                    Administración de lotes agrícolas
                </p>
            </div>

            <a href="{{ route('lotes-gis.create') }}"
               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                Nuevo Lote
            </a>
        </div>

        <div class="bg-white shadow rounded-xl overflow-hidden">

            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3 text-left">Código</th>
                        <th class="p-3 text-left">Nombre</th>
                        <th class="p-3 text-left">Finca</th>
                        <th class="p-3 text-left">Área</th>
                        <th class="p-3 text-left">Altitud</th>
                        <th class="p-3 text-center">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($lotes as $lote)
                    <tr class="border-b">

                        <td class="p-3">
                            {{ $lote->codigo_lote }}
                        </td>

                        <td class="p-3">
                            {{ $lote->nombre_lote }}
                        </td>

                        <td class="p-3">
                            {{ $lote->finca->nombre }}
                        </td>

                        <td class="p-3">
                            {{ $lote->area_hectareas_declaradas }} ha
                        </td>

                        <td class="p-3">
                            {{ $lote->altitud_mediana_msnm }} msnm
                        </td>

                        <td class="p-3 text-center">
                            <a href="{{ route('lotes-gis.show',$lote) }}"
                               class="text-blue-600 font-semibold">
                               Ver
                            </a>
                        </td>

                    </tr>
                    @endforeach
                </tbody>

            </table>

        </div>

    </div>
</x-app-layout>