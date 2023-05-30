<?php

include "../../bd.php";

if ($_POST) {

    //recolectamos los datos del metodo POST
    $nombre_producto = (isset($_POST["nombre_producto"]) ? $_POST["nombre_producto"] : "");
    $precio = (isset($_POST["precio"]) ? $_POST["precio"] : "");
    $precio_compra = (isset($_POST["precio_compra"]) ? $_POST["precio_compra"] : "");
    $image = (isset($_FILES["foto_producto"]['name']) ? $_FILES["foto_producto"]['name'] : "");
    $id_categoria = (isset($_POST["id_categoria"]) ? $_POST["id_categoria"] : "");
    $id_proovedor = (isset($_POST["id_proovedor"]) ? $_POST["id_proovedor"] : "");
    $id_sede = (isset($_POST["id_sede"]) ? $_POST["id_sede"] : "");

    //Preparar la insersion de los datos
    $sentencia = $conexion->prepare("INSERT INTO productos(nombre_producto, precio, precio_compra, image, created_at, updated_at, id_categoria, id_proovedor, id_sede)
    VALUES (:nombre_producto, :precio, :precio_compra, :image, NOW(), NOW(), :id_categoria, :id_proovedor, :id_sede)");

    //Asignando los valores que vienen del metodo POST ( los que vienen del formulario)
    $sentencia->bindParam(":nombre_producto", $nombre_producto);
    $sentencia->bindParam(":precio", $precio);
    $sentencia->bindParam(":precio_compra", $precio_compra);

    $fecha_foto = new DateTime();
    $nombreArchivo_foto = ($image != '') ? $fecha_foto->getTimestamp() . "_" . $_FILES["foto_producto"]['name'] : "";
    $tmp_foto = $_FILES["foto_producto"]['tmp_name'];
    if ($tmp_foto != '') {
        move_uploaded_file($tmp_foto, "./" . $nombreArchivo_foto);
    }
    $sentencia->bindParam(":image", $nombreArchivo_foto);

    $sentencia->bindParam(":id_categoria", $id_categoria);
    $sentencia->bindParam(":id_proovedor", $id_proovedor);
    $sentencia->bindParam(":id_sede", $id_sede);

    $sentencia->execute();
    $mensaje = "Registro agregado";
    header("Location:index.php?mensaje=" . $mensaje);
}

include "../../templates/header.php";

$sentencia = $conexion->prepare("SELECT * FROM categorias WHERE id_sede = " . $_SESSION['id_sede'] . "");
$sentencia->execute();
$lista_categorias = $sentencia->fetchAll(PDO::FETCH_ASSOC);

$sentencia = $conexion->prepare("SELECT * FROM `proveedores`");
$sentencia->execute();
$lista_proovedores = $sentencia->fetchAll(PDO::FETCH_ASSOC);

$sentencia = $conexion->prepare("SELECT * FROM sedes s INNER JOIN usuarios u WHERE s.id_sede= " . $_SESSION['id_sede']);
$sentencia->execute();
$registro = $sentencia->fetch(PDO::FETCH_LAZY);

$nombre_sede = $registro["nombre_sede"];
$id_sede = $registro["id_sede"];

?>

  <br/>

  <div class="card">
    <div class="card-header">
        Agregar Productos
    </div>
    <div class="card-body">

        <form action="" method="post" enctype="multipart/form-data">

            <div class="mb-3">
              <label for="foto_producto" class="form-label">Foto Producto</label>
              <input type="file"
                class="form-control" name="foto_producto" id="foto_producto" aria-describedby="helpId" placeholder="Foto">
            </div>
            <div class="mb-3">
              <label for="nombre_producto" class="form-label">Nombre Producto:</label>
              <input type="text"
                class="form-control" required name="nombre_producto" id="nombre_producto" aria-describedby="helpId" placeholder="Nombre Producto">
            </div>
            <div class="mb-3">
              <label for="precio" class="form-label">Precio de Venta:</label>
              <input type="text"
                class="form-control" required name="precio" id="precio" aria-describedby="helpId" placeholder="$ 10.000">
            </div>

            <div class="mb-3">
              <label for="precio_compra" class="form-label">Precio de Compra:</label>
              <input type="text"
                class="form-control" required name="precio_compra" id="precio_compra" aria-describedby="helpId" placeholder="$ 10.000">
            </div>

            <div class="mb-3">
                <label for="id_categoria" class="form-label">Categoria</label>
                <select class="form-select form-select-sm" name="id_categoria" id="id_categoria" required>
                  <?php foreach ($lista_categorias as $registro) {?>
                    <option value="<?php echo $registro['id_categoria']; ?>">
                      <?php echo $registro['nombre_categoria']; ?>
                    </option>

                  <?php }?>
                </select>
            </div>

            <div class="mb-3">
                <label for="id_proovedor" class="form-label">Proovedor</label>
                <select class="form-select form-select-sm" name="id_proovedor" id="id_proovedor" required>
                  <?php foreach ($lista_proovedores as $registro) {?>
                      <option value="<?php echo $registro['id_proovedor']; ?>">
                        <?php echo $registro['nombre_proovedor']; ?>
                      </option>
                  <?php }?>
                </select>
            </div>

            <div class="mb-3" style="display:none;">
              <label for="id_sede" class="form-label">Id Sede:</label>
              <input type="text"
              value="<?php echo $id_sede; ?>"
                class="form-control" readonly required name="id_sede" id="id_sede" aria-describedby="helpId" placeholder="$ 10.000">
            </div>

         <div class="mb-3">
           <label for="nombre_sede" class="form-label">Sede:</label>
           <h5 name="nombre_sede" id="nombre_sede"> <?php echo $nombre_sede; ?></h5>
         </div>

            <br/>

            <button type="submit" class="btn btn-success">Agregar registro</button>
            <a name="" id="" class="btn btn-secondary" href="index.php" role="button">Cancelar</a>

        </form>


    </div>
  </div>

<?php include "../../templates/footer.php";?>