<?php
class AuthController {

    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function login($email, $password) {
        if (empty($email) || empty($password)) {
            header("Location: " . BASE_URL . "/login.php?error=Por favor completa todos los campos");
            exit();
        }

        $sql  = "SELECT * FROM usuarios WHERE email_usuarios = ?";
        $stmt = mysqli_prepare($this->conexion, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);

        if ($fila = mysqli_fetch_assoc($resultado)) {
            if (password_verify($password, $fila['clave_usuarios'])) {
                if ($fila['estado_usuarios'] == 'activo') {

                    $_SESSION['id_usuario'] = $fila['id_usuarios'];
                    $_SESSION['nombre']     = $fila['nombres_usuarios'];
                    $_SESSION['apellido']   = $fila['apellidos_usuarios'];
                    $_SESSION['email']      = $fila['email_usuarios'];
                    $_SESSION['logueado']   = true;

                    $sql_rol  = "SELECT r.nombre_roles FROM roles r 
                                 JOIN usuarios_roles ur ON r.id_roles = ur.id_roles 
                                 WHERE ur.id_usuarios = ?";
                    $stmt_rol = mysqli_prepare($this->conexion, $sql_rol);
                    mysqli_stmt_bind_param($stmt_rol, "i", $fila['id_usuarios']);
                    mysqli_stmt_execute($stmt_rol);
                    $res_rol = mysqli_stmt_get_result($stmt_rol);
                    $rol     = mysqli_fetch_assoc($res_rol);

                    $_SESSION['rol'] = $rol ? $rol['nombre_roles'] : 'cliente';

                    if ($_SESSION['rol'] == 'admin') {
                        header("Location: " . BASE_URL . "/admin/dashboard.php");
                    } else {
                        header("Location: " . BASE_URL . "/index.php");
                    }
                    exit();

                } else {
                    header("Location: " . BASE_URL . "/login.php?error=Tu cuenta está inactiva");
                    exit();
                }
            } else {
                header("Location: " . BASE_URL . "/login.php?error=Correo o contraseña incorrectos");
                exit();
            }
        } else {
            header("Location: " . BASE_URL . "/login.php?error=Correo o contraseña incorrectos");
            exit();
        }
    }

    public function registro($datos) {
        $nombres   = trim($datos['nombres']);
        $apellidos = trim($datos['apellidos']);
        $email     = trim($datos['email']);
        $password  = trim($datos['password']);
        $confirmar = trim($datos['confirmar']);
        $telefono  = trim($datos['telefono']);

        if (empty($nombres) || empty($apellidos) || empty($email) || empty($password)) {
            header("Location: " . BASE_URL . "/login.php?tab=register&error=Por favor completa todos los campos");
            exit();
        }

        if ($password !== $confirmar) {
            header("Location: " . BASE_URL . "/login.php?tab=register&error=Las contraseñas no coinciden");
            exit();
        }

        if (strlen($password) < 6) {
            header("Location: " . BASE_URL . "/login.php?tab=register&error=La contraseña debe tener mínimo 6 caracteres");
            exit();
        }

        $sql_check = "SELECT id_usuarios FROM usuarios WHERE email_usuarios = ?";
        $stmt_check = mysqli_prepare($this->conexion, $sql_check);
        mysqli_stmt_bind_param($stmt_check, "s", $email);
        mysqli_stmt_execute($stmt_check);
        mysqli_stmt_store_result($stmt_check);

        if (mysqli_stmt_num_rows($stmt_check) > 0) {
            header("Location: " . BASE_URL . "/login.php?tab=register&error=Este correo ya está registrado");
            exit();
        }

        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $fecha_hoy     = date('Y-m-d');

        $sql  = "INSERT INTO usuarios (nombres_usuarios, apellidos_usuarios, email_usuarios, clave_usuarios, telefono_usuarios, estado_usuarios, fecha_creacion_usuarios) 
                 VALUES (?, ?, ?, ?, ?, 'activo', ?)";
        $stmt = mysqli_prepare($this->conexion, $sql);
        mysqli_stmt_bind_param($stmt, "ssssss", $nombres, $apellidos, $email, $password_hash, $telefono, $fecha_hoy);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: " . BASE_URL . "/login.php?exito=Cuenta creada exitosamente. Ya puedes iniciar sesión");
            exit();
        } else {
            header("Location: " . BASE_URL . "/login.php?tab=register&error=Error al crear la cuenta. Intenta de nuevo");
            exit();
        }
    }

    public function logout() {
        session_destroy();
        header("Location: " . BASE_URL . "/index.php");
        exit();
    }
}