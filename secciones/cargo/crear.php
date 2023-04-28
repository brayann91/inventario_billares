<?php

include("../../bd.php");

if($_POST){

  //recolectamos los datos del metodo POST
  $nombre_cargo=(isset($_POST["nombre_cargo"])?$_POST["nombre_cargo"]:"");

  //Preparar la insersion de los datos
  $sentencia=$conexion->prepare("INSERT INTO cargo(nombre_cargo) VALUES (:nombre_cargo)");
  
  //Asignando los valores que vienen del metodo POST ( los que vienen del formulario)
  $sentencia->bindParam(":nombre_cargo", $nombre_cargo);
  $sentencia->execute();
  $mensaje="Registro agregado";
  header("Location:index.php?mensaje=".$mensaje);
}

?>

<?php include("../../templates/header.php"); ?>


<br/>
   
   <div class="card">
     <div class="card-header">
         Agregar Rol
     </div>
     <div class="card-body">
         
         <form action="" method="post" enctype="multipart/form-data">
 

             <div class="mb-3">
               <label for="nombre_cargo" class="form-label">Nombre Rol:</label>
               <input type="text" required
                 class="form-control" name="nombre_cargo" id="nombre_cargo" aria-describedby="helpId" placeholder="Nombre Categoria">
             </div>

             <br/>
 
             <button type="submit" class="btn btn-success">Agregar registro</button>
             <a name="" id="" class="btn btn-secondary" href="index.php" role="button">Cancelar</a>
 
         </form>
 
 
     </div>
   </div>


<?php include("../../templates/footer.php"); ?>