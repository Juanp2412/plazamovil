<?php
// filepath: c:\xampp\htdocs\Plaza_Movil\controller\gestion_productos.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../model/gestion_prod.php';

$productosModel = new ProductosModel();

// Función para manejar subida de imágenes
function uploadImage($inputName) {
    if (isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] === UPLOAD_ERR_OK) {
        $imagen = uniqid() . "_" . basename($_FILES[$inputName]['name']);
        $ruta_destino = '../img/' . $imagen;

        if (move_uploaded_file($_FILES[$inputName]['tmp_name'], $ruta_destino)) {
            return $imagen;
        }
    }
    return null;
}

// Manejar solicitudes POST (Agregar, Actualizar, Eliminar)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {

    if ($_POST['accion'] === 'agregar') {
        $foto = uploadImage('foto');
        $productosModel->agregarProducto(
            $_POST['nombre'],
            $_POST['descripcion'],
            $_POST['precio_unitario'],
            $foto,
            $_SESSION['user_id_agricultor'],
            $_POST['stock'],
            $_POST['id_categoria'],
            $_POST['id_unidad'],
            date("Y-m-d H:i:s")
        );

    } elseif ($_POST['accion'] === 'actualizar') {
        $foto = uploadImage('foto');
        if (!$foto) {
            $foto = $_POST['foto_actual']; // conservar imagen anterior si no se sube nueva
        }

        $productosModel->actualizarProducto(
            $_POST['id_producto'],
            $_POST['nombre'],
            $_POST['descripcion'],
            $_POST['precio_unitario'],
            $foto,
            $_POST['stock'],
            $_POST['id_categoria'],
            $_POST['id_unidad'],
            date("Y-m-d H:i:s")
        );

    } elseif ($_POST['accion'] === 'eliminar') {
        $productosModel->eliminarProducto($_POST['id_producto']);
    }

    header("Location: ../view/gestion_productos.php");
    exit;

    
}

$productos = $productosModel->obtenerProductos();
// Obtener productos para la vista




