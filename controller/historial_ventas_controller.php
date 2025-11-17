<?php
session_start();
if (!isset($_SESSION['user_id_usuario'])) {
    header('Location: login.php');
    exit();
}

require_once '../config/conexion.php';
$id_agricultor = $_SESSION['user_id_usuario'];

// Inicializar variables con valores por defecto
$ventas = [];
$ventas_agrupadas = [];
$total_general = 0;
$total_pedidos = 0;
$total_productos_vendidos = 0;

try {
    // Consulta ventas del agricultor
    $stmt = $pdo->prepare('
        SELECT 
            p.id_pedido,
            p.fecha,
            p.estado,
            pd.id_producto,
            pd.cantidad,
            pd.precio_unitario,
            pr.nombre as producto_nombre,
            pr.descripcion,
            u.nombre as cliente_nombre,
            u.apellido as cliente_apellido,
            u.email as cliente_email,
            (pd.cantidad * pd.precio_unitario) as total_linea
        FROM pedido_detalle pd
        INNER JOIN productos pr ON pd.id_producto = pr.id_producto
        INNER JOIN pedidos p ON pd.id_pedido = p.id_pedido
        INNER JOIN usuarios u ON p.id_usuario = u.id_usuario
        WHERE pr.id_agricultor = ?
        ORDER BY p.fecha DESC
    ');
    $stmt->execute([$id_agricultor]);
    $ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Agrupar ventas por pedido solo si hay resultados
    if ($ventas) {
        foreach ($ventas as $venta) {
            $id_pedido = $venta['id_pedido'];
            if (!isset($ventas_agrupadas[$id_pedido])) {
                $ventas_agrupadas[$id_pedido] = [
                    'id_pedido' => $venta['id_pedido'],
                    'fecha' => $venta['fecha'],
                    'estado' => $venta['estado'],
                    'cliente' => $venta['cliente_nombre'] . ' ' . $venta['cliente_apellido'],
                    'cliente_email' => $venta['cliente_email'],
                    'productos' => [],
                    'total_pedido' => 0
                ];
                $total_pedidos++;
            }
            
            $ventas_agrupadas[$id_pedido]['productos'][] = [
                'id_producto' => $venta['id_producto'],
                'producto_nombre' => $venta['producto_nombre'],
                'descripcion' => $venta['descripcion'],
                'cantidad' => $venta['cantidad'],
                'precio_unitario' => $venta['precio_unitario'],
                'total_linea' => $venta['total_linea']
            ];
            
            $ventas_agrupadas[$id_pedido]['total_pedido'] += $venta['total_linea'];
            $total_general += $venta['total_linea'];
        }
        $total_productos_vendidos = count($ventas);
    }

} catch (PDOException $e) {
    error_log("Error al obtener historial de ventas: " . $e->getMessage());
    $_SESSION['error'] = "Error al cargar el historial de ventas";
}
?>