<?php
$servidor = "localhost";
$usuario = "root";
$password = "";
$base_datos = "libreria_utiles";

$conexion = new mysqli($servidor, $usuario, $password, $base_datos);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Asegurar caracteres UTF-8
$conexion->set_charset("utf8mb4");
?>
