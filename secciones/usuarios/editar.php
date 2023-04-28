<?php

include "../../bd.php";

if (isset($_GET['txtID'])) {

    $txtID = (isset($_GET['txtID'])) ? $_GET['txtID'] : "";

    $sentencia = $conexion->prepare("SELECT * FROM usuarios WHERE id_usuario=:id_usuario");
    $sentencia->bindParam(":id_usuario", $txtID);
    $sentencia->execute();

    $registro = $sentencia->fetch(PDO::FETCH_LAZY);

    $nombre_usuario = $registro["nombre_usuario"];
    $apellido_usuario = $registro["apellido_usuario"];
    $usuario = $registro["usuario"];
    $password = $registro["password"];
    $email = $registro["email"];
    $telefono = $registro["telefono"];
    $id_cargo = $registro["id_cargo"];
    $id_sede = $registro["id_sede"];

    //print_r($registro["id_sede"]);

    $sentencia = $conexion->prepare("SELECT * FROM cargo");
    $sentencia->execute();
    $lista_cargos = $sentencia->fetchAll(PDO::FETCH_ASSOC);

    $sentencia = $conexion->prepare("SELECT * FROM sedes");
    $sentencia->execute();
    $lista_sedes = $sentencia->fetchAll(PDO::FETCH_ASSOC);

}


if ($_POST) {

    //recolectamos los datos del metodo POST
    $txtID = (isset($_GET['txtID'])) ? $_GET['txtID'] : "";
    $nombre_usuario = (isset($_POST["nombre_usuario"]) ? $_POST["nombre_usuario"] : "");
    $apellido_usuario = (isset($_POST["apellido_usuario"]) ? $_POST["apellido_usuario"] : "");
    $usuario = (isset($_POST["usuario"]) ? $_POST["usuario"] : "");
    $password = (isset($_POST["password"]) ? $_POST["password"] : "");
    $email = (isset($_POST["email"]) ? $_POST["email"] : "");
    $telefono = (isset($_POST["telefono"]) ? $_POST["telefono"] : "");
    $id_cargo = (isset($_POST["id_cargo"]) ? $_POST["id_cargo"] : "");
    $id_sede = (isset($_POST["id_sede"]) ? $_POST["id_sede"] : "");

    print_r($id_sede);

    //Preparar la insersion de los datos
    $sentencia = $conexion->prepare("UPDATE usuarios SET
    nombre_usuario=:nombre_usuario,
    apellido_usuario=:apellido_usuario,
    usuario=:usuario,
    password=:password,
    email=:email,
    telefono=:telefono,
    id_cargo=:id_cargo,
    id_sede=:id_sede
    WHERE id_usuario=:id_usuario");

    //Asignando los valores que vienen del metodo POST ( los que vienen del formulario)
    $sentencia->bindParam(":id_usuario", $txtID);
    $sentencia->bindParam(":nombre_usuario", $nombre_usuario);
    $sentencia->bindParam(":apellido_usuario", $apellido_usuario);
    $sentencia->bindParam(":usuario", $usuario);
    $sentencia->bindParam(":password", $password);
    $sentencia->bindParam(":email", $email);
    $sentencia->bindParam(":telefono", $telefono);
    $sentencia->bindParam(":id_cargo", $id_cargo);
    $sentencia->bindParam(":id_sede", $id_sede);
    
    $sentencia->execute();
    //$mensaje = "Registro actualizado";
    //header("Location:index.php?mensaje=" . $mensaje);
}

?>


<?php include "../../templates/header.php";?>

<br/>

   <div class="card">
     <div class="card-header">
         Editar Usuario
     </div>
     <div class="card-body">

         <form action="" method="post" enctype="multipart/form-data">

         <div class="mb-3">
           <label for="txtID" class="form-label">ID:</label>
           <h5 name="txtID" id="txtID"><?php echo $txtID; ?></h5>
         </div>

             <div class="mb-3">
               <label for="nombre_usuario" class="form-label">Nombre de Usuario:</label>
               <input type="text" required
               value="<?php echo $nombre_usuario; ?>"
                 class="form-control" name="nombre_usuario" id="nombre_usuario" aria-describedby="helpId" placeholder="Nombre">
             </div>

             <div class="mb-3">
               <label for="apellido_usuario" class="form-label">Apellido:</label>
               <input type="text" required
               value="<?php echo $apellido_usuario; ?>"
                 class="form-control" name="apellido_usuario" id="apellido_usuario" aria-describedby="helpId" placeholder="Apellido">
             </div>

             <div class="mb-3">
               <label for="usuario" class="form-label">Usuario:
               <p class="form-text text-muted">(Usuario de inicio de sesión)</p></label>
               <input type="text" required
               value="<?php echo $usuario; ?>"
                 class="form-control" name="usuario" id="usuario" aria-describedby="helpId" placeholder="Nombre Usuario">
             </div>

             <div class="mb-3">
               <label for="password" class="form-label">Password</label>
               <input type="password" required
               value="<?php echo $password; ?>"
               class="form-control" name="password" id="password" placeholder="*****">
             </div>

             <div class="mb-3">
               <label for="email" class="form-label">Email</label>
               <input type="email"
               value="<?php echo $email; ?>"
               class="form-control" name="email" id="email" aria-describedby="emailHelpId" placeholder="abc@mail.com">
             </div>

             <div class="mb-3">
               <label for="telefono" class="form-label">Telefono:</label>
               <input type="telefono"
               value="<?php echo $telefono; ?>"
               class="form-control" name="telefono" id="telefono" aria-describedby="emailHelpId" placeholder="3153457865">
             </div>

             <div class="mb-3">
                 <label for="id_cargo" class="form-label">Rol:</label>
                 <select required class="form-select form-select-sm" name="id_cargo" id="id_cargo">
                   <?php foreach ($lista_cargos as $registro) {?>
                     <option <?php echo ($id_cargo == $registro['id_cargo']) ? "selected" : ""; ?>
                     value="<?php echo $registro['id_cargo']; ?>">
                       <?php echo $registro['nombre_cargo']; ?>
                     </option>
                   <?php }?>
                 </select>
             </div>

             <div class="mb-3">
                 <label for="id_sede" class="form-label">Rol:</label>
                 <select required class="form-select form-select-sm" name="id_sede" id="id_sede">
                   <?php foreach ($lista_sedes as $registro) {?>
                     <option <?php echo ($id_sede == $registro['id_sede']) ? "selected" : ""; ?>
                     value="<?php echo $registro['id_sede']; ?>">
                       <?php echo $registro['nombre_sede']; ?>
                     </option>
                   <?php }?>
                 </select>
             </div>

                   </br>
             <button type="submit" class="btn btn-success">Actualizar</button>
             <a name="" id="" class="btn btn-secondary" href="index.php" role="button">Cancelar</a>

         </form>


     </div>
   </div>


<?php include "../../templates/footer.php";?>