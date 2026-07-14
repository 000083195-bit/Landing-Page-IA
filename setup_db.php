<?php
// ============================================
// Setup de Base de Datos para Railway
// ============================================

function env($key, $default = null) {
    $value = getenv($key);
    return ($value !== false && $value !== '') ? $value : $default;
}

$host = env('DB_HOST', env('MYSQL_HOST', 'localhost'));
$user = env('DB_USER', env('MYSQL_USER', env('DB_USERNAME', 'root')));
$pass = env('DB_PASS', env('MYSQL_PASSWORD', env('DB_PASSWORD', '')));
$port = env('DB_PORT', env('MYSQL_PORT', 3306));
$dbName = env('DB_NAME', env('MYSQL_DATABASE', env('DATABASE_NAME', 'pesado_fallo')));

$mysqli = new mysqli($host, $user, $pass, '', $port);
if ($mysqli->connect_error) {
    die('Error al conectar a MySQL: ' . $mysqli->connect_error);
}

$mysqli->set_charset('utf8mb4');

$sql = "CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";
if (!$mysqli->query($sql)) {
    die('Error creando la base de datos: ' . $mysqli->error);
}

if (!$mysqli->select_db($dbName)) {
    die('Error seleccionando la base de datos: ' . $mysqli->error);
}

$statements = [
    "CREATE TABLE IF NOT EXISTS usuarios (
        id INT PRIMARY KEY AUTO_INCREMENT,
        nombre VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        contraseña VARCHAR(255) NOT NULL,
        rol ENUM('admin', 'user') DEFAULT 'user',
        estado ENUM('activo', 'inactivo') DEFAULT 'activo',
        fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_email (email),
        INDEX idx_rol (rol)
    );",

    "CREATE TABLE IF NOT EXISTS ventas (
        id INT PRIMARY KEY AUTO_INCREMENT,
        fecha DATE NOT NULL,
        producto VARCHAR(150) NOT NULL,
        cantidad INT NOT NULL DEFAULT 1,
        precio_unitario DECIMAL(10, 2) NOT NULL,
        total DECIMAL(10, 2) NOT NULL,
        cliente_email VARCHAR(100),
        metodo_pago VARCHAR(50),
        estado ENUM('completada', 'pendiente', 'cancelada') DEFAULT 'completada',
        fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_fecha (fecha),
        INDEX idx_estado (estado)
    );",

    "CREATE TABLE IF NOT EXISTS categorias (
        id INT PRIMARY KEY AUTO_INCREMENT,
        nombre VARCHAR(100) NOT NULL,
        descripcion TEXT,
        estado ENUM('activo', 'inactivo') DEFAULT 'activo',
        fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );",

    "CREATE TABLE IF NOT EXISTS productos (
        id INT PRIMARY KEY AUTO_INCREMENT,
        nombre VARCHAR(150) NOT NULL,
        descripcion TEXT,
        categoria_id INT,
        precio DECIMAL(10, 2) NOT NULL,
        stock INT DEFAULT 0,
        estado ENUM('activo', 'inactivo') DEFAULT 'activo',
        fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (categoria_id) REFERENCES categorias(id)
    );",

    "CREATE TABLE IF NOT EXISTS sesiones (
        id INT PRIMARY KEY AUTO_INCREMENT,
        usuario_id INT NOT NULL,
        token VARCHAR(255) NOT NULL UNIQUE,
        ip_address VARCHAR(45),
        user_agent VARCHAR(255),
        fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        fecha_expiracion DATETIME,
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
        INDEX idx_token (token)
    );",
];

foreach ($statements as $statement) {
    if (!$mysqli->query($statement)) {
        die('Error ejecutando statement: ' . $mysqli->error . '\n' . $statement);
    }
}

// Insertar datos de ejemplo solo si no existen
$result = $mysqli->query("SELECT COUNT(*) as total FROM usuarios");
$row = $result->fetch_assoc();
if ((int)$row['total'] === 0) {
    $passHash = password_hash('password123', PASSWORD_BCRYPT);
    $mysqli->query("INSERT INTO usuarios (nombre, email, contraseña, rol) VALUES ('Admin Pesado', 'admin@pesado.com', '$passHash', 'admin')");
    $mysqli->query("INSERT INTO usuarios (nombre, email, contraseña, rol) VALUES ('Usuario Test', 'user@pesado.com', '$passHash', 'user')");
}

echo "Base de datos '$dbName' creada/actualizada correctamente.\n";
$mysqli->close();
