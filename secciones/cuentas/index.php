<?php

include("../../bd.php");

if(isset($_GET['txtID'])){

    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";
    
    $sentencia=$conexion->prepare("UPDATE cuentas SET nombre_cuenta = CONCAT('borrada_', nombre_cuenta) WHERE id_cuenta =:id_cuenta");
    $sentencia->bindParam(":id_cuenta", $txtID);
    $sentencia->execute();
    $mensaje="Registro eliminado";
    header("Location:index.php?mensaje=".$mensaje);
}

include("../../templates/header.php");

$sentencia=$conexion->prepare("SELECT * FROM cuentas c 
INNER JOIN sedes s ON c.id_sede = s.id_sede 
WHERE c.id_sede = " . $_SESSION['id_sede'] . " AND c.nombre_cuenta NOT LIKE 'borrada_%'
GROUP BY c.id_cuenta");
$sentencia->execute();
$lista_cuentas=$sentencia->fetchAll(PDO::FETCH_ASSOC);

?>


<br/>
   
   <h4>CUENTAS</h4>
    
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
                     <th scope="col">PRECIO POR HORA</th>
                     <th scope="col">ESTADO CUENTA</th>
                     <?php if($_SESSION['id_cargo']!=2){ ?> <th scope="col">ACCIONES</th> <?php } ?>
                 </tr>
             </thead>
             <tbody>

             <?php foreach($lista_cuentas as $registro){ ?>

                 <tr class="">
                     <td scope="row"><?php echo $registro['id_cuenta'];?></td>
                     <td><?php echo $registro['nombre_cuenta'];?></td>
                     <td>$ <?php echo number_format($registro['precio_cuenta'], 1); ?></td>
                     <td>
                        <?php 
                          if ($registro['estado'] == 1){
                            echo "Abierta";
                          }else{
                            echo "Cerrada";
                          }
                        ?>
                     </td>
                     <?php if($_SESSION['id_cargo']!=2){ ?>
                     <td>
                        <a name="" id="editar_cuenta" class="btn btn-info" href="editar.php?txtID=<?php echo $registro['id_cuenta']; ?>" role="button">Editar</a>
                        <a name="" id="eliminar_cuenta" class="btn btn-danger" href="javascript:borrar(<?php echo $registro['id_cuenta']; ?>);" role="button">Borrar</a>
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