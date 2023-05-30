<?php

include "../../bd.php";

if ($_POST) {

    session_start();
    //recolectamos los datos del metodo POST
    $nombre_categoria = (isset($_POST["nombre_categoria"]) ? $_POST["nombre_categoria"] : "");

    //Preparar la insersion de los datos
    $sentencia = $conexion->prepare("INSERT INTO categorias(nombre_categoria, id_sede) VALUES (:nombre_categoria, :id_sede)");
    $sentencia->bindParam(":nombre_categoria", $nombre_categoria);
    $sentencia->bindParam(":id_sede", $_SESSION['id_sede']);
    $sentencia->execute();
    $mensaje = "Registro agregado";
    header("Location:index.php?mensaje=" . $mensaje);
}

include "../../templates/header.php";

?>


<br/>

   <div class="card">
     <div class="card-header">
         Agregar Categoria
     </div>
     <div class="card-body">
         <form action="" method="post" enctype="multipart/form-data">
             <div class="mb-3">
               <label for="nombre_categoria" class="form-label">Nombre Categoria:</label>
               <input type="text" required
                 class="form-control" name="nombre_categoria" id="nombre_categoria" aria-describedby="helpId" placeholder="Nombre Categoria">
             </div>
             <br/>
             <button type="submit" class="btn btn-success">Agregar registro</button>
             <a name="" id="" class="btn btn-secondary" href="index.php" role="button">Cancelar</a>
         </form>
     </div>
   </div>


<?php include "../../templates/footer.php";?>