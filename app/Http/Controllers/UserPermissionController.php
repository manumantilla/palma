<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserPermissionsRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserPermissionController extends Controller
{
    /**
     * Listado de usuarios con conteo de roles y permisos.
     */
    public function index(): View
    {
        $this->authorize('viewAny', User::class);

        $users = User::with(['roles:id,name', 'permissions:id,name'])
            ->withCount(['roles', 'permissions'])
            ->orderBy('name')
            ->paginate(15);

        return view('admin.users.permissions.index', compact('users'));
    }

    /**
     * Formulario para gestionar roles y permisos de un usuario.
     */
    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        $roles = Role::with('permissions')->get();

        $permissions = Permission::all()->groupBy(function ($perm) {
            return explode('.', $perm->name)[0];
        });

        // Permisos DIRECTOS (asignados al usuario, no vía rol)
        $userDirectPermissions = $user->permissions->pluck('name')->toArray();

        // Permisos que hereda por sus roles
        $permissionsViaRoles = $user->getPermissionsViaRoles()->pluck('name')->toArray();

        // Todos los permisos efectivos
        $allPermissions = $user->getAllPermissions()->pluck('name')->toArray();

        $userRoles = $user->roles->pluck('name')->toArray();

        return view('admin.users.permissions.edit', compact(
            'user',
            'roles',
            'permissions',
            'userRoles',
            'userDirectPermissions',
            'permissionsViaRoles',
            'allPermissions'
        ));
    }

    /**
     * Actualiza roles y permisos DIRECTOS del usuario (sync masivo).
     */
    public function update(UpdateUserPermissionsRequest $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        // Protección Super Admin
        if ($user->hasRole('Super Admin') && ! $request->user()->hasRole('Super Admin')) {
            return back()->withErrors(['roles' => 'No puedes modificar a un Super Admin.']);
        }

        // Sincroniza roles (los que no vengan, se quitan)
        $user->syncRoles($request->input('roles', []));

        // Sincroniza permisos directos (los heredados por rol NO se tocan)
        $user->syncPermissions($request->input('permissions', []));

        return redirect()
            ->route('admin.users.permissions.index')
            ->with('flash.banner', 'Permisos actualizados exitosamente.');
    }

    /**
     * Asignar un permiso DIRECTO puntual.
     */
    public function assignPermission(Request $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $validated = $request->validate([
            'permission' => ['required', 'string', 'exists:permissions,name'],
        ]);

        if ($user->hasDirectPermission($validated['permission'])) {
            return back()->withErrors(['permission' => 'El usuario ya tiene ese permiso directo.']);
        }

        $user->givePermissionTo($validated['permission']);

        return back()->with('flash.banner', 'Permiso asignado correctamente.');
    }

    /**
     * Revocar un permiso DIRECTO puntual.
     */
    public function revokePermission(Request $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $validated = $request->validate([
            'permission' => ['required', 'string', 'exists:permissions,name'],
        ]);

        if (! $user->hasDirectPermission($validated['permission'])) {
            return back()->withErrors(['permission' => 'El usuario no tiene ese permiso directo.']);
        }

        $user->revokePermissionTo($validated['permission']);

        return back()->with('flash.banner', 'Permiso revocado correctamente.');
    }

    /**
     * Asignar rol puntual.
     */
    public function assignRole(Request $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $validated = $request->validate([
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        if ($user->hasRole($validated['role'])) {
            return back()->withErrors(['role' => 'El usuario ya tiene ese rol.']);
        }

        $user->assignRole($validated['role']);

        return back()->with('flash.banner', 'Rol asignado correctamente.');
    }

    /**
     * Revocar rol puntual.
     */
    public function revokeRole(Request $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $validated = $request->validate([
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        if ($validated['role'] === 'Super Admin') {
            return back()->withErrors(['role' => 'No se puede quitar el rol Super Admin.']);
        }

        if (! $user->hasRole($validated['role'])) {
            return back()->withErrors(['role' => 'El usuario no tiene ese rol.']);
        }

        $user->removeRole($validated['role']);

        return back()->with('flash.banner', 'Rol revocado correctamente.');
    }

    /**
     * Quitar TODOS los permisos directos (deja solo los heredados por rol).
     */
    public function resetPermissions(User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $user->syncPermissions([]);

        return back()->with('flash.banner', 'Permisos directos eliminados.');
    }

    /**
     * Quitar TODOS los roles (deja al usuario sin permisos por rol).
     */
    public function resetRoles(User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        if ($user->hasRole('Super Admin')) {
            return back()->withErrors(['role' => 'No se puede quitar el rol Super Admin.']);
        }

        $user->syncRoles([]);

        return back()->with('flash.banner', 'Roles eliminados.');
    }
}