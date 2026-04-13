#!/bin/bash

# Configuration
DB_NAME="libreria_utiles"
DB_USER="root"
# Check if mariadb/mysql is running and if we can connect
if ! mysql -u "$DB_USER" -e "status" >/dev/null 2>&1; then
    echo "Error: No se puede conectar a MySQL con el usuario $DB_USER. Asegúrate de que el servicio esté corriendo y el usuario no tenga contraseña (o edita este script)."
    exit 1
fi

echo "Configurando base de datos..."
mysql -u "$DB_USER" < db_setup.sql

if [ $? -eq 0 ]; then
    echo "Base de datos '$DB_NAME' lista."
else
    echo "Error al configurar la base de datos."
    exit 1
fi

echo "Iniciando servidor PHP en http://localhost:8080..."
php -S localhost:8080
