# Reporte Maestro de Proyecto: HalconERP Hybrid API & Cloud Deployment

## 1. Introducción y Objetivo
El proyecto consistió en la transformación de un monolito ERP construido en **Laravel 12** a una arquitectura híbrida que expone una **API REST segura**. El objetivo principal fue permitir que una aplicación móvil/web moderna desarrollada en **Next.js** consumiera datos del ERP en tiempo real, manteniendo la seguridad y escalabilidad mediante contenedores y túneles en la nube.

---

## 2. Fase 1: Transformación de Monolito a API (Backend)
Para permitir la comunicación externa, se implementaron los siguientes componentes en Laravel:

*   **Laravel Sanctum:** Se integró para gestionar la autenticación basada en **Tokens Bearer**. Esto sustituye las sesiones tradicionales basadas en cookies, permitiendo que aplicaciones externas se identifiquen de forma segura.
*   **Middleware de Protección:** Se creó un middleware personalizado (`CheckRoleApi`) para asegurar que las respuestas de error (401 No autorizado / 403 Prohibido) se entreguen en formato **JSON** y no como redirecciones web.
*   **Rutas de API:** Se definieron endpoints específicos bajo el prefijo `/api` para:
    *   Autenticación (`/login`, `/logout`, `/user`).
    *   Gestión de Pedidos (CRUD de órdenes, estados y fotos).
    *   Inventario y Dashboard de estadísticas.

---

## 3. Fase 2: Integración con Next.js (Vercel)
La conexión entre el frontend en Vercel y el backend se diseñó bajo los siguientes principios:

*   **Seguridad CORS:** Se configuró el backend para aceptar peticiones exclusivamente desde el dominio de la App en Vercel, protegiendo contra ataques de origen cruzado.
*   **Consumo de API:** Se implementó una lógica de `fetch` asíncrona que adjunta el token de seguridad en las cabeceras HTTP (`Authorization: Bearer <token>`).
*   **Gestión de Estados:** El frontend maneja la persistencia del token para mantener la sesión activa sin necesidad de cookies persistentes en el servidor.

---

## 4. Fase 3: Infraestructura y Dockerización
Para asegurar que el proyecto fuera fácil de desplegar y se comportara igual en cualquier servidor, se utilizó **Docker**:

*   **Contenedores Independientes:** Se crearon imágenes para la aplicación (PHP-FPM), el servidor web (Nginx) y la base de datos (MySQL 8).
*   **Orquestación:** Mediante `docker-compose.yml`, se configuró la red interna donde los servicios se comunican entre sí (ej: la app se conecta a `db:3306`), aislándolos de Internet.
*   **Persistencia:** Se utilizaron volúmenes de Docker para asegurar que los datos de la base de datos y los archivos del proyecto no se pierdan al reiniciar los contenedores.

---

## 5. Fase 4: Despliegue Seguro (Oracle Cloud + Cloudflare)
El despliegue final se realizó en una instancia de **Oracle Cloud (Ubuntu)** con una capa adicional de seguridad avanzada:

*   **Cloudflare Tunnels:** En lugar de abrir los puertos de la máquina virtual al mundo (exponiendo la IP pública), se configuró un túnel inverso. 
    *   El servidor de Oracle "llama" a Cloudflare.
    *   Cloudflare gestiona el certificado SSL (HTTPS) y redirige el tráfico de `halconerp.crzr.org` al túnel.
*   **Beneficio:** El servidor es invisible para atacantes (no hay puertos web abiertos), pero accesible de forma segura para la App de Next.js.

---

## 6. Conclusión
El resultado es un sistema ERP robusto y moderno, capaz de servir a múltiples plataformas mediante una API estandarizada, con una arquitectura de despliegue de grado empresarial que prioriza la seguridad y la portabilidad mediante contenedores.

---
**Desarrollado para:** Tarea Académica - Diseño Web / Ingeniería de Software.
**Tecnologías:** Laravel 12, Next.js, Docker, MySQL, Oracle Cloud, Cloudflare Zero Trust.
