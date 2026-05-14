# Informe Técnico: Implementación de Capa API REST en HalconERP

Este documento detalla paso a paso las modificaciones y adiciones realizadas al proyecto monolítico Laravel (HalconERP) para habilitar una capa API REST. El objetivo principal fue permitir que una aplicación Next.js alojada en Vercel pudiera consumir los datos y servicios del sistema sin alterar el funcionamiento del frontend original en Blade.

## 1. Análisis y Preparación
Se identificó que el proyecto original devolvía vistas HTML mediante Blade y utilizaba un sistema de autenticación basado en sesiones. Para soportar peticiones desde un frontend desacoplado (Next.js) alojado en un dominio distinto, se requería:
- Habilitar el enrutamiento de API (`routes/api.php`).
- Implementar Cross-Origin Resource Sharing (CORS).
- Integrar autenticación basada en Tokens (Laravel Sanctum).
- Crear controladores dedicados para retornar respuestas en formato JSON.

## 2. Instalación y Configuración de Dependencias
- **Laravel Sanctum:** Se instaló mediante Composer (`composer require laravel/sanctum`) para manejar la autenticación mediante Bearer Tokens.
- **Configuración Sanctum:** Se publicó la configuración y las migraciones de Sanctum usando `php artisan vendor:publish`. Esto generó el archivo `config/sanctum.php` y la migración para la tabla `personal_access_tokens`.

## 3. Configuración del Núcleo de Laravel (`bootstrap/app.php`)
Se modificó el archivo de configuración de inicialización de la aplicación para:
- Registrar el archivo de rutas `routes/api.php`.
- Añadir el middleware `statefulApi()` para soportar peticiones de Sanctum.
- Configurar un alias para el middleware de verificación de roles específico de la API (`role.api`).
- Forzar a que las excepciones devuelvan respuestas JSON cuando las peticiones comiencen con `api/*` o esperen JSON.

## 4. Configuración de CORS (`config/cors.php` y `.env`)
Se creó y configuró el archivo `config/cors.php` para permitir que el frontend en Vercel se comunique con el backend en InfinityFree.
- Se añadieron variables al archivo `.env` (`FRONTEND_URL` y `SANCTUM_STATEFUL_DOMAINS`) para gestionar dinámicamente los orígenes permitidos (ej. `localhost:3000` y `tu-app.vercel.app`).

## 5. Autenticación y Modelos
- Se modificó el modelo `App\Models\User` añadiendo el trait `HasApiTokens` proporcionado por Sanctum. Esto permite a los usuarios emitir y gestionar tokens de acceso.
- Se generó un script SQL manual (`database/sanctum_migration.sql`) para crear la tabla de tokens directamente en phpMyAdmin, dado que InfinityFree no permite conexiones externas de base de datos para ejecutar migraciones vía Artisan de forma remota.

## 6. Creación de Middleware para API
Se creó el middleware `App\Http\Middleware\CheckRoleApi`. 
A diferencia del middleware web original que redirigía a la página de login o lanzaba vistas de error 403, este nuevo middleware devuelve respuestas estrictamente en JSON con los códigos HTTP correspondientes (`401 Unauthorized` y `403 Forbidden`).

## 7. Desarrollo de Controladores API
Se crearon nuevos controladores dentro del directorio `app/Http/Controllers/Api/` para duplicar la lógica de negocio, pero retornando estructuras JSON:
- **`AuthController`:** Maneja el login (emisión de token), logout (revocación de token) y la obtención de datos del usuario actual (`/api/me`).
- **`DashboardController`:** Calcula y devuelve las estadísticas globales.
- **`OrderController`:** Maneja el CRUD de pedidos, validación de stock al cambiar estados y gestión de la papelera de reciclaje.
- **`InventoryController`:** Permite consultar el stock actual y registrar reabastecimientos.
- **`UserController`:** CRUD completo de empleados y asignación de roles.
- **`TrackingController`:** Endpoint público para rastrear el estado de un pedido sin necesidad de autenticación.

## 8. Definición de Rutas API (`routes/api.php`)
Se construyó el archivo `routes/api.php` agrupando las rutas según sus requerimientos de seguridad:
- Rutas públicas (`/login`, `/track`).
- Rutas protegidas genéricas (`/logout`, `/user`, `/dashboard`, `/orders`).
- Rutas protegidas por roles específicos (ej. creación de pedidos solo para `sales` o `admin`, inventario para `warehouse`).

## Conclusión
La arquitectura resultante mantiene el sistema monolítico intacto para los usuarios que acceden directamente a la URL de InfinityFree, mientras expone una robusta API REST bajo el prefijo `/api` lista para ser consumida de manera segura por la aplicación Next.js.
