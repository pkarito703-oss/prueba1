<?php
class CitaController {

    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function crear($datos, $usuario_id) {
        $nombre_cliente = trim($datos['nombre_cliente']);
        $telefono       = trim($datos['telefono']);
        $servicio_id    = intval($datos['servicio_id']);
        $fecha          = trim($datos['fecha']);
        $hora           = trim($datos['hora']);
        $notas          = trim($datos['notas']);

        if (empty($nombre_cliente) || empty($telefono) || empty($servicio_id) || empty($fecha) || empty($hora)) {
            header("Location: " . BASE_URL . "/citas.php?error=Por favor completa todos los campos obligatorios");
            exit();
        }

        $dia_semana = date('w', strtotime($fecha));
        if ($dia_semana == 0) {
            header("Location: " . BASE_URL . "/citas.php?error=Lo sentimos, los domingos estamos cerrados");
            exit();
        }

        if (strtotime($fecha) < strtotime(date('Y-m-d'))) {
            header("Location: " . BASE_URL . "/citas.php?error=La fecha no puede ser en el pasado");
            exit();
        }

        $sql_check = "SELECT id FROM citas WHERE fecha = ? AND hora = ? AND estado != 'cancelada'";
        $stmt_check = mysqli_prepare($this->conexion, $sql_check);
        mysqli_stmt_bind_param($stmt_check, "ss", $fecha, $hora);
        mysqli_stmt_execute($stmt_check);
        mysqli_stmt_store_result($stmt_check);

        if (mysqli_stmt_num_rows($stmt_check) > 0) {
            header("Location: " . BASE_URL . "/citas.php?error=Ese horario ya está ocupado. Por favor elige otra hora");
            exit();
        }

        $sql  = "INSERT INTO citas (usuario_id, servicio_id, fecha, hora, nombre_cliente, telefono, notas, estado) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, 'pendiente')";
        $stmt = mysqli_prepare($this->conexion, $sql);
        mysqli_stmt_bind_param($stmt, "iisssss", $usuario_id, $servicio_id, $fecha, $hora, $nombre_cliente, $telefono, $notas);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: " . BASE_URL . "/citas.php?exito=¡Cita reservada exitosamente! Te contactaremos para confirmar.");
            exit();
        } else {
            header("Location: " . BASE_URL . "/citas.php?error=Error al reservar la cita. Intenta de nuevo.");
            exit();
        }
    }

    public function confirmar($id) {
        $id = intval($id);
        mysqli_query($this->conexion, "UPDATE citas SET estado = 'confirmada' WHERE id = $id");
    }

    public function cancelar($id) {
        $id = intval($id);
        mysqli_query($this->conexion, "UPDATE citas SET estado = 'cancelada' WHERE id = $id");
    }

    public function completar($id) {
        $id = intval($id);
        mysqli_query($this->conexion, "UPDATE citas SET estado = 'completada' WHERE id = $id");
    }

    public function obtenerTodas($filtro = 'todas') {
        $where = $filtro != 'todas' ? "WHERE c.estado = '" . mysqli_real_escape_string($this->conexion, $filtro) . "'" : "";
        $sql   = "SELECT c.*, s.nombre as servicio, s.precio 
                  FROM citas c 
                  JOIN servicios s ON c.servicio_id = s.id 
                  $where
                  ORDER BY c.fecha DESC, c.hora DESC";
        return mysqli_query($this->conexion, $sql);
    }

    public function obtenerPorUsuario($usuario_id, $limite = 3) {
        $sql  = "SELECT c.fecha, c.hora, c.estado, s.nombre as servicio 
                 FROM citas c 
                 JOIN servicios s ON c.servicio_id = s.id
                 WHERE c.usuario_id = ? 
                 ORDER BY c.fecha DESC, c.hora DESC 
                 LIMIT $limite";
        $stmt = mysqli_prepare($this->conexion, $sql);
        mysqli_stmt_bind_param($stmt, "i", $usuario_id);
        mysqli_stmt_execute($stmt);
        return mysqli_stmt_get_result($stmt);
    }

    public function contarPorEstado($estado) {
        $estado = mysqli_real_escape_string($this->conexion, $estado);
        return mysqli_fetch_assoc(mysqli_query($this->conexion, "SELECT COUNT(*) as t FROM citas WHERE estado='$estado'"))['t'];
    }
}