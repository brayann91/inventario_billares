<?php

include "../../bd.php";

if (isset($_GET['txtID'])) {

    $txtID = (isset($_GET['txtID'])) ? $_GET['txtID'] : "";

    $sentencia = $conexion->prepare("DELETE FROM sedes WHERE id_sede=:id_sede");
    $sentencia->bindParam(":id_sede", $txtID);
    $sentencia->execute();
    $mensaje = "Registro eliminado";
    header("Location:index.php?mensaje=" . $mensaje);
}

$sentencia = $conexion->prepare("SELECT * FROM sedes");
$sentencia->execute();
$lista_sedes = $sentencia->fetchAll(PDO::FETCH_ASSOC);

?>

<?php include "../../templates/header.php";?>

<br/>

   <h4>SEDES</h4>

   <br/>

   <div class="card">
     <div class="card-header">
     <?php if ($_SESSION['id_cargo'] != 2) {?>
        <a name="" id="" class="btn btn-primary"
        href="crear.php"
        role="button">
        Agregar</a>
    <?php }?>
     </div>
     <div class="card-body">

     <div class="table-responsive-sm">
         <table class="table" id="tabla_id">
             <thead>
                 <tr>
                     <th scope="col">ID</th>
                     <th scope="col">NOMBRE</th>
                     <th scope="col">DIRECCION</th>
                     <th scope="col">TELEFONO</th>
                     <?php if ($_SESSION['id_cargo'] != 2) {?>
                     <th scope="col">ACCIONES</th> <?php }?>
                 </tr>
             </thead>
             <tbody>

             <?php foreach ($lista_sedes as $registro) {?>

                 <tr class="">
                     <td scope="row"><?php echo $registro['id_sede']; ?></td>
                     <td><?php echo $registro['nombre_sede']; ?></td>
                     <td><?php echo $registro['direccion_sede']; ?></td>
                     <td><?php echo $registro['telefono_sede']; ?></td>
                     <?php if ($_SESSION['id_cargo'] != 2) {?>
                     <td>
                        <a name="" id="editar_sede" class="btn btn-info" href="editar.php?txtID=<?php echo $registro['id_sede']; ?>" role="button">Editar</a>
                         <a name="" id="eliminar_sede" class="btn btn-danger" href="javascript:borrar(<?php echo $registro['id_sede']; ?>);" role="button">Borrar</a>
                     </td>
                     <?php }?>
                 </tr>

             <?php }?>

             </tbody>
         </table>
     </div>


     </div>

   </div>

<?php include "../../templates/footer.php";?>