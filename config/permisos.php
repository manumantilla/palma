<?php

/*
 * Catálogo único de permisos de PALMA, agrupado por módulo.
 * Lo leen RolesAndPermissionsSeeder (para crearlos) y el gestor de permisos (para agruparlos y
 * describirlos en pantalla). Para agregar un permiso: se añade aquí y se corre el seeder.
 */

return [
    'modulos' => [
        'tablero' => [
            'nombre' => 'Tablero y analítica',
            'permisos' => [
                'ver_dashboard'              => 'Ver el tablero principal',
                'ver_analitica_grafos'       => 'Ver el grafo de vecindad de árboles',
                'ejecutar_simulacion_grafos' => 'Correr simulaciones de contagio en el motor Python',
                'ver_bitacora'               => 'Consultar la bitácora',
            ],
        ],
        'terreno' => [
            'nombre' => 'Fincas, lotes y cultivos',
            'permisos' => [
                'gestionar_configuracion_agricola' => 'Administrar cultivos, ciclos, lotes y árboles',
                'crear_lotes'                      => 'Crear lotes y dibujar su polígono',
            ],
        ],
        'fitosanidad' => [
            'nombre' => 'Fitosanidad y fenología',
            'permisos' => [
                'gestionar_fitosanidad' => 'Registrar historial fitosanitario, fenología y análisis de suelo',
            ],
        ],
        'operaciones' => [
            'nombre' => 'Labores de campo',
            'permisos' => [
                'gestionar_operaciones' => 'Programar y registrar labores, trabajadores y jornales',
            ],
        ],
        'cosecha' => [
            'nombre' => 'Cosecha',
            'permisos' => [
                'gestionar_cosecha'       => 'Registrar sesiones, pesajes y mermas',
                'aprobar_ordenes_cosecha' => 'Aprobar órdenes de cosecha',
            ],
        ],
        'inventario' => [
            'nombre' => 'Bodega e insumos',
            'permisos' => [
                'gestionar_inventario' => 'Administrar insumos, proveedores y compras',
                'gestionar_kardex'     => 'Hacer ajustes de kardex (mermas, hurtos, vencidos)',
            ],
        ],
        'finanzas' => [
            'nombre' => 'Finanzas',
            'permisos' => [
                'gestionar_finanzas' => 'Registrar gastos, pagos, clientes y liquidaciones',
            ],
        ],
    ],

    // Rol => permisos que recibe. El Super Admin no se lista: tiene todo por Gate::before.
    'roles' => [
        'Administrador de Finca' => [
            'ver_dashboard', 'ver_bitacora', 'ver_analitica_grafos',
            'gestionar_configuracion_agricola', 'crear_lotes',
            'gestionar_operaciones', 'gestionar_cosecha', 'aprobar_ordenes_cosecha',
            'gestionar_inventario', 'gestionar_finanzas',
        ],
        'Agrónomo' => [
            'ver_dashboard', 'ver_analitica_grafos', 'ejecutar_simulacion_grafos',
            'gestionar_configuracion_agricola', 'gestionar_fitosanidad',
        ],
        'Almacenista' => [
            'gestionar_inventario', 'gestionar_kardex',
        ],
        'Supervisor de Campo' => [
            'gestionar_operaciones', 'gestionar_cosecha', 'gestionar_configuracion_agricola',
        ],
        'Contador' => [
            'ver_dashboard', 'gestionar_finanzas',
        ],
    ],
];
