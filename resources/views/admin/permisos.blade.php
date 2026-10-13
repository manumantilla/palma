<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-stone-800 leading-tight">Usuarios, roles y permisos</h2>
        <p class="text-sm text-stone-500 mt-1">Quién puede hacer qué en PALMA. Los cambios se aplican al instante.</p>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <livewire:admin.user-permissions-manager />
        </div>
    </div>
</x-app-layout>
