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
    $url=$registro["url"];
    $cam=$registro["cam"];

}

if($_POST){

  //recolectamos los datos del metodo POST
  $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";
  $nombre_cuenta=(isset($_POST["nombre_cuenta"])?$_POST["nombre_cuenta"]:"");
  $precio_cuenta=(isset($_POST["precio_cuenta"])?$_POST["precio_cuenta"]:"");
  $url=(isset($_POST["url"])?$_POST["url"]:"");
  $cam=(isset($_POST["cam"])?$_POST["cam"]:"");

  //Preparar la insersion de los datos

  if($url == ""){
    $sentencia=$conexion->prepare("UPDATE cuentas SET nombre_cuenta=:nombre_cuenta, precio_cuenta=:precio_cuenta WHERE id_cuenta=:id_cuenta ");
  }else{
    $sentencia=$conexion->prepare("UPDATE cuentas SET nombre_cuenta=:nombre_cuenta, precio_cuenta=:precio_cuenta, url=:url, cam=:cam WHERE id_cuenta=:id_cuenta ");
  }
  
  $sentencia->bindParam(":nombre_cuenta", $nombre_cuenta);
  $sentencia->bindParam(":precio_cuenta", $precio_cuenta);
  if($url != ""){
    $sentencia->bindParam(":url", $url);
    $sentencia->bindParam(":cam", $cam);
  }
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

             <?php if($_SESSION['id_cargo']==1){ ?>
             <div class="mb-3">
               <label for="url" class="form-label">URL RTSP:</label>
               <input type="text"
               value="<?php  if(isset($url)){echo $url;} else { echo "";};?>"
                 class="form-control" name="url" id="url" aria-describedby="helpId" placeholder="rtsp://...">
             </div>
             <?php } ?>

             <?php if($_SESSION['id_cargo']==1){ ?>
             <div class="mb-3">
               <label for="cam" class="form-label">URL CAM:</label>
               <input type="text"
               value="<?php  if(isset($cam)){echo $cam;} else { echo "";};?>"
                 class="form-control" name="cam" id="cam" aria-describedby="helpId" placeholder="../ffmpeg...">
             </div>
             <?php } ?>

             <br/>
 
             <button type="submit" class="btn btn-success">Actualizar</button>
             <a name="" id="" class="btn btn-secondary" href="index.php" role="button">Cancelar</a>
 
         </form>
 
 
     </div>
   </div>
   

<?php include("../../templates/footer.php"); ?>