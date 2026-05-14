# Arquitectura de Despliegue — HalconERP (Oracle Cloud + Docker)

Esta documentación detalla la nueva arquitectura implementada para desplegar la API de HalconERP. Se ha pasado de un entorno de hosting compartido tradicional (InfinityFree) a una arquitectura moderna basada en contenedores dentro de una Máquina Virtual Privada (Oracle Cloud).

## 1. Visión General de la Arquitectura

La nueva infraestructura utiliza **Docker Compose** para orquestar múltiples servicios aislados que se comunican a través de una red interna cerrada. El acceso desde el exterior (Internet) está gestionado exclusivamente por **Cloudflare Tunnels**, eliminando la necesidad de exponer puertos públicos en el servidor.

### Diagrama Lógico
```text
[Cliente / Vercel] 
       │ (HTTPS Seguro automático)
[Red de Cloudflare]
       │ (Túnel Inverso Encriptado)
       ▼
[ Servidor Oracle Cloud ]
   └─ [ Red Interna de Docker (app-network) ]
        ├─ 🚇 cloudflared (Recibe tráfico de Cloudflare)
        │       │
        ├─ 🌐 nginx (Servidor Web en puerto 80 interno)
        │       │
        ├─ ⚙️ app (Laravel / PHP-FPM en puerto 9000 interno)
        │       │
        └─ 🗄️ db (MySQL 8 en puerto 3306 interno)
```

---

## 2. Componentes y Contenedores

El archivo `docker-compose.yml` orquesta 4 contenedores principales:

### A. Aplicación Laravel (`app`)
- **Imagen Base:** `php:8.2-fpm` (Optimizada para FastCGI Process Manager).
- **Rol:** Ejecuta el código PHP de Laravel. No sirve archivos estáticos ni entiende HTTP directamente.
- **Configuración:** A través del `Dockerfile` personalizado, se instalan dependencias del sistema operativo y extensiones de PHP críticas (PDO, GD, Zip, etc.). Además, incluye Composer.
- **Volúmenes:** Sincroniza el código fuente (`./:/var/www`) para permitir actualizaciones en tiempo real y persistir logs de la app.

### B. Servidor Web Nginx (`nginx`)
- **Imagen Base:** `nginx:alpine` (Versión ultraligera).
- **Rol:** Actúa como el punto de entrada HTTP. Sirve los archivos estáticos de la carpeta `public` (como imágenes o CSS/JS compilado de Vite) de forma extremadamente rápida.
- **Configuración:** Utiliza el archivo `nginx/conf.d/app.conf` para saber que cualquier petición terminada en `.php` o peticiones dinámicas deben ser enviadas al contenedor `app` por el puerto interno 9000.

### C. Base de Datos (`db`)
- **Imagen Base:** `mysql:8.0`
- **Rol:** Almacenamiento relacional de HalconERP.
- **Seguridad:** No expone puertos al exterior de la VM de Oracle. Solo es accesible por los contenedores que están en la red `app-network`.
- **Volumen:** Utiliza un volumen con nombre (`dbdata`) para asegurar que los datos no se pierdan si el contenedor se destruye o reinicia.

### D. Cloudflare Tunnel (`cloudflared`)
- **Imagen Base:** `cloudflare/cloudflared:latest`
- **Rol:** Es un daemon ligero que crea una conexión saliente (outbound) hacia la red perimetral de Cloudflare.
- **Ventaja de Seguridad:** Como la conexión nace "desde adentro hacia afuera", no es necesario configurar Reglas de Entrada (Ingress) en el Firewall de Oracle Cloud para los puertos 80/443. La máquina es invisible para escáneres de puertos en Internet.

---

## 3. Resumen de Archivos Clave

### `Dockerfile`
Es la "receta" para construir el entorno de PHP. En lugar de instalar dependencias manualmente en el servidor, este archivo asegura que la aplicación tenga exactamente las mismas librerías sin importar dónde se ejecute (Local u Oracle).

### `docker-compose.yml`
El "director de orquesta". Define cómo se conectan los contenedores, qué variables de entorno inyectar (leyendo del `.env` principal) y cómo nombrar la red interna.

### `nginx/conf.d/app.conf`
La configuración de ruteo de Nginx. Define el `index.php` como archivo principal y establece el proxy inverso hacia `app:9000` usando el protocolo FastCGI.

## 4. Beneficios Finales
1. **Resolución del bloqueo anti-bot:** Al salir de InfinityFree, la API ya no está protegida por el desafío AES de JavaScript, permitiendo que la App de Next.js en Vercel consuma el JSON sin restricciones.
2. **Escalabilidad:** Cada componente (BD, PHP, Web) está separado.
3. **Seguridad Zero Trust:** El servidor no tiene puertos web expuestos; está completamente oculto detrás de Cloudflare.
