<?php

include("../../bd.php");

if(isset($_GET['txtID'])){

    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";
    
    $sentencia=$conexion->prepare("DELETE FROM usuarios WHERE id_usuario=:id_usuario");
    $sentencia->bindParam(":id_usuario", $txtID);
    $sentencia->execute();
    header("Location:index.php");
}

include("../../templates/header.php"); 

$sentencia = $conexion->prepare("SELECT * FROM usuarios u INNER JOIN cargo c ON u.id_cargo = c.id_cargo
INNER JOIN sedes s ON u.id_sede = s.id_sede INNER JOIN grupo_sedes g ON s.id_grupo_sede=g.id_grupo_sede");
$sentencia->execute();
$lista_usuarios = $sentencia->fetchAll(PDO::FETCH_ASSOC);

?>

<br/>
   
   <h4>USUARIOS</h4>
    
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
                     <th scope="col">USUARIO</th>
                     <th scope="col">NOMBRE USUARIO</th>
                     <th scope="col">APELLIDO USUARIO</th>
                     <th scope="col">EMAIL</th>
                     <th scope="col">TELEFONO</th>
                     <th scope="col">ROL</th>
                     <th scope="col">SEDE</th>
                     <th scope="col">GRUPO SEDE</th>
                     <th scope="col">ACCIONES</th>
                 </tr>
             </thead>
             <tbody>
             <?php foreach($lista_usuarios as $registro){ ?>
                 <tr class="">
                     <td scope="row"><?php echo $registro['id_usuario'];?></td>
                     <td><?php echo $registro['usuario'];?></td>
                     <td><?php echo $registro['nombre_usuario'];?></td>
                     <td><?php echo $registro['apellido_usuario'];?></td>
                     <td><?php echo $registro['email'];?></td>
                     <td><?php echo $registro['telefono'];?></td>
                     <td><?php echo $registro['nombre_cargo'];?></td>
                     <td><?php echo $registro['nombre_sede'];?></td>
                     <td><?php echo $registro['nombre_grupo_sede'];?></td>
                     <td>
                        <a name="" id="editar_usuario" class="btn btn-info" href="editar.php?txtID=<?php echo $registro['id_usuario']; ?>" role="button">Editar</a>
                        <a name="" id="eliminar_usuario" class="btn btn-danger" href="javascript:borrar(<?php echo $registro['id_usuario']; ?>);" role="button">Borrar</a>
                     </td>
                     </td>
                 </tr>
             <?php } ?>
             </tbody>
         </table>
     </div>
     
 
     </div>
   
   </div>
<?php include("../../templates/footer.php"); ?>