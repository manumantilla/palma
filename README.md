# 🚜 Sistema Integrado de Trazabilidad Postcosecha

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)

## 📋 Descripción del Proyecto
Este proyecto es una plataforma de software diseñada para gestionar y auditar la trazabilidad de la fruta desde su recepción en campo hasta su clasificación en planta de empaque. El sistema permite registrar movimientos, asignar lotes a tolvas/contenedores y documentar mermas con rigor agronómico, garantizando la consistencia del inventario mediante transacciones de base de datos.

Este sistema está preparado para entornos agroindustriales con conectividad intermitente, implementando sincronización diferida y borrados lógicos para auditorías.

## ✨ Módulos Principales
- **Recepción de Campo:** Registro de lotes que ingresan de las fincas, con control de saldos y kilos disponibles.
- **Gestión de Contenedores:** Administración del ciclo de vida de tolvas y bins (apertura, llenado y cierre).
- **Movimientos de Clasificación:** Motor transaccional que asigna fruta de la recepción a los contenedores, actualizando inventarios en tiempo real.
- **Registro de Mermas:** Control estricto de pérdidas por motivos mecánicos, fitosanitarios o deshidratación, manteniendo la cuadratura de kilos.

## 🛠️ Tecnologías y Arquitectura
- **Framework:** Laravel 11 (PHP 8.2+)
- **Base de Datos:** MySQL / MariaDB (Estructura normalizada con UUIDs)
- **Frontend:** Blade Templates con TailwindCSS
- **Patrones Aplicados:** 
  - MVC (Model-View-Controller)
  - Query Scopes para reportes gerenciales
  - Database Transactions (ACID) para consistencia de inventarios
  - Soft Deletes (Borrado lógico) para protección de datos

## ⚙️ Requisitos Previos
Para ejecutar este proyecto en un entorno local, necesitas tener instalado:
- [PHP >= 8.2](https://www.php.net/)
- [Composer](https://getcomposer.org/)
- [Node.js y npm](https://nodejs.org/)
- Servidor local de base de datos (XAMPP, Laragon, o Docker/Laravel Sail)

## 🚀 Instalación y Despliegue

Sigue estos pasos para levantar el entorno de desarrollo:

1. **Clonar el repositorio**
   ```bash
   git clone [https://github.com/tu-usuario/tu-repositorio.git](https://github.com/tu-usuario/tu-repositorio.git)
   cd tu-repositorio
