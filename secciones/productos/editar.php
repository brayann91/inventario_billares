<?php

include "../../bd.php";

if (isset($_GET['txtID'])) {

    $txtID = (isset($_GET['txtID'])) ? $_GET['txtID'] : "";

    $sentencia = $conexion->prepare("SELECT * FROM productos WHERE id_producto=:id_producto");
    $sentencia->bindParam(":id_producto", $txtID);
    $sentencia->execute();

    $registro = $sentencia->fetch(PDO::FETCH_LAZY);

    $nombre_producto = $registro["nombre_producto"];
    $precio = $registro["precio"];
    $precio_compra = $registro["precio_compra"];
    $image = $registro["image"];
    $id_categoria = $registro["id_categoria"];
    $id_proovedor = $registro["id_proovedor"];

    $sentencia = $conexion->prepare("SELECT * FROM `categorias`");
    $sentencia->execute();
    $lista_categorias = $sentencia->fetchAll(PDO::FETCH_ASSOC);

    $sentencia = $conexion->prepare("SELECT * FROM `proveedores`");
    $sentencia->execute();
    $lista_proovedores = $sentencia->fetchAll(PDO::FETCH_ASSOC);

}

if ($_POST) {

    //recolectamos los datos del metodo POST
    $txtID = (isset($_GET['txtID'])) ? $_GET['txtID'] : "";
    $nombre_producto = (isset($_POST["nombre_producto"]) ? $_POST["nombre_producto"] : "");
    $precio = (isset($_POST["precio"]) ? $_POST["precio"] : "");
    $precio_compra = (isset($_POST["precio_compra"]) ? $_POST["precio_compra"] : "");
    //$image=(isset($_FILES["foto_producto"]['name'])?$_FILES["foto_producto"]['name']:"");
    $id_categoria = (isset($_POST["id_categoria"]) ? $_POST["id_categoria"] : "");
    $id_proovedor = (isset($_POST["id_proovedor"]) ? $_POST["id_proovedor"] : "");

    //Preparar la insersion de los datos
    $sentencia = $conexion->prepare("UPDATE productos SET
    nombre_producto=:nombre_producto,
    precio=:precio,
    precio_compra=:precio_compra,
    id_categoria=:id_categoria,
    id_producto=:id_producto,
    updated_at=now()
    WHERE id_producto=:id_producto");

    //Asignando los valores que vienen del metodo POST ( los que vienen del formulario)
    $sentencia->bindParam(":id_producto", $txtID);
    $sentencia->bindParam(":nombre_producto", $nombre_producto);
    $sentencia->bindParam(":precio", $precio);
    $sentencia->bindParam(":precio_compra", $precio_compra);
    $sentencia->bindParam(":id_categoria", $id_categoria);
    $sentencia->bindParam(":id_proovedor", $id_proovedor);
    $sentencia->execute();

    $image = (isset($_FILES["foto_producto"]['name']) ? $_FILES["foto_producto"]['name'] : "");

    $fecha_foto = new DateTime();

    $nombreArchivo_foto = ($image != '') ? $fecha_foto->getTimestamp() . "_" . $_FILES["foto_producto"]['name'] : "";
    $tmp_foto = $_FILES["foto_producto"]['tmp_name'];
    if ($tmp_foto != '') {
        move_uploaded_file($tmp_foto, "./" . $nombreArchivo_foto);

        //Buscar la imagen de producto
        $sentencia = $conexion->prepare("SELECT image FROM `productos` WHERE id_producto=:id_producto");
        $sentencia->bindParam(":id_producto", $txtID);
        $sentencia->execute();
        $registro_recuperado = $sentencia->fetch(PDO::FETCH_LAZY);

        if (isset($registro_recuperado["image"]) && $registro_recuperado["image"] != "") {
            if (file_exists("./" . $registro_recuperado["image"])) {
                unlink("./" . $registro_recuperado["image"]);
            }
        }

        $sentencia = $conexion->prepare("UPDATE productos SET image=:image WHERE id_producto=:id_producto");
        $sentencia->bindParam(":image", $nombreArchivo_foto);
        $sentencia->bindParam(":id_producto", $txtID);
        $sentencia->execute();
    }

    $mensaje = "Registro actualizado";
    header("Location:index.php?mensaje=" . $mensaje);

}

?>

<?php include "../../templates/header.php";?>

<br/>

   <div class="card">
     <div class="card-header">
         Agregar Productos
     </div>
     <div class="card-body">

         <form action="" method="post" enctype="multipart/form-data">

         <div class="mb-3">
           <label for="txtID" class="form-label">ID:</label>
           <h5 name="txtID" id="txtID"><?php echo $txtID;?></h5>
         </div>

             <div class="mb-3">
               <label for="foto_producto" class="form-label">Foto Producto:</label>
                <br/>
               <img width="60" height="60"
                src="<?php echo $image; ?>"
                class="img-fluid rounded" alt="">
                <br/><br/>
               <input type="file"
                 class="form-control" name="foto_producto" id="foto_producto" aria-describedby="helpId" placeholder="Foto">
             </div>

             <div class="mb-3">
               <label for="nombre_producto" class="form-label">Nombre Producto:</label>
               <input type="text"
               value="<?php echo $nombre_producto; ?>"
                 class="form-control" required name="nombre_producto" id="nombre_producto" aria-describedby="helpId" placeholder="Nombre Producto">
             </div>

             <div class="mb-3">
               <label for="precio" class="form-label">Precio por Venta:</label>
               <input type="text"
               value="<?php echo $precio; ?>"
                 class="form-control" required name="precio" id="precio" aria-describedby="helpId" placeholder="$ 10.000">
             </div>

             <div class="mb-3">
              <label for="precio_compra" class="form-label">Precio de Compra:</label>
              <input type="text"
              value="<?php echo $precio_compra; ?>"
                class="form-control" required name="precio_compra" id="precio_compra" aria-describedby="helpId" placeholder="$ 10.000">
            </div>

             <div class="mb-3">
                 <label for="id_categoria" class="form-label">Categoria:</label>
                 <select class="form-select form-select-sm" name="id_categoria" id="id_categoria" required>
                   <?php foreach ($lista_categorias as $registro) {?>
                     <option <?php echo ($id_categoria == $registro['id_categoria']) ? "selected" : ""; ?>
                     value="<?php echo $registro['id_categoria']; ?>">
                       <?php echo $registro['nombre_categoria']; ?>
                     </option>

                   <?php }?>
                 </select>
             </div>

             <div class="mb-3">
                 <label for="id_proovedor" class="form-label">Proovedor:</label>
                 <select class="form-select form-select-sm" name="id_proovedor" id="id_proovedor" required>
                   <?php foreach ($lista_proovedores as $registro) {?>
                       <option <?php echo ($id_proovedor == $registro['id_proovedor']) ? "selected" : ""; ?>
                       value="<?php echo $registro['id_proovedor']; ?>">
                         <?php echo $registro['nombre_proovedor']; ?>
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