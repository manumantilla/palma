# 🚜 Sistema AgTech Integrado de Trazabilidad y Georreferenciación Postcosecha

![Laravel 13](https://img.shields.io/badge/Laravel_13-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-4169E1?style=for-the-badge&logo=postgresql&logoColor=white)
![PostGIS](https://img.shields.io/badge/PostGIS-002E62?style=for-the-badge&logo=qgis&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white)
![Python](https://img.shields.io/badge/Python_Microservices-3776AB?style=for-the-badge&logo=python&logoColor=white)

## 📋 Descripción del Proyecto
Plataforma AgTech distribuida para la gestión, auditoría y trazabilidad geográfica de fruta desde su origen en lote hasta su procesamiento y clasificación en planta de empaque. 

El sistema combina el poder transaccional de **Laravel 13**, análisis espacial con **PostgreSQL + PostGIS**, procesamiento distribuido con **microservicios en Python**, todo orquestado bajo contenedores **Docker**.

## 🏗️ Arquitectura del Sistema
El ecosistema está compuesto por los siguientes módulos y servicios:

- **Core Monolith (Laravel 13):** Gestión de reglas de negocio, recepciones, contenedores, mermas, autenticación y API REST.
- **Microservicios de Análisis (Python):** Servicios independientes dedicados a procesamiento pesado de datos agrícolas, métricas predictivas y analítica.
- **Base de Datos Espacial (PostgreSQL + PostGIS):** Almacenamiento normalizado con soporte para polígonos geoespaciales (coordenadas de fincas, lotes y trazabilidad física).
- **Entorno de Contenedores (Docker & Docker Compose):** Aislamiento de entornos de desarrollo y producción de todos los microservicios.

## ✨ Características Técnicas Clave
- **Trazabilidad de Masa y Espacio:** Vinculación de kilos asignados y mermas a lotes georreferenciados.
- **Integridad ACID:** Uso estricto de `DB::transaction` para prevenir descuadres en inventarios de fruta.
- **Auditoría Completa:** Implementación de *Soft Deletes* y logs estructurados para eventos de modificación o merma.
- **Sincronización Off-Grid:** Preparado para sincronización diferida (`synced_at`, `client_updated_at`) desde dispositivos de campo.

## ⚙️ Requisitos Previos
- [Docker](https://www.docker.com/) y [Docker Compose](https://docs.docker.com/compose/)
- [Git](https://git-scm.com/)

*(No requieres instalar PHP, Python o PostgreSQL localmente, todo corre dentro del entorno Dockerized).*

## 🚀 Instalación y Despliegue con Docker

1. **Clonar el repositorio**
   ```bash
   git clone [https://github.com/manumantilla/palma.git](https://github.com/tu-usuario/tu-repositorio.git)
   cd tu-repositorio
