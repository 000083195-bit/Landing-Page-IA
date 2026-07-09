# API Endpoints Disponibles

## Autenticación

### Login
**URL:** `POST /login.php`
**Parámetros:**
- `email` (string, requerido)
- `contraseña` (string, requerido)

**Respuesta exitosa:**
```php
[
    'success' => true,
    'message' => 'Login exitoso',
    'rol' => 'admin' | 'user'
]
```

### Logout
**URL:** `GET /?logout=1` o `landing.php?logout=1` o `dashboard.php?logout=1`

---

## Usuarios (Admin Only)

### Agregar Usuario
**URL:** `POST /dashboard.php`
**Parámetros:**
- `accion` = `agregar_usuario`
- `nombre` (string, requerido)
- `email` (string, requerido, único)
- `contraseña` (string, requerido)
- `rol` (enum: 'admin' | 'user', default: 'user')

**Respuesta:** Redirect a dashboard.php con mensaje de éxito/error

### Actualizar Usuario
**URL:** `POST /dashboard.php`
**Parámetros:**
- `accion` = `actualizar_usuario`
- `usuario_id` (int, requerido)
- `nombre` (string, requerido)
- `email` (string, requerido)
- `rol` (enum: 'admin' | 'user', requerido)

**Respuesta:** Redirect a dashboard.php con mensaje de éxito/error

### Eliminar Usuario
**URL:** `POST /dashboard.php`
**Parámetros:**
- `accion` = `eliminar_usuario`
- `usuario_id` (int, requerido)

**Nota:** No puedes eliminar tu propia cuenta de admin

**Respuesta:** Redirect a dashboard.php con mensaje de éxito/error

---

## Rutas Protegidas

### Landing Page (Usuarios autenticados solamente)
**URL:** `/landing.php`
**Requiere:** Sesión activa como 'user'
**Redirección:** 
- Si no está logueado → /login.php
- Si es admin → /dashboard.php

### Dashboard (Admins solamente)
**URL:** `/dashboard.php`
**Requiere:** Sesión activa como 'admin'
**Redirección:** Si no es admin → Error 403

---

## Funciones de Utilidad PHP

### En `config.php`

```php
isLoggedIn()           // Retorna true si hay sesión activa
isAdmin()              // Retorna true si es admin
getCurrentUser()       // Retorna array con datos del usuario
requireLogin()         // Redirige a login si no está logueado
requireAdmin()         // Redirige a login o error 403 si no es admin
redirect($path)        // Redirige a una ruta
getBaseUrl()           // Retorna la URL base de la aplicación
```

### En `auth.php` (Clase Auth)

```php
$auth = new Auth();

// Registrar usuario
$auth->register($nombre, $email, $contraseña)
// Retorna: ['success' => bool, 'message' => string]

// Login
$auth->login($email, $contraseña)
// Retorna: ['success' => bool, 'message' => string, 'rol' => string]

// Logout
$auth->logout()

// Obtener usuario por ID
$auth->getUserById($id)
// Retorna: array con datos del usuario o null

// Actualizar usuario
$auth->updateUser($id, $nombre, $email)
// Retorna: bool

// Cambiar contraseña
$auth->changePassword($usuario_id, $contraseña_actual, $contraseña_nueva)
// Retorna: ['success' => bool, 'message' => string]
```

### En `db.php` (Clase Database)

```php
global $db;

// Ejecutar query con resultado
$result = $db->query($sql, $params)
// Retorna: mysqli_result

// Ejecutar query sin resultado (INSERT, UPDATE, DELETE)
$db->execute($sql, $params)
// Retorna: bool

// Obtener el último ID insertado
$db->lastInsertId()
// Retorna: int

// Obtener número de filas afectadas
$db->affectedRows()
// Retorna: int
```

---

## Ejemplos de Uso

### Obtener usuario actual
```php
<?php
require_once 'config.php';

$usuario = getCurrentUser();
echo $usuario['nombre'];
?>
```

### Verificar si es admin
```php
<?php
require_once 'config.php';

if (isAdmin()) {
    echo "Es administrador";
} else {
    echo "No es administrador";
}
?>
```

### Ejecutar query a BD
```php
<?php
require_once 'db.php';
global $db;

$result = $db->query("SELECT * FROM usuarios WHERE id = ?", [$id]);
$usuario = $result->fetch_assoc();
?>
```

### Insertar datos
```php
<?php
require_once 'db.php';
global $db;

$db->execute(
    "INSERT INTO ventas (producto, cantidad, total) VALUES (?, ?, ?)",
    ['Producto X', 2, 99.99]
);

$nuevo_id = $db->lastInsertId();
?>
```

---

## Variables de Sesión

Cuando un usuario hace login, se crean las siguientes variables de sesión:

```php
$_SESSION['usuario_id']    // ID del usuario (int)
$_SESSION['nombre']        // Nombre completo (string)
$_SESSION['email']         // Email (string)
$_SESSION['rol']           // Rol: 'admin' o 'user' (string)
$_SESSION['usuario']       // Array con todos los datos anteriores
```

---

## Respuestas de Errores Comunes

### 403 Forbidden
Accesaste un recurso que requiere permisos de administrador

### 404 Not Found
El archivo .php no existe o la ruta es incorrecta

### Credenciales inválidas (Login)
Email o contraseña incorrectos

### Error de conexión a BD
Verifica que MySQL está corriendo y las credenciales en .env son correctas

---

## Agregar nuevos Endpoints

### Ejemplo: Crear ruta personalizada

```php
<?php
// archivo.php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

// Requiere login
requireLogin();

global $db;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    
    if ($accion === 'mi_accion') {
        // Tu lógica aquí
        $resultado = $db->query("SELECT * FROM ...");
    }
}

// HTML aquí
?>
```

---

## Seguridad

✅ Todas las contraseñas están hasheadas con bcrypt
✅ Todas las consultas SQL usan prepared statements (protección contra SQL injection)
✅ Las sesiones están protegidas
✅ Headers de seguridad configurados
✅ Validación de entrada básica

**Recomendaciones adicionales:**
- Usar HTTPS en producción (Railway lo proporciona)
- Cambiar credenciales de ejemplo antes de desplegar
- Implementar rate limiting en login
- Agregar 2FA para admins
- Hacer backup regular de BD

---

Última actualización: 2024
