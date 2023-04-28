<?php

include("../../bd.php");



if($_POST){

  //recolectamos los datos del metodo POST
  $valor=(isset($_POST["valor"])?$_POST["valor"]:"");
  $id_sede = (isset($_POST["id_sede"]) ? $_POST["id_sede"] : "");

  $sentencia = $conexion->prepare("SELECT * FROM cajas WHERE id_sede=:id_sede
  ORDER BY id_caja DESC LIMIT 1");
  $sentencia->bindParam(":id_sede", $id_sede);
  $sentencia->execute();
  $registro_caja = $sentencia->fetch(PDO::FETCH_LAZY);

  //Preparar la insersion de los datos
  $sentencia=$conexion->prepare("INSERT INTO cajas(fecha_apertura, fecha_cierre, valor, valor_acomulado, estado, id_sede) 
  VALUES (CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP(), -:valor, " . $registro_caja['valor_acomulado'] . "-:valor, 0, :id_sede)");
  
  //Asignando los valores que vienen del metodo POST ( los que vienen del formulario)
  $sentencia->bindParam(":valor", $valor);
  $sentencia->bindParam(":id_sede", $id_sede);
  $sentencia->execute();
  $mensaje="Registro agregado";
  header("Location:index.php?mensaje=".$mensaje);
}

include("../../templates/header.php");

$sentencia = $conexion->prepare("SELECT * FROM sedes WHERE id_sede= " . $_SESSION['id_sede']);
$sentencia->execute();
$registro = $sentencia->fetch(PDO::FETCH_LAZY);

$sentencia = $conexion->prepare("SELECT * FROM cajas WHERE id_sede='" . $_SESSION['id_sede'] . "'" .
" ORDER BY id_caja DESC LIMIT 1");
$sentencia->execute();
$registro_caja = $sentencia->fetch(PDO::FETCH_LAZY);

$nombre_sede = $registro["nombre_sede"];
$id_sede = $registro["id_sede"];

?>

<br/>
   
   <div class="card">
     <div class="card-header">
         Retirar Dinero de Caja
     </div>
     <div class="card-body">
         
         <form action="" method="post" enctype="multipart/form-data">

             <div class="mb-3">
               <label for="valor" class="form-label">Valor:</label>
               <input type="text" required
                 class="form-control" name="valor" id="valor" aria-describedby="helpId" placeholder="$10.000">
             </div>

             <div class="mb-3" style="display:none;">
              <label for="id_sede" class="form-label">Id Sede:</label>
              <input type="text"
              value="<?php echo $id_sede; ?>"
                class="form-control" readonly required name="id_sede" id="id_sede" aria-describedby="helpId">
            </div>

         <div class="mb-3">
           <label for="nombre_sede" class="form-label">Sede:</label>
           <h5 name="nombre_sede" id="nombre_sede"> <?php echo $nombre_sede; ?></h5>
         </div>

             <br/>
 
             <?php if($registro_caja["estado"]==0){ ?>
              <button type="submit" class="btn btn-danger">Retirar Dinero</button>
            <?php } else { ?>
              <label for="mensaje" class="form-label">Cierra la caja para poder retirar dinero.</label>
              </br>
            <?php } ?>
             <a name="" id="" class="btn btn-secondary" href="index.php" role="button">Cancelar</a>
 
         </form>
 
     </div>
   </div>


<?php include("../../templates/footer.php"); ?>