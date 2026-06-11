<?php
class UsuarioController {

    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function obtenerTodos($buscar = '') {
        $where = '';
        if (!empty($buscar)) {
            $buscar = mysqli_real_escape_string($this->conexion, $buscar);
            $where  = "WHERE u.nombres_usuarios LIKE '%$buscar%' 
                       OR u.email_usuarios LIKE '%$buscar%' 
                       OR u.apellidos_usuarios LIKE '%$buscar%'";
        }
        $sql = "SELECT u.*, r.nombre_roles 
                FROM usuarios u 
                LEFT JOIN usuarios_roles ur ON u.id_usuarios = ur.id_usuarios
                LEFT JOIN roles r ON ur.id_roles = r.id_roles
                $where
                ORDER BY u.fecha_creacion_usuarios DESC";
        return mysqli_query($this->conexion, $sql);
    }

    public function activar($id) {
        $id = intval($id);
        mysqli_query($this->conexion, "UPDATE usuarios SET estado_usuarios = 'activo' WHERE id_usuarios = $id");
    }

    public function desactivar($id, $id_sesion) {
        $id = intval($id);
        if ($id == $id_sesion) return false;
        mysqli_query($this->conexion, "UPDATE usuarios SET estado_usuarios = 'inactivo' WHERE id_usuarios = $id");
        return true;
    }

    public function hacerAdmin($id) {
        $id    = intval($id);
        $check = mysqli_fetch_assoc(mysqli_query($this->conexion, "SELECT * FROM usuarios_roles WHERE id_usuarios = $id"));
        if ($check) {
            mysqli_query($this->conexion, "UPDATE usuarios_roles SET id_roles = 1 WHERE id_usuarios = $id");
        } else {
            mysqli_query($this->conexion, "INSERT INTO usuarios_roles (id_usuarios, id_roles) VALUES ($id, 1)");
        }
    }

    public function hacerCliente($id, $id_sesion) {
        $id = intval($id);
        if ($id == $id_sesion) return false;
        mysqli_query($this->conexion, "UPDATE usuarios_roles SET id_roles = 2 WHERE id_usuarios = $id");
        return true;
    }

    public function contarTotal() {
        return mysqli_fetch_assoc(mysqli_query($this->conexion, "SELECT COUNT(*) as t FROM usuarios"))['t'];
    }

    public function contarPorEstado($estado) {
        $estado = mysqli_real_escape_string($this->conexion, $estado);
        return mysqli_fetch_assoc(mysqli_query($this->conexion, "SELECT COUNT(*) as t FROM usuarios WHERE estado_usuarios='$estado'"))['t'];
    }

    public function obtenerRecientes($limite = 5) {
        return mysqli_query($this->conexion, "SELECT * FROM usuarios ORDER BY fecha_creacion_usuarios DESC LIMIT $limite");
    }
}
