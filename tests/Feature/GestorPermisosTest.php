<?php

namespace Tests\Feature;

use App\Livewire\Admin\UserPermissionsManager;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use Tests\TestCase;

class GestorPermisosTest extends TestCase
{
    use RefreshDatabase;

    private User $fundador;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Postgres no reinicia la secuencia entre tests: el fundador se crea con id 1 a la fuerza
        // y la secuencia se adelanta para que los demás usuarios no choquen con él.
        $this->fundador = User::factory()->create(['id' => User::ID_FUNDADOR, 'name' => 'Fundador']);
        DB::statement("SELECT setval(pg_get_serial_sequence('users', 'id'), GREATEST((SELECT MAX(id) FROM users), 100))");

        $this->seed(RolesAndPermissionsSeeder::class); // le da Super Admin al usuario 1

        // withPersonalTeam: el menú de Jetstream lee currentTeam y sin equipo la página revienta
        $this->admin = User::factory()->withPersonalTeam()->create(['name' => 'Otra Admin'])->assignRole(User::SUPER_ADMIN);
    }

    public function test_solo_el_super_admin_entra_a_la_pagina(): void
    {
        $agronomo = User::factory()->withPersonalTeam()->create()->assignRole('Agrónomo');

        $this->actingAs($agronomo)->get(route('admin.permisos'))->assertForbidden();
        $this->actingAs($this->admin)->get(route('admin.permisos'))->assertOk()->assertSee('Roles y permisos');
    }

    public function test_el_componente_rechaza_a_quien_no_es_super_admin_aunque_salte_la_ruta(): void
    {
        $this->actingAs(User::factory()->create()->assignRole('Almacenista'));

        Livewire::test(UserPermissionsManager::class)->assertForbidden();
    }

    public function test_nadie_le_quita_el_super_admin_al_fundador(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(UserPermissionsManager::class)
            ->call('seleccionar', $this->fundador->id)
            ->call('removeRole', User::SUPER_ADMIN)
            ->assertDispatched('toast', tipo: 'error');

        $this->assertTrue($this->fundador->fresh()->hasRole(User::SUPER_ADMIN));
    }

    public function test_no_puedes_quitarte_tu_propio_super_admin(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(UserPermissionsManager::class)
            ->call('seleccionar', $this->admin->id)
            ->call('syncAll', ['Agrónomo'], [])
            ->assertDispatched('toast', tipo: 'error');

        $this->assertTrue($this->admin->fresh()->hasRole(User::SUPER_ADMIN));
    }

    public function test_un_super_admin_si_puede_retirarle_el_rol_a_otro(): void
    {
        $otro = User::factory()->create()->assignRole(User::SUPER_ADMIN);
        $this->actingAs($this->admin);

        Livewire::test(UserPermissionsManager::class)
            ->call('seleccionar', $otro->id)
            ->call('removeRole', User::SUPER_ADMIN)
            ->assertDispatched('toast', tipo: 'ok');

        $this->assertFalse($otro->fresh()->hasRole(User::SUPER_ADMIN));
    }

    public function test_asignar_rol_y_conceder_permiso_directo(): void
    {
        $operario = User::factory()->create();
        $this->actingAs($this->admin);

        Livewire::test(UserPermissionsManager::class)
            ->call('seleccionar', $operario->id)
            ->call('assignRole', 'Supervisor de Campo')
            ->call('togglePermission', 'gestionar_kardex');

        $operario = $operario->fresh();
        $this->assertTrue($operario->hasRole('Supervisor de Campo'));
        $this->assertTrue($operario->hasDirectPermission('gestionar_kardex'));
        $this->assertTrue($operario->can('gestionar_cosecha'));   // por el rol
        $this->assertFalse($operario->can('gestionar_finanzas')); // ni rol ni directo

        // Segundo clic: lo retira
        Livewire::test(UserPermissionsManager::class)
            ->call('seleccionar', $operario->id)
            ->call('togglePermission', 'gestionar_kardex');
        $this->assertFalse($operario->fresh()->hasDirectPermission('gestionar_kardex'));
    }

    public function test_no_duplica_como_directo_un_permiso_que_ya_da_el_rol(): void
    {
        $almacenista = User::factory()->create()->assignRole('Almacenista');
        $this->actingAs($this->admin);

        Livewire::test(UserPermissionsManager::class)
            ->call('seleccionar', $almacenista->id)
            ->call('togglePermission', 'gestionar_inventario')
            ->assertDispatched('toast', tipo: 'info');

        $this->assertFalse($almacenista->fresh()->hasDirectPermission('gestionar_inventario'));
    }

    public function test_ignora_roles_y_permisos_inventados_desde_el_navegador(): void
    {
        $usuario = User::factory()->create();
        $this->actingAs($this->admin);

        Livewire::test(UserPermissionsManager::class)
            ->call('seleccionar', $usuario->id)
            ->call('syncAll', ['Rol Inventado', 'Contador'], ['borrar_todo', 'ver_dashboard']);

        $usuario = $usuario->fresh();
        $this->assertSame(['Contador'], $usuario->getRoleNames()->all());
        $this->assertSame(['ver_dashboard'], $usuario->getDirectPermissions()->pluck('name')->all());
    }

    public function test_busca_por_nombre_correo_o_rol(): void
    {
        User::factory()->create(['name' => 'Carmenza Rueda', 'email' => 'carmenza@finca.co'])->assignRole('Contador');
        User::factory()->create(['name' => 'Pedro Ardila', 'email' => 'pedro@finca.co']);
        $this->actingAs($this->admin);

        $componente = Livewire::test(UserPermissionsManager::class);

        $componente->set('search', 'carmenza')->assertSee('Carmenza Rueda')->assertDontSee('Pedro Ardila');
        $componente->set('search', 'contador')->assertSee('Carmenza Rueda')->assertDontSee('Pedro Ardila');
        $componente->set('search', 'pedro@')->assertSee('Pedro Ardila')->assertDontSee('Carmenza Rueda');
    }

    public function test_el_super_admin_pasa_cualquier_permiso_por_gate_before(): void
    {
        $this->assertTrue(Gate::forUser($this->admin)->allows('aprobar_ordenes_cosecha'));
        $this->assertTrue(Gate::forUser($this->admin)->allows('permiso_que_aun_no_existe'));
    }

    public function test_el_seeder_se_puede_correr_dos_veces(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $this->assertSame(1, \Spatie\Permission\Models\Role::where('name', 'Agrónomo')->count());
        $this->assertTrue($this->fundador->fresh()->hasRole(User::SUPER_ADMIN));
    }
}
