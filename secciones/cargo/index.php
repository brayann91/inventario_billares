<?php

include("../../bd.php");

if(isset($_GET['txtID'])){

    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";
    
    $sentencia=$conexion->prepare("DELETE FROM cargo WHERE id_cargo=:id_cargo");
    $sentencia->bindParam(":id_cargo", $txtID);
    $sentencia->execute();
    $mensaje="Registro eliminado";
    header("Location:index.php?mensaje=".$mensaje);
}

$sentencia=$conexion->prepare("SELECT * FROM cargo");
$sentencia->execute();
$lista_categorias=$sentencia->fetchAll(PDO::FETCH_ASSOC);

?>

<?php include("../../templates/header.php"); ?>

<br/>
   
   <h4>ROLES DE USUARIO</h4>
    
   <br/>
 
   <div class="card">
     <div class="card-header">
     <?php if($_SESSION['id_cargo']!=2){ ?>
        <a name="" id="" class="btn btn-primary" 
        href="crear.php" 
        role="button">
        Agregar</a>
    <?php } ?>
     </div>
     <div class="card-body">
        
     <div class="table-responsive-sm">
         <table class="table" id="tabla_id">
             <thead>
                 <tr>
                     <th scope="col">ID</th>
                     <th scope="col">NOMBRE</th>
                     <?php if($_SESSION['id_cargo']!=2){ ?> <th scope="col">ACCIONES</th> <?php } ?>
                 </tr>
             </thead>
             <tbody>

             <?php foreach($lista_categorias as $registro){ ?>

                 <tr class="">
                     <td scope="row"><?php echo $registro['id_cargo'];?></td>
                     <td><?php echo $registro['nombre_cargo'];?></td>
                     <?php if($_SESSION['id_cargo']!=2){ ?>
                     <td>
                        <a name="" id="editar_categoria" class="btn btn-info" href="editar.php?txtID=<?php echo $registro['id_cargo']; ?>" role="button">Editar</a>
                        <a name="" id="eliminar_categoria" class="btn btn-danger" href="javascript:borrar(<?php echo $registro['id_cargo']; ?>);" role="button">Borrar</a>
                     </td>
                     <?php } ?>
                 </tr>
                     
             <?php } ?>

             </tbody>
         </table>
     </div>
     
 
     </div>
   
   </div>

<?php include("../../templates/footer.php"); ?>