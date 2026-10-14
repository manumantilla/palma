{{--
    Campos comunes de crear/editar. El x-data del formulario padre define:
    tipo, cantidad, valor, costo, costoEditado, trabajadorId, kilos, pesajes, sugerido(), ajuste().
--}}
@php $r = $registro ?? null; @endphp

<div>
    <label class="block text-sm font-medium text-gray-700">Trabajador registrado</label>
    <select name="trabajador_id" x-model="trabajadorId" class="input-agri">
        <option value="">— Jornalero de paso (escribir nombre) —</option>
        @foreach($trabajadores as $t)
            <option value="{{ $t->id }}">{{ $t->nombres }} {{ $t->apellidos }}{{ $t->numero_documento ? ' · ' . $t->numero_documento : '' }}</option>
        @endforeach
    </select>
</div>

<div x-show="!trabajadorId" class="grid grid-cols-2 gap-3">
    <div>
        <label class="block text-sm font-medium text-gray-700">Nombre *</label>
        <input type="text" name="nombre_trabajador" value="{{ old('nombre_trabajador', $r?->trabajador_id ? '' : $r?->nombre_trabajador) }}"
               placeholder="Nombre completo" class="input-agri" />
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Documento <span class="text-gray-400">(si lo tiene)</span></label>
        <input type="text" name="cedula" value="{{ old('cedula', $r?->trabajador_id ? '' : $r?->cedula) }}" class="input-agri" />
    </div>
</div>

<div>
    <label class="block text-sm font-medium text-gray-700">Forma de pago *</label>
    <select name="tipo_labor" x-model="tipo" class="input-agri" required>
        <option value="jornal_dia_completo">Jornal día completo</option>
        <option value="jornal_medio_dia">Medio jornal</option>
        <option value="hora_extra">Horas</option>
        <option value="destajo">Destajo (por unidad)</option>
    </select>
</div>

<div>
    <label class="block text-sm font-medium text-gray-700">Día trabajado *</label>
    <input type="date" name="fecha_labor" max="{{ now()->toDateString() }}"
           value="{{ old('fecha_labor', $r?->fecha_labor?->toDateString() ?? now()->toDateString()) }}" class="input-agri" required />
</div>

<div>
    <label class="block text-sm font-medium text-gray-700">
        Cantidad * <span class="text-gray-400" x-text="tipo === 'destajo' ? '' : (tipo === 'hora_extra' ? '(horas)' : '(jornales)')"></span>
    </label>
    <input type="number" step="0.01" min="0.01" name="cantidad" x-model.number="cantidad" class="input-agri" required />
    <p x-show="kilos !== null" class="mt-1 text-xs text-emerald-700">
        Báscula: <strong x-text="kilos.toLocaleString('es-CO')"></strong> kg en <span x-text="pesajes"></span> pesaje(s)
        <button type="button" class="underline ml-1" x-show="cantidad != kilos" @click="cantidad = kilos">usar</button>
    </p>
</div>

<div x-show="tipo === 'destajo'">
    <label class="block text-sm font-medium text-gray-700">Unidad del destajo *</label>
    <input type="text" name="unidad_destajo" value="{{ old('unidad_destajo', $r?->unidad_destajo) }}" x-ref="unidad"
           placeholder="kg, racimos, árboles, surcos..." class="input-agri" />
</div>

<div>
    <label class="block text-sm font-medium text-gray-700">Valor unitario *</label>
    <input type="number" step="1" min="0" name="valor_unitario" x-model.number="valor" class="input-agri" required />
</div>

<div class="md:col-span-2 rounded-lg border border-emerald-200 bg-emerald-50/50 p-4">
    <div class="flex flex-wrap items-end gap-4">
        <div class="flex-1 min-w-[12rem]">
            <label class="block text-sm font-medium text-gray-700">Valor a pagar *</label>
            <input type="number" step="1" min="0" name="costo_total" x-model.number="costo" @input="costoEditado = true"
                   class="input-agri text-lg font-semibold" required />
        </div>
        <div class="text-sm text-gray-600 pb-2">
            Calculado: <strong x-text="'$' + sugerido().toLocaleString('es-CO')"></strong>
            <template x-if="Math.abs(ajuste()) >= 1">
                <span class="ml-2" :class="ajuste() > 0 ? 'text-amber-700' : 'text-sky-700'"
                      x-text="(ajuste() > 0 ? '+' : '−') + '$' + Math.abs(ajuste()).toLocaleString('es-CO') + ' ajustado a mano'"></span>
            </template>
            <button type="button" x-show="costoEditado" @click="costoEditado = false; costo = sugerido()" class="ml-2 underline">volver al calculado</button>
        </div>
    </div>
    <p class="text-xs text-gray-500 mt-1">Se puede subir o bajar según lo acordado con el trabajador.</p>
</div>

<div>
    <label class="block text-sm font-medium text-gray-700">Hectáreas trabajadas</label>
    <input type="number" step="0.01" min="0" name="area_ejecutada_ha" value="{{ old('area_ejecutada_ha', $r?->area_ejecutada_ha) }}" class="input-agri" />
</div>
<div>
    <label class="block text-sm font-medium text-gray-700">Árboles atendidos</label>
    <input type="number" step="1" min="0" name="arboles_atendidos" value="{{ old('arboles_atendidos', $r?->arboles_atendidos) }}" class="input-agri" />
</div>

<div class="md:col-span-2">
    <label class="block text-sm font-medium text-gray-700">Observaciones</label>
    <textarea name="observaciones" rows="2" class="input-agri" placeholder="Ej.: se le sumó por terreno pendiente">{{ old('observaciones', $r?->observaciones) }}</textarea>
</div>
