<?php

include("../../bd.php");

if(isset($_GET['txtID'])){

    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";
    
    $sentencia=$conexion->prepare("SELECT * FROM cuentas WHERE id_cuenta=:id_cuenta");
    $sentencia->bindParam(":id_cuenta", $txtID);
    $sentencia->execute();

    $registro=$sentencia->fetch(PDO::FETCH_LAZY);
    $nombre_cuenta=$registro["nombre_cuenta"];
    $precio_cuenta=$registro["precio_cuenta"];

}

if($_POST){

  //recolectamos los datos del metodo POST
  $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";
  $nombre_cuenta=(isset($_POST["nombre_cuenta"])?$_POST["nombre_cuenta"]:"");
  $precio_cuenta=(isset($_POST["precio_cuenta"])?$_POST["precio_cuenta"]:"");

  //Preparar la insersion de los datos
  $sentencia=$conexion->prepare("UPDATE cuentas SET nombre_cuenta=:nombre_cuenta, precio_cuenta=:precio_cuenta WHERE id_cuenta=:id_cuenta ");
  
  //Asignando los valores que vienen del metodo POST ( los que vienen del formulario)
  $sentencia->bindParam(":nombre_cuenta", $nombre_cuenta);
  $sentencia->bindParam(":precio_cuenta", $precio_cuenta);
  $sentencia->bindParam(":id_cuenta", $txtID);
  $sentencia->execute();
  $mensaje="Registro actualizado";
  header("Location:index.php?mensaje=".$mensaje);
}

?>

<?php include("../../templates/header.php"); ?>

<br/>
   
   <div class="card">
     <div class="card-header">
         Editar Cuenta
     </div>
     <div class="card-body">
         
         <form action="" method="post" enctype="multipart/form-data">

         <div class="mb-3">
           <label for="txtID" class="form-label">ID:</label>
           <h5 name="txtID" id="txtID"><?php echo $txtID;?></h5>
         </div>

             <div class="mb-3">
               <label for="nombre_cuenta" class="form-label">Nombre cuenta:</label>
               <input type="text" required
               value="<?php echo $nombre_cuenta;?>"
                 class="form-control" name="nombre_cuenta" id="nombre_cuenta" aria-describedby="helpId" placeholder="Nombre cuenta">
             </div>

             <div class="mb-3">
               <label for="nombre_cuenta" class="form-label">Precio por Hora:</label>
               <input type="text" required
               value="<?php echo $precio_cuenta;?>"
                 class="form-control" name="precio_cuenta" id="precio_cuenta" aria-describedby="helpId" placeholder="$10.000">
             </div>

             <br/>
 
             <button type="submit" class="btn btn-success">Actualizar</button>
             <a name="" id="" class="btn btn-secondary" href="index.php" role="button">Cancelar</a>
 
         </form>
 
 
     </div>
   </div>
   

<?php include("../../templates/footer.php"); ?>