<?php
// index.php (corregido)

// Iniciar sesión solo si aún no existe
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cargar conexión a la base de datos ANTES de usar $pdo
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/config/paths.php';

// Verificar login (si esto debe estar aquí)
$id_rol = $_SESSION['user_id_rol'] ?? null;
if (!isset($_SESSION['user_id_rol'])) {
    header("Location: view/login.php");
    exit;
}

// Leer id_categoria de GET y asegurarse de que sea entero
$id_categoria = isset($_GET['id_categoria']) ? (int) $_GET['id_categoria'] : null;

// Parámetros de búsqueda
$busqueda = $_GET['busqueda'] ?? '';
$categoria_filtro = $_GET['categoria_filtro'] ?? '';
$precio_min = $_GET['precio_min'] ?? '';
$precio_max = $_GET['precio_max'] ?? '';

try {
    // Construir consulta base con filtros
    $sql = "SELECT p.*, u.nombre AS unidad, c.nombre AS categoria_nombre
            FROM productos p
            LEFT JOIN unidades_de_medida u ON p.id_unidad = u.id_unidad
            LEFT JOIN categoria c ON p.id_categoria = c.id_categoria
            WHERE 1=1";
    
    $params = [];
    
    // Filtro por categoría específica (si se selecciona desde el menú)
    if ($id_categoria) {
        $sql .= " AND p.id_categoria = ?";
        $params[] = $id_categoria;
        
        // Traer el nombre de la categoría seleccionada
        $catStmt = $pdo->prepare("SELECT nombre FROM categoria WHERE id_categoria = ?");
        $catStmt->execute([$id_categoria]);
        $categoriaSeleccionada = $catStmt->fetchColumn();
    }
    
    // Filtro de búsqueda por texto
    if (!empty($busqueda)) {
        $sql .= " AND (p.nombre LIKE ? OR p.descripcion LIKE ?)";
        $params[] = "%$busqueda%";
        $params[] = "%$busqueda%";
    }
    
    // Filtro por categoría desde el filtro de búsqueda
    if (!empty($categoria_filtro)) {
        $sql .= " AND p.id_categoria = ?";
        $params[] = $categoria_filtro;
    }
    
    // Filtro por precio mínimo
    if (!empty($precio_min)) {
        $sql .= " AND p.precio_unitario >= ?";
        $params[] = $precio_min;
    }
    
    // Filtro por precio máximo
    if (!empty($precio_max)) {
        $sql .= " AND p.precio_unitario <= ?";
        $params[] = $precio_max;
    }
    
    $sql .= " ORDER BY p.fecha_publicacion DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

} catch (PDOException $e) {
    // Manejo sencillo del error: log y mostrar mensaje mínimo
    error_log("Error BD en index.php: " . $e->getMessage());
    die("Error al cargar los productos. Revisa el log del servidor.");
}

// Consulta para obtener el promedio de estrellas de cada producto
$promedios = [];
$promStmt = $pdo->query("SELECT id_producto, AVG(estrellas) as promedio FROM producto_resenas GROUP BY id_producto");
while ($row = $promStmt->fetch(PDO::FETCH_ASSOC)) {
    $promedios[$row['id_producto']] = $row['promedio'];
}

