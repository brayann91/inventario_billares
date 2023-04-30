<?php include "templates/header.php";

include "bd.php";

if ($_POST) {

  //session_start();

  $id_sede = (isset($_POST["id_sede"]) ? $_POST["id_sede"] : "");

  $_SESSION['id_sede'] =  $id_sede;
}

$sentencia = $conexion->prepare("SELECT * FROM sedes");
$sentencia->execute();
$lista_sedes = $sentencia->fetchAll(PDO::FETCH_ASSOC);

$sentencia = $conexion->prepare("SELECT * FROM sedes WHERE id_sede=" . $_SESSION['id_sede'] . "");
$sentencia->execute();
$sede_actual = $sentencia->fetch(PDO::FETCH_LAZY);

?>

  <br/>

    <div class="p-5 mb-4 bg-light rounded-3" >
        <div class="container-fluid py-5" >
          <h1 class="display-5 fw-bold" >Bienvenido <?php echo $_SESSION['usuario']; ?> </h1>
          <h2 class="display-5 fw-bold">Login en la Sede: <span style="color: red;"><?php echo $sede_actual['nombre_sede']; ?></span></h2>
          <p class="col-md-8 fs-4"></p>
        </div>
      </div>

      <?php if($_SESSION['id_cargo']!=2){ ?>

      <form action="" method="post" enctype="multipart/form-data">

      <div class="mb-3">
        <label for="id_sede" class="form-label">Seleccione la Sede:</label>
        <select class="form-select form-select-sm" name="id_sede" id="id_sede" required>
          <option value="">Seleccione una opción</option> <!-- Agregar esta opción -->
          <?php foreach ($lista_sedes as $registro) {?>
            <option value="<?php echo $registro['id_sede']; ?>">
              <?php echo $registro['nombre_sede']; ?>
            </option>
          <?php }?>
        </select>
      </div>

      <button type="submit" class="btn btn-success">Actualizar sede</button>

      </form>

      <?php } ?>



<?php include "templates/footer.php";?>