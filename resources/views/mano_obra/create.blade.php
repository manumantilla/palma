<x-app-layout>
<div class="card-agri p-6 max-w-4xl mx-auto">
    <h2 class="text-2xl font-bold text-agri-green mb-6">Registrar mano de obra</h2>

    @if(session('error'))
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">
            @foreach($errors->all() as $error) <p>{{ $error }}</p> @endforeach
        </div>
    @endif

    <form action="{{ route('mano-obra.store') }}" method="POST"
          x-data="formManoObra({
              modo: @js(old('sesion_id') ? 'destajo_cosecha' : 'labor'),
              tipo: @js(old('tipo_labor', 'jornal_dia_completo')),
              cantidad: @js((float) old('cantidad', 1)),
              valor: @js((float) old('valor_unitario', 0)),
              costo: @js(old('costo_total') !== null ? (float) old('costo_total') : null),
              costoEditado: @js(old('costo_total') !== null),
              trabajadorId: @js((string) old('trabajador_id', '')),
              sesionId: @js((string) old('sesion_id', '')),
          })">
        @csrf

        {{-- ¿Labor normal o destajo de cosecha? --}}
        <div class="mb-5 inline-flex rounded-lg border border-gray-200 p-1 bg-gray-50 text-sm">
            <button type="button" @click="modo = 'labor'"
                    :class="modo === 'labor' ? 'bg-white shadow text-gray-900' : 'text-gray-500'" class="px-4 py-1.5 rounded-md">
                Jornal de una labor
            </button>
            <button type="button" @click="modo = 'destajo_cosecha'"
                    :class="modo === 'destajo_cosecha' ? 'bg-white shadow text-gray-900' : 'text-gray-500'" class="px-4 py-1.5 rounded-md">
                Destajo de cosecha
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="md:col-span-2" x-show="modo === 'labor'">
                <label class="block text-sm font-medium text-gray-700">Labor *</label>
                <select name="evento_campo_id" class="input-agri" :disabled="modo !== 'labor'">
                    <option value="">Seleccione la labor</option>
                    @foreach($eventos as $ev)
                        <option value="{{ $ev->id }}" @selected(old('evento_campo_id', $eventoSeleccionado) == $ev->id)>
                            #{{ $ev->id }} · {{ $ev->tipoEvento->nombre }} · {{ $ev->lote->nombre_lote ?? 'General' }} · {{ $ev->fecha_programada?->format('d/m/Y') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2" x-show="modo === 'destajo_cosecha'">
                <label class="block text-sm font-medium text-gray-700">Sesión de cosecha *</label>
                <select name="sesion_id" x-model="sesionId" class="input-agri" :disabled="modo !== 'destajo_cosecha'">
                    <option value="">Seleccione la sesión</option>
                    @foreach($sesiones as $ses)
                        <option value="{{ $ses->id }}">{{ $ses->fecha?->format('d/m/Y') }} · labor #{{ $ses->evento_campo_id }}</option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500">Elija también el trabajador: se mostrará lo que pesó en la báscula. Un recolector tiene un solo destajo por sesión.</p>
            </div>

            @include('mano_obra._campos', ['registro' => null])
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ url()->previous() }}" class="btn-outline-agri px-5 py-2 rounded-lg">Cancelar</a>
            <button type="submit" class="btn-agri px-6 py-2 rounded-lg shadow">Guardar</button>
        </div>
    </form>
</div>
@include('mano_obra._script')
</x-app-layout>
