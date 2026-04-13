<?php
require_once 'db_connection.php';

$editing = false;
$product = [
    'id_producto' => '',
    'codigo_barra' => '',
    'codigo_qr' => '',
    'nombre_producto' => '',
    'id_lote' => '',
    'tipo_producto' => '',
    'ciudad' => '',
    'num_estante' => '',
    'cant_inventario_piezas' => 0,
    'cant_cajas' => 0,
    'piezas_por_caja' => 1
];

if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $result = $conexion->query("SELECT * FROM productos WHERE id_producto = $id");
    if ($result && $result->num_rows > 0) {
        $product = $result->fetch_assoc();
        $editing = true;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario Mis Útiles Escolares</title>
    <link rel="stylesheet" href="estilos.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>Mis Útiles Escolares</h1>
            <p>Sistema de Gestión de Inventario para tiendas Santiago y Valparaíso</p>
        </header>

        <div class="grid">
            <!-- Formulario Section -->
            <section class="card">
                <h2>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                    <?php echo $editing ? 'Editar Producto' : 'Nuevo Producto'; ?>
                </h2>
                
                <form action="gestionar_producto.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id_producto" value="<?php echo $product['id_producto']; ?>">
                    <input type="hidden" name="accion" value="<?php echo $editing ? 'actualizar' : 'insertar'; ?>">

                    <div class="form-group">
                        <label for="nombre_producto">Nombre del Producto *</label>
                        <input type="text" id="nombre_producto" name="nombre_producto" required value="<?php echo htmlspecialchars($product['nombre_producto']); ?>" placeholder="Ej: Cuaderno universitario 100hj">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label for="codigo_barra">Código de Barras</label>
                            <input type="text" id="codigo_barra" name="codigo_barra" value="<?php echo htmlspecialchars($product['codigo_barra']); ?>" maxlength="13">
                        </div>
                        <div class="form-group">
                            <label for="id_lote">ID de Lote (Único) *</label>
                            <input type="number" id="id_lote" name="id_lote" required value="<?php echo htmlspecialchars($product['id_lote']); ?>">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label for="tipo_producto">Tipo de Producto *</label>
                            <select id="tipo_producto" name="tipo_producto" required>
                                <option value="">Seleccione...</option>
                                <option value="oficina" <?php if($product['tipo_producto'] == 'oficina') echo 'selected'; ?>>Oficina</option>
                                <option value="escolar" <?php if($product['tipo_producto'] == 'escolar') echo 'selected'; ?>>Escolar</option>
                                <option value="muebleria" <?php if($product['tipo_producto'] == 'muebleria') echo 'selected'; ?>>Mueblería</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="ciudad">Ciudad *</label>
                            <select id="ciudad" name="ciudad" required>
                                <option value="">Seleccione...</option>
                                <option value="Santiago" <?php if($product['ciudad'] == 'Santiago') echo 'selected'; ?>>Santiago</option>
                                <option value="Valparaiso" <?php if($product['ciudad'] == 'Valparaiso') echo 'selected'; ?>>Valparaíso</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="num_estante">Número de Estante (3 dígitos) *</label>
                        <input type="text" id="num_estante" name="num_estante" required pattern="\d{3}" maxlength="3" value="<?php echo htmlspecialchars($product['num_estante']); ?>" placeholder="001">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.5rem; background: #f8fafc; padding: 1rem; border-radius: 8px; margin-bottom: 1.25rem;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="cant_inventario_piezas">Piezas Sueltas</label>
                            <input type="number" id="cant_inventario_piezas" name="cant_inventario_piezas" value="<?php echo htmlspecialchars($product['cant_inventario_piezas']); ?>" onchange="updateTotal()">
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="cant_cajas">Cajas</label>
                            <input type="number" id="cant_cajas" name="cant_cajas" value="<?php echo htmlspecialchars($product['cant_cajas']); ?>" onchange="updateTotal()">
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="piezas_por_caja">Pzs x Caja</label>
                            <input type="number" id="piezas_por_caja" name="piezas_por_caja" value="<?php echo htmlspecialchars($product['piezas_por_caja']); ?>" onchange="updateTotal()">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Total de Piezas Calculado</label>
                        <div id="total_display" style="font-size: 1.5rem; font-weight: 800; color: var(--primary);">0</div>
                    </div>

                    <div class="form-group">
                        <label for="imagen_producto">Imagen del Producto</label>
                        <input type="file" id="imagen_producto" name="imagen_producto" accept="image/*">
                        <?php if($product['ruta_imagen']): ?>
                            <small>Imagen actual: <?php echo basename($product['ruta_imagen']); ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="form-actions" style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                        <button type="submit" class="btn btn-primary" style="flex: 1;">
                            <?php echo $editing ? 'Guardar Cambios' : 'Registrar Producto'; ?>
                        </button>
                        <?php if($editing): ?>
                            <a href="index.php" class="btn btn-secondary">Cancelar</a>
                        <?php endif; ?>
                    </div>
                </form>
            </section>

            <!-- Listado Section -->
            <section class="card">
                <h2>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                    Inventario Actual
                </h2>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Lote</th>
                                <th>Ubicación</th>
                                <th>Stock Total</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT *, (cant_inventario_piezas + (cant_cajas * piezas_por_caja)) AS total FROM productos ORDER BY created_at DESC";
                            $result = $conexion->query($query);
                            if ($result && $result->num_rows > 0):
                                while($row = $result->fetch_assoc()):
                            ?>
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <?php if($row['ruta_imagen']): ?>
                                            <img src="<?php echo $row['ruta_imagen']; ?>" class="product-img" alt="">
                                        <?php else: ?>
                                            <div class="product-img" style="background: #e2e8f0; display:flex; align-items:center; justify-content:center; color:#94a3b8; font-size:10px;">IMG</div>
                                        <?php endif; ?>
                                        <div>
                                            <div style="font-weight: 600;"><?php echo htmlspecialchars($row['nombre_producto']); ?></div>
                                            <div style="font-size: 0.75rem; color: var(--text-muted);"><?php echo $row['tipo_producto']; ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><code style="background:#f1f5f9; padding:2px 4px; border-radius:4px;"><?php echo htmlspecialchars($row['id_lote']); ?></code></td>
                                <td>
                                    <span class="badge <?php echo $row['ciudad'] == 'Santiago' ? 'badge-blue' : 'badge-green'; ?>">
                                        <?php echo $row['ciudad']; ?>
                                    </span>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">Estante: <?php echo $row['num_estante']; ?></div>
                                </td>
                                <td>
                                    <div style="font-weight: 700; color: var(--primary);"><?php echo $row['total']; ?></div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);"><?php echo $row['cant_cajas']; ?> cjs + <?php echo $row['cant_inventario_piezas']; ?> pzs</div>
                                </td>
                                <td class="actions-cell">
                                    <a href="index.php?edit=<?php echo $row['id_producto']; ?>" class="btn" style="padding: 0.5rem; background: #f1f5f9; color: var(--text-main);">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                    </a>
                                    <a href="gestionar_producto.php?delete=<?php echo $row['id_producto']; ?>" class="btn" style="padding: 0.5rem; background: #fee2e2; color: var(--danger);" onclick="return confirm('¿Está seguro de eliminar este producto?')">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">No hay productos registrados aún.</div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>

    <script>
        function updateTotal() {
            const piezas = parseInt(document.getElementById('cant_inventario_piezas').value) || 0;
            const cajas = parseInt(document.getElementById('cant_cajas').value) || 0;
            const pxCaja = parseInt(document.getElementById('piezas_por_caja').value) || 0;
            const total = piezas + (cajas * pxCaja);
            document.getElementById('total_display').innerText = total;
        }
        // Initialize total on load
        updateTotal();
    </script>
</body>
</html>
