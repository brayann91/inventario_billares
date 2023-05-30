<?php

include("../../bd.php");



if(isset($_GET['txtID'])){

    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";
    
    //Buscar la imagen de producto
    $sentencia=$conexion->prepare("SELECT image FROM `productos` WHERE id_producto=:id_producto");
    $sentencia->bindParam(":id_producto", $txtID);
    $sentencia->execute();
    $registro_recuperado=$sentencia->fetch(PDO::FETCH_LAZY);

    if(isset($registro_recuperado["image"]) && $registro_recuperado["image"]!=""){
        if(file_exists("./".$registro_recuperado["image"])){
            unlink("./".$registro_recuperado["image"]);
        }
    }

    $sentencia=$conexion->prepare("SELECT SUM(cantidad) cantidad
    FROM entradas e INNER JOIN productos p ON e.id_producto=p.id_producto WHERE e.id_producto=:id_producto 
    GROUP BY p.id_producto");
    $sentencia->bindParam(":id_producto", $txtID);
    $sentencia->execute();
    $inventario=$sentencia->fetch(PDO::FETCH_LAZY);

    if($inventario['cantidad']>0){
        $mensaje="No se puede eliminar el producto cuando existe inventario activo";
        header("Location:index.php?mensaje=".$mensaje);
    }else{
        $sentencia=$conexion->prepare("DELETE FROM productos WHERE id_producto=:id_producto");
        $sentencia->bindParam(":id_producto", $txtID);
        $sentencia->execute();
        $mensaje="Registro eliminado";
        header("Location:index.php?mensaje=".$mensaje);
    }
}

include("../../templates/header.php");


$sentencia=$conexion->prepare("SELECT DISTINCT p.*, c.nombre_categoria, s.nombre_sede
FROM productos p
INNER JOIN categorias c ON p.id_categoria = c.id_categoria
INNER JOIN sedes s ON p.id_sede = " . $_SESSION['id_sede'] . "
GROUP BY p.id_producto");
$sentencia->execute();
$lista_productos=$sentencia->fetchAll(PDO::FETCH_ASSOC);

?>

  <br/>
   
  <h4>PRODUCTOS</h4>
   
  <br/>

  <div class="card">
    <div class="card-header">
        <a name="" id="" class="btn btn-primary" 
        href="crear.php" 
        role="button">
        Agregar</a>
    </div>
    <div class="card-body">
       
    <div class="table-responsive-sm">
        <table class="table" id="tabla_id">
            <thead>
                <tr valign="middle" align="center">
                    <th scope="col">ID</th>
                    <th scope="col">FOTO</th>
                    <th scope="col">NOMBRE</th>
                    <th scope="col">PRECIO VENTA</th>
                    <th scope="col">PRECIO COMPRA</th>
                    <th scope="col">CATEGORIA</th>
                    <th scope="col">CREACIÓN</th>
                    <th scope="col">ACTUALIZACIÓN</th>
                    <th scope="col">SEDE</th>
                    <th scope="col">ACCIONES</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($lista_productos as $registro){ ?>
                <tr class="" valign="middle" align="center">
                    <td scope="row"><?php echo $registro['id_producto'];?></td>
                    <td>
                    <img width="60" height="60"
                        src="<?php echo $registro['image'];?>"
                        class="img-fluid rounded"
                        style="height: 60px !important;"
                        alt="">
                    </td>
                    <td><?php echo $registro['nombre_producto'];?></td>
                    <td>$ <?php echo number_format($registro['precio'],1);?></td>
                    <td>$ <?php echo number_format($registro['precio_compra'],1);?></td>
                    <td><?php echo $registro['nombre_categoria'];?></td>
                    <td><?php echo date('M-d H:i:s', strtotime($registro['created_at']));?></td>
                    <td><?php echo date('M-d H:i:s', strtotime($registro['updated_at']));?></td>
                    <td><?php echo $registro['id_sede'];?></td>
                    <td>
                        <a name="" id="editar_producto" class="btn btn-info" href="editar.php?txtID=<?php echo $registro['id_producto']; ?>" role="button">Editar</a>
                        <a name="" id="eliminar_producto" class="btn btn-danger" href="javascript:borrar(<?php echo $registro['id_producto']; ?>);" role="button">Borrar</a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    

    </div>
  
  </div>


<?php include("../../templates/footer.php"); ?>