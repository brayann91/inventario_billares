<?php

  session_start();
  // Conexión a la base de datos
  include "../../../bd.php";

  // Recuperar la categoría seleccionada
  $id_categoria = $_GET['id_categoria'];
    
  $sentencia = $conexion->prepare("SELECT id_producto, nombre_producto, id_categoria, id_sede FROM productos 
  WHERE id_categoria = ".$id_categoria. " AND id_sede = '".$_SESSION['id_sede']."'");
  $sentencia->execute();
  $resultado_productos = $sentencia->fetchAll(PDO::FETCH_ASSOC);

  // Generar un array con los datos de los productos
  $productos = array();
  
  foreach ($resultado_productos as $row_producto) {
    $productos[] = $row_producto;
  } 

  // Devolver los datos en formato JSON
  header('Content-Type: application/json');
  echo json_encode($productos);

?>