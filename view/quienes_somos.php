<?php
require_once __DIR__ . '/../config/paths.php';
// quienes_somos.php
session_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¿Quiénes Somos? - Plaza Móvil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/styles.css">
    

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }

        h1,
        h2 {
            font-weight: 700;
        }

        h1 {
            color: #198754;
            /* Verde corporativo Bootstrap */
        }

        .section-title {
            margin-bottom: 1rem;
            color: #198754;
        }

        .custom-section {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            padding: 2rem;
            margin-bottom: 3rem;
            transition: transform 0.3s ease;
        }

        .custom-section:hover {
            transform: translateY(-5px);
        }

        img {
            border-radius: 15px;
        }

        footer {
            background: #198754;
            color: white;
        }

        footer p {
            margin: 0;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <?php include __DIR__. '/../navbar.php'; ?>

    <!-- Espacio para no tapar contenido -->
    <div style="height:70px;"></div>

    <div class="container mt-5">
        <!-- Sección de título -->
        <div class="text-center mb-5">
            <h1 class="fw-bold">¿Quiénes Somos?</h1>
            <p class="text-muted">Conectamos agricultores y compradores para un comercio justo y directo.</p>
        </div>

        <!-- Misión -->
        <div class="row align-items-center custom-section">
            <div class="col-md-6 mb-3 mb-md-0">
                <img src="../img/mision.jpg" class="img-fluid shadow-sm" alt="Misión Plaza Móvil">
            </div>
            <div class="col-md-6">
                <h2 class="section-title">Nuestra Misión</h2>
                <p>
                    En <strong>Plaza Móvil</strong>, nuestra misión es crear un puente directo entre agricultores
                    locales y compradores,
                    promoviendo el comercio justo, el consumo de productos frescos y el desarrollo de comunidades
                    rurales.
                    Eliminamos intermediarios, empoderamos a los productores y ofrecemos precios accesibles a los
                    clientes.
                </p>
            </div>
        </div>

        <!-- Visión -->
        <div class="row align-items-center flex-md-row-reverse custom-section">
            <div class="col-md-6 mb-3 mb-md-0">
                <img src="../img/vision.jpg" class="img-fluid shadow-sm" alt="Visión Plaza Móvil">
            </div>
            <div class="col-md-6">
                <h2 class="section-title">Nuestra Visión</h2>
                <p>
                    Aspiramos a ser la plataforma líder en Latinoamérica en la digitalización de mercados agrícolas,
                    facilitando el acceso a productos frescos y saludables mientras apoyamos a los pequeños y medianos
                    agricultores.
                    Queremos construir un ecosistema donde tecnología, sostenibilidad y comercio justo trabajen de la
                    mano.
                </p>
            </div>
        </div>

        <!-- Problemática -->
        <div class="row align-items-center custom-section">
            <div class="col-md-6 mb-3 mb-md-0">
                <img src="../img/problematica.jpg" class="img-fluid shadow-sm" alt="Problemática del mercado agrícola">
            </div>
            <div class="col-md-6">
                <h2 class="section-title text-danger">La Problemática</h2>
                <p>
                    Los agricultores locales enfrentan grandes dificultades: bajos precios de venta por intermediación,
                    falta de canales de distribución directa y desconocimiento de herramientas digitales para
                    promocionar sus productos.
                    Al mismo tiempo, los consumidores pagan altos costos y tienen poco acceso a alimentos frescos y
                    locales.
                </p>
            </div>
        </div>

        <!-- Soluciones -->
        <div class="row align-items-center flex-md-row-reverse custom-section">
            <div class="col-md-6 mb-3 mb-md-0">
                <img src="../img/soluciones.jpg" class="img-fluid shadow-sm" alt="Soluciones Plaza Móvil">
            </div>
            <div class="col-md-6">
                <h2 class="section-title text-warning">Nuestras Soluciones</h2>
                <ul class="mt-3">
                    <li><strong>Plataforma en línea:</strong> Conecta agricultores y compradores de manera fácil y
                        rápida.</li>
                    <li><strong>Precios justos:</strong> Eliminamos intermediarios para garantizar ganancias justas y
                        productos más económicos.</li>
                    <li><strong>Promoción digital:</strong> Ayudamos a los agricultores a exhibir sus productos de forma
                        profesional.</li>
                    <li><strong>Acceso a productos frescos:</strong> Ofrecemos alimentos locales, frescos y de alta
                        calidad.</li>
                    <li><strong>Comunidad sostenible:</strong> Fomentamos el consumo responsable y el apoyo a
                        productores locales.</li>
                </ul>
            </div>
        </div>
    </div>

    <footer class="text-center py-4 mt-5">
        <p>&copy; 2025 Plaza Móvil. Todos los derechos reservados.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>