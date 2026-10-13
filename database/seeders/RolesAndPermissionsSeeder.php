<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Roles y permisos de PALMA según config/permisos.php. Se puede correr las veces que se quiera:
 * crea lo que falta y deja a cada rol con exactamente los permisos del catálogo.
 */
class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (config('permisos.modulos') as $modulo) {
            foreach (array_keys($modulo['permisos']) as $permiso) {
                Permission::firstOrCreate(['name' => $permiso, 'guard_name' => 'web']);
            }
        }

        // Sin permisos asignados: los recibe todos por Gate::before
        Role::firstOrCreate(['name' => User::SUPER_ADMIN, 'guard_name' => 'web']);

        foreach (config('permisos.roles') as $rol => $permisos) {
            Role::firstOrCreate(['name' => $rol, 'guard_name' => 'web'])->syncPermissions($permisos);
        }

        // Sin esto, después de un migrate:fresh nadie podría entrar al gestor de permisos
        User::find(User::ID_FUNDADOR)?->assignRole(User::SUPER_ADMIN);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
