<?php

include("../../bd.php");

if(isset($_GET['txtID'])){

    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";
    
    $sentencia=$conexion->prepare("SELECT * FROM categorias WHERE id_categoria=:id_categoria");
    $sentencia->bindParam(":id_categoria", $txtID);
    $sentencia->execute();

    $registro=$sentencia->fetch(PDO::FETCH_LAZY);
    $nombre_categoria=$registro["nombre_categoria"];

}

if($_POST){

  //recolectamos los datos del metodo POST
  $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";
  $nombre_categoria=(isset($_POST["nombre_categoria"])?$_POST["nombre_categoria"]:"");

  //Preparar la insersion de los datos
  $sentencia=$conexion->prepare("UPDATE categorias SET nombre_categoria=:nombre_categoria WHERE id_categoria=:id_categoria ");
  
  //Asignando los valores que vienen del metodo POST ( los que vienen del formulario)
  $sentencia->bindParam(":nombre_categoria", $nombre_categoria);
  $sentencia->bindParam(":id_categoria", $txtID);
  $sentencia->execute();
  $mensaje="Registro actualizado";
  header("Location:index.php?mensaje=".$mensaje);
}

?>

<?php include("../../templates/header.php"); ?>

<br/>
   
   <div class="card">
     <div class="card-header">
         Editar Categoria
     </div>
     <div class="card-body">
         
         <form action="" method="post" enctype="multipart/form-data">

         <div class="mb-3">
           <label for="txtID" class="form-label">ID:</label>
           <h5 name="txtID" id="txtID"><?php echo $txtID;?></h5>
         </div>

             <div class="mb-3">
               <label for="nombre_categoria" class="form-label">Nombre Categoria:</label>
               <input type="text" required
               value="<?php echo $nombre_categoria;?>"
                 class="form-control" name="nombre_categoria" id="nombre_categoria" aria-describedby="helpId" placeholder="Nombre Categoria">
             </div>

             <br/>
 
             <button type="submit" class="btn btn-success">Actualizar</button>
             <a name="" id="" class="btn btn-secondary" href="index.php" role="button">Cancelar</a>
 
         </form>
 
 
     </div>
   </div>
   

<?php include("../../templates/footer.php"); ?>