<?php
class Usuario {
    public $id;
    public $nombres;
    public $apellidos;
    public $email;
    public $telefono;
    public $estado;
    public $fecha_creacion;
    public $rol;

    public static function fromArray($data) {
        $u                = new self();
        $u->id            = $data['id_usuarios'];
        $u->nombres       = $data['nombres_usuarios'];
        $u->apellidos     = $data['apellidos_usuarios'];
        $u->email         = $data['email_usuarios'];
        $u->telefono      = $data['telefono_usuarios'] ?? '';
        $u->estado        = $data['estado_usuarios'];
        $u->fecha_creacion= $data['fecha_creacion_usuarios'] ?? '';
        $u->rol           = $data['nombre_roles'] ?? 'cliente';
        return $u;
    }

    public function nombreCompleto() {
        return $this->nombres . ' ' . $this->apellidos;
    }
}
