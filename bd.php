<?php

$servidor="localhost"; //127.0.0.1
$baseDeDatos="inventario";
$usuario="root";
$contrasena="";
try {
    $conexion= new PDO("mysql:host=$servidor;dbname=$baseDeDatos", $usuario, $contrasena);
} catch (Exception $ex) {
    echo $ex->getMessage();
}

?>