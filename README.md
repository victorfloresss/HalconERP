# HalconERP - Intelligent Logistics System

HalconERP es el núcleo de gestión logística diseñado para la optimización de entregas, control de inventarios y seguimiento de pedidos en tiempo real. Construido con Laravel 12, actúa como el motor central (REST API) para el ecosistema Halcon, alimentando aplicaciones móviles y paneles administrativos.

---

## Especificaciones Técnicas

* **Framework:** Laravel 12.x
* **Lenguaje:** PHP 8.2+
* **Autenticación:** Laravel Sanctum (Token-based)
* **Base de Datos:** MySQL 8.0
* **Entorno:** Docker & Docker Compose

---

## Características del Sistema

### Gestión de Pedidos (Order Lifecycle)
Flujo completo de estados: `Ordered` -> `In Process` -> `In Route` -> `Delivered`.
* Creación de pedidos con múltiples productos.
* Seguimiento público mediante folio y número de cliente.
* Evidencia fotográfica de carga y entrega.

### Control de Acceso Basado en Roles (RBAC)
* **Admin:** Control total del sistema y usuarios.
* **Sales:** Creación y gestión de pedidos.
* **Purchasing:** Gestión de compras y reabastecimiento.
* **Warehouse:** Control de inventario y preparación.
* **Route:** Gestión de entregas y evidencias.

### Inventario
* Control de stock por producto.
* Sistema de reabastecimiento con logs de transacciones.
* Alertas de inventario bajo.

---

## Arquitectura del Proyecto

```text
HalconERP/
├── app/Http/Controllers/Api/   # Controladores REST
├── app/Models/                # Modelos Eloquent (User, Order, Product, etc.)
├── database/migrations/       # Esquema de base de datos
├── database/seeders/          # Datos de prueba y configuración inicial
├── docs/                      # Documentación técnica detallada
├── nginx/                     # Configuración de servidor para Docker
└── routes/api.php             # Definición de endpoints
```

---

## Configuración e Instalación

### Requisitos
* PHP 8.2 & Composer
* MySQL 8
* Docker (Opcional)

### Pasos

1. **Clonación del repositorio**
   ```bash
   git clone https://github.com/victorfloresss/HalconERP.git
   cd HalconERP
   ```

2. **Instalación de dependencias**
   ```bash
   composer install
   ```

3. **Variables de entorno**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Base de datos**
   ```bash
   php artisan migrate --seed
   ```

5. **Servidor local**
   ```bash
   php artisan serve
   ```

---

## Resumen de Endpoints

| Método | Endpoint | Descripción | Acceso |
| :--- | :--- | :--- | :--- |
| POST | /api/login | Inicio de sesión y obtención de Token | Público |
| GET | /api/orders | Lista de pedidos | Protegido |
| POST | /api/track | Rastreo público de pedido | Público |
| PATCH | /api/orders/{id}/status | Actualización de estado y fotos | Protegido |
| GET | /api/inventory | Consulta de stock | Almacén/Admin |

---

## Despliegue con Docker

El proyecto incluye soporte nativo para contenedores:

```bash
docker-compose up -d --build
```

---

## Desarrollo y Créditos

Este sistema ha sido desarrollado por:

* **Victor** (Admin & Lead Developer)
* **Emiliano** (Sales Module)
* **Antonio** (Warehouse & Inventory)
* **Jordan** (Route & Delivery Logistics)
* **Karla** (Purchasing & Supply Chain)

HalconERP - 2026
