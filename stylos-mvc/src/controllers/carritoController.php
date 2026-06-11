<?php
class CarritoController {

    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    // Obtener o crear el carrito activo del usuario
    public function obtenerOCrearCarrito($id_usuario) {
        $id_usuario = intval($id_usuario);

        // Buscar carrito no pagado existente
        $sql  = "SELECT id_carrito FROM carrito WHERE id_usuario = ? AND pagado = 0 LIMIT 1";
        $stmt = mysqli_prepare($this->conexion, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id_usuario);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $fila   = mysqli_fetch_assoc($result);

        if ($fila) {
            return $fila['id_carrito'];
        }

        // Crear carrito nuevo
        $sql  = "INSERT INTO carrito (id_usuario, subtotal, total, pagado) VALUES (?, 0, 0, 0)";
        $stmt = mysqli_prepare($this->conexion, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id_usuario);
        mysqli_stmt_execute($stmt);
        return mysqli_insert_id($this->conexion);
    }

    // Agregar producto al carrito
    public function agregar($id_usuario, $id_producto, $cantidad = 1) {
        $id_producto = intval($id_producto);
        $cantidad    = intval($cantidad);

        // Verificar que el producto existe y tiene stock
        $sql  = "SELECT id_productos, nombre_producto, precio_venta, stock FROM productos WHERE id_productos = ?";
        $stmt = mysqli_prepare($this->conexion, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id_producto);
        mysqli_stmt_execute($stmt);
        $producto = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        if (!$producto) {
            return ['ok' => false, 'mensaje' => 'Producto no encontrado'];
        }

        if ($producto['stock'] < $cantidad) {
            return ['ok' => false, 'mensaje' => 'Stock insuficiente'];
        }

        $id_carrito     = $this->obtenerOCrearCarrito($id_usuario);
        $precio         = $producto['precio_venta'];
        $subtotal_linea = $precio * $cantidad;

        // Verificar si ya está en el carrito
        $sql  = "SELECT id_detalle, cantidad FROM carrito_detalle WHERE id_carrito = ? AND id_producto = ?";
        $stmt = mysqli_prepare($this->conexion, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $id_carrito, $id_producto);
        mysqli_stmt_execute($stmt);
        $existe = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        if ($existe) {
            // Actualizar cantidad
            $nueva_cantidad     = $existe['cantidad'] + $cantidad;
            $nuevo_subtotal     = $precio * $nueva_cantidad;
            $sql  = "UPDATE carrito_detalle SET cantidad = ?, subtotal_linea = ? WHERE id_detalle = ?";
            $stmt = mysqli_prepare($this->conexion, $sql);
            mysqli_stmt_bind_param($stmt, "idi", $nueva_cantidad, $nuevo_subtotal, $existe['id_detalle']);
        } else {
            // Insertar nuevo item
            $sql  = "INSERT INTO carrito_detalle (id_carrito, id_producto, cantidad, precio_unitario, subtotal_linea) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($this->conexion, $sql);
            mysqli_stmt_bind_param($stmt, "iiidd", $id_carrito, $id_producto, $cantidad, $precio, $subtotal_linea);
        }

        mysqli_stmt_execute($stmt);
        $this->recalcularTotales($id_carrito);

        return [
            'ok'      => true,
            'mensaje' => '¡Producto agregado al carrito!',
            'total_items' => $this->contarItems($id_carrito)
        ];
    }

   public function eliminar($id_usuario, $id_detalle) {
    $id_detalle = intval($id_detalle);
    $id_carrito = $this->obtenerOCrearCarrito($id_usuario);

    $sql  = "DELETE FROM carrito_detalle WHERE id_detalle = ? AND id_carrito = ?";
    $stmt = mysqli_prepare($this->conexion, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $id_detalle, $id_carrito);
    mysqli_stmt_execute($stmt);

    $eliminado = mysqli_stmt_affected_rows($stmt) > 0;

    $this->recalcularTotales($id_carrito);

    return [
        'ok'          => $eliminado,
        'mensaje'     => $eliminado ? 'Producto eliminado del carrito.' : 'No se encontró el item.',
        'total_items' => $this->contarItems($id_carrito),
    ];
}

    // Obtener items del carrito para mostrar en el header
    public function obtenerItems($id_usuario) {
        $id_carrito = $this->obtenerOCrearCarrito($id_usuario);

        $sql = "SELECT cd.id_detalle, cd.cantidad, cd.precio_unitario, cd.subtotal_linea,
                       p.nombre_producto, p.imagen
                FROM carrito_detalle cd
                JOIN productos p ON cd.id_producto = p.id_productos
                WHERE cd.id_carrito = ?";
        $stmt = mysqli_prepare($this->conexion, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id_carrito);
        mysqli_stmt_execute($stmt);
        return mysqli_stmt_get_result($stmt);
    }

    // Contar items totales
    public function contarItems($id_carrito) {
        $id_carrito = intval($id_carrito);
        $res = mysqli_fetch_assoc(mysqli_query($this->conexion,
            "SELECT COALESCE(SUM(cantidad), 0) as total FROM carrito_detalle WHERE id_carrito = $id_carrito"));
        return (int)$res['total'];
    }

    // Obtener subtotal
   // Dentro de la clase CarritoController, junto a los otros métodos
private function obtenerSubtotal($id_carrito) {
    $sql  = "SELECT COALESCE(SUM(subtotal), 0) as total FROM carrito_detalle WHERE id_carrito = ?";
    $stmt = mysqli_prepare($this->conexion, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id_carrito);
    mysqli_stmt_execute($stmt);
    $res  = mysqli_stmt_get_result($stmt);
    $row  = mysqli_fetch_assoc($res);
    return $row['total'];
}

    // Recalcular y guardar totales en tabla carrito
    private function recalcularTotales($id_carrito) {
        $id_carrito = intval($id_carrito);
        $subtotal   = $this->obtenerSubtotal($id_carrito);
        $total      = $subtotal; // Aquí podrías agregar impuestos o descuentos
        mysqli_query($this->conexion,
            "UPDATE carrito SET subtotal = $subtotal, total = $total WHERE id_carrito = $id_carrito");
    }
}