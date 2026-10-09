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

## Cómo está armado

```
┌──────────────────────┐        HTTP         ┌──────────────────────────┐
│   Laravel 13 (Sail)  │ ──────────────────▶ │  python_service (FastAPI) │
│  Blade + Livewire    │                     │  grafos, analítica        │
│  Jetstream, Spatie   │                     └────────────┬─────────────┘
└──────────┬───────────┘                                  │
           │                                              │
           ▼                                              ▼
     ┌─────────────────────────────────────────────────────────┐
     │          PostgreSQL 15 + PostGIS 3.4 (geometrías 4326)   │
     └─────────────────────────────────────────────────────────┘
```

- **Laravel 13** maneja toda la lógica de negocio, la autenticación (Jetstream + Sanctum) y los permisos
  (spatie/laravel-permission). Las vistas son Blade con Tailwind; los mapas usan Leaflet.
- **PostgreSQL + PostGIS** guarda lotes como `MULTIPOLYGON`, zonas como `POLYGON` y cada planta como
  punto. En los modelos uso [laravel-eloquent-spatial](https://github.com/MatanYadaev/laravel-eloquent-spatial)
  para que las geometrías lleguen como objetos y no como texto.
- **python_service** es un microservicio en FastAPI que lee la misma base de datos. Hoy expone la
  simulación de contagio sobre el grafo de vecindad de árboles (NetworkX / igraph). La idea es mover ahí
  todo lo pesado: predicción de etapas con grados-día, pronóstico de cosecha y proyección de caja.
- **Docker Compose** levanta los tres servicios.

---

## Requisitos

- Docker y Docker Compose
- Git
- En Windows recomiendo trabajar dentro de **WSL2** (Ubuntu); el proyecto vive ahí y corre mucho más rápido

No hace falta tener PHP, Composer, Python ni Postgres instalados en la máquina: todo corre en contenedores.

## Instalación

```bash
git clone https://github.com/manumantilla/palma.git
cd palma
cp .env.example .env
```

En el `.env` configura la base de datos para que apunte al contenedor:

```dotenv
DB_CONNECTION=pgsql
DB_HOST=pgsql
DB_PORT=5432
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password
```

Instala las dependencias de PHP (con un contenedor temporal, así no necesitas Composer local) y levanta todo:

```bash
docker run --rm -u "$(id -u):$(id -g)" -v "$(pwd):/var/www/html" -w /var/www/html \
    laravelsail/php84-composer:latest composer install --ignore-platform-reqs

./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```

| Servicio | URL / puerto |
|---|---|
| Aplicación | http://localhost |
| Vite (dev) | http://localhost:5173 |
| Postgres (desde tu máquina) | `localhost:5433` |
| Microservicio Python | http://localhost:8081/health · docs en http://localhost:8081/docs |

> Tip: crea el alias `alias sail='./vendor/bin/sail'` para no escribir la ruta completa.

### Datos de prueba

`migrate --seed` deja la base lista para probar:

- Un usuario `test@example.com` / `password`
- Roles y permisos
- Cultivos base (palma, maíz, frijol, lulo) y la fenología de referencia de palma y maíz
- Un lote demo en ladera con dos zonas de manejo y ~1.800 árboles de aguacate Hass georreferenciados,
  con su red de vecindad para el grafo de contagio

Los seeders se pueden correr varias veces sin duplicar datos. Si quieres empezar de cero:

```bash
sail artisan migrate:fresh --seed
```

---

## Tests

```bash
sail artisan test
```

Además de los tests de Jetstream hay dos que me importan mucho:

- **`ModelosEsquemaTest`** compara cada modelo contra el esquema real de Postgres: que el `$fillable` no
  tenga columnas que no existen ni columnas calculadas, que fechas, decimales, JSON y geometrías tengan su
  cast, y que cada relación apunte a la tabla correcta según la llave foránea. Si alguien cambia una
  migración y no actualiza el modelo, este test lo detecta.
- **`RestriccionesEloquentTest`** guarda registros reales para probar los `CHECK`, los `UNIQUE`, la llave
  foránea compuesta de gastos, las columnas generadas y las geometrías de ida y vuelta.

Los tests usan la base `testing` (la crea el contenedor de Postgres). Si es la primera vez, activa PostGIS ahí:

```bash
sail exec pgsql psql -U sail -d testing -c "CREATE EXTENSION IF NOT EXISTS postgis;"
```

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

## Decisiones que vale la pena explicar

- **UUID en lo que se captura en campo.** Pesajes, sesiones de cosecha, contenedores, mermas y
  observaciones fenológicas usan UUID generado en el dispositivo, más `client_updated_at` y `synced_at`.
  Así un registro hecho sin señal se puede reenviar sin duplicarse.
- **Fenología que no es una línea recta.** Un maíz pasa por sus etapas una tras otra, pero una palma
  adulta tiene racimos en todas las etapas al mismo tiempo. Por eso las etapas tienen tipo
  (`secuencial`, `vida`, `repetitiva`, `temporada`) y el historial admite etapas simultáneas y cohortes.
- **Un solo libro de gastos.** Todo costo termina en la tabla `gastos` con su categoría PUC, el ciclo y la
  etapa a la que pertenece. `total`, `neto_a_pagar` y `costo_cop` los calcula Postgres, y una llave
  foránea compuesta impide cargar un gasto a una etapa de otro ciclo.
- **Restricciones en la base, no solo en el formulario.** Rangos BBCH, duraciones, fechas y estados están
  protegidos con `CHECK` en Postgres, así un error de código no deja datos inconsistentes.
- **Parámetros tributarios por año.** UVT, salario mínimo, carga prestacional y tarifas de retención viven
  en tablas y no en el código, porque cambian cada año.

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

## Autor

Proyecto de grado · Ingeniería de Sistemas · Universidad Autónoma de Bucaramanga (UNAB)

GitHub: [@manumantilla](https://github.com/manumantilla)
