<?php
session_start();
if (!isset($_SESSION['user_id_usuario'])) {
    header('Location: login.php');
    exit();
}

require_once '../config/conexion.php';

$user_id_usuario = $_SESSION['user_id_usuario'];

// Traer datos del usuario + rol + agricultor + zona
$stmt = $pdo->prepare("
    SELECT u.*, r.nombre, a.id_agricultor, a.certificaciones, a.fotos, 
           a.metodo_entrega, a.metodos_de_pago, z.zona AS nombre_zona
    FROM usuarios u
    LEFT JOIN rol r ON u.id_rol = r.id_rol
    LEFT JOIN agricultor a ON u.id_usuario = a.id_usuario
    LEFT JOIN zona z ON a.id_zona = z.id_zona
    WHERE u.id_usuario  = ?
");
$stmt->execute([$user_id_usuario]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo '<div class="alert alert-danger">Usuario no encontrado.</div>';
    exit();
}

// Manejo de fotos de agricultor (si existen en la BD, separadas por coma)
$fotosTerreno = !empty($user['fotos']) ? explode(',', $user['fotos']) : [];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
        }
        .terreno-img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .terreno-img:hover {
            transform: scale(1.05);
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <?php include '../navbar.php'; ?>
    <div style="height:70px"></div>

    <div class="container mt-5">
        <div class="card p-4">
            <div class="text-center">
                <img src="<?php echo !empty($user['Foto']) ? '../img/' . htmlspecialchars($user['Foto']) : '../img/default_profile.png'; ?>"
                    alt="Foto de perfil" class="profile-img mb-3">
                <h2><?php echo htmlspecialchars($user['nombre_completo']); ?></h2>
                <p class="text-muted">Usuario: <?php echo htmlspecialchars($user['username']); ?></p>
                <p class="text-muted">Rol: <?php echo htmlspecialchars($user['nombre']); ?></p>
            </div>

            <!-- Datos generales -->
            <div class="mt-4">
                <h4>Información Personal</h4>
                <ul class="list-group">
                    <li class="list-group-item"><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></li>
                    <li class="list-group-item"><strong>Teléfono:</strong> <?php echo htmlspecialchars($user['telefono'] ?? 'No disponible'); ?></li>
                    <li class="list-group-item"><strong>Documento:</strong> <?php echo htmlspecialchars($user['tipo_documento']." ".$user['numero_documento']); ?></li>
                    <li class="list-group-item"><strong>Fecha de nacimiento:</strong> <?php echo htmlspecialchars($user['fecha_nacimiento'] ?? 'No disponible'); ?></li>
                </ul>
            </div>

            <!-- Si es agricultor -->
            <?php if ($user['id_rol'] == 3): ?>
            <div class="mt-4">
                <h4>Información de Agricultor</h4>
                <ul class="list-group">
                    <li class="list-group-item"><strong>Zona:</strong> <?php echo htmlspecialchars($user['nombre_zona'] ?? 'No asignada'); ?></li>
                    <li class="list-group-item"><strong>Certificaciones:</strong> <?php echo htmlspecialchars($user['certificaciones'] ?? 'Ninguna'); ?></li>
                    <li class="list-group-item"><strong>Método de Entrega:</strong> <?php echo htmlspecialchars($user['metodo_entrega'] ?? 'No especificado'); ?></li>
                    <li class="list-group-item"><strong>Métodos de Pago:</strong> <?php echo htmlspecialchars($user['metodos_de_pago'] ?? 'No especificado'); ?></li>
                </ul>
            </div>

            <!-- Fotos del terreno -->
            <div class="mt-4">
                <h4>Fotos del Terreno</h4>
                <?php if (!empty($fotosTerreno)): ?>
                    <div class="d-flex flex-wrap gap-3">
                        <?php foreach ($fotosTerreno as $foto): ?>
                            <img src="../uploads/<?php echo htmlspecialchars(trim($foto)); ?>" 
                                class="terreno-img" 
                                data-bs-toggle="modal" data-bs-target="#modalFoto" 
                                onclick="verFoto('../uploads/<?php echo htmlspecialchars(trim($foto)); ?>')">
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No hay fotos registradas del terreno.</p>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Botones -->
            <div class="mt-4 text-center d-flex justify-content-center gap-3">
                <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalEditarPerfil">
                    <i class="bi bi-pencil"></i> Editar Perfil
                </button>
                <a href="historial_pedidos.php" class="btn btn-primary">
                    <i class="bi bi-clock-history"></i> Historial de Pedidos
                </a>
                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalEliminarCuenta">
                    <i class="bi bi-trash"></i> Eliminar Cuenta
                </button>
            </div>
        </div>
    </div>

    <!-- Modal para editar perfil -->
    <!-- Modal para editar perfil -->
<div class="modal fade" id="modalEditarPerfil" tabindex="-1" aria-labelledby="modalEditarPerfilLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" id="formEditarPerfil" method="POST" action="../controller/editarperfilcontroller.php" enctype="multipart/form-data">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarPerfilLabel">Editar Perfil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="id_usuario" value="<?php echo htmlspecialchars($user['id_usuario']); ?>">

                <div class="row">
                    <div class="col-lg-8">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($user['nombre_completo']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="correo" class="form-label">Correo</label>
                            <input type="email" class="form-control" id="correo" name="correo" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="usuario" class="form-label">Usuario</label>
                            <input type="text" class="form-control" id="usuario" name="usuario" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo htmlspecialchars($user['telefono']); ?>">
                        </div>

                        <!-- Campos extra si es agricultor -->
                        <?php if ($user['id_rol'] == 3): ?>
                        <hr>
                        <h6>Datos de Agricultor</h6>

                        <div class="mb-3">
                            <label for="certificaciones" class="form-label">Certificaciones</label>
                            <textarea class="form-control" id="certificaciones" name="certificaciones"><?php echo htmlspecialchars($user['certificaciones']); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="metodo_entrega" class="form-label">Método de Entrega</label>
                            <input type="text" class="form-control" id="metodo_entrega" name="metodo_entrega" value="<?php echo htmlspecialchars($user['metodo_entrega']); ?>">
                        </div>

                        <div class="mb-3">
                            <label for="metodos_de_pago" class="form-label">Métodos de Pago</label>
                            <input type="text" class="form-control" id="metodos_de_pago" name="metodos_de_pago" value="<?php echo htmlspecialchars($user['metodos_de_pago']); ?>">
                        </div>

                        <!-- Fotos existentes del terreno (se pueden marcar para eliminar) -->
                        <div class="mb-3">
                            <label class="form-label">Fotos actuales del terreno</label>
                            <div class="d-flex flex-wrap gap-2" id="existingFotosContainer">
                                <?php foreach ($fotosTerreno as $foto): 
                                    $f = trim($foto);
                                    if ($f === '') continue;
                                ?>
                                <div class="position-relative foto-existente" style="width:110px;">
                                    <img src="../uploads/<?php echo htmlspecialchars($f); ?>" class="img-thumbnail w-100" alt="foto finca">
                                    <!-- boton eliminar (solo marcado visualmente) -->
                                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 remove-existing" data-filename="<?php echo htmlspecialchars($f); ?>" title="Marcar para eliminar">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                    <!-- input hidden para enviar que este archivo se mantiene -->
                                    <input type="hidden" name="existing_fotos[]" value="<?php echo htmlspecialchars($f); ?>">
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="form-text">Haz clic en la "x" de una foto para marcarla para eliminación.</div>
                        </div>

                        <!-- Subir nuevas fotos -->
                        <div class="mb-3">
                            <label for="nuevas_fotos" class="form-label">Agregar nuevas fotos del terreno</label>
                            <input type="file" id="nuevas_fotos" name="nuevas_fotos[]" class="form-control" accept="image/*" multiple>
                            <div class="mt-2 d-flex flex-wrap gap-2" id="previewNuevas"></div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="col-lg-4 text-center">
                        <label class="form-label d-block">Foto de Perfil</label>
                        <img id="previewProfile" src="<?php echo !empty($user['Foto']) ? '../img/' . htmlspecialchars($user['Foto']) : '../img/default_profile.png'; ?>" alt="preview perfil" class="img-fluid rounded-circle mb-2" style="width:150px;height:150px;object-fit:cover;border:4px solid #fff;box-shadow:0 6px 18px rgba(0,0,0,0.12);">
                        <div class="mb-3">
                            <input type="file" id="foto_perfil" name="foto_perfil" class="form-control" accept="image/*">
                            <div class="form-text">Max 2MB. Tipos: jpg, png, webp.</div>
                        </div>
                    </div>
                </div> <!-- row -->
            </div> <!-- modal-body -->

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<!-- JS para preview y manejo de fotos -->
<script>
    // Preview foto de perfil
    document.getElementById('foto_perfil').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function(ev) {
            document.getElementById('previewProfile').src = ev.target.result;
        }
        reader.readAsDataURL(file);
    });

    // Preview y manejo de nuevas fotos (multiples) con posibilidad de remover antes de enviar
    const inputNuevas = document.getElementById('nuevas_fotos');
    const previewNuevas = document.getElementById('previewNuevas');

    inputNuevas.addEventListener('change', function () {
        previewNuevas.innerHTML = '';
        const files = Array.from(inputNuevas.files);
        files.forEach((file, idx) => {
            const reader = new FileReader();
            reader.onload = function(ev) {
                const wrapper = document.createElement('div');
                wrapper.className = 'position-relative';
                wrapper.style.width = '110px';
                wrapper.innerHTML = `
                    <img src="${ev.target.result}" class="img-thumbnail" style="width:110px;height:80px;object-fit:cover;">
                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 remove-new" data-index="${idx}" title="Quitar"><i class="bi bi-x-lg"></i></button>
                `;
                previewNuevas.appendChild(wrapper);
            }
            reader.readAsDataURL(file);
        });
    });

    // Quitar una nueva foto (reconstruir FileList usando DataTransfer)
    previewNuevas.addEventListener('click', function(e) {
        if (!e.target.closest('.remove-new')) return;
        const btn = e.target.closest('.remove-new');
        const idx = parseInt(btn.getAttribute('data-index'));
        const dt = new DataTransfer();
        Array.from(inputNuevas.files).forEach((file, i) => { if (i !== idx) dt.items.add(file); });
        inputNuevas.files = dt.files;

        // volver a renderizar previews
        const evt = new Event('change');
        inputNuevas.dispatchEvent(evt);
    });

    // Manejo de eliminación de fotos existentes: alterna entre mantener/eliminar
    document.getElementById('existingFotosContainer')?.addEventListener('click', function(e) {
        const btn = e.target.closest('.remove-existing');
        if (!btn) return;
        const filename = btn.getAttribute('data-filename');
        const wrapper = btn.closest('.foto-existente');

        // si está marcado para borrar, lo desmarcamos (revert)
        if (wrapper.classList.contains('marked-for-delete')) {
            wrapper.classList.remove('marked-for-delete');
            wrapper.querySelector('.img-thumbnail').style.opacity = '1';
            // reponer hidden input keep (si no existe)
            if (!wrapper.querySelector('input[name="existing_fotos[]"]')) {
                const inpt = document.createElement('input');
                inpt.type = 'hidden';
                inpt.name = 'existing_fotos[]';
                inpt.value = filename;
                wrapper.appendChild(inpt);
            }
            // eliminar hidden delete si existe
            const del = wrapper.querySelector('input[name="delete_fotos[]"]');
            if (del) del.remove();
            btn.classList.remove('btn-outline-secondary');
            btn.classList.add('btn-danger');
        } else {
            // marcar para borrar
            wrapper.classList.add('marked-for-delete');
            wrapper.querySelector('.img-thumbnail').style.opacity = '0.35';
            // remover hidden keep
            const keep = wrapper.querySelector('input[name="existing_fotos[]"]');
            if (keep) keep.remove();
            // agregar hidden delete
            const inpt = document.createElement('input');
            inpt.type = 'hidden';
            inpt.name = 'delete_fotos[]';
            inpt.value = filename;
            wrapper.appendChild(inpt);
            btn.classList.remove('btn-danger');
            btn.classList.add('btn-outline-secondary');
        }
    });
</script>


    <!-- Modal para ver foto -->
    <div class="modal fade" id="modalFoto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <img id="fotoAmpliada" src="" class="w-100 rounded">
            </div>
        </div>
    </div>

    <!-- Modal eliminar cuenta -->
    <div class="modal fade" id="modalEliminarCuenta" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" action="../controller/eliminarcuentacontroller.php">
                <div class="modal-header">
                    <h5 class="modal-title">Eliminar Cuenta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p class="text-danger">¿Estás seguro de que deseas eliminar tu cuenta? Esta acción no se puede deshacer.</p>
                    <input type="hidden" name="id_usuario" value="<?php echo htmlspecialchars($user['id_usuario']); ?>">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Eliminar Cuenta</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function verFoto(src) {
            document.getElementById('fotoAmpliada').src = src;
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
