<?php

include "../../bd.php";

if (isset($_GET['txtID'])) {

    $txtID = (isset($_GET['txtID'])) ? $_GET['txtID'] : "";

    $sentencia = $conexion->prepare("SELECT * FROM tiempos t INNER JOIN cuentas c ON t.id_cuenta=c.id_cuenta WHERE id_tiempo=:id_tiempo");
    $sentencia->bindParam(":id_tiempo", $txtID);
    $sentencia->execute();

    $registro = $sentencia->fetch(PDO::FETCH_LAZY);

    $nombre_cuenta = $registro["nombre_cuenta"];
    $precio_cuenta = $registro["precio_cuenta"];
    $fecha_inicio = $registro["fecha_inicio"];
    $fecha_fin = $registro["fecha_fin"];
    $tiempo_invertido = $registro["tiempo_invertido"];
    $precio_final = $registro["precio_final"];
    $id_cuenta = $registro["id_cuenta"];

    $sentencia = $conexion->prepare("SELECT * FROM `cuentas`");
    $sentencia->execute();
    $lista_cuentas = $sentencia->fetchAll(PDO::FETCH_ASSOC);

}

if ($_POST) {

    //recolectamos los datos del metodo POST
    $txtID = (isset($_GET['txtID'])) ? $_GET['txtID'] : "";
    $fecha_inicio = (isset($_POST["fecha_inicio"]) ? $_POST["fecha_inicio"] : "");
    $fecha_fin = (isset($_POST["fecha_fin"]) ? $_POST["fecha_fin"] : "");
    $tiempo_invertido = (isset($_POST["tiempo_invertido"]) ? $_POST["tiempo_invertido"] : "");
    $precio_final = (isset($_POST["precio_final"]) ? $_POST["precio_final"] : "");
    $id_cuenta = (isset($_POST["id_cuenta"]) ? $_POST["id_cuenta"] : "");

    //Preparar la insersion de los datos
    $sentencia = $conexion->prepare("UPDATE tiempos t
    INNER JOIN cuentas c ON t.id_cuenta=c.id_cuenta
    SET t.fecha_inicio=:fecha_inicio,
        t.fecha_fin=:fecha_fin,
        t.tiempo_invertido=SEC_TO_TIME(TIMESTAMPDIFF(SECOND, :fecha_inicio, :fecha_fin)),
        t.precio_final=(TIME_TO_SEC(SEC_TO_TIME(TIMESTAMPDIFF(SECOND, :fecha_inicio, :fecha_fin))) / 3600) * c.precio_cuenta,
        t.id_cuenta=:id_cuenta
    WHERE id_tiempo=:id_tiempo");

    //Asignando los valores que vienen del metodo POST ( los que vienen del formulario)
    $sentencia->bindParam(":id_tiempo", $txtID);
    $sentencia->bindParam(":fecha_inicio", $fecha_inicio);
    $sentencia->bindParam(":fecha_fin", $fecha_fin);
    $sentencia->bindParam(":tiempo_invertido", $tiempo_invertido);
    $sentencia->bindParam(":precio_final", $precio_final);
    $sentencia->bindParam(":id_cuenta", $id_cuenta);
    $sentencia->execute();

    $mensaje = "Registro actualizado";
    header("Location:index.php?mensaje=" . $mensaje);
}

?>

<?php include "../../templates/header.php";?>


<br/>

   <div class="card">
     <div class="card-header">
         Actualizar tiempos
     </div>
     <div class="card-body">

         <form action="" method="post" enctype="multipart/form-data">

         <div class="mb-3">
           <label for="txtID" class="form-label">ID:</label>
           <h5 name="txtID" id="txtID"><?php echo $txtID;?></h5>
         </div>

             <div class="mb-3">
               <label for="nombre_cuenta" class="form-label">Nombre Cuenta:</label>
               <h5 name="nombre_cuenta" id="nombre_cuenta"><?php echo $nombre_cuenta;?></h5>
             </div>

             <div class="mb-3">
               <label for="precio" class="form-label">Precio por Hora:</label>
               <h5 name="precio_cuenta" id="precio_cuenta">$ <?php echo number_format($registro['precio_cuenta'], 1); ?></h5>
             </div>

             <div class="form-group">
                <label for="fecha_inicio">Fecha Inicio:</label>
                <input type="datetime-local" class="form-control" id="fecha_inicio" value="<?php echo $fecha_inicio; ?>" name="fecha_inicio" required>
            </div>

            </br>

             <div class="form-group">
                <label for="fecha_fin">Fecha Fin:</label>
                <input type="datetime-local" class="form-control" id="fecha_fin" value="<?php echo $fecha_fin; ?>" name="fecha_fin">
            </div>

            </br>

             <div class="mb-3">
              <label for="tiempo_invertido" class="form-label">Tiempo Invertido:</label>
              <small class="text-muted">(Este tiempo se modificara cuando se actualice el formulario)</small>
              <h5 name="tiempo_invertido" id="tiempo_invertido"><?php echo $tiempo_invertido;?></h5>
            </div>

            </br>

            <div class="mb-3">
              <label for="precio_final" class="form-label">Precio Total:</label>
              <small class="text-muted">(Este valor se modificara cuando se actualice el formulario)</small>
              <h5 name="precio_final" id="precio_final">$ <?php echo number_format($registro['precio_final'], 1); ?></h5>
            </div>

            </br>

             <div class="mb-3">
                 <label for="id_cuenta" class="form-label">Cuenta:</label>
                 <select class="form-select form-select-sm" name="id_cuenta" id="id_cuenta" required>
                   <?php foreach ($lista_cuentas as $registro) {?>
                     <option <?php echo ($id_cuenta == $registro['id_cuenta']) ? "selected" : ""; ?>
                     value="<?php echo $registro['id_cuenta']; ?>">
                       <?php echo $registro['nombre_cuenta']; ?>
                     </option>
                   <?php }?>
                 </select>
             </div>

             <br/>

             <button type="submit" class="btn btn-success">Actualizar</button>
             <a name="" id="" class="btn btn-secondary" href="index.php" role="button">Cancelar</a>

         </form>


     </div>
   </div>


<?php include "../../templates/footer.php";?>