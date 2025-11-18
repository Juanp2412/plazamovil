<?php
require_once __DIR__ . '/../config/paths.php';
// Incluir el controlador al inicio del archivo
require_once '../controller/historial_ventas_controller.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagina Principal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/styles.css">
    <!-- Mover el script de Bootstrap al final del body -->
     
</head>

<body>
    
    <?php include '../navbar.php'; ?>
    <div style="height:70px"></div>
    <div class="container mt-5">
        <h2 class="mb-4 text-center">Historial de Ventas</h2>
        
        <!-- Resumen de ventas -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-white bg-success">
                    <div class="card-body text-center">
                        <i class="bi bi-currency-dollar display-6"></i>
                        <h5 class="card-title mt-2">Total Ventas</h5>
                        <h3>$<?php echo number_format($total_general, 2); ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-primary">
                    <div class="card-body text-center">
                        <i class="bi bi-cart-check display-6"></i>
                        <h5 class="card-title mt-2">Pedidos Totales</h5>
                        <h3><?php echo $total_pedidos; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-info">
                    <div class="card-body text-center">
                        <i class="bi bi-box-seam display-6"></i>
                        <h5 class="card-title mt-2">Productos Vendidos</h5>
                        <h3><?php echo $total_productos_vendidos; ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-funnel"></i> Filtros</h5>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label for="estado" class="form-label">Estado del Pedido</label>
                        <select class="form-select" id="estado" name="estado">
                            <option value="">Todos los estados</option>
                            <option value="pendiente">Pendiente</option>
                            <option value="confirmado">Confirmado</option>
                            <option value="completado">Completado</option>
                            <option value="cancelado">Cancelado</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="fecha_desde" class="form-label">Fecha Desde</label>
                        <input type="date" class="form-control" id="fecha_desde" name="fecha_desde">
                    </div>
                    <div class="col-md-4">
                        <label for="fecha_hasta" class="form-label">Fecha Hasta</label>
                        <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-filter"></i> Filtrar</button>
                        <a href="historial_ventas.php" class="btn btn-secondary"><i class="bi bi-arrow-clockwise"></i> Limpiar</a>
                    </div>
                </form>
            </div>
        </div>

        <?php if (count($ventas_agrupadas) === 0): ?>
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle display-4 d-block mb-3"></i>
                <h4>No tienes ventas registradas</h4>
                <p class="mb-0">Tus ventas aparecerán aquí cuando los clientes compren tus productos.</p>
            </div>
        <?php else: ?>
            <?php foreach ($ventas_agrupadas as $pedido): ?>
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h5 class="mb-0">
                                    <i class="bi bi-receipt"></i> Pedido #<?php echo htmlspecialchars($pedido['id_pedido']); ?>
                                </h5>
                                <small class="d-block mt-1">
                                    <i class="bi bi-calendar"></i> <?php echo htmlspecialchars($pedido['fecha']); ?>
                                </small>
                            </div>
                            <div class="col-md-6 text-end">
                                <span class="badge bg-<?php 
                                    switch($pedido['estado']) {
                                        case 'completado': echo 'success'; break;
                                        case 'confirmado': echo 'info'; break;
                                        case 'pendiente': echo 'warning'; break;
                                        case 'cancelado': echo 'danger'; break;
                                        default: echo 'secondary';
                                    }
                                ?> fs-6">
                                    <?php echo htmlspecialchars($pedido['estado']); ?>
                                </span>
                                <div class="mt-1">
                                    <strong>Total: $<?php echo number_format($pedido['total_pedido'], 2); ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong><i class="bi bi-person"></i> Cliente:</strong> 
                                <?php echo htmlspecialchars($pedido['cliente']); ?>
                            </div>
                            <div class="col-md-6">
                                <strong><i class="bi bi-envelope"></i> Email:</strong> 
                                <?php echo htmlspecialchars($pedido['cliente_email']); ?>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Producto</th>
                                        <th>Descripción</th>
                                        <th class="text-center">Cantidad</th>
                                        <th class="text-end">Precio Unitario</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pedido['productos'] as $producto): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($producto['producto_nombre']); ?></strong>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?php echo htmlspecialchars(substr($producto['descripcion'], 0, 50)); ?>...
                                                </small>
                                            </td>
                                            <td class="text-center"><?php echo htmlspecialchars($producto['cantidad']); ?></td>
                                            <td class="text-end">$<?php echo number_format($producto['precio_unitario'], 2); ?></td>
                                            <td class="text-end">$<?php echo number_format($producto['total_linea'], 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="table-active">
                                        <td colspan="4" class="text-end"><strong>Total del Pedido:</strong></td>
                                        <td class="text-end"><strong>$<?php echo number_format($pedido['total_pedido'], 2); ?></strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        
                        <div class="mt-3 d-flex gap-2 flex-wrap">
                            <!-- Comprobante de venta -->
                            <a href="../controller/generar_comprobante_venta.php?id_pedido=<?php echo $pedido['id_pedido']; ?>&id_agricultor=<?php echo $id_agricultor; ?>"
                                class="btn btn-primary btn-sm" target="_blank">
                                <i class="bi bi-receipt"></i> Comprobante de Venta
                            </a>
                            
                            <!-- Contactar al cliente -->
                            <a href="mailto:<?php echo $pedido['cliente_email']; ?>?subject=Consulta sobre tu pedido #<?php echo $pedido['id_pedido']; ?>"
                                class="btn btn-outline-info btn-sm">
                                <i class="bi bi-envelope"></i> Contactar Cliente
                            </a>
                            
                            <!-- Actualizar estado del pedido -->
                            <div class="dropdown">
                                <button class="btn btn-outline-warning btn-sm dropdown-toggle" type="button" 
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-pencil"></i> Cambiar Estado
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="../controller/actualizar_estado_pedido.php?id_pedido=<?php echo $pedido['id_pedido']; ?>&estado=pendiente">Pendiente</a></li>
                                    <li><a class="dropdown-item" href="../controller/actualizar_estado_pedido.php?id_pedido=<?php echo $pedido['id_pedido']; ?>&estado=confirmado">Confirmado</a></li>
                                    <li><a class="dropdown-item" href="../controller/actualizar_estado_pedido.php?id_pedido=<?php echo $pedido['id_pedido']; ?>&estado=completado">Completado</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <div class="mt-3 text-center">
            <a href="<?php echo BASE_URL; ?>/index.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Volver al Inicio
            </a>
        </div>
    </div>

    <!-- Scripts al final del body para evitar conflictos -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Aplicar filtros desde URL
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            
            if (urlParams.has('estado')) {
                document.getElementById('estado').value = urlParams.get('estado');
            }
            if (urlParams.has('fecha_desde')) {
                document.getElementById('fecha_desde').value = urlParams.get('fecha_desde');
            }
            if (urlParams.has('fecha_hasta')) {
                document.getElementById('fecha_hasta').value = urlParams.get('fecha_hasta');
            }

            // Forzar la inicialización de dropdowns de Bootstrap
            var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
            var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
                return new bootstrap.Dropdown(dropdownToggleEl);
            });
        });
    </script>
</body>
</html>