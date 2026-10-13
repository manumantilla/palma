@php
    $usuario = $this->usuario;
    $esSuperAdmin = $usuario?->hasRole(\App\Models\User::SUPER_ADMIN);
    $directos = $usuario?->permissions->pluck('name')->all() ?? [];
    $rolesUsuario = $usuario?->roles->pluck('name')->all() ?? [];
    $rolProtegido = fn (string $rol) => $rol === \App\Models\User::SUPER_ADMIN
        && $usuario
        && in_array($rol, $rolesUsuario, true)
        && ($usuario->id === \App\Models\User::ID_FUNDADOR || $usuario->id === auth()->id());
@endphp

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    {{-- ============ Usuarios ============ --}}
    <section class="lg:col-span-4 bg-white rounded-xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-stone-100">
            <label for="buscar-usuario" class="sr-only">Buscar usuario</label>
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-stone-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.45 4.39l3.08 3.08a.75.75 0 11-1.06 1.06l-3.08-3.08A7 7 0 012 9z" clip-rule="evenodd"/>
                </svg>
                <input id="buscar-usuario" type="search" wire:model.live.debounce.300ms="search"
                       placeholder="Nombre, correo o rol"
                       class="w-full pl-9 rounded-lg border-stone-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
            </div>
        </div>

        <ul class="divide-y divide-stone-100" wire:loading.class="opacity-60" wire:target="search">
            @forelse ($this->usuarios as $u)
                <li wire:key="usuario-{{ $u->id }}">
                    <button type="button" wire:click="seleccionar({{ $u->id }})"
                            @class([
                                'w-full text-left px-4 py-3 flex items-center gap-3 transition',
                                'bg-emerald-50' => $usuario?->id === $u->id,
                                'hover:bg-stone-50' => $usuario?->id !== $u->id,
                            ])>
                        <img src="{{ $u->profile_photo_url }}" alt="" class="h-9 w-9 rounded-full object-cover shrink-0">
                        <span class="min-w-0 flex-1">
                            <span class="block text-sm font-medium text-stone-800 truncate">{{ $u->name }}</span>
                            <span class="block text-xs text-stone-500 truncate">{{ $u->email }}</span>
                            <span class="mt-1 flex flex-wrap gap-1">
                                @forelse ($u->roles as $r)
                                    <span class="text-[11px] px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800">{{ $r->name }}</span>
                                @empty
                                    <span class="text-[11px] px-1.5 py-0.5 rounded bg-stone-100 text-stone-500">Sin rol</span>
                                @endforelse
                                @if ($u->permissions_count)
                                    <span class="text-[11px] px-1.5 py-0.5 rounded bg-amber-100 text-amber-800">+{{ $u->permissions_count }} directos</span>
                                @endif
                            </span>
                        </span>
                    </button>
                </li>
            @empty
                <li class="px-4 py-10 text-center text-sm text-stone-500">Ningún usuario coincide con “{{ $search }}”.</li>
            @endforelse
        </ul>

        @if ($this->usuarios->hasPages())
            <div class="p-3 border-t border-stone-100">{{ $this->usuarios->links() }}</div>
        @endif
    </section>

    {{-- ============ Detalle ============ --}}
    <section class="lg:col-span-8 space-y-6">
        @if (! $usuario)
            <div class="bg-white rounded-xl border border-dashed border-stone-300 p-12 text-center">
                <p class="text-stone-700 font-medium">Seleccione un usuario</p>
                <p class="text-sm text-stone-500 mt-1">Verá sus roles y podrá conceder o quitar permisos al instante.</p>
            </div>
        @else
            {{-- Encabezado --}}
            <div class="bg-white rounded-xl border border-stone-200 shadow-sm p-5 flex flex-wrap items-center gap-4">
                <img src="{{ $usuario->profile_photo_url }}" alt="" class="h-14 w-14 rounded-full object-cover">
                <div class="flex-1 min-w-0">
                    <h3 class="text-lg font-semibold text-stone-800 truncate">{{ $usuario->name }}</h3>
                    <p class="text-sm text-stone-500 truncate">{{ $usuario->email }}</p>
                </div>
                @if ($directos)
                    <button type="button" wire:click="limpiarDirectos"
                            wire:confirm="¿Quitar los {{ count($directos) }} permisos directos de {{ $usuario->name }}? Conservará los de sus roles."
                            class="text-sm px-3 py-2 rounded-lg border border-stone-300 text-stone-700 hover:bg-stone-50">
                        Quitar permisos directos
                    </button>
                @endif
            </div>

            @if ($esSuperAdmin)
                <div class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-900">
                    <strong>Acceso total.</strong> Como Super Admin pasa todas las verificaciones de permisos;
                    los de abajo solo aplicarían si dejara de serlo.
                </div>
            @endif

            {{-- Roles --}}
            <div class="bg-white rounded-xl border border-stone-200 shadow-sm p-5">
                <h4 class="text-sm font-semibold text-stone-800">Roles</h4>
                <p class="text-xs text-stone-500 mb-4">Clic para asignar o retirar. Un rol trae su paquete de permisos.</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3">
                    @foreach ($this->roles as $rol)
                        @php
                            $tiene = in_array($rol->name, $rolesUsuario, true);
                            $bloqueado = $rolProtegido($rol->name);
                        @endphp
                        <button type="button" wire:key="rol-{{ $rol->id }}"
                                @if ($bloqueado) disabled title="Este rol no se le puede retirar a este usuario"
                                @else wire:click="{{ $tiene ? 'removeRole' : 'assignRole' }}(@js($rol->name))" @endif
                                wire:loading.attr="disabled"
                                @class([
                                    'text-left rounded-lg border p-3 transition',
                                    'border-emerald-600 bg-emerald-50 ring-1 ring-emerald-600' => $tiene,
                                    'border-stone-200 hover:border-stone-400' => ! $tiene,
                                    'cursor-not-allowed' => $bloqueado,
                                ])>
                            <span class="flex items-center justify-between gap-2">
                                <span @class(['text-sm font-medium', 'text-emerald-900' => $tiene, 'text-stone-700' => ! $tiene])>{{ $rol->name }}</span>
                                @if ($bloqueado)
                                    <svg class="h-4 w-4 text-emerald-700" viewBox="0 0 20 20" fill="currentColor" aria-label="Protegido">
                                        <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd"/>
                                    </svg>
                                @elseif ($tiene)
                                    <svg class="h-4 w-4 text-emerald-700" viewBox="0 0 20 20" fill="currentColor" aria-label="Asignado">
                                        <path fill-rule="evenodd" d="M16.7 5.3a1 1 0 010 1.4l-8 8a1 1 0 01-1.4 0l-4-4a1 1 0 111.4-1.4L8 12.58l7.3-7.3a1 1 0 011.4 0z" clip-rule="evenodd"/>
                                    </svg>
                                @endif
                            </span>
                            <span class="block text-xs text-stone-500 mt-1">
                                {{ $rol->name === \App\Models\User::SUPER_ADMIN ? 'Todos los permisos' : $rol->permissions->count() . ' permisos' }}
                                · {{ $rol->users_count }} {{ $rol->users_count === 1 ? 'usuario' : 'usuarios' }}
                            </span>
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Permisos --}}
            <div class="bg-white rounded-xl border border-stone-200 shadow-sm p-5">
                <div class="flex flex-wrap items-end justify-between gap-3 mb-4">
                    <div>
                        <h4 class="text-sm font-semibold text-stone-800">Permisos</h4>
                        <p class="text-xs text-stone-500">Los directos se suman a los de sus roles.</p>
                    </div>
                    <div class="flex flex-wrap gap-2 text-[11px]">
                        <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">Concedido</span>
                        <span class="px-2 py-0.5 rounded-full bg-sky-100 text-sky-800">Por rol</span>
                        <span class="px-2 py-0.5 rounded-full bg-stone-100 text-stone-500">No asignado</span>
                    </div>
                </div>

                <div class="space-y-5">
                    @foreach ($this->catalogo as $clave => $modulo)
                        <div wire:key="modulo-{{ $clave }}">
                            <p class="text-xs font-semibold uppercase tracking-wide text-stone-400 mb-2">{{ $modulo['nombre'] }}</p>
                            <ul class="divide-y divide-stone-100 border border-stone-100 rounded-lg">
                                @foreach ($modulo['permisos'] as $permiso => $descripcion)
                                    @php
                                        $directo = in_array($permiso, $directos, true);
                                        $porRol = $this->viaRoles->get($permiso);
                                        $encendido = $directo || $porRol; // por rol se ve encendido pero en azul
                                    @endphp
                                    <li wire:key="permiso-{{ $permiso }}" class="flex items-center gap-3 px-3 py-2.5">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm text-stone-800">{{ $descripcion }}</p>
                                            <p class="text-[11px] text-stone-400 font-mono">{{ $permiso }}</p>
                                        </div>

                                        @if ($directo)
                                            <span class="text-[11px] px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">Concedido</span>
                                        @elseif ($porRol)
                                            <span class="text-[11px] px-2 py-0.5 rounded-full bg-sky-100 text-sky-800" title="{{ implode(', ', $porRol) }}">Por rol</span>
                                        @elseif ($esSuperAdmin)
                                            <span class="text-[11px] px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">Total</span>
                                        @else
                                            <span class="text-[11px] px-2 py-0.5 rounded-full bg-stone-100 text-stone-500">No asignado</span>
                                        @endif

                                        <button type="button" role="switch" aria-checked="{{ $directo ? 'true' : 'false' }}"
                                                aria-label="Permiso directo: {{ $descripcion }}"
                                                wire:click="togglePermission(@js($permiso))"
                                                wire:loading.attr="disabled" wire:target="togglePermission(@js($permiso))"
                                                @class([
                                                    'relative inline-flex h-6 w-11 shrink-0 rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:ring-offset-2 disabled:opacity-50',
                                                    'bg-emerald-600' => $directo,
                                                    'bg-sky-300' => ! $directo && $porRol,
                                                    'bg-stone-300' => ! $directo && ! $porRol,
                                                ])>
                                            <span @class([
                                                'inline-block h-5 w-5 mt-0.5 rounded-full bg-white shadow transition-transform',
                                                'translate-x-5' => $encendido,
                                                'translate-x-0.5' => ! $encendido,
                                            ])></span>
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </section>

    {{-- ============ Toasts ============ --}}
    <div x-data="{
            avisos: [],
            mostrar(detalle) {
                const id = Date.now() + Math.random();
                this.avisos.push({ id, ...detalle });
                setTimeout(() => this.avisos = this.avisos.filter(a => a.id !== id), 4000);
            }
         }"
         x-on:toast.window="mostrar($event.detail)"
         class="fixed bottom-4 right-4 z-50 flex flex-col gap-2 w-[calc(100%-2rem)] max-w-sm"
         aria-live="polite">
        <template x-for="aviso in avisos" :key="aviso.id">
            <div x-transition.opacity
                 class="rounded-lg shadow-lg border px-4 py-3 text-sm bg-white"
                 :class="{
                    'border-emerald-300 text-emerald-900': aviso.tipo === 'ok',
                    'border-red-300 text-red-800': aviso.tipo === 'error',
                    'border-sky-300 text-sky-900': aviso.tipo === 'info',
                 }"
                 x-text="aviso.mensaje"></div>
        </template>
    </div>
</div>
