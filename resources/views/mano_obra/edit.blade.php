<x-app-layout>
<div class="card-agri p-6 max-w-4xl mx-auto">
    <h2 class="text-2xl font-bold text-agri-green mb-1">Editar registro #{{ $registro->id }}</h2>
    <p class="text-sm text-gray-500 mb-6">
        Labor #{{ $registro->evento_campo_id }} · {{ $registro->eventoCampo->tipoEvento->nombre ?? '' }}
        @if($registro->kilos_pesados) · báscula: {{ number_format($registro->kilos_pesados, 2, ',', '.') }} kg @endif
    </p>

    @if(session('error'))
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">
            @foreach($errors->all() as $error) <p>{{ $error }}</p> @endforeach
        </div>
    @endif

    <form action="{{ route('mano-obra.update', $registro) }}" method="POST"
          x-data="formManoObra({
              tipo: @js(old('tipo_labor', $registro->tipo_labor)),
              cantidad: @js((float) old('cantidad', $registro->cantidad)),
              valor: @js((float) old('valor_unitario', $registro->valor_unitario)),
              costo: @js((float) old('costo_total', $registro->costo_total)),
              costoEditado: @js(abs($registro->ajuste) >= 1),
              trabajadorId: @js((string) old('trabajador_id', $registro->trabajador_id ?? '')),
          })">
        @csrf @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @include('mano_obra._campos', ['registro' => $registro])
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('mano-obra.index') }}" class="btn-outline-agri px-5 py-2 rounded-lg">Cancelar</a>
            <button type="submit" class="btn-agri px-6 py-2 rounded-lg shadow">Actualizar</button>
        </div>
    </form>
</div>
@include('mano_obra._script')
</x-app-layout>
