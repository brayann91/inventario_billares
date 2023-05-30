<?php

include("../../bd.php");

if(isset($_GET['txtID'])){

    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";
    
    $sentencia=$conexion->prepare("SELECT * FROM grupo_sedes WHERE id_grupo_sede=:id_grupo_sede");
    $sentencia->bindParam(":id_grupo_sede", $txtID);
    $sentencia->execute();
    $registro=$sentencia->fetch(PDO::FETCH_LAZY);

    $nombre_grupo_sede=$registro["nombre_grupo_sede"];

}

if($_POST){
  //recolectamos los datos del metodo POST
  $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";
  $nombre_grupo_sede=(isset($_POST["nombre_grupo_sede"])?$_POST["nombre_grupo_sede"]:"");

  //Preparar la insersion de los datos
  $sentencia=$conexion->prepare("UPDATE grupo_sedes SET nombre_grupo_sede=:nombre_grupo_sede
  WHERE id_grupo_sede=:id_grupo_sede");
  $sentencia->bindParam(":nombre_grupo_sede", $nombre_grupo_sede);
  $sentencia->bindParam(":id_grupo_sede", $txtID);
  $sentencia->execute();
  $mensaje="Registro actualizado";
  header("Location:index.php?mensaje=".$mensaje);
}

?>

<?php include("../../templates/header.php"); ?>

<br/>
   
   <div class="card">
     <div class="card-header">
         Editar Grupo Sede
     </div>
     <div class="card-body">
         
         <form action="" method="post" enctype="multipart/form-data">

         <div class="mb-3">
           <label for="txtID" class="form-label">ID:</label>
           <h5 name="txtID" id="txtID"><?php echo $txtID;?></h5>
         </div>

             <div class="mb-3">
               <label for="nombre_grupo_sede" class="form-label">Nombre Grupo Sede:</label>
               <input type="text" required
               value="<?php echo $nombre_grupo_sede;?>"
                 class="form-control" name="nombre_grupo_sede" id="nombre_grupo_sede" aria-describedby="helpId" placeholder="Nombre Grupo Sede">
             </div>

             <br/>
 
             <button type="submit" class="btn btn-success">Actualizar</button>
             <a name="" id="" class="btn btn-secondary" href="index.php" role="button">Cancelar</a>

         </form>
 
     </div>
   </div>
   
<?php include("../../templates/footer.php"); ?>