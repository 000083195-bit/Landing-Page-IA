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

            if ($this->conexion->connect_error) {
                throw new Exception('Error de conexión: ' . $this->conexion->connect_error);
            }

            // Configurar charset UTF-8
            $this->conexion->set_charset("utf8mb4");

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
