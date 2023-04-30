<?php

$servidor="127.0.0.1:3306"; //127.0.0.1
$baseDeDatos="u758506060_inventario";
$usuario="u758506060_brayann91";
$contrasena="3:7LL^rJ";
$opciones = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone = '-05:00'");
try {
    $conexion = new PDO("mysql:host=$servidor;dbname=$baseDeDatos", $usuario, $contrasena, $opciones);
} catch (Exception $ex) {
    echo $ex->getMessage();
}

?>