# Sistema de Inventario - Mis Útiles Escolares

Este proyecto es un sistema de gestión de inventario desarrollado con PHP y MySQL, diseñado para ser responsivo y eficiente.

## Requisitos
- Servidor PHP (v7.4+)
- MySQL / MariaDB
- Permisos de escritura en la carpeta `uploads/`

## Estructura del Proyecto
- `index.php`: Interfaz principal que muestra el listado y el formulario de ingreso/edición.
- `gestionar_producto.php`: Controlador que procesa las solicitudes de creación, actualización y eliminación.
- `db_connection.php`: Configuración de la conexión a la base de datos.
- `db_setup.sql`: Script SQL para crear la base de datos y la tabla necesaria.
- `estilos.css`: Diseño moderno y responsivo.
- `start_server.sh`: Script para automatizar la configuración y ejecutar el servidor local.

## Instrucciones de Ejecución
Para iniciar el sistema de forma rápida, ejecuta:
```bash
./start_server.sh
```
Esto creará la base de datos automáticamente e iniciará el servidor en http://localhost:8080.

## Características
- **CRUD Completo**: Ingreso, consulta, edición y eliminación de productos.
- **Cálculo Automático**: El total de piezas se calcula dinámicamente según las piezas sueltas y las cajas.
- **Validaciones**: Comprobaciones de formato (estante de 3 dígitos), campos obligatorios y lote único.
- **Gestión de Imágenes**: Soporte para subir imágenes de los productos.
