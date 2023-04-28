<?php

include("../../bd.php");

if(isset($_GET['txtID'])){

    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";
    $nombre_producto=(isset($_GET['nombre_producto']))?$_GET['nombre_producto']:"";
    
    $sentencia=$conexion->prepare("SELECT * FROM entradas e INNER JOIN productos p ON e.id_producto=p.id_producto WHERE id_entrada=:id_entrada");
    $sentencia->bindParam(":id_entrada", $txtID);
    $sentencia->execute();
    $registro=$sentencia->fetch(PDO::FETCH_LAZY);

    $cantidad=$registro["cantidad"];
    $nombre_producto=$registro["nombre_producto"];
    $precio=$registro["precio"];

}

if($_POST){

  //recolectamos los datos del metodo POST
  $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";
  $cantidad=(isset($_POST["cantidad"])?$_POST["cantidad"]:"");

  $sentencia2=$conexion->prepare("SELECT p.precio FROM entradas e INNER JOIN productos p ON e.id_producto=p.id_producto WHERE id_entrada=:id_entrada");
  $sentencia2->bindParam(":id_entrada", $txtID);
  $sentencia2->execute();
  $registro2=$sentencia2->fetch(PDO::FETCH_LAZY);

  $precio=$registro2["precio"];

  //Preparar la insersion de los datos
  $sentencia=$conexion->prepare("UPDATE entradas SET cantidad=:cantidad, precio_total=(:precio*:cantidad) WHERE id_entrada=:id_entrada ");
  
  //Asignando los valores que vienen del metodo POST ( los que vienen del formulario)
  $sentencia->bindParam(":cantidad", $cantidad);
  $sentencia->bindParam(":id_entrada", $txtID);
  $sentencia->bindParam(":precio", $precio);
  $sentencia->execute();
  $mensaje="Registro actualizado";
  header("Location:index.php?mensaje=".$mensaje);
}

?>

<?php include("../../templates/header.php"); ?>

<br/>
   
   <div class="card">
     <div class="card-header">
         Editar Cantidad de Inventario
     </div>
     <div class="card-body">
         
         <form action="" method="post" enctype="multipart/form-data">

         <div class="mb-3">
           <label for="txtID" class="form-label">ID:</label>
           <h5 name="txtID" id="txtID"><?php echo $txtID;?></h5>
         </div>

             <div class="mb-3">
               <label for="nombre_producto" class="form-label">Producto:</label>
               <h5 name="nombre_producto" id="nombre_producto"><?php echo $nombre_producto;?></h5>
             </div>

             <div class="mb-3">
               <img width="60" height="60"
                src="<?php echo "../productos/" . $registro['image']; ?>"
                class="img-fluid rounded" alt="">
                <br/>
             </div>

             <div class="mb-3">
               <label for="cantidad" class="form-label">Cantidad:</label>
               <input type="text"
               value="<?php echo $cantidad;?>" required
                 class="form-control" name="cantidad" id="cantidad" aria-describedby="helpId" placeholder="cantidad">
             </div>

             <br/>
 
             <button type="submit" class="btn btn-success">Actualizar</button>
             <a name="" id="" class="btn btn-secondary" href="index.php" role="button">Cancelar</a>
 
         </form>
 
 
     </div>
   

<?php include("../../templates/footer.php"); ?>