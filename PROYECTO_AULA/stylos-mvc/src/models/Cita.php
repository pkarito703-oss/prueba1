<?php
class Cita {
    public $id;
    public $usuario_id;
    public $servicio_id;
    public $servicio_nombre;
    public $precio;
    public $fecha;
    public $hora;
    public $nombre_cliente;
    public $telefono;
    public $notas;
    public $estado;

    public static function fromArray($data) {
        $c                  = new self();
        $c->id              = $data['id'];
        $c->usuario_id      = $data['usuario_id'];
        $c->servicio_id     = $data['servicio_id'];
        $c->servicio_nombre = $data['servicio'] ?? '';
        $c->precio          = $data['precio'] ?? 0;
        $c->fecha           = $data['fecha'];
        $c->hora            = $data['hora'];
        $c->nombre_cliente  = $data['nombre_cliente'];
        $c->telefono        = $data['telefono'];
        $c->notas           = $data['notas'] ?? '';
        $c->estado          = $data['estado'];
        return $c;
    }

    public function fechaFormateada() {
        return date('d/m/Y', strtotime($this->fecha));
    }

    public function horaFormateada() {
        return substr($this->hora, 0, 5);
    }

    public function colorEstado() {
        switch ($this->estado) {
            case 'confirmada': return '#d4edda; color:#155724';
            case 'pendiente':  return '#fff3cd; color:#856404';
            case 'cancelada':  return '#f8d7da; color:#721c24';
            case 'completada': return '#e2d9f3; color:#4a1d96';
            default:           return '#d1ecf1; color:#0c5460';
        }
    }
}
