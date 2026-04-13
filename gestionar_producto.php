<?php
require_once 'db_connection.php';

// Capa 1: Validación Asíncrona (endpoint para AJAX)
if (isset($_GET['check_lote'])) {
    $lote = (int)$_GET['check_lote'];
    $exclude_id = isset($_GET['exclude_id']) ? (int)$_GET['exclude_id'] : 0;
    
    $query = "SELECT id_producto FROM productos WHERE id_lote = $lote AND id_producto != $exclude_id";
    $result = $conexion->query($query);
    
    echo json_encode(['exists' => ($result && $result->num_rows > 0)]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    
    // Capa 2: Sanitización de datos para evitar Inyección SQL
    $nombre = $conexion->real_escape_string($_POST['nombre_producto']);
    $codigo_barra = $conexion->real_escape_string($_POST['codigo_barra']);
    $id_lote = (int)$_POST['id_lote'];
    $tipo_producto = $conexion->real_escape_string($_POST['tipo_producto']);
    $ciudad = $conexion->real_escape_string($_POST['ciudad']);
    $num_estante = $conexion->real_escape_string($_POST['num_estante']);
    $cant_piezas = (int)$_POST['cant_inventario_piezas'];
    $cant_cajas = (int)$_POST['cant_cajas'];
    $piezas_por_caja = (int)$_POST['piezas_por_caja'];
    
    // Handle image upload
    $ruta_imagen = null;
    if (isset($_FILES['imagen_producto']) && $_FILES['imagen_producto']['error'] === UPLOAD_ERR_OK) {
        $nombre_archivo = time() . '_' . basename($_FILES['imagen_producto']['name']);
        $directorio = 'uploads/';
        $ruta_destino = $directorio . $nombre_archivo;
        
        if (move_uploaded_file($_FILES['imagen_producto']['tmp_name'], $ruta_destino)) {
            $ruta_imagen = $ruta_destino;
        }
    }

    if ($accion === 'insertar') {
        $sql = "INSERT INTO productos (codigo_barra, nombre_producto, ruta_imagen, id_lote, tipo_producto, ciudad, num_estante, cant_inventario_piezas, cant_cajas, piezas_por_caja) 
                VALUES ('$codigo_barra', '$nombre', '$ruta_imagen', $id_lote, '$tipo_producto', '$ciudad', '$num_estante', $cant_piezas, $cant_cajas, $piezas_por_caja)";
        
        if ($conexion->query($sql)) {
            header("Location: index.php?status=inserted");
        } else {
            echo "Error: " . $conexion->error;
        }
    } elseif ($accion === 'actualizar') {
        $id_producto = (int)$_POST['id_producto'];
        
        $sql = "UPDATE productos SET 
                codigo_barra = '$codigo_barra',
                nombre_producto = '$nombre',
                id_lote = $id_lote,
                tipo_producto = '$tipo_producto',
                ciudad = '$ciudad',
                num_estante = '$num_estante',
                cant_inventario_piezas = $cant_piezas,
                cant_cajas = $cant_cajas,
                piezas_por_caja = $piezas_por_caja";
        
        if ($ruta_imagen) {
            $sql .= ", ruta_imagen = '$ruta_imagen'";
        }
        
        $sql .= " WHERE id_producto = $id_producto";
        
        if ($conexion->query($sql)) {
            header("Location: index.php?status=updated");
        } else {
            echo "Error: " . $conexion->error;
        }
    }
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $sql = "DELETE FROM productos WHERE id_producto = $id";
    if ($conexion->query($sql)) {
        header("Location: index.php?status=deleted");
    } else {
        echo "Error: " . $conexion->error;
    }
}

$conexion->close();
?>
