# Guía de Pruebas Locales para la API HalconERP

Esta guía te ayudará a levantar el entorno de backend de Laravel en tu computadora local para poder desarrollar y probar el frontend Next.js sin afectar el entorno de producción.

## Requisitos Previos
Asegúrate de tener instalado en tu sistema local:
1. **PHP 8.2 o superior**.
2. **Composer** (Gestor de dependencias de PHP).
3. **Servidor MySQL** (XAMPP, WAMP, Laragon, o MySQL puro).
4. **Postman, Insomnia o Thunder Client** (Extensiones de VSCode) para probar los endpoints.

---

## 1. Configuración de Base de Datos Local
1. Abre tu gestor de base de datos MySQL (ej. phpMyAdmin en localhost o DBeaver).
2. Crea una nueva base de datos vacía llamada `halcon_db` (o el nombre que prefieras).

## 2. Preparar el Entorno `.env`
El proyecto cuenta con un archivo `.env.local`. 
1. Haz una copia del archivo `.env.local` y renómbralo a `.env` (sobrescribiendo el actual si estás trabajando estrictamente en local).
   *Ojo: si sobrescribes el `.env`, guarda la contraseña de InfinityFree en un lugar seguro.*
2. Asegúrate de que las credenciales de BD en tu nuevo `.env` coincidan con tu MySQL local:
   ```env
   APP_ENV=local
   APP_DEBUG=true
   APP_URL=http://localhost:8000
   
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=halcon_db
   DB_USERNAME=root
   DB_PASSWORD=
   
   FRONTEND_URL=http://localhost:3000
   SANCTUM_STATEFUL_DOMAINS=localhost,localhost:3000,127.0.0.1
   ```

## 3. Instalar Dependencias y Migrar Base de Datos
Abre una terminal en la carpeta raíz del proyecto (`HalconERP`) y ejecuta:

```bash
# 1. Instalar librerías de PHP
composer install

# 2. Generar las tablas en tu base de datos local
php artisan migrate

# 3. (Opcional) Si tienes seeders creados para datos de prueba:
php artisan db:seed
```

## 4. Levantar el Servidor Local
Ejecuta el servidor de desarrollo de Laravel:

```bash
php artisan serve
```
El servidor de la API se iniciará en `http://localhost:8000`.

---

## 5. Cómo probar con Postman o Thunder Client

A continuación se muestra el flujo típico para probar la API:

### Paso 5.1: Obtener un Token (Login)
1. Crea un nuevo request tipo **POST** a `http://localhost:8000/api/login`.
2. En la pestaña **Headers**, añade:
   - `Accept`: `application/json`
3. En la pestaña **Body** (selecciona *raw* y formato *JSON*), añade:
   ```json
   {
       "email": "tu-usuario-admin@ejemplo.com",
       "password": "tu-contraseña"
   }
   ```
4. Envía la petición.
5. Copia el valor de `"token"` que te devuelve el sistema.

### Paso 5.2: Usar el Token (Consultar Inventario)
1. Crea un nuevo request tipo **GET** a `http://localhost:8000/api/inventory`.
2. En la pestaña **Headers**, añade:
   - `Accept`: `application/json`
3. En la pestaña **Auth** (Autorización), selecciona el tipo **Bearer Token** y pega el token que copiaste en el paso anterior.
4. Envía la petición. Deberías ver la lista de productos en formato JSON.

---

## 6. Integración con el Frontend (Next.js)
En tu proyecto de Next.js, asegúrate de configurar tu variable de entorno apuntando a local:

En tu archivo `.env.local` de Next.js:
```env
NEXT_PUBLIC_API_URL=http://localhost:8000/api
```

Levanta tu servidor Next.js (`npm run dev`) y tu app frontend ya debería estar comunicándose con la API local de Laravel.
