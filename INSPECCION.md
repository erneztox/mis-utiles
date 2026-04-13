# Guía de Auditoría Técnica: Inspección de Almacenamiento

Esta guía detalla los pasos y puntos de inspección para cumplir con el requerimiento de auditar el flujo de datos desde el formulario hasta el SGBD (MySQL).

## Capa 1: Inspección en el Origen (Cliente)

### 1.1 Cuadros Desplegables (`<select>`)
- **Evidencia**: En `index.php`, los campos `tipo_producto` y `ciudad` utilizan etiquetas `<select>`.
- **Verificación**: Inspecciona el código fuente (F12 en el navegador). Los valores coinciden con los permitidos en la DB (`oficina`, `escolar`, `muebleria` y `Santiago`, `Valparaiso`).
- **Archivo**: `index.php` líneas 77-92.

### 1.2 Atributos HTML5 de Validación
- **Presencia**: Atributo `required` en todos los campos obligatorios.
- **Longitud**: El campo `num_estante` tiene `pattern="\d{3}"` y `maxlength="3"`.
- **Tipo**: Campos numéricos usan `type="number"` y `min="0"`.
- **Archivo**: `index.php` líneas 60, 70, 96, 102.

### 1.3 Validación Asíncrona (AJAX) - Identificador Único
- **Acción**: Al escribir un `id_lote` ya existente, el sistema consulta al servidor en tiempo real.
- **Evidencia**: Si ingresas un lote duplicado y sales del campo (blur), aparecerá un mensaje: "⚠️ Este lote ya está registrado".
- **Archivo**: `index.php` líneas 219-241 (JavaScript) y `gestionar_producto.php` líneas 4-15.

---

## Capa 2: Inspección en el Tránsito (Software Cliente)

### 2.1 Cadena de Conexión (CBD)
- **Evidencia**: Uso de la clase `mysqli` con credenciales configuradas.
- **Archivo**: `db_connection.php`.

### 2.2 Sanitización (Protección contra Inyección SQL)
- **Evidencia**: Uso de `$conexion->real_escape_string()` para todos los inputs de texto recibidos por `$_POST`.
- **Archivo**: `gestionar_producto.php` líneas 20-30.

### 2.3 Lógica de Cálculo (Campo Derivado)
- **Evidencia**: El campo "Total de Piezas" no se almacena. Se calcula dinámicamente en la consulta SQL.
- **Fórmula**: `(cant_inventario_piezas + (cant_cajas * piezas_por_caja)) AS total`.
- **Archivo**: `index.php` línea 158.

---

## Capa 3: Inspección en el Destino (SGBD MySQL)

### 3.1 Inspección del Esquema
Ejecuta en **DBeaver**:
```sql
DESCRIBE productos;
```
Esto confirma:
- `tipo_producto` y `ciudad` son `ENUM`.
- `id_lote` tiene restricción `UNIQUE`.
- Los campos obligatorios son `NOT NULL`.

### 3.2 Verificación de Comprobaciones (Fallas del Sistema)
Pruebas de "Análisis Forense" en **DBeaver**:

1. **Violación de UNIQUE (Lote Duplicado)**:
   ```sql
   INSERT INTO productos (nombre_producto, id_lote, tipo_producto, ciudad, num_estante) 
   VALUES ('Test', 999, 'escolar', 'Santiago', '001');
   -- Ejecuta lo mismo otra vez para ver el ERROR 1062 (Duplicate entry)
   ```

2. **Violación de Dominio (ENUM)**:
   ```sql
   INSERT INTO productos (nombre_producto, id_lote, tipo_producto, ciudad, num_estante) 
   VALUES ('Test', 1000, 'invalido', 'SCL', '001');
   -- Verás el ERROR 1265 (Data truncated) o de restricción.
   ```
