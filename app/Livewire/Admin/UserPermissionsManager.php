<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Gestor de roles y permisos directos por usuario. Solo para Super Admin.
 *
 * Todo cambio pasa por syncAll(), que es donde viven las reglas de protección: así da igual
 * si el cambio vino de una tarjeta de rol, de un switch de permiso o de un botón masivo.
 */
class UserPermissionsManager extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(as: 'usuario')]
    public ?int $usuarioId = null;

    /**
     * Corre en CADA petición de Livewire, no solo al cargar la página. Los clics del componente
     * van a /livewire/update y no pasan por el middleware de la ruta, así que se valida aquí.
     */
    public function boot(): void
    {
        abort_unless(auth()->user()?->hasRole(User::SUPER_ADMIN), 403);
    }

    public function mount(): void
    {
        // ?usuario= en la URL puede venir de un enlace viejo o manipulado
        if ($this->usuarioId !== null && ! User::whereKey($this->usuarioId)->exists()) {
            $this->usuarioId = null;
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function seleccionar(int $id): void
    {
        $this->usuarioId = User::findOrFail($id)->id;
        unset($this->usuario, $this->viaRoles);
    }

    public function assignRole(string $rol): void
    {
        if (! $usuario = $this->usuario) {
            return;
        }

        $this->syncAll(
            $usuario->roles->pluck('name')->push($rol)->unique()->all(),
            $usuario->permissions->pluck('name')->all(),
            "Rol {$rol} asignado a {$usuario->name}."
        );
    }

    public function removeRole(string $rol): void
    {
        if (! $usuario = $this->usuario) {
            return;
        }

        $this->syncAll(
            $usuario->roles->pluck('name')->reject(fn ($r) => $r === $rol)->values()->all(),
            $usuario->permissions->pluck('name')->all(),
            "Rol {$rol} retirado a {$usuario->name}."
        );
    }

    public function togglePermission(string $permiso): void
    {
        if (! $usuario = $this->usuario) {
            return;
        }

        $directos = $usuario->permissions->pluck('name');

        if ($directos->contains($permiso)) {
            $nuevos = $directos->reject(fn ($p) => $p === $permiso)->values()->all();
            $mensaje = "Permiso directo retirado: {$this->etiqueta($permiso)}.";
        } else {
            if ($roles = $this->viaRoles()->get($permiso)) {
                $this->toast("Ya lo tiene por su rol " . implode(', ', $roles) . '. Para quitarlo, retire el rol.', 'info');
                return;
            }
            $nuevos = $directos->push($permiso)->all();
            $mensaje = "Permiso concedido: {$this->etiqueta($permiso)}.";
        }

        $this->syncAll($usuario->roles->pluck('name')->all(), $nuevos, $mensaje);
    }

    /** Deja al usuario solo con lo que le dan sus roles. */
    public function limpiarDirectos(): void
    {
        if ($usuario = $this->usuario) {
            $this->syncAll($usuario->roles->pluck('name')->all(), [], 'Permisos directos eliminados.');
        }
    }

    /**
     * Reemplaza roles y permisos directos del usuario seleccionado en una sola transacción.
     * Los nombres que no existan en la base se descartan (los argumentos llegan del navegador).
     */
    public function syncAll(array $roles, array $permisos, ?string $mensaje = null): void
    {
        if (! $usuario = $this->usuario) {
            return;
        }

        $roles = Role::whereIn('name', $roles)->pluck('name')->all();
        $permisos = Permission::whereIn('name', $permisos)->pluck('name')->all();

        if ($error = $this->violaProteccion($usuario, $roles)) {
            $this->toast($error, 'error');
            return;
        }

        DB::transaction(function () use ($usuario, $roles, $permisos) {
            $usuario->syncRoles($roles);
            $usuario->syncPermissions($permisos);
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        unset($this->usuario, $this->usuarios, $this->roles, $this->viaRoles);

        $this->toast($mensaje ?? "Permisos de {$usuario->name} guardados.");
    }

    // ------------------------------------------------------------------

    #[Computed]
    public function usuarios()
    {
        $termino = '%' . trim($this->search) . '%';

        return User::query()
            ->with('roles:id,name')
            ->withCount('permissions')
            ->when(trim($this->search) !== '', fn ($q) => $q->where(fn ($q) => $q
                ->where('name', 'ilike', $termino)
                ->orWhere('email', 'ilike', $termino)
                ->orWhereHas('roles', fn ($r) => $r->where('name', 'ilike', $termino))))
            ->orderBy('name')
            ->paginate(12);
    }

    #[Computed]
    public function usuario(): ?User
    {
        return $this->usuarioId
            ? User::with(['roles.permissions:id,name,guard_name', 'permissions:id,name,guard_name'])->find($this->usuarioId)
            : null;
    }

    #[Computed]
    public function roles(): Collection
    {
        // No se usa withCount('users'): Role::users() busca el modelo según el guard por defecto,
        // y el middleware auth:sanctum lo cambia a 'sanctum', que no tiene provider (null) y revienta
        // con "Class name must be a valid object or a string". Se cuenta directo en la tabla pivote.
        $usuariosPorRol = DB::table(config('permission.table_names.model_has_roles'))
            ->where('model_type', (new User)->getMorphClass())
            ->selectRaw(app(PermissionRegistrar::class)->pivotRole . ' AS rol, COUNT(*) AS total')
            ->groupBy('rol')
            ->pluck('total', 'rol');

        return Role::with('permissions:id,name,guard_name')
            ->orderBy('name')
            ->get()
            ->each(fn (Role $rol) => $rol->users_count = (int) ($usuariosPorRol[$rol->id] ?? 0));
    }

    /**
     * Permisos agrupados por módulo según config/permisos.php. Los que existan en la base
     * pero no estén en el catálogo aparecen en "Otros" para que nunca queden ocultos.
     *
     * @return array<string, array{nombre: string, permisos: array<string, string>}>
     */
    #[Computed]
    public function catalogo(): array
    {
        $modulos = config('permisos.modulos');
        $conocidos = collect($modulos)->flatMap(fn ($m) => array_keys($m['permisos']));

        $otros = Permission::whereNotIn('name', $conocidos)->orderBy('name')->pluck('name');
        if ($otros->isNotEmpty()) {
            $modulos['otros'] = ['nombre' => 'Otros', 'permisos' => $otros->mapWithKeys(fn ($p) => [$p => $p])->all()];
        }

        return $modulos;
    }

    /** permiso => roles del usuario que lo conceden */
    #[Computed]
    public function viaRoles(): Collection
    {
        $mapa = collect();
        foreach ($this->usuario?->roles ?? [] as $rol) {
            foreach ($rol->permissions as $permiso) {
                $mapa[$permiso->name] = [...($mapa[$permiso->name] ?? []), $rol->name];
            }
        }

        return $mapa;
    }

    public function render()
    {
        return view('livewire.admin.user-permissions-manager');
    }

    // ------------------------------------------------------------------

    /** Devuelve el motivo si el cambio dejaría al sistema sin quien lo administre. */
    private function violaProteccion(User $usuario, array $rolesNuevos): ?string
    {
        $pierdeSuperAdmin = $usuario->hasRole(User::SUPER_ADMIN) && ! in_array(User::SUPER_ADMIN, $rolesNuevos, true);

        if (! $pierdeSuperAdmin) {
            return null;
        }

        // Quien está aquí ya es Super Admin y no puede quitarse el rol: siempre queda al menos uno
        return match (true) {
            $usuario->id === User::ID_FUNDADOR => 'El usuario fundador (ID 1) siempre conserva el rol Super Admin.',
            $usuario->id === auth()->id()      => 'No puedes quitarte a ti mismo el rol Super Admin: perderías el acceso a esta pantalla.',
            default => null,
        };
    }

    private function etiqueta(string $permiso): string
    {
        foreach (config('permisos.modulos') as $modulo) {
            if (isset($modulo['permisos'][$permiso])) {
                return $modulo['permisos'][$permiso];
            }
        }

        return $permiso;
    }

    private function toast(string $mensaje, string $tipo = 'ok'): void
    {
        $this->dispatch('toast', mensaje: $mensaje, tipo: $tipo);
    }
}
