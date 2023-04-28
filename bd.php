<?php

$servidor="localhost"; //127.0.0.1
$baseDeDatos="u758506060_inventario";
$usuario="u758506060_brayann91";
$contrasena="2P$xmJqMOc+m";
try {
    $conexion= new PDO("mysql:host=$servidor;dbname=$baseDeDatos", $usuario, $contrasena);
} catch (Exception $ex) {
    echo $ex->getMessage();
}

?>