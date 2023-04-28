<?php

include("../../bd.php");

if(isset($_GET['txtID'])){

    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";
    
    $sentencia=$conexion->prepare("SELECT * FROM cargo WHERE id_cargo=:id_cargo");
    $sentencia->bindParam(":id_cargo", $txtID);
    $sentencia->execute();

    $registro=$sentencia->fetch(PDO::FETCH_LAZY);
    $nombre_cargo=$registro["nombre_cargo"];

}

if($_POST){

  //recolectamos los datos del metodo POST
  $txtID=(isset($_POST['txtID']))?$_POST['txtID']:"";
  $nombre_cargo=(isset($_POST["nombre_cargo"])?$_POST["nombre_cargo"]:"");

  //Preparar la insersion de los datos
  $sentencia=$conexion->prepare("UPDATE cargo SET nombre_cargo=:nombre_cargo WHERE id_cargo=:id_cargo ");
  
  //Asignando los valores que vienen del metodo POST ( los que vienen del formulario)
  $sentencia->bindParam(":nombre_cargo", $nombre_cargo);
  $sentencia->bindParam(":id_cargo", $txtID);
  $sentencia->execute();
  $mensaje="Registro actualizado";
  header("Location:index.php?mensaje=".$mensaje);
}

?>

<?php include("../../templates/header.php"); ?>

<br/>
   
   <div class="card">
     <div class="card-header">
         Editar Rol
     </div>
     <div class="card-body">
         
         <form action="" method="post" enctype="multipart/form-data">

          <!-- <p class="form-text text-muted" name="txtID" id="txtID">Prueba</p> -->

         <div class="mb-3">
           <label for="txtID" class="form-label">ID:</label>
           <h5 name="txtID" id="txtID"><?php echo $txtID;?></h5>
         </div>

             <div class="mb-3">
               <label for="nombre_cargo" class="form-label">Nombre Rol:</label>
               <input type="text" required
               value="<?php echo $nombre_cargo;?>"
                 class="form-control" name="nombre_cargo" id="nombre_cargo" aria-describedby="helpId" placeholder="Nombre Categoria">
             </div>

             <br/>
 
             <button type="submit" class="btn btn-success">Actualizar</button>
             <a name="" id="" class="btn btn-secondary" href="index.php" role="button">Cancelar</a>
 
         </form>
 
 
     </div>
   </div>
   

<?php include("../../templates/footer.php"); ?>