<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Limpiar la caché de permisos de spatie
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. DEFINIR PERMISOS BASADOS EN TUS CONTROLADORES

        $permissions = [
            // Módulo Dashboard y Analítica
            'ver_dashboard',
            'ver_analitica_grafos', // Para ArbolGrafoController
            'ver_bitacora',
            
            // Módulo Configuración Agrícola (Lotes, Árboles, Cultivos)
            'gestionar_configuracion_agricola', // LoteController, ArbolController, CultivoController
            
            // Módulo Fitosanitario y Suelos (Ingenieros)
            'gestionar_fitosanidad', // ArbolHistorialFitosanitario, FenologiaEtapa, LoteAnaliticaSuelo
            
            // Módulo Operaciones y Eventos de Campo
            'gestionar_operaciones', // EventoController, TrabajadorController, TipoEvento
            
            // Módulo Cosecha y Producción
            'gestionar_cosecha', // OrdenCosecha, SesionCosecha, RecepcionCampo, MovimientoClasificacion, Merma
            
            // Módulo Inventario
            'gestionar_inventario', // Insumo, StockInsumo, CategoriaInsumo, Contenedor, Proveedor
            
            // Módulo Financiero
            'gestionar_finanzas', // GastoController, ClienteController, RendimientoVentaController
        ];

        // Crear los permisos en la base de datos
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // 2. CREAR ROLES Y ASIGNAR PERMISOS

        // A. Rol: Super Admin
        $roleSuperAdmin = Role::create(['name' => 'Super Admin']);
        // El Super Admin recibe todos los permisos mágicamente mediante un Gate en AuthServiceProvider (te lo muestro abajo), 
        // pero puedes asignarle todo directamente con: $roleSuperAdmin->givePermissionTo(Permission::all());

        // B. Rol: Administrador de Finca
        $roleAdminFinca = Role::create(['name' => 'Administrador de Finca']);
        $roleAdminFinca->givePermissionTo([
            'ver_dashboard',
            'ver_bitacora',
            'gestionar_configuracion_agricola',
            'gestionar_cosecha',
            'gestionar_finanzas',
            'gestionar_inventario',
            'gestionar_operaciones'
        ]);

        // C. Rol: Agrónomo / Ingeniero (Foco en ciencia y plantas)
        $roleAgronomo = Role::create(['name' => 'Agrónomo']);
        $roleAgronomo->givePermissionTo([
            'ver_dashboard',
            'ver_analitica_grafos',
            'gestionar_configuracion_agricola',
            'gestionar_fitosanidad'
        ]);

        // D. Rol: Almacenista (Foco en bodega)
        $roleAlmacenista = Role::create(['name' => 'Almacenista']);
        $roleAlmacenista->givePermissionTo([
            'gestionar_inventario'
        ]);

        // E. Rol: Supervisor de Campo
        $roleSupervisor = Role::create(['name' => 'Supervisor de Campo']);
        $roleSupervisor->givePermissionTo([
            'gestionar_operaciones',
            'gestionar_cosecha',
            'gestionar_configuracion_agricola' // Solo para ver lotes/arboles
        ]);
    }
}