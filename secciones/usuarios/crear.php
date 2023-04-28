<?php

include("../../bd.php");

if($_POST){

  //recolectamos los datos del metodo POST
  $nombre_usuario=(isset($_POST["nombre_usuario"])?$_POST["nombre_usuario"]:"");
  $apellido_usuario=(isset($_POST["apellido_usuario"])?$_POST["apellido_usuario"]:"");
  $usuario=(isset($_POST["usuario"])?$_POST["usuario"]:"");
  $password=(isset($_POST["password"])?$_POST["password"]:"");
  $email=(isset($_POST["email"])?$_POST["email"]:"");
  $telefono=(isset($_POST["telefono"])?$_POST["telefono"]:"");
  $id_cargo=(isset($_POST["id_cargo"])?$_POST["id_cargo"]:"");
  $id_sede=(isset($_POST["id_sede"])?$_POST["id_sede"]:"");

  //Preparar la insersion de los datos
  $sentencia=$conexion->prepare("INSERT INTO usuarios(nombre_usuario, apellido_usuario, usuario, password, email, telefono, id_cargo, id_sede) 
  VALUES (:nombre_usuario, :apellido_usuario, :usuario, :password, :email, :telefono, :id_cargo, :id_sede)");
  
  //Asignando los valores que vienen del metodo POST ( los que vienen del formulario)
  $sentencia->bindParam(":nombre_usuario", $nombre_usuario);
  $sentencia->bindParam(":apellido_usuario", $apellido_usuario);
  $sentencia->bindParam(":usuario", $usuario);
  $sentencia->bindParam(":password", $password);
  $sentencia->bindParam(":email", $email);
  $sentencia->bindParam(":telefono", $telefono);
  $sentencia->bindParam(":id_cargo", $id_cargo);
  $sentencia->bindParam(":id_sede", $id_sede);
  $sentencia->execute();
  $mensaje="Registro agregado";
  header("Location:index.php?mensaje=".$mensaje);
}

  $sentencia = $conexion->prepare("SELECT * FROM cargo");
  $sentencia->execute();
  $lista_cargos = $sentencia->fetchAll(PDO::FETCH_ASSOC);

  $sentencia = $conexion->prepare("SELECT * FROM sedes");
  $sentencia->execute();
  $lista_sedes = $sentencia->fetchAll(PDO::FETCH_ASSOC);

?>

<?php include("../../templates/header.php"); ?>

<br/>
   
<div class="card">
     <div class="card-header">
         Crear Usuario
     </div>
     <div class="card-body">

         <form action="" method="post" enctype="multipart/form-data">

             <div class="mb-3">
               <label for="nombre_usuario" class="form-label">Nombre de Usuario:</label>
               <input type="text"
                 class="form-control" required name="nombre_usuario" id="nombre_usuario" aria-describedby="helpId" placeholder="Nombre">
             </div>

             <div class="mb-3">
               <label for="apellido_usuario" class="form-label">Apellido:</label>
               <input type="text"
                 class="form-control" required name="apellido_usuario" id="apellido_usuario" aria-describedby="helpId" placeholder="Apellido">
             </div>

             <div class="mb-3">
               <label for="usuario" class="form-label">Usuario:
               <p class="form-text text-muted">(Usuario de inicio de sesión)</p></label>
               <input type="text"
                 class="form-control" required name="usuario" id="usuario" aria-describedby="helpId" placeholder="Nombre Usuario">
             </div>

             <div class="mb-3">
               <label for="password" class="form-label">Password</label>
               <input type="password"
               class="form-control" required name="password" id="password" placeholder="*****">
             </div>

             <div class="mb-3">
               <label for="email" class="form-label">Email</label>
               <input type="email"
               class="form-control" name="email" id="email" aria-describedby="emailHelpId" placeholder="abc@mail.com">
             </div>

             <div class="mb-3">
               <label for="telefono" class="form-label">Telefono:</label>
               <input type="telefono"
               class="form-control" name="telefono" id="telefono" aria-describedby="emailHelpId" placeholder="315...">
             </div>

             <div class="mb-3">
                <label for="id_cargo" class="form-label">Categoria</label>
                <select required class="form-select form-select-sm" name="id_cargo" id="id_cargo">
                  <?php foreach ($lista_cargos as $registro) {?>
                    <option value="<?php echo $registro['id_cargo']; ?>">
                      <?php echo $registro['nombre_cargo']; ?>
                    </option>
                  <?php }?>
                </select>
            </div>

            <div class="mb-3">
                <label for="id_sede" class="form-label">Sede:</label>
                <select required class="form-select form-select-sm" name="id_sede" id="id_sede">
                  <?php foreach ($lista_sedes as $registro) {?>
                    <option value="<?php echo $registro['id_sede']; ?>">
                      <?php echo $registro['nombre_sede']; ?>
                    </option>
                  <?php }?>
                </select>
            </div>

                   </br>
             <button type="submit" class="btn btn-success">Agregar Registro</button>
             <a name="" id="" class="btn btn-secondary" href="index.php" role="button">Cancelar</a>

         </form>


     </div>
   </div>

<?php include("../../templates/footer.php"); ?>