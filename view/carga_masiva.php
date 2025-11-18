<?php
require_once __DIR__ . '/../config/paths.php';
// Verificar si la sesión ya está iniciada antes de iniciarla
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id_usuario']) || $_SESSION['user_id_rol'] != 3) {
    header('Location: login.php');
    exit();
}

require_once '../config/conexion.php';
require_once '../controller/carga_masiva_controller.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carga Masiva de Productos - Agricultor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/styles.css">
</head>
<body>
    <?php include __DIR__ . '/../navbar.php'; ?>
    <div style="height:70px"></div>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">
                            <i class="bi bi-upload"></i> Carga Masiva de Productos
                        </h4>
                    </div>
                    <div class="card-body">
                        
                        <?php if (!empty($mensaje)): ?>
                            <div class="alert alert-<?php echo $tipoMensaje; ?> alert-dismissible fade show" role="alert">
                                <h5 class="alert-heading">
                                    <?php echo $tipoMensaje == 'success' ? '✅ Éxito' : ($tipoMensaje == 'warning' ? '⚠️ Advertencia' : '❌ Error'); ?>
                                </h5>
                                <?php echo htmlspecialchars($mensaje); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <div class="alert alert-info">
                            <h5><i class="bi bi-info-circle"></i> Instrucciones:</h5>
                            <p>El archivo CSV debe tener este formato (la columna "Unidad" es opcional):</p>
                            
                            <div class="table-responsive mt-3">
                                <table class="table table-sm table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Descripción</th>
                                            <th>Categoría</th>
                                            <th>Precio</th>
                                            <th>Stock</th>
                                            <th>Unidad (Opcional)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Manzana Roja</td>
                                            <td>Manzana fresca de la región</td>
                                            <td>Frutas</td>
                                            <td>2500</td>
                                            <td>100</td>
                                            <td>Kilo</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <form action="carga_masiva.php" method="POST" enctype="multipart/form-data" id="formCarga">
                            <div class="mb-4">
                                <label for="archivo_excel" class="form-label">
                                    <strong>Seleccionar archivo CSV:</strong>
                                </label>
                                <input type="file" class="form-control" id="archivo_excel" name="archivo_excel" 
                                       accept=".csv" required>
                                <div class="form-text">
                                    Tamaño máximo: 10MB. Formato permitido: CSV (.csv)
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="mis_productos.php" class="btn btn-secondary me-md-2">
                                    <i class="bi bi-arrow-left"></i> Volver a Mis Productos
                                </a>
                                <button type="submit" class="btn btn-success" id="btnCargar">
                                    <i class="bi bi-upload"></i> Iniciar Carga Masiva
                                </button>
                            </div>
                        </form>

                        <div class="mt-5">
                            <div class="alert alert-warning">
                                <h5><i class="bi bi-download"></i> ¿No tienes una plantilla?</h5>
                                <a href="../controller/descargar_plantilla.php?tipo=csv" 
                                   class="btn btn-outline-primary">
                                    <i class="bi bi-file-earmark-text"></i> Descargar Plantilla CSV
                                </a>
                            </div>
                        </div>

                        <!-- Información de categorías -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="bi bi-list-check"></i> Categorías Disponibles en tu Sistema</h6>
                                    </div>
                                    <div class="card-body">
                                        <?php
                                        echo '<div class="small">';
                                        foreach ($categorias as $cat) {
                                            echo "<span class='badge bg-secondary me-1'>{$cat['nombre']} (ID:{$cat['id_categoria']})</span>";
                                        }
                                        echo '</div>';
                                        ?>
                                        <p class="mt-2 text-muted"><small>Usa exactamente estos nombres en la columna "Categoría" del CSV</small></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('formCarga').addEventListener('submit', function(e) {
            const archivo = document.getElementById('archivo_excel').files[0];
            const btnCargar = document.getElementById('btnCargar');
            
            if (archivo) {
                const fileName = archivo.name.toLowerCase();
                if (!fileName.endsWith('.csv')) {
                    e.preventDefault();
                    alert('Solo se permiten archivos CSV (.csv)');
                    return;
                }
                
                btnCargar.innerHTML = '<i class="bi bi-hourglass-split"></i> Procesando...';
                btnCargar.disabled = true;
            }
        });
    </script>
</body>
</html>