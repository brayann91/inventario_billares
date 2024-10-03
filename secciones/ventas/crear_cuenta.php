<?php

include("../../bd.php");

if($_POST){
  session_start();
  //recolectamos los datos del metodo POST
  $nombre_cuenta=(isset($_POST["nombre_cuenta"])?$_POST["nombre_cuenta"]:"");
  $precio_cuenta=(isset($_POST["precio_cuenta"])?$_POST["precio_cuenta"]:"");
  $id_sede = (isset($_POST["id_sede"]) ? $_POST["id_sede"] : "");

  //Verificar si el nombre de la cuenta ya existe
  $consulta = $conexion->prepare("SELECT COUNT(*) as cuenta FROM cuentas WHERE nombre_cuenta=:nombre_cuenta AND id_sede=" . $_SESSION['id_sede'] . "");
  $consulta->bindParam(":nombre_cuenta", $nombre_cuenta);
  $consulta->execute();
  $resultado = $consulta->fetch(PDO::FETCH_ASSOC);


  if($resultado['cuenta'] > 0) {
    $mensaje_error="El nombre de la cuenta ya existe";
    header("Location:crear.php?mensaje_error=".$mensaje_error);
    exit(); //salir del script para que no se ejecute el resto del código
  }

  //Preparar la insersion de los datos
  $sentencia=$conexion->prepare("INSERT INTO cuentas(nombre_cuenta, precio_cuenta, estado, estado_cuenta, id_sede) VALUES (:nombre_cuenta, 0, 1, 1, :id_sede)");

  //Asignando los valores que vienen del metodo POST ( los que vienen del formulario)
  $sentencia->bindParam(":nombre_cuenta", $nombre_cuenta);
  $sentencia->bindParam(":id_sede", $id_sede);
  $sentencia->execute();
  $mensaje="Registro agregado";
  header("Location:index.php?mensaje=".$mensaje);
}

include("../../templates/header.php");

$sentencia = $conexion->prepare("SELECT * FROM sedes WHERE id_sede= " . $_SESSION['id_sede']);
$sentencia->execute();
$registro = $sentencia->fetch(PDO::FETCH_LAZY);

$nombre_sede = $registro["nombre_sede"];
$id_sede = $registro["id_sede"];

?>

<br/>

<div class="card">
    <div class="card-header">
        Agregar cuenta
    </div>
    <div class="card-body">

        <form action="" method="post" enctype="multipart/form-data" onsubmit="return validarNombreCuenta()">

            <div class="mb-3">
                <label for="nombre_cuenta" class="form-label">Nombre cuenta:</label>
                <input type="text" required class="form-control" name="nombre_cuenta" id="nombre_cuenta" aria-describedby="helpId" placeholder="Nombre cuenta">
            </div>

            <div class="mb-3" style="display:none;">
                <label for="id_sede" class="form-label">Id Sede:</label>
                <input type="text" value="<?php echo $id_sede; ?>" class="form-control" readonly required name="id_sede" id="id_sede" aria-describedby="helpId" placeholder="$ 10.000">
            </div>

            <div class="mb-3">
                <label for="nombre_sede" class="form-label">Sede:</label>
                <h5 name="nombre_sede" id="nombre_sede"><?php echo $nombre_sede; ?></h5>
            </div>

            <br/>

            <button type="submit" class="btn btn-success">Agregar registro</button>
            <a name="" id="" class="btn btn-secondary" href="index.php" role="button">Cancelar</a>

        </form>

    </div>
</div>

<script>
    function validarNombreCuenta() {
        var nombreCuenta = document.getElementById("nombre_cuenta").value.toLowerCase(); // Convertir todo a minúsculas
        var regex = /^[a-zA-Z0-9\s]*$/; // Expresión regular para permitir solo letras, números y espacios

        // Verifica si contiene la palabra "mesa" o "ñ" y si tiene caracteres especiales
        if (nombreCuenta.includes("ñ") || nombreCuenta.includes("mesa") || !regex.test(nombreCuenta)) {
            alert("El nombre de la cuenta no puede contener una 'ñ', la palabra 'mesa', o caracteres especiales.");
            return false; // Evita que el formulario se envíe
        }

        return true; // Permite enviar el formulario si cumple con la validación
    }
</script>



<?php include("../../templates/footer.php"); ?>