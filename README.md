# Pesado y al Fallo - Sistema de Gestión

Sistema completo con Landing Page, Dashboard Admin, y Gestión de Usuarios.

## Características

✅ **Autenticación de Usuarios**
- Login con email y contraseña
- Roles: Admin y User
- Sesiones seguras

✅ **Dashboard para Administradores**
- Ver ventas diarias del negocio
- Agregar, actualizar y eliminar usuarios
- Gestión completa de usuarios

✅ **Landing Page**
- Saludo personalizado por nombre de usuario
- Página de inicio optimizada
- Acceso solo para usuarios autenticados

✅ **Base de Datos MySQL**
- Tabla de usuarios
- Tabla de ventas
- Tabla de productos
- Tabla de categorías
- Tabla de sesiones

## Estructura de Carpetas

```
.
├── index.html              # Landing page original
├── index-auth.php         # Landing page con autenticación
├── index-landing-updated.html # Landing page con saludo personalizado
├── login.php              # Página de login
├── dashboard.php          # Dashboard para admins
├── config.php             # Configuración general
├── db.php                 # Clase para gestionar BD
├── auth.php               # Funciones de autenticación
├── database.sql           # Script SQL para crear BD
├── styles.css             # Estilos del landing page
├── composer.json          # Dependencias PHP
├── .env.example           # Variables de entorno
├── Procfile              # Configuración para Heroku/Railway
└── railway.json          # Configuración para Railway
```

## Instalación Local

### 1. Clonar el repositorio
```bash
git clone tu-repositorio.git
cd tu-repositorio
```

### 2. Crear base de datos
```bash
mysql -u root -p < database.sql
```

### 3. Configurar variables de entorno
```bash
cp .env.example .env
# Editar .env con tus datos
```

### 4. Instalar dependencias (opcional)
```bash
composer install
```

### 5. Iniciar servidor PHP
```bash
php -S localhost:8000
```

### 6. Acceder a la aplicación
- Login: http://localhost:8000/login.php
- Landing Page: http://localhost:8000/index.html

## Credenciales de Prueba

**Admin:**
- Email: admin@pesado.com
- Contraseña: password123

**Usuario:**
- Email: user@pesado.com
- Contraseña: password123

## Despliegue en Railway

### Paso 1: Conectar tu repositorio a Railway
1. Ve a [railway.app](https://railway.app)
2. Crea un nuevo proyecto y conecta tu repositorio

### Paso 2: Agregar servicio de MySQL
1. En el dashboard de Railway, agrega un servicio "MySQL"
2. Anota las credenciales proporcionadas

### Paso 3: Configurar variables de entorno
En el dashboard de Railway, configura:
```
ENVIRONMENT=production
APP_URL=https://tu-app.railway.app
DB_HOST=tu-host-railway
DB_USER=usuario_railway
DB_PASS=contraseña_railway
DB_NAME=pesado_fallo
DB_PORT=puerto_railway
```

### Paso 4: Ejecutar migraciones
1. Conéctate a la BD de Railway
2. Ejecuta el script `database.sql`

### Paso 5: Deploy
Railway desplegará automáticamente con cada push a tu rama principal

## Flujo de Usuarios

### Usuario Regular (USER)
1. Accede a `/login.php`
2. Se autentica con sus credenciales
3. Es redirigido a la landing page con saludo personalizado
4. Puede cerrar sesión

### Administrador (ADMIN)
1. Accede a `/login.php`
2. Se autentica con sus credenciales
3. Es redirigido al dashboard
4. Puede:
   - Ver estadísticas de ventas del día
   - Agregar nuevos usuarios
   - Actualizar información de usuarios
   - Eliminar usuarios
   - Ver historial de ventas recientes

## Estructura de la Base de Datos

### Tabla: usuarios
```sql
- id (INT, PRIMARY KEY)
- nombre (VARCHAR)
- email (VARCHAR, UNIQUE)
- contraseña (VARCHAR)
- rol (ENUM: admin, user)
- estado (ENUM: activo, inactivo)
- fecha_creacion (TIMESTAMP)
- fecha_actualizacion (TIMESTAMP)
```

### Tabla: ventas
```sql
- id (INT, PRIMARY KEY)
- fecha (DATE)
- producto (VARCHAR)
- cantidad (INT)
- precio_unitario (DECIMAL)
- total (DECIMAL)
- cliente_email (VARCHAR)
- metodo_pago (VARCHAR)
- estado (ENUM: completada, pendiente, cancelada)
- fecha_creacion (TIMESTAMP)
```

### Tabla: productos
```sql
- id (INT, PRIMARY KEY)
- nombre (VARCHAR)
- descripcion (TEXT)
- categoria_id (INT, FOREIGN KEY)
- precio (DECIMAL)
- stock (INT)
- estado (ENUM: activo, inactivo)
```

### Tabla: categorias
```sql
- id (INT, PRIMARY KEY)
- nombre (VARCHAR)
- descripcion (TEXT)
- estado (ENUM: activo, inactivo)
```

## Seguridad

✅ Contraseñas hasheadas con bcrypt
✅ Protección CSRF básica
✅ Headers de seguridad
✅ Validación de entrada
✅ Sesiones seguras
✅ Protección de rutas según rol

## Soporte

Para reportar bugs o sugerencias, crea un issue en el repositorio.

---

**Versión:** 1.0.0  
**Último actualizado:** 2024
