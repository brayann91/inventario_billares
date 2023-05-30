<?php

include("../../bd.php");

if($_POST){

  //recolectamos los datos del metodo POST
  $nombre_grupo_sede=(isset($_POST["nombre_grupo_sede"])?$_POST["nombre_grupo_sede"]:"");

  //Preparar la insersion de los datos
  $sentencia=$conexion->prepare("INSERT INTO grupo_sedes(nombre_grupo_sede) 
  VALUES (:nombre_grupo_sede)");
  
  //Asignando los valores que vienen del metodo POST ( los que vienen del formulario)
  $sentencia->bindParam(":nombre_grupo_sede", $nombre_grupo_sede);
  $sentencia->execute();
  $mensaje="Registro agregado";
  header("Location:index.php?mensaje=".$mensaje);
}

?>

<?php include("../../templates/header.php"); ?>

<br/>
   
   <div class="card">
     <div class="card-header">
         Agregar un Grupo a la Sede
     </div>
     <div class="card-body">
         
         <form action="" method="post" enctype="multipart/form-data">
 
             <div class="mb-3">
               <label for="nombre_grupo_sede" class="form-label">Nombre Grupo Sede:</label>
               <input type="text" required
                 class="form-control" name="nombre_grupo_sede" id="nombre_grupo_sede" aria-describedby="helpId" placeholder="Nombre Grupo Sede">
             </div>

             <br/>
 
             <button type="submit" class="btn btn-success">Agregar registro</button>
             <a name="" id="" class="btn btn-secondary" href="index.php" role="button">Cancelar</a>

         </form> 
 
     </div>
   </div>


<?php include("../../templates/footer.php"); ?>