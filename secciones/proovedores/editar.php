<?php

include("../../bd.php");

if(isset($_GET['txtID'])){

    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";
    
    $sentencia=$conexion->prepare("SELECT * FROM proveedores WHERE id_proovedor=:id_proovedor");
    $sentencia->bindParam(":id_proovedor", $txtID);
    $sentencia->execute();

    $registro=$sentencia->fetch(PDO::FETCH_LAZY);
    $nombre_proovedor=$registro["nombre_proovedor"];

}

if($_POST){

  //recolectamos los datos del metodo POST
  $txtID=(isset($_POST['txtID']))?$_POST['txtID']:"";
  $nombre_proovedor=(isset($_POST["nombre_proovedor"])?$_POST["nombre_proovedor"]:"");

  //Preparar la insersion de los datos
  $sentencia=$conexion->prepare("UPDATE proveedores SET nombre_proovedor=:nombre_proovedor WHERE id_proovedor=:id_proovedor ");
  
  //Asignando los valores que vienen del metodo POST ( los que vienen del formulario)
  $sentencia->bindParam(":nombre_proovedor", $nombre_proovedor);
  $sentencia->bindParam(":id_proovedor", $txtID);
  $sentencia->execute();
  $mensaje="Registro actualizado";
  header("Location:index.php?mensaje=".$mensaje);
}

?>

<?php include("../../templates/header.php"); ?>

<br/>
   
   <div class="card">
     <div class="card-header">
         Editar Proovedor
     </div>
     <div class="card-body">
         
         <form action="" method="post" enctype="multipart/form-data">

         <div class="mb-3">
           <label for="txtID" class="form-label">ID:</label>
           <h5 name="txtID" id="txtID"><?php echo $txtID;?></h5>
         </div>

             <div class="mb-3">
               <label for="nombre_proovedor" class="form-label">Nombre Proovedor:</label>
               <input type="text"
               value="<?php echo $nombre_proovedor;?>"
                 class="form-control" required name="nombre_proovedor" id="nombre_proovedor" aria-describedby="helpId" placeholder="Nombre Proovedor">
             </div>

             <br/>
 
             <button type="submit" class="btn btn-success">Actualizar</button>
             <a name="" id="" class="btn btn-secondary" href="index.php" role="button">Cancelar</a>
 
         </form>
 
 
     </div>
   </div>
   

<?php include("../../templates/footer.php"); ?>