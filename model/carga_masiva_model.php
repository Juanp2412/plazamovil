<?php
function procesarCSV($archivoTemp, $pdo, $id_agricultor, &$debug_info) {
    $productos = [];
    $fila = 0;
    $debug_info .= "Abriendo archivo CSV...<br>";
    if (($handle = fopen($archivoTemp, "r")) !== FALSE) {
        $debug_info .= "Archivo abierto correctamente<br>";
        // Leer encabezados
        $encabezados = fgetcsv($handle, 1000, ",");
        $debug_info .= "Encabezados: " . implode(" | ", $encabezados) . "<br>";
        $debug_info .= "Número de columnas: " . count($encabezados) . "<br>";
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $fila++;
            // Saltar filas vacías
            if (count($data) == 1 && empty(trim($data[0]))) {
                continue;
            }
            $debug_info .= "Fila $fila: " . implode(" | ", $data) . "<br>";
            $debug_info .= "Columnas en fila $fila: " . count($data) . "<br>";
            // Limpiar datos
            $data = array_map('trim', $data);
            $data = array_filter($data, function($value) { 
                return $value !== '' && $value !== null; 
            });
            $debug_info .= "Datos limpios: " . implode(" | ", $data) . "<br>";
            // Solo necesitamos 5 columnas mínimas, la 6ta (Unidad) es opcional
            if (count($data) >= 5) {
                $producto = validarFilaProducto($data, $fila, $pdo, $id_agricultor, $debug_info);
                if ($producto) {
                    $productos[] = $producto;
                    $debug_info .= "✅ Producto válido: " . $producto['nombre'] . "<br>";
                } else {
                    $debug_info .= "❌ Producto inválido<br>";
                }
            } else {
                $debug_info .= "❌ Fila $fila: Solo tiene " . count($data) . " columnas, se necesitan al menos 5<br>";
            }
        }
        fclose($handle);
    }
    return $productos;
}

function validarFilaProducto($data, $fila, $pdo, $id_agricultor, &$debug_info) {
    if (count($data) < 5) {
        $debug_info .= "❌ Fila $fila: Solo tiene " . count($data) . " columnas<br>";
        return null;
    }
    $producto = [
        'nombre' => trim($data[0]),
        'descripcion' => trim($data[1]),
        'id_categoria' => obtenerIdCategoria(trim($data[2]), $pdo, $debug_info),
        'precio_unitario' => floatval(str_replace(['$', ','], '', $data[3])),
        'stock' => intval($data[4]),
        // El campo unidad es opcional (columna 6)
        'unidad' => isset($data[5]) ? trim($data[5]) : 'Unidad'
    ];
    $debug_info .= "📊 Producto - Nombre: '{$producto['nombre']}', Precio: {$producto['precio_unitario']}, Stock: {$producto['stock']}, Categoría: {$data[2]}<br>";
    // Validaciones básicas
    if (empty($producto['nombre'])) {
        $debug_info .= "❌ Nombre vacío<br>";
        return null;
    }
    if ($producto['precio_unitario'] <= 0) {
        $debug_info .= "❌ Precio inválido: " . $producto['precio_unitario'] . "<br>";
        return null;
    }
    if ($producto['stock'] < 0) {
        $debug_info .= "❌ Stock inválido: " . $producto['stock'] . "<br>";
        return null;
    }
    if (!$producto['id_categoria']) {
        $debug_info .= "❌ Categoría no encontrada: '" . $data[2] . "'<br>";
        return null;
    }
    $debug_info .= "✅ Producto válido<br>";
    return $producto;
}

function obtenerIdCategoria($nombreCategoria, $pdo, &$debug_info) {
    if (empty($nombreCategoria)) {
        $debug_info .= "⚠️ Nombre de categoría vacío<br>";
        return null;
    }
    $debug_info .= "🔍 Buscando categoría: '$nombreCategoria'<br>";
    $stmt = $pdo->prepare("SELECT id_categoria, nombre FROM categoria WHERE nombre = ?");
    $stmt->execute([$nombreCategoria]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        $debug_info .= "✅ Categoría encontrada: {$result['nombre']} (ID: {$result['id_categoria']})<br>";
    } else {
        $debug_info .= "❌ Categoría NO encontrada: '$nombreCategoria'<br>";
        // Mostrar categorías disponibles
        $stmt = $pdo->query("SELECT id_categoria, nombre FROM categoria ORDER BY nombre");
        $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $debug_info .= "📋 Categorías disponibles: ";
        foreach ($categorias as $cat) {
            $debug_info .= "{$cat['nombre']} (ID:{$cat['id_categoria']}), ";
        }
        $debug_info .= "<br>";
    }
    return $result ? $result['id_categoria'] : null;
}

function insertarProductos($productos, $pdo, $id_agricultor) {
    $resultados = [
        'insertados' => 0,
        'actualizados' => 0,
        'errores' => 0
    ];
    foreach ($productos as $producto) {
        try {
            // Verificar si el producto ya existe
            $sql = "SELECT id_producto FROM productos WHERE nombre = '".$producto['nombre']."' AND id_agricultor =  '".$id_agricultor."'";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $existente = $stmt->fetch(PDO::FETCH_ASSOC);




            if ($existente) {
                // Actualizar producto existente
                $stmt = $pdo->prepare("
                    UPDATE productos SET 
                        descripcion = ?, id_categoria = ?, precio_unitario = ?, 
                        stock = stock + ?, fecha_publicacion = NOW()
                    WHERE id_producto = ?
                ");
                $stmt->execute([
                    $producto['descripcion'],
                    $producto['id_categoria'],
                    $producto['precio_unitario'],
                    $producto['stock'],
                    $existente['id_producto']
                ]);
                $resultados['actualizados']++;
            } else {
                $sqlInsert = "INSERT INTO productos (
                        id_agricultor, id_categoria, descripcion, nombre, 
                        stock, precio_unitario, fecha_publicacion
                    ) VALUES (
                        '".$id_agricultor."',
                        '".$producto['id_categoria']."',
                        '".$producto['descripcion']."',
                        '".$producto['nombre']."',
                        '".$producto['stock']."',
                        '".$producto['precio_unitario']."',
                        NOW()
                    )";

                $pdo->exec($sqlInsert);
                $resultados['insertados']++;
            }
        } catch (Exception $e) {
            error_log("Error insertando producto '{$producto['nombre']}': " . $e->getMessage());
            $resultados['errores']++;
        }
    }
    return $resultados;
}

?>
