CREATE DATABASE IF NOT EXISTS libreria_utiles;
USE libreria_utiles;

CREATE TABLE IF NOT EXISTS productos (
    id_producto INT AUTO_INCREMENT PRIMARY KEY COMMENT 'ID único del producto',
    codigo_barra VARCHAR(20) COMMENT 'Código de barra del fabricante',
    codigo_qr VARCHAR(255) COMMENT 'Información del código QR',
    nombre_producto VARCHAR(100) NOT NULL COMMENT 'Nombre del producto',
    ruta_imagen VARCHAR(255) COMMENT 'Ruta de la imagen en el servidor',
    id_lote INT NOT NULL UNIQUE COMMENT 'Identificador de lote único',
    tipo_producto ENUM('oficina', 'escolar', 'muebleria') NOT NULL COMMENT 'Tipo de producto',
    ciudad ENUM('Santiago', 'Valparaiso') NOT NULL COMMENT 'Ciudad de la tienda',
    num_estante CHAR(3) NOT NULL COMMENT 'Número de estante de 3 dígitos',
    cant_inventario_piezas INT NOT NULL DEFAULT 0 COMMENT 'Cantidad de piezas sueltas',
    cant_cajas INT NOT NULL DEFAULT 0 COMMENT 'Cantidad de cajas/empaques',
    piezas_por_caja INT NOT NULL DEFAULT 1 COMMENT 'Piezas por caja/empaque',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Restricción CHECK para num_estante (si la versión de MySQL lo soporta, >= 8.0.16)
-- En sistemas más antiguos esto se ignora o falla, así que lo ponemos como opcional
-- ALTER TABLE productos ADD CONSTRAINT chk_num_estante CHECK (num_estante REGEXP '^[0-9]{3}$');
