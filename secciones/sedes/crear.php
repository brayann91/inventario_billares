<?php

include("../../bd.php");

if($_POST){

  //recolectamos los datos del metodo POST
  $nombre_sede=(isset($_POST["nombre_sede"])?$_POST["nombre_sede"]:"");
  $direccion_sede=(isset($_POST["direccion_sede"])?$_POST["direccion_sede"]:"");
  $telefono_sede=(isset($_POST["telefono_sede"])?$_POST["telefono_sede"]:"");
  $id_grupo_sede=(isset($_POST["id_grupo_sede"])?$_POST["id_grupo_sede"]:"");

  //Preparar la insersion de los datos
  $sentencia=$conexion->prepare("INSERT INTO sedes(nombre_sede, direccion_sede, telefono_sede, id_grupo_sede) 
  VALUES (:nombre_sede, :direccion_sede, :telefono_sede, :id_grupo_sede)");
  
  //Asignando los valores que vienen del metodo POST ( los que vienen del formulario)
  $sentencia->bindParam(":nombre_sede", $nombre_sede);
  $sentencia->bindParam(":direccion_sede", $direccion_sede);
  $sentencia->bindParam(":telefono_sede", $telefono_sede);
  $sentencia->bindParam(":id_grupo_sede", $id_grupo_sede);
  $sentencia->execute();
  $mensaje="Registro agregado";
  header("Location:index.php?mensaje=".$mensaje);
}

$sentencia = $conexion->prepare("SELECT * FROM grupo_sedes");
$sentencia->execute();
$lista_sedes = $sentencia->fetchAll(PDO::FETCH_ASSOC);

?>

<?php include("../../templates/header.php"); ?>

<br/>
   
   <div class="card">
     <div class="card-header">
         Agregar Sede
     </div>
     <div class="card-body">
         
         <form action="" method="post" enctype="multipart/form-data">
 
             <div class="mb-3">
               <label for="nombre_sede" class="form-label">Nombre Sede:</label>
               <input type="text" required
                 class="form-control" name="nombre_sede" id="nombre_sede" aria-describedby="helpId" placeholder="Nombre Sede">
             </div>

             <div class="mb-3">
               <label for="direccion_sede" class="form-label">Direccion:</label>
               <input type="text" required
                 class="form-control" name="direccion_sede" id="direccion_sede" aria-describedby="helpId" placeholder="Dirección Sede">
             </div>

             <div class="mb-3">
               <label for="telefono_sede" class="form-label">Telefono:</label>
               <input type="text" required
                 class="form-control" name="telefono_sede" id="telefono_sede" aria-describedby="helpId" placeholder="Telefono Sede">
             </div>

             <div class="mb-3">
                <label for="id_grupo_sede" class="form-label">Grupo Sede</label>
                <select class="form-select form-select-sm" name="id_grupo_sede" id="id_grupo_sede" required>
                  <?php foreach ($lista_sedes as $registro) {?>
                      <option value="<?php echo $registro['id_grupo_sede']; ?>">
                        <?php echo $registro['nombre_grupo_sede']; ?>
                      </option>
                  <?php }?>
                </select>
            </div>

             <br/>
 
             <button type="submit" class="btn btn-success">Agregar registro</button>
             <a name="" id="" class="btn btn-secondary" href="index.php" role="button">Cancelar</a>

         </form> 
 
     </div>
   </div>


<?php include("../../templates/footer.php"); ?>