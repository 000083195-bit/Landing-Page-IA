<?php
// ============================================
// Clase para gestionar la conexión a BD
// ============================================

class Database {
    private $conexion;
    private $error;

    public function __construct() {
        try {
            $this->conexion = new mysqli(
                DB_HOST,
                DB_USER,
                DB_PASS,
                DB_NAME,
                DB_PORT
            );

            if ($this->conexion->connect_errno === 1049) {
                $this->conexion->close();
                $this->createDatabaseIfMissing();

                $this->conexion = new mysqli(
                    DB_HOST,
                    DB_USER,
                    DB_PASS,
                    DB_NAME,
                    DB_PORT
                );
            }

            if ($this->conexion->connect_error) {
                throw new Exception('Error de conexión: ' . $this->conexion->connect_error);
            }

            // Configurar charset UTF-8
            $this->conexion->set_charset("utf8mb4");

            $this->ensureSchema();

        } catch (Exception $e) {
            $this->error = $e->getMessage();
            error_log($this->error);
            $debug = getenv('DEBUG');
            if ($debug === 'true' || getenv('ENVIRONMENT') === 'development') {
                die('Error de conexión a la base de datos: ' . $this->error);
            }
            die('Error de conexión a la base de datos. Revisa la configuración de las variables de entorno y la existencia de la base de datos.');
        }
    }

    private function createDatabaseIfMissing() {
        $tmpConnection = new mysqli(
            DB_HOST,
            DB_USER,
            DB_PASS,
            '',
            DB_PORT
        );

        if ($tmpConnection->connect_error) {
            throw new Exception('Error al conectar a MySQL para crear la base de datos: ' . $tmpConnection->connect_error);
        }

        $dbNameEscaped = $tmpConnection->real_escape_string(DB_NAME);
        $sql = "CREATE DATABASE IF NOT EXISTS `$dbNameEscaped` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";
        if (!$tmpConnection->query($sql)) {
            throw new Exception('Error creando la base de datos: ' . $tmpConnection->error);
        }

        $tmpConnection->close();
    }

    private function ensureSchema() {
        foreach ($this->getSchemaStatements() as $statement) {
            if (!$this->conexion->query($statement)) {
                throw new Exception('Error creando tabla o índice: ' . $this->conexion->error);
            }
        }

        $result = $this->conexion->query("SELECT COUNT(*) as total FROM usuarios");
        if ($result) {
            $row = $result->fetch_assoc();
            if ((int)$row['total'] === 0) {
                $passHash = password_hash('password123', PASSWORD_BCRYPT);
                $this->conexion->query("INSERT INTO usuarios (nombre, email, contraseña, rol) VALUES ('Admin Pesado', 'admin@pesado.com', '$passHash', 'admin')");
                $this->conexion->query("INSERT INTO usuarios (nombre, email, contraseña, rol) VALUES ('Usuario Test', 'user@pesado.com', '$passHash', 'user')");
            }
            $result->free();
        }
    }

    private function getSchemaStatements() {
        return [
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
    }

    public function query($sql, $params = []) {
        try {
            if (empty($params)) {
                $result = $this->conexion->query($sql);
                if (!$result) {
                    throw new Exception('Error en la consulta: ' . $this->conexion->error);
                }
                return $result;
            } else {
                $stmt = $this->conexion->prepare($sql);
                if (!$stmt) {
                    throw new Exception('Error al preparar: ' . $this->conexion->error);
                }

                // Determinar tipos de datos
                $types = '';
                foreach ($params as $param) {
                    if (is_int($param)) {
                        $types .= 'i';
                    } elseif (is_float($param)) {
                        $types .= 'd';
                    } else {
                        $types .= 's';
                    }
                }

                $stmt->bind_param($types, ...$params);
                $stmt->execute();

                return $stmt->get_result();
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    public function execute($sql, $params = []) {
        try {
            $stmt = $this->conexion->prepare($sql);
            if (!$stmt) {
                throw new Exception('Error al preparar: ' . $this->conexion->error);
            }

            if (!empty($params)) {
                $types = '';
                foreach ($params as $param) {
                    if (is_int($param)) {
                        $types .= 'i';
                    } elseif (is_float($param)) {
                        $types .= 'd';
                    } else {
                        $types .= 's';
                    }
                }
                $stmt->bind_param($types, ...$params);
            }

            $stmt->execute();
            return true;
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function lastInsertId() {
        return $this->conexion->insert_id;
    }

    public function affectedRows() {
        return $this->conexion->affected_rows;
    }

    public function close() {
        if ($this->conexion) {
            $this->conexion->close();
        }
    }

    public function getError() {
        return $this->error;
    }
}

// Crear instancia global de base de datos
$db = new Database();
?>
