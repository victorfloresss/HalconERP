# Documentación de Uso de la API HalconERP

Esta documentación describe cómo interactuar con la API REST de HalconERP desde la aplicación frontend (Next.js).

## URL Base
Todas las peticiones deben dirigirse a:
- **Producción:** `https://halconerp.crzr.org/api`
- **Desarrollo (Local):** `http://localhost:8000/api`

## Formato de Peticiones y Respuestas
- Todos los envíos de datos (POST/PUT/PATCH) deben realizarse con el encabezado `Content-Type: application/json`.
- Para asegurar que la API devuelve errores en formato JSON, se recomienda incluir el encabezado `Accept: application/json`.

## Autenticación (Bearer Token)
La mayoría de los endpoints requieren autenticación. Debes enviar el token obtenido en el login dentro de los encabezados HTTP:
```http
Authorization: Bearer <TU_TOKEN_AQUI>
```

---

## 1. Endpoints Públicos (No requieren Token)

### Iniciar Sesión (Obtener Token)
- **Ruta:** `POST /login`
- **Body:**
  ```json
  {
    "email": "usuario@ejemplo.com",
    "password": "password123",
    "device_name": "nextjs-web" // Opcional
  }
  ```
- **Respuesta Exitosa (200 OK):**
  Devuelve el token que se usará para peticiones futuras.
  ```json
  {
    "message": "Inicio de sesión exitoso.",
    "token": "1|abcdef123456...",
    "user": { "id": 1, "name": "Admin", "email": "...", "role": {...} }
  }
  ```

### Rastreo de Pedido
- **Ruta:** `POST /track`
- **Body:**
  ```json
  {
    "invoice_number": "FAC-001",
    "customer_number": "CLI-123"
  }
  ```

---

## 2. Endpoints Protegidos (Requieren Token)

### Cerrar Sesión
- **Ruta:** `POST /logout`
- **Descripción:** Revoca el token de acceso actual.

### Obtener Usuario Actual
- **Ruta:** `GET /user`
- **Descripción:** Retorna los datos y el rol del usuario logueado.

### Estadísticas del Dashboard
- **Ruta:** `GET /dashboard`
- **Respuesta:**
  ```json
  {
    "stats": {
      "total": 150,
      "ordered": 10,
      "process": 5,
      "in_route": 2,
      "delivered": 133
    }
  }
  ```

---

## 3. Gestión de Pedidos

### Listar Pedidos Activos
- **Ruta:** `GET /orders`

### Crear un Pedido
- **Roles:** `sales`, `admin`
- **Ruta:** `POST /orders`
- **Body:**
  ```json
  {
    "invoice_number": "INV-100",
    "customer_number": "CUST-01",
    "customer_name": "Empresa S.A.",
    "delivery_address": "Calle Principal 123",
    "product_id": [1, 2],
    "quantity": [10, 5],
    "notes": "Entregar por la tarde"
  }
  ```

### Cambiar Estado de un Pedido
- **Roles:** Depende del estado (Almacén, Ruta, Admin).
- **Ruta:** `PATCH /orders/{id}/status`
- **Nota sobre fotos:** Si el estado requiere foto (`In route` o `Delivered`), la petición debe enviarse como `multipart/form-data` para adjuntar los archivos `loaded_unit_photo` o `delivered_material_photo`. Si es solo cambio de texto, usa JSON.
  ```json
  {
    "status": "In process"
  }
  ```

### Eliminar Pedido (Soft-delete)
- **Roles:** `sales`, `admin`
- **Ruta:** `DELETE /orders/{id}`

---

## 4. Inventario

### Ver Stock
- **Roles:** `warehouse`, `purchasing`, `admin`
- **Ruta:** `GET /inventory`

### Reabastecer Stock
- **Roles:** `purchasing`, `admin`
- **Ruta:** `POST /inventory/restock`
- **Body:**
  ```json
  {
    "product_id": 1,
    "quantity": 50
  }
  ```

---

## 5. Gestión de Empleados (Usuarios)
*Todas estas rutas requieren el rol `admin`.*

- **Listar roles disponibles:** `GET /users/roles`
- **Listar usuarios:** `GET /users`
- **Ver un usuario:** `GET /users/{id}`
- **Crear usuario:** `POST /users`
- **Actualizar usuario:** `PUT /users/{id}`
- **Eliminar usuario:** `DELETE /users/{id}`
