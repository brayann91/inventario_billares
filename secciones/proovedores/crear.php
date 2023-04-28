<?php

include("../../bd.php");

if($_POST){

  //recolectamos los datos del metodo POST
  $nombre_proovedor=(isset($_POST["nombre_proovedor"])?$_POST["nombre_proovedor"]:"");

  //Preparar la insersion de los datos
  $sentencia=$conexion->prepare("INSERT INTO proveedores(nombre_proovedor) VALUES (:nombre_proovedor)");
  
  //Asignando los valores que vienen del metodo POST ( los que vienen del formulario)
  $sentencia->bindParam(":nombre_proovedor", $nombre_proovedor);
  $sentencia->execute();
  $mensaje="Registro agregado";
  header("Location:index.php?mensaje=".$mensaje);
}

?>

<?php include("../../templates/header.php"); ?>

<br/>
   
   <div class="card">
     <div class="card-header">
         Agregar Proovedor
     </div>
     <div class="card-body">
         
         <form action="" method="post" enctype="multipart/form-data">
 
             <div class="mb-3">
               <label for="nombre_proovedor" class="form-label">Nombre Proovedor:</label>
               <input type="text"
                 class="form-control" required name="nombre_proovedor" id="nombre_proovedor" aria-describedby="helpId" placeholder="Nombre Proovedor">
             </div>

             <br/>
 
             <button type="submit" class="btn btn-success">Agregar registro</button>
             <a name="" id="" class="btn btn-secondary" href="index.php" role="button">Cancelar</a>
 
         </form>
 
 
     </div>
   </div>


<?php include("../../templates/footer.php"); ?>