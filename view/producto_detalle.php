<?php
require_once __DIR__ . '/../config/paths.php';
require_once '../config/conexion.php';
require_once '../model/resena_model.php';
session_start();
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Agregar un producto al carrito
$id_producto = $_POST['id_producto'] ?? null;
if ($id_producto) {
    $_SESSION['carrito'][] = $id_producto;
}

// Verificar si se ha pasado un ID de producto
if (!isset($_GET['id_producto'])) {
    echo "Producto no encontrado.";
    exit;
}

// Obtener el ID del producto
$id_producto = $_GET['id_producto'];

// Consultar los detalles del producto y del vendedor
$stmt = $pdo->prepare("
    SELECT p.*, um.nombre AS unidad,
           u.nombre_completo AS agricultor, u.telefono, u.foto AS foto_usuario
    FROM productos p
    JOIN agricultor a ON p.id_agricultor = a.id_agricultor
    JOIN usuarios u ON a.id_usuario = u.id_usuario
    LEFT JOIN unidades_de_medida um ON p.id_unidad = um.id_unidad
    WHERE p.id_producto = ?
");
$stmt->execute([$id_producto]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

// Verificar si el producto existe
if (!$producto) {
    echo "Producto no encontrado.";
    exit;
}

// Obtener la ruta correcta de la imagen del producto
$rutaImgProducto = "../img/" . $producto['foto'];
if (empty($producto['foto']) || !is_file(__DIR__ . "/../img/" . $producto['foto'])) {
    $rutaImgProducto = "../img/default.png";
}

// Obtener la ruta correcta de la imagen del usuario
$rutaImgUsuario = "../img/" . $producto['foto_usuario'];
if (empty($producto['foto_usuario']) || !is_file(__DIR__ . "/../img/" . $producto['foto_usuario'])) {
    $rutaImgUsuario = "../img/default.png";
}

$id_rol = $_SESSION['user_id_rol'] ?? null;

$resenas = ResenaModel::obtenerResenas($id_producto);

// Obtener agricultor del producto
$stmt = $pdo->prepare("SELECT id_agricultor FROM productos WHERE id_producto = ?");
$stmt->execute([$id_producto]);
$id_agricultor = $stmt->fetchColumn();
$calificacion = ResenaModel::promedioAgricultor($id_agricultor);

// Procesar reseña
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['estrellas'], $_POST['comentario'])) {
    $estrellas = (int)$_POST['estrellas'];
    $comentario = trim($_POST['comentario']);
    $id_usuario = $_SESSION['user_id_usuario'];
    ResenaModel::agregarResena($id_producto, $id_usuario, $estrellas, $comentario);
    header("Location: producto_detalle.php?id_producto=$id_producto");
    exit;
}
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
   
    <style>
        .resena-card {
            background: #f1f8e9;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(44, 62, 80, 0.07);
            padding: 1rem 1.2rem;
            margin-bottom: 1rem;
        }
        .star-rating {
            direction: ltr;
            unicode-bidi: bidi-override;
            font-size: 1.3rem;
            cursor: pointer;
        }
        .star-rating input[type="radio"] {
            display: none;
        }
        .star-rating label {
            color: #bdbdbd;
            margin: 0 2px;
            transition: color 0.15s;
        }
        .star-rating input[type="radio"]:checked ~ label,
        .star-rating label:hover,
        .star-rating label:hover ~ label {
            color: #ffd600;
        }
        /* Animación para productos recomendados */
        .reco-animate {
            opacity: 0;
            transform: translateY(30px) scale(0.97);
            animation: recoFadeIn 0.7s cubic-bezier(.4,0,.2,1) forwards;
        }
        @keyframes recoFadeIn {
            to {
                opacity: 1;
                transform: none;
            }
        }
        .reco-stars {
            margin-bottom: 6px;
        }
    </style>
</head>
<body>
    <?php include '../navbar.php'; ?>
    <div style="height:70px"></div>

    <div class="container mt-5">
        <div class="row">
            <!-- Imagen del producto -->
            <div class="col-md-6 d-flex align-items-center justify-content-center">
                <img src="<?php echo $rutaImgProducto; ?>" class="img-fluid rounded shadow"
                     alt="<?php echo htmlspecialchars($producto['nombre']); ?>" style="max-height: 400px;">
            </div>

            <!-- Detalles del producto -->
            <div class="col-md-6">
                <div class="product-details">
                    <h2><?php echo htmlspecialchars($producto['nombre']); ?></h2>
                    <p><strong>Descripción:</strong> <?php echo htmlspecialchars($producto['descripcion']); ?></p>
                    <p><strong>Precio:</strong> $<?php echo number_format($producto['precio_unitario']); ?> 
                    / <?php echo htmlspecialchars($producto['unidad'] ?? ''); ?></p>
                    <p><strong>Fecha de publicación:</strong> <?php echo htmlspecialchars($producto['fecha_publicacion']); ?></p>
                    <hr>
                    <div class="d-flex align-items-center mb-3">
                        <img src="<?php echo $rutaImgUsuario; ?>" alt="Foto del agricultor"
                             style="width:50px; height:50px; object-fit:cover; border-radius:50%; margin-right:15px;">
                        <div>
                            <span class="fw-bold"><?php echo htmlspecialchars($producto['agricultor']); ?></span><br>
                            <small class="text-muted"><i class="bi bi-telephone"></i>
                                <?php echo htmlspecialchars($producto['telefono']); ?></small>
                        </div>
                    </div>

                    <!-- <form action="../controller/procesar_compra.php" method="POST" class="mt-3">
                        <input type="hidden" name="id_producto" value="php echo $producto['id_producto']; ?>">
                        <button type="submit" class="btn btn-outline-success w-100 mb-3">Comprar Ahora</button>
                    </form> -->
                    <form action="../controller/carritocontroller.php" method="POST">
                        <input type="hidden" name="id_producto" value="<?php echo $producto['id_producto']; ?>">
                        <button type="submit" class="btn btn-success w-100">Añadir al Carrito</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Reseñas del producto -->
        <div class="container mt-4">
            <h5>Reseñas del producto</h5>
            <?php
            $ultimasResenas = array_slice($resenas, 0, 3);
            foreach ($ultimasResenas as $resena): ?>
                <div class="resena-card">
                    <strong><?= htmlspecialchars($resena['nombre_completo']) ?></strong>
                    <span>
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="bi bi-star<?= $i <= $resena['estrellas'] ? '-fill text-warning' : '' ?>"></i>
                        <?php endfor; ?>
                    </span>
                    <small class="text-muted"><?= $resena['fecha'] ?></small>
                    <p><?= htmlspecialchars($resena['comentario']) ?></p>
                </div>
            <?php endforeach; ?>

            <?php if (count($resenas) > 3): ?>
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalResenas">
                    Ver todas las reseñas
                </button>
            <?php endif; ?>

            <hr>
            <h5>Deja tu reseña</h5>
            <form method="POST">
                <div class="mb-2">
                    <label class="form-label d-block mb-1">Puntuación:</label>
                    <div class="star-rating">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <input type="radio" id="estrella<?= $i ?>" name="estrellas" value="<?= $i ?>" required>
                            <label for="estrella<?= $i ?>"><i class="bi bi-star-fill"></i></label>
                        <?php endfor; ?>
                    </div>
                </div>
                <div class="mb-2">
                    <label class="form-label">Comentario:</label>
                    <textarea name="comentario" class="form-control" rows="2" maxlength="250" required></textarea>
                </div>
                <button type="submit" class="btn btn-success">Enviar reseña</button>
            </form>
        </div>

        <!-- Modal para todas las reseñas -->
        <div class="modal fade" id="modalResenas" tabindex="-1" aria-labelledby="modalResenasLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="modalResenasLabel">Todas las reseñas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
              </div>
              <div class="modal-body">
                <?php foreach ($resenas as $resena): ?>
                    <div class="resena-card">
                        <strong><?= htmlspecialchars($resena['nombre_completo']) ?></strong>
                        <span>
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="bi bi-star<?= $i <= $resena['estrellas'] ? '-fill text-warning' : '' ?>"></i>
                            <?php endfor; ?>
                        </span>
                        <small class="text-muted"><?= $resena['fecha'] ?></small>
                        <p><?= htmlspecialchars($resena['comentario']) ?></p>
                    </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        </div>

        <!-- Productos recomendados -->
        <div class="container mt-5">
            <h4 class="mb-4 text-center">Productos recomendados</h4>
            <div class="row">
                <?php
                $stmtRecomendados = $pdo->prepare("SELECT id_producto, nombre, foto, precio_unitario FROM productos WHERE id_producto != ? ORDER BY RAND() LIMIT 3");
                $stmtRecomendados->execute([$producto['id_producto']]);
                $recomendados = $stmtRecomendados->fetchAll(PDO::FETCH_ASSOC);

                // Obtener promedios de estrellas para recomendados
                $idsReco = array_column($recomendados, 'id_producto');
                $promediosReco = [];
                if ($idsReco) {
                    $in = implode(',', array_map('intval', $idsReco));
                    $stmtProm = $pdo->query("SELECT id_producto, AVG(estrellas) as promedio FROM producto_resenas WHERE id_producto IN ($in) GROUP BY id_producto");
                    while ($row = $stmtProm->fetch(PDO::FETCH_ASSOC)) {
                        $promediosReco[$row['id_producto']] = $row['promedio'];
                    }
                }

                $delay = 0.1;
                foreach ($recomendados as $idx => $reco):
                    $rutaReco = "../img/" . $reco['foto'];
                    if (empty($reco['foto']) || !is_file(__DIR__ . "/../img/" . $reco['foto'])) {
                        $rutaReco = "../img/default.png";
                    }
                    $promedio = isset($promediosReco[$reco['id_producto']]) ? $promediosReco[$reco['id_producto']] : 0;
                ?>
                    <div class="col-md-4 mb-4">
                        <a href="producto_detalle.php?id_producto=<?php echo $reco['id_producto']; ?>"
                           style="text-decoration:none; color:inherit;">
                            <div class="card h-100 shadow-sm reco-animate" style="animation-delay: <?= $delay * $idx ?>s;">
                                <img src="<?php echo $rutaReco; ?>" class="card-img-top"
                                     style="height:200px; object-fit:cover;"
                                     alt="<?php echo htmlspecialchars($reco['nombre']); ?>">
                                <div class="card-body text-center">
                                    <div class="reco-stars">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="bi bi-star<?= $i <= round($promedio) ? '-fill text-warning' : '' ?>"></i>
                                        <?php endfor; ?>
                                        <?php if ($promedio > 0): ?>
                                            <span class="text-muted small">(<?= number_format($promedio, 2) ?>)</span>
                                        <?php endif; ?>
                                    </div>
                                    <h5 class="card-title"><?php echo htmlspecialchars($reco['nombre']); ?></h5>
                                    <p class="card-text text-success fw-bold">
                                        $<?php echo number_format($reco['precio_unitario']); ?></p>
                                    <a href="producto_detalle.php?id_producto=<?php echo $reco['id_producto']; ?>"
                                       class="btn btn-outline-primary btn-sm">Ver Detalles</a>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="container mt-4">
            <h4>Calificación del Agricultor:
                <?php
                $prom = $calificacion['promedio'] ?? 0;
                for ($i = 1; $i <= 5; $i++) {
                    echo '<i class="bi bi-star' . ($i <= round($prom) ? '-fill text-warning' : '') . '"></i>';
                }
                echo " (" . number_format($prom, 2) . " / 5, " . ($calificacion['total'] ?? 0) . " votos)";
                ?>
            </h4>
            <hr>
        </div>

        <footer class="bg-light text-center py-3 mt-5">
            <p class="mb-0">&copy; 2025 Plaza Móvil. Todos los derechos reservados.</p>
        </footer>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>