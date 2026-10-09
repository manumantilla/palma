# PALMA · Gestión agrícola para el campo colombiano

![Laravel](https://img.shields.io/badge/Laravel_13-FF2D20?style=flat-square&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP_8.3+-777BB4?style=flat-square&logo=php&logoColor=white)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL_15-4169E1?style=flat-square&logo=postgresql&logoColor=white)
![PostGIS](https://img.shields.io/badge/PostGIS_3.4-002E62?style=flat-square)
![Python](https://img.shields.io/badge/FastAPI-009688?style=flat-square&logo=fastapi&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-2496ED?style=flat-square&logo=docker&logoColor=white)

PALMA es mi proyecto de grado de Ingeniería de Sistemas en la Universidad Autónoma de Bucaramanga (UNAB).
Es una plataforma para administrar una finca de principio a fin: los lotes y cada planta, lo que se le
aplica, cuánto cuesta producir, cuánto se cosecha y a quién se le vende.

Arrancó pensada para palma de aceite (de ahí el nombre), pero el modelo de datos sirve para cualquier
cultivo perenne o transitorio: café, cacao, plátano, aguacate, maíz, arroz, frutales, etc. Todo está
planteado desde la realidad colombiana: registro ICA de insumos, facturación electrónica y documento
soporte de la DIAN, retenciones en UVT, PUC, fondos parafiscales y fincas en veredas sin señal.

---

## ¿Qué se puede hacer?

| Área | Qué cubre |
|---|---|
| **Cultivo y terreno** | Fincas, lotes con polígono GPS (PostGIS), zonas de manejo, análisis de suelo, sistemas de riego, inventario planta por planta |
| **Fenología** | Etapas por cultivo con escala BBCH, materiales genéticos (ej. palma guineensis vs. híbrido OxG), plan contra real por ciclo, recomendaciones técnicas por etapa |
| **Labores de campo** | Eventos programados y ejecutados, insumos aplicados por lote de compra, jornales y destajo, uso de maquinaria |
| **Insumos e inventario** | Catálogo con registro ICA, carencia y reingreso, lotes de compra con vencimiento, movimientos de stock |
| **Compras** | Proveedores, facturas, retenciones, pagos y cuentas por pagar |
| **Cosecha** | Órdenes de cosecha, sesiones, pesaje por trabajador, clasificación en contenedores, mermas |
| **Comercial** | Despachos, carta porte, recepción en destino, liquidaciones y cartera por cliente |
| **Finanzas** | Gastos clasificados por PUC, costo por ciclo y por etapa, activos amortizables (palma improductiva, maquinaria), parámetros tributarios por año |
| **Sanidad** | Historial fitosanitario por árbol y simulación de contagio entre plantas vecinas |
| **Clima** | Estaciones (propias, IDEAM o APIs) y registro diario para grados-día y riesgo de enfermedades |
| **Usuarios** | Equipos (Jetstream) y roles: Super Admin, Administrador de Finca, Agrónomo, Almacenista y Supervisor de Campo |

---

## Estructura

```
app/
├── Http/Controllers/     Un controlador por módulo
├── Models/               Modelos Eloquent (uno por tabla de negocio)
└── Services/             Lógica que no cabe en un controlador (ciclos, cliente del microservicio)
database/
├── migrations/           Esquema completo, con CHECK y restricciones de Postgres
└── seeders/              Datos de referencia y lote demo
docker/                   Dockerfiles de PHP y Python, script de la base de testing
python_service/           Microservicio FastAPI (algoritmos de grafos y analítica)
resources/views/          Vistas Blade por módulo
tests/Feature/            Tests de Jetstream y de coherencia modelo-esquema
```

---

## Estado del proyecto

El proyecto sigue en desarrollo. Lo que ya está:

- [x] Esquema completo de cultivo, cosecha, inventario, compras, comercial, finanzas, fenología y clima
- [x] Modelos coherentes con el esquema y cubiertos por tests
- [x] Lotes, zonas y árboles georreferenciados con mapa
- [x] Grafo de vecindad y simulación de contagio en Python
- [x] Compras, roles y permisos

En lo que estoy trabajando:

- [ ] `FenologiaService`: un solo servicio que genere el cronograma de cada ciclo, las cohortes de los
      perennes y las labores programadas
- [ ] Captura offline real (PWA con IndexedDB y sincronización por lotes) empezando por los pesajes de cosecha
- [ ] Dashboard financiero: costo por kilo, margen por ciclo, flujo de caja y cartera
- [ ] Predicción de etapas con grados-día y pronóstico de cosecha en el microservicio
- [ ] Soporte para varias fincas y empresas en la misma instalación

---

## Autores

Manuel Delgado Mantilla
Andres Felipe Jaimes Rico

Proyecto de grado · Ingeniería de Sistemas · Universidad Autónoma de Bucaramanga (UNAB)

GitHub: [@manumantilla](https://github.com/manumantilla)