// Obtener categorías para el filtro
$categoriasFiltro = $pdo->query("SELECT id_categoria, nombre FROM categoria ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Menu -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagina Principal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .producto-animate {
            opacity: 0;
            transform: translateY(30px) scale(0.97);
            animation: productoFadeIn 0.7s cubic-bezier(.4,0,.2,1) forwards;
        }
        @keyframes productoFadeIn {
            to {
                opacity: 1;
                transform: none;
            }
        }
        .search-box {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }
        .filter-section {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .results-count {
            background: #198754;
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <?php include 'navbar.php'; ?>

    <!-- Sección de bienvenida -->
    <section class="container welcome-section mt-4 mb-0">
        <span class="welcome-icon"><i class="bi bi-shop-window"></i></span>
        <div>
             <div class="welcome-title">Bienvenido a Plaza Móvil</div>
            <div class="welcome-title"></div>
            <div class="welcome-desc">
                Descubre los mejores productos frescos del campo, frutas y verduras seleccionadas para ti.<br>
                Explora una inumerable cantidad de productos agricolas, fresco y de buena calidad.
                <br>
                <br>
            </div>
        </div>
    </section>

    <!-- Carrousel -->
    <div id="carouselExampleCaptions" class="carousel slide mb-4 custom-carousel" data-bs-ride="carousel"
        data-bs-interval="3000">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active"
                aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1"
                aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2"
                aria-label="Slide 3"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="img/carousel1.jpg" class="d-block w-100 h-100" style="object-fit: cover;" alt="...">
                <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded p-2">
                    <h5>LAS MEJORES VERDURAS</h5>
                    <p>Encuentra aqui, las mejores verduras del mercado.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="img/carousel2.jpg" class="d-block w-100 h-100" style="object-fit: cover;" alt="...">
                <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded p-2">
                    <h5>LO MEJOR DEL CAMPO</h5>
                    <p>Solo los mejores productos para nuestros usuarios.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="img/carousel3.jpg" class="d-block w-100 h-100" style="object-fit: cover;" alt="...">
                <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded p-2">
                    <h5>LAS FRUTAS MAS FRESCAS</h5>
                    <p>Mira las ultimas publicaciones en frutas.</p>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions"
            data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions"
            data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <hr class="section-divider">

    <!-- Barra de búsqueda y filtros -->
    <section class="container mt-5">
        <div class="search-box">
            <h3 class="text-center mb-4 fw-bold">
                <i class="bi bi-search me-2"></i>Buscar Productos
            </h3>
            
            <form method="GET" class="row g-3">
                <div class="col-12 position-relative">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" class="form-control ps-5" name="busqueda" placeholder="Buscar productos por nombre o descripción..." 
                           value="<?php echo htmlspecialchars($busqueda); ?>">
                </div>
                
                <div class="col-md-4">
                    <label class="form-label fw-semibold"><i class="bi bi-tag me-1"></i>Categoría</label>
                    <select class="form-select" name="categoria_filtro">
                        <option value="">Todas las categorías</option>
                        <?php foreach ($categoriasFiltro as $cat): ?>
                            <option value="<?php echo $cat['id_categoria']; ?>" 
                                <?php echo ($categoria_filtro == $cat['id_categoria']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label fw-semibold"><i class="bi bi-currency-dollar me-1"></i>Precio Mínimo</label>
                    <input type="number" class="form-control" name="precio_min" placeholder="0" 
                           value="<?php echo htmlspecialchars($precio_min); ?>" min="0" step="0.01">
                </div>
                
                <div class="col-md-4">
                    <label class="form-label fw-semibold"><i class="bi bi-currency-dollar me-1"></i>Precio Máximo</label>
                    <input type="number" class="form-control" name="precio_max" placeholder="100000" 
                           value="<?php echo htmlspecialchars($precio_max); ?>" min="0" step="0.01">
                </div>
                
                <div class="col-12 text-center">
                    <button type="submit" class="btn btn-success btn-lg me-2">
                        <i class="bi bi-search me-1"></i>Buscar
                    </button>
                    <a href="index.php" class="btn btn-outline-secondary btn-lg">
                        <i class="bi bi-arrow-clockwise me-1"></i>Limpiar
                    </a>
                </div>
            </form>
            
            <?php if ($busqueda || $categoria_filtro || $precio_min || $precio_max): ?>
                <div class="filter-section mt-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0"><i class="bi bi-funnel me-1"></i>Filtros aplicados:</h6>
                            <small class="text-muted">
                                <?php
                                $filtros = [];
                                if ($busqueda) $filtros[] = "Búsqueda: \"$busqueda\"";
                                if ($categoria_filtro) {
                                    $catNombre = $categoriasFiltro[array_search($categoria_filtro, array_column($categoriasFiltro, 'id_categoria'))]['nombre'];
                                    $filtros[] = "Categoría: $catNombre";
                                }
                                if ($precio_min) $filtros[] = "Precio mínimo: $$precio_min";
                                if ($precio_max) $filtros[] = "Precio máximo: $$precio_max";
                                echo implode(', ', $filtros);
                                ?>
                            </small>
                        </div>
                        <span class="results-count">
                            <i class="bi bi-grid-3x3-gap me-1"></i>
                            <?php echo $stmt->rowCount(); ?> productos
                        </span>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Apartado de productos -->
    <section class="container mt-5 productos-fondo">
        <h2 class="text-center mb-4 fw-bold display-6 border-bottom pb-2" style="letter-spacing:1px;">
            <i class="bi bi-box-seam text-success me-2"></i>
            <?php echo ($id_categoria && isset($categoriaSeleccionada)) ? "Productos de $categoriaSeleccionada" : "Productos Publicados"; ?>
        </h2>
        
        <?php if ($stmt->rowCount() > 0): ?>
            <div class="row g-4 justify-content-center">
                <?php
                $delay = 0.3;
                $idx = 0;
                while ($producto = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $promedio = isset($promedios[$producto['id_producto']]) ? $promedios[$producto['id_producto']] : 0;
                    ?>
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3 d-flex align-items-stretch">
                        <a href="view/producto_detalle.php?id_producto=<?php echo $producto['id_producto']; ?>"
                            class="w-100 text-decoration-none text-dark">
                            <div class="card h-100 border-0 shadow-sm minimal-card producto-animate"
                                 style="animation-delay: <?= $delay * $idx ?>s;">
                                <img src="img/<?php echo htmlspecialchars($producto['foto']); ?>"
                                    class="card-img-top rounded-top" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                                <div class="card-body d-flex flex-column justify-content-between">
                                    <h5 class="card-title mb-2 fw-semibold text-truncate">
                                        <?php echo htmlspecialchars($producto['nombre']); ?>
                                    </h5>
                                    <!-- Mostrar estrellas -->
                                    <div class="mb-2">
                                        <?php
                                        for ($i = 1; $i <= 5; $i++) {
                                            echo '<i class="bi bi-star' . ($i <= round($promedio) ? '-fill text-warning' : '') . '"></i>';
                                        }
                                        if ($promedio > 0) {
                                            echo ' <span class="text-muted small">(' . number_format($promedio, 2) . ')</span>';
                                        }
                                        ?>
                                    </div>
                                    <p class="card-text small text-muted mb-2" style="min-height:48px;">
                                        <?php echo htmlspecialchars($producto['descripcion']); ?>
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold text-success">
                                            $<?php echo number_format($producto['precio_unitario']); ?> 
                                            / <?php echo htmlspecialchars($producto['unidad']); ?>
                                        </span>
                                        <small class="text-muted">
                                            <i class="bi bi-tag"></i> <?php echo htmlspecialchars($producto['categoria_nombre']); ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php $idx++; } ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-search display-1 text-muted"></i>
                <h4 class="text-muted mt-3">No se encontraron productos</h4>
                <p class="text-muted">Intenta ajustar los filtros de búsqueda</p>
                <a href="index.php" class="btn btn-success">Ver todos los productos</a>
            </div>
        <?php endif; ?>
    </section>

    <hr class="section-divider">

    <!-- Apartado de productos por categoría (solo si no hay búsqueda activa) -->
    <?php if (!$busqueda && !$categoria_filtro && !$precio_min && !$precio_max): ?>
    <section class="container mt-5">
        <h2 class="text-center mb-4 fw-bold display-6 border-bottom pb-2" style="letter-spacing:1px;">
            <i class="bi bi-tags text-success me-2"></i>Productos por Categoría
        </h2>
        <?php
        // Consulta para obtener las categorías desde la tabla `categoria`
        $categoriasStmt = $pdo->query("SELECT id_categoria, nombre FROM categoria ORDER BY nombre ASC");
        $categorias = $categoriasStmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($categorias as $categoria) {
            $categoriaNombre = htmlspecialchars($categoria['nombre']);
            $categoriaId = $categoria['id_categoria'];
            ?>
            <div class="mb-5">
                <h3 class="text-success border-start border-4 ps-3 mb-4" style="font-weight:600; letter-spacing:0.5px;">
                    <i class="bi bi-tag-fill me-2"></i><?php echo $categoriaNombre; ?>
                </h3>
                <div class="row g-4 justify-content-center">
                    <?php
                    // Consulta para obtener los productos de la categoría actual
                    $productosStmt = $pdo->prepare("SELECT p.*, u.nombre AS unidad
                        FROM productos p
                        LEFT JOIN unidades_de_medida u ON p.id_unidad = u.id_unidad
                        WHERE p.id_categoria = ?
                        ORDER BY p.fecha_publicacion DESC
                    ");
                    $productosStmt->execute([$categoriaId]);
                    $delay = 0.1;
                    $idx = 0;
                    while ($producto = $productosStmt->fetch(PDO::FETCH_ASSOC)) {
                        $promedio = isset($promedios[$producto['id_producto']]) ? $promedios[$producto['id_producto']] : 0;
                        ?>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3 d-flex align-items-stretch">
                            <a href="view/producto_detalle.php?id_producto=<?php echo $producto['id_producto']; ?>"
                                class="w-100 text-decoration-none text-dark">
                                <div class="card h-100 border-0 shadow-sm minimal-card producto-animate"
                                     style="animation-delay: <?= $delay * $idx ?>s;">
                                    <img src="img/<?php echo htmlspecialchars($producto['foto']); ?>"
                                        class="card-img-top rounded-top"
                                        alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                                    <div class="card-body d-flex flex-column justify-content-between">
                                        <h5 class="card-title mb-2 fw-semibold text-truncate">
                                            <?php echo htmlspecialchars($producto['nombre']); ?></h5>
                                        <!-- Mostrar estrellas -->
                                        <div class="mb-2">
                                            <?php
                                            for ($i = 1; $i <= 5; $i++) {
                                                echo '<i class="bi bi-star' . ($i <= round($promedio) ? '-fill text-warning' : '') . '"></i>';
                                            }
                                            if ($promedio > 0) {
                                                echo ' <span class="text-muted small">(' . number_format($promedio, 2) . ')</span>';
                                            }
                                            ?>
                                        </div>
                                        <p class="card-text small text-muted mb-2" style="min-height:48px;">
                                            <?php echo htmlspecialchars($producto['descripcion']); ?>
                                        </p>
                                        <p class="card-text mb-0"><span
                                                class="fw-bold text-success">$<?php echo number_format($producto['precio_unitario']); ?> 
                                                 / <?php echo htmlspecialchars($producto['unidad']); ?></span>
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php $idx++; } ?>
                </div>
                <hr class="categoria-separator">
            </div>
        <?php } ?>
    </section>
    <?php endif; ?>

    </div>
    <?php include 'config/conexion.php'; ?>
    <div class="container mt-5"></div>

    <footer class="text-center py-3">
        <p class="mb-0">&copy; 2025 Plaza Móvil. Todos los derechos reservados.</p>
    </footer>

    <script>
        // Función para limpiar filtros individuales
        function limpiarFiltro(tipo) {
            const url = new URL(window.location.href);
            url.searchParams.delete(tipo);
            window.location.href = url.toString();
        }
    </script>
</body>
</html>