<?php

include("../../bd.php");

if(isset($_GET['txtID'])){

    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";
    
    $sentencia=$conexion->prepare("DELETE FROM proveedores WHERE id_proovedor=:id_proovedor");
    $sentencia->bindParam(":id_proovedor", $txtID);
    $sentencia->execute();
    header("Location:index.php");
}

$sentencia=$conexion->prepare("SELECT * FROM `proveedores`");
$sentencia->execute();
$lista_proovedores=$sentencia->fetchAll(PDO::FETCH_ASSOC);

?>

<?php include("../../templates/header.php"); ?>

<br/>
   
   <h4>PROOVEDORES</h4>
    
   <br/>
 
   <div class="card">
     <div class="card-header">
         <a name="" id="" class="btn btn-primary" 
         href="crear.php" 
         role="button">
         Agregar</a>
     </div>
     <div class="card-body">
        
     <div class="table-responsive-sm">
         <table class="table" id="tabla_id">
             <thead>
                 <tr>
                     <th scope="col">ID</th>
                     <th scope="col">NOMBRE</th>
                     <th scope="col">ACCIONES</th>
                 </tr>
             </thead>
             <tbody>

             <?php foreach($lista_proovedores as $registro){ ?>

                 <tr class="">
                     <td scope="row"><?php echo $registro['id_proovedor'];?></td>
                     <td><?php echo $registro['nombre_proovedor'];?></td>
                     <td>
                        <a name="" id="editar_proovedor" class="btn btn-info" href="editar.php?txtID=<?php echo $registro['id_proovedor']; ?>" role="button">Editar</a>
                        <a name="" id="eliminar_proovedor" class="btn btn-danger" href="javascript:borrar(<?php echo $registro['id_proovedor']; ?>);" role="button">Borrar</a>
                     </td>
                 </tr>
                     
             <?php } ?>

             </tbody>
         </table>
     </div>
     
 
     </div>
   
   </div>
   

<?php include("../../templates/footer.php"); ?>