# Instrucciones de Instalación Local

## Pre-requisitos

- PHP 7.4+ instalado
- MySQL/MariaDB instalado y corriendo
- Composer (opcional)
- Git

## Paso 1: Clonar el repositorio

```bash
cd tu-directorio
git clone tu-repositorio.git
cd tu-repositorio
```

## Paso 2: Crear la base de datos

### Opción A: Usando PHPMyAdmin
1. Abre http://localhost/phpmyadmin
2. Haz clic en "Importar"
3. Selecciona el archivo `database.sql`
4. Haz clic en "Importar"

### Opción B: Usando terminal
```bash
# En Windows (PowerShell o CMD)
mysql -u root < database.sql

# En Linux/Mac
mysql -u root -p < database.sql
```

## Paso 3: Configurar variables de entorno

```bash
# Copiar archivo de ejemplo
cp .env.example .env

# Editar .env con tus datos de MySQL
# Nota: En Windows, puedes editar el archivo directamente
```

Contenido de `.env` para desarrollo local:
```
DB_HOST=localhost
DB_USER=root
DB_PASS=
DB_NAME=pesado_fallo
DB_PORT=3306
ENVIRONMENT=development
APP_URL=http://localhost:8000
```

## Paso 4: Iniciar servidor PHP

```bash
# Crear carpeta public (si no existe)
mkdir public

# Copiar archivos al public
cp index.php landing.php login.php dashboard.php styles.css public/
cp config.php db.php auth.php public/
cp database.sql public/

# Iniciar servidor
php -S localhost:8000 -t public/
```

O simplemente:
```bash
php -S localhost:8000
```

## Paso 5: Acceder a la aplicación

Abre en tu navegador:

### Login
http://localhost:8000/login.php

### Landing Page (después de login como user)
http://localhost:8000/landing.php

### Dashboard (después de login como admin)
http://localhost:8000/dashboard.php

## Credenciales de prueba

**Administrador:**
```
Email: admin@pesado.com
Contraseña: password123
```

**Usuario Regular:**
```
Email: user@pesado.com
Contraseña: password123
```

## Estructura de archivos esperada

```
tu-proyecto/
├── index.php                    # Maneja redirecciones
├── index.html                   # Landing original (opcional)
├── login.php                    # Página de login
├── landing.php                  # Landing page autenticada
├── dashboard.php                # Dashboard para admins
├── config.php                   # Configuración
├── db.php                       # Conexión a BD
├── auth.php                     # Funciones de autenticación
├── styles.css                   # Estilos
├── database.sql                 # Script SQL
├── composer.json                # Dependencias
├── .env.example                 # Variables de entorno
├── .env                         # Variables locales (crear)
├── README.md                    # Este archivo
└── RAILWAY_DEPLOYMENT.md        # Guía de despliegue
```

## Crear un usuario adicional

### Vía MySQL (fácil):
```sql
-- En MySQL/PHPMyAdmin, ejecutar:
INSERT INTO usuarios (nombre, email, contraseña, rol) 
VALUES ('Mi Nombre', 'miemail@example.com', '$2y$10$92IXUNpkm1BrD3NK3l.Ei.kT6kfEe7j5KPGEOq3N5rEzMZ8BX6pZm', 'user');
```

Esta contraseña hasheada corresponde a: `password123`

### Vía Dashboard (después de crear admin):
1. Login con credenciales de admin
2. Ve a "Gestión de Usuarios"
3. Haz clic en "+ Agregar Usuario"
4. Completa el formulario
5. Haz clic en "Agregar Usuario"

## Solución de problemas

### Error: "SQLSTATE[HY000]: General error: No such file or directory"
- Verifica que MySQL está corriendo
- Comprueba que la BD `pesado_fallo` existe
- Ejecuta nuevamente `database.sql`

### Error: "Access denied for user 'root'@'localhost'"
- Verifica que el usuario y contraseña en `.env` son correctos
- Si tienes contraseña en MySQL, actualiza `.env`:
  ```
  DB_PASS=tu_contraseña_mysql
  ```

### Error 404 - Página no encontrada
- Asegúrate que el servidor PHP está corriendo
- Verifica la ruta: `http://localhost:8000/login.php`
- Comprueba que todos los archivos .php están en la raíz

### Sesiones no funcionan
- Verifica que `session_start()` está en `config.php`
- Comprueba permisos de carpetas (especialmente /tmp en Linux)

### BD vacía después de importar
- Verifica que importaste el archivo completo
- En PHPMyAdmin, comprueba que se ejecutaron todos los queries
- Busca mensajes de error durante la importación

## Desarrollo

### Agregar nuevas funcionalidades:

1. **Nueva tabla:** Modificar `database.sql` y ejecutar migraciones
2. **Nuevas rutas:** Crear `.php` files en la raíz
3. **Proteger rutas:** Usar `requireLogin()` o `requireAdmin()` en config.php

### Mejores prácticas:
- Siempre hacer backup de la BD antes de cambios
- Usar prepared statements (ya está hecho en el código)
- No guardar credentials en archivos públicos
- Usar `.gitignore` para excluir `.env`

## Próximos pasos

1. Personalizar los estilos en `styles.css`
2. Agregar más productos a la tabla `productos`
3. Implementar carrito de compras
4. Agregar pasarela de pagos
5. Enviar emails de confirmación
6. Desplegar en Railway

---

¿Necesitas ayuda? Consulta el archivo `README.md` o `RAILWAY_DEPLOYMENT.md`
