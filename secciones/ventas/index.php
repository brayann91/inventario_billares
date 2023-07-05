<?php

include "../../bd.php";

include "../../templates/header.php";

if(isset($_GET['txtID'])){

  $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";
  
}

$sentencia = $conexion->prepare("SELECT * FROM cuentas c INNER JOIN sedes s ON c.id_sede=s.id_sede WHERE c.nombre_cuenta NOT LIKE 'borrada_%' AND c.id_sede= '" . $_SESSION['id_sede'] . "'");
$sentencia->execute();
$lista_cuentas = $sentencia->fetchAll(PDO::FETCH_ASSOC);

$id_categoria_3 = (isset($_POST["id_categoria_3"]) ? $_POST["id_categoria_3"] : "");

$menu = '<ul class="nav nav-pills" id="nav-tab" role="tablist">';
$menu2 = '<ul class="navbar-nav me-auto mb-2 mb-lg-0">';
$menu3 = '';
$nuevoRegistro = '';

$array_name_cuenta = array();

foreach ($lista_cuentas as $registro) {

    $nuevoRegistro = str_replace(" ", "_", $registro["nombre_cuenta"]);

    $menu .= '<li class="nav-item"><button class="nav-link';
    if ($registro["estado"] == 0) {
        $menu .= '" style="display:none';
    }
    $menu .= '" id="nav-' .
        $nuevoRegistro . '-tab" data-bs-toggle="tab" data-bs-target="#nav-' .
        $nuevoRegistro . '" type="button" role="tab" aria-controls="nav-' .
        $nuevoRegistro . '" aria-selected="false" >' .
        $registro["nombre_cuenta"] . '</button></li>';

    $menu2 .= '<div class="tab-pane fade show active" id="nav-' .
        $nuevoRegistro . '" role="tabpanel" aria-labelledby="nav-' .
        $nuevoRegistro . '-tab" tabindex="0"></div>';

    $menu3 .= 'document.getElementById("nav-' .
        $nuevoRegistro . '-tab").addEventListener("click", function() {';

    $menu3 .= 'changeActiveTab("nav-' .
        $nuevoRegistro . '-tab","#' .
        $nuevoRegistro . '");});';

    $array_name_cuenta[] = $nuevoRegistro;
}
$menu .= '</ul>';
$menu2 .= '</ul>';

$sentencia = $conexion->prepare("SELECT p.*, e.*, c.*, SUM(e.cantidad) total_inventario
FROM productos p
INNER JOIN entradas e ON p.id_producto = e.id_producto
INNER JOIN categorias c ON p.id_categoria = c.id_categoria
WHERE p.id_sede= '" . $_SESSION['id_sede'] . "'
GROUP BY p.id_producto");
$sentencia->execute();
$lista_productos = $sentencia->fetchAll(PDO::FETCH_ASSOC);

if ($_POST) {

    $sentencia = $conexion->prepare("SELECT * FROM categorias WHERE id_sede = " . $_SESSION['id_sede'] . "");
    $sentencia->execute();
    $lista_categorias = $sentencia->fetchAll(PDO::FETCH_ASSOC);

    $idcategoria = "";

    $id_sede = (isset($_POST["id_sede"]) ? $_POST["id_sede"] : "");

    foreach ($_POST as $key => $value) {
        if (strpos($key, 'id_categoria_') !== false) {
            $idcategoria = $value;
            break;
        }
    }

    $sentencia = $conexion->prepare("SELECT p.*, e.*, c.*, SUM(e.cantidad) total_inventario
    FROM productos p
    INNER JOIN entradas e ON p.id_producto = e.id_producto
    INNER JOIN categorias c ON p.id_categoria = c.id_categoria
    WHERE p.id_categoria = '" . $idcategoria . "' " . " AND p.id_sede= '" . $_SESSION['id_sede'] . "'
    GROUP BY p.id_producto");
    $sentencia->execute();
    $lista_productos = $sentencia->fetchAll(PDO::FETCH_ASSOC);

    if (isset($_POST["idCuenta"])) {
      //recolectamos los datos del metodo POST
      $idCuenta = (isset($_POST["idCuenta"]) ? $_POST["idCuenta"] : "");
      $estado = (isset($_POST["estado"]) ? $_POST["estado"] : "");

      //Preparar la insersion de los datos
      $sentencia = $conexion->prepare("UPDATE cuentas SET estado=:estado WHERE id_cuenta=:idCuenta ");

      //Asignando los valores que vienen del metodo POST ( los que vienen del formulario)
      $sentencia->bindParam(":idCuenta", $idCuenta);
      $sentencia->bindParam(":estado", $estado);
      $sentencia->execute();
    }

    $sentencia = $conexion->prepare("SELECT * FROM cajas WHERE id_sede='" . $_SESSION['id_sede'] . "'" .
    " ORDER BY id_caja DESC LIMIT 1");
    $sentencia->execute();
    $registro_caja = $sentencia->fetch(PDO::FETCH_LAZY);

    
    $sentencia = $conexion->prepare("SELECT * FROM cajas WHERE id_sede='" . $_SESSION['id_sede'] . "'" .
    " ORDER BY id_caja DESC LIMIT 1 OFFSET 1");
    $sentencia->execute();
    $registro_caja2 = $sentencia->fetch(PDO::FETCH_LAZY);

    if (isset($_POST["abrir_caja"])) {
      $sentencia = $conexion->prepare("INSERT INTO cajas(fecha_apertura, valor, valor_acomulado, estado, id_sede)
      VALUES (CURRENT_TIMESTAMP(), 0, " . $registro_caja['valor_acomulado'] . ", 1, " . $_SESSION['id_sede'] . ")");
      $sentencia->execute();
    }

    if (isset($_POST["id_caja"])) {

      $id_caja = (isset($_POST["id_caja"]) ? $_POST["id_caja"] : "");

      $sentencia = $conexion->prepare("SELECT SUM(precio_total) precio_total
      FROM entradas e
      INNER JOIN productos p ON p.id_producto = e.id_producto
      WHERE p.id_sede=" . $_SESSION['id_sede'] . " AND e.id_cuenta<>0
      AND e.fecha>=(SELECT fecha_apertura FROM cajas WHERE id_caja=:id_caja)
      GROUP BY e.fecha>=(SELECT fecha_apertura FROM cajas WHERE id_caja=:id_caja)");
      $sentencia->bindParam(":id_caja", $id_caja);
      $sentencia->execute();
      $lista_productos_vendidos = $sentencia->fetch(PDO::FETCH_LAZY);

      $sentencia = $conexion->prepare("SELECT SUM(precio_total_tiempo) precio_final
      FROM facturas t
      INNER JOIN cuentas c ON c.id_cuenta = t.id_cuenta
      WHERE c.id_sede=" . $_SESSION['id_sede'] . 
      " AND t.fecha>=(SELECT fecha_apertura FROM cajas WHERE id_caja=:id_caja)
      GROUP BY t.fecha>=(SELECT fecha_apertura FROM cajas WHERE id_caja=:id_caja)");
      $sentencia->bindParam(":id_caja", $id_caja);
      $sentencia->execute();
      $lista_tiempos_vendidos = $sentencia->fetch(PDO::FETCH_LAZY);

      $sentencia = $conexion->prepare("UPDATE cajas 
      SET fecha_cierre=CURRENT_TIMESTAMP(), valor=" . 
      abs($lista_productos_vendidos['precio_total']) . " + " . abs($lista_tiempos_vendidos['precio_final']) .
      ", valor_acomulado= " . $registro_caja['valor_acomulado'] . " + " .
      abs($lista_productos_vendidos['precio_total']) . " + " . abs($lista_tiempos_vendidos['precio_final']) .
      ", estado=0 WHERE id_caja=:id_caja");
      $sentencia->bindParam(":id_caja", $id_caja);
      $sentencia->execute();

      $sentencia = $conexion->prepare("UPDATE cuentas SET estado=0 WHERE id_sede= " . $_SESSION['id_sede'] . " AND nombre_cuenta NOT LIKE 'MESA%' AND nombre_cuenta NOT LIKE 'CLIENTE'");
      $sentencia->execute();

    }

    if (isset($_POST["id_cuenta"])) {
        //recolectamos los datos del metodo POST
        $id_cuenta = (isset($_POST["id_cuenta"]) ? $_POST["id_cuenta"] : "");

        $sentencia = $conexion->prepare("INSERT INTO tiempos(fecha_inicio, fecha_fin, tiempo_invertido, precio_final, estado_tiempo, estado_liquidado, id_cuenta)
      VALUES (CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP(), SEC_TO_TIME(TIMESTAMPDIFF(SECOND, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP())), null, 1, 1, :id_cuenta)");

        //Asignando los valores que vienen del metodo POST ( los que vienen del formulario)
        $sentencia->bindParam(":id_cuenta", $id_cuenta);
        $sentencia->execute();
    }

    if (isset($_POST["actualizar_id_tiempo"])) {
      //recolectamos los datos del metodo POST
      $actualizar_id_tiempo = (isset($_POST["actualizar_id_tiempo"]) ? $_POST["actualizar_id_tiempo"] : "");

      $sentencia = $conexion->prepare("UPDATE tiempos t
    INNER JOIN cuentas c ON t.id_cuenta=c.id_cuenta
    SET t.fecha_fin=CURRENT_TIMESTAMP(),
        t.tiempo_invertido=SEC_TO_TIME(TIMESTAMPDIFF(SECOND, t.fecha_inicio, CURRENT_TIMESTAMP())),
        t.precio_final=(TIME_TO_SEC(SEC_TO_TIME(TIMESTAMPDIFF(SECOND, t.fecha_inicio, CURRENT_TIMESTAMP()))) / 3600) * c.precio_cuenta
    WHERE id_tiempo=:actualizar_id_tiempo;");

      //Asignando los valores que vienen del metodo POST ( los que vienen del formulario)
      $sentencia->bindParam(":actualizar_id_tiempo", $actualizar_id_tiempo);
      $sentencia->execute();
  }

    if (isset($_POST["id_cuenta_end"])) {
        //recolectamos los datos del metodo POST
        $id_cuenta_end = (isset($_POST["id_cuenta_end"]) ? $_POST["id_cuenta_end"] : "");
        $id_tiempo = (isset($_POST["id_tiempo"]) ? $_POST["id_tiempo"] : "");

        $sentencia = $conexion->prepare("UPDATE tiempos t
      INNER JOIN cuentas c ON t.id_cuenta=c.id_cuenta
      SET t.fecha_fin=CURRENT_TIMESTAMP(),
          t.tiempo_invertido=SEC_TO_TIME(TIMESTAMPDIFF(SECOND, t.fecha_inicio, CURRENT_TIMESTAMP())),
          t.precio_final=(TIME_TO_SEC(SEC_TO_TIME(TIMESTAMPDIFF(SECOND, t.fecha_inicio, CURRENT_TIMESTAMP()))) / 3600) * c.precio_cuenta, t.estado_tiempo=0,
          t.id_cuenta=:id_cuenta_end
      WHERE id_tiempo=:id_tiempo;");

        //Asignando los valores que vienen del metodo POST ( los que vienen del formulario)
        $sentencia->bindParam(":id_cuenta_end", $id_cuenta_end);
        $sentencia->bindParam(":id_tiempo", $id_tiempo);
        $sentencia->execute();
    }

    if (isset($_POST["id_cuenta_continuar"])) {
        //recolectamos los datos del metodo POST
        $id_cuenta_continuar = (isset($_POST["id_cuenta_continuar"]) ? $_POST["id_cuenta_continuar"] : "");
        $id_tiempo = (isset($_POST["id_tiempo"]) ? $_POST["id_tiempo"] : "");

        $sentencia = $conexion->prepare("UPDATE tiempos t
      INNER JOIN cuentas c ON t.id_cuenta=c.id_cuenta
      SET t.fecha_fin=CURRENT_TIMESTAMP(),
          t.tiempo_invertido=SEC_TO_TIME(TIMESTAMPDIFF(SECOND, t.fecha_inicio, CURRENT_TIMESTAMP())),
          t.precio_final=(TIME_TO_SEC(SEC_TO_TIME(TIMESTAMPDIFF(SECOND, t.fecha_inicio, CURRENT_TIMESTAMP()))) / 3600) * c.precio_cuenta, t.estado_tiempo=1,
          t.id_cuenta=:id_cuenta_continuar
      WHERE id_tiempo=:id_tiempo;");

        //Asignando los valores que vienen del metodo POST ( los que vienen del formulario)
        $sentencia->bindParam(":id_cuenta_continuar", $id_cuenta_continuar);
        $sentencia->bindParam(":id_tiempo", $id_tiempo);
        $sentencia->execute();
    }

    if (isset($_POST["id_cuenta_temporal"])) {

      //recolectamos los datos del metodo POST
      $id_producto_temporal = (isset($_POST["id_producto_temporal"]) ? $_POST["id_producto_temporal"] : "");
      $nombre_cuenta_temporal = (isset($_POST["nombre_cuenta_temporal"]) ? str_replace('_', ' ', $_POST["nombre_cuenta_temporal"]) : "");
      $id_cuenta_temporal = (isset($_POST["id_cuenta_temporal"]) ? $_POST["id_cuenta_temporal"] : "");

        $sentencia = $conexion->prepare("SELECT * FROM entradas WHERE id_cuenta = :id_cuenta_temporal AND id_producto = :id_producto_temporal AND estado = 1");
        $sentencia->bindParam(":id_cuenta_temporal", $id_cuenta_temporal);
        $sentencia->bindParam(":id_producto_temporal", $id_producto_temporal);
        $sentencia->execute();
        $registro_entrada = $sentencia->fetch(PDO::FETCH_LAZY);

        $id_entrada = $registro_entrada['id_entrada'];
        

      if(isset($registro_entrada['estado'])){
        $sentencia = $conexion->prepare("UPDATE entradas e
        SET cantidad=cantidad-1,
          precio_total=precio_total-(SELECT precio FROM productos WHERE id_producto = :id_producto_temporal),
          fecha=CURRENT_TIMESTAMP()
          WHERE id_entrada=:id_entrada_mas");
          $sentencia->bindParam(":id_entrada_mas", $id_entrada);
          $sentencia->bindParam(":id_producto_temporal", $id_producto_temporal);
          $sentencia->execute();
      }else{
        $sentencia = $conexion->prepare("INSERT INTO entradas(cantidad, precio_total, precio_venta, fecha, estado, id_producto, id_cuenta)
        VALUES (-1, (SELECT precio FROM productos WHERE id_producto = :id_producto_temporal)*-1,
        (SELECT precio FROM productos WHERE id_producto = :id_producto_temporal)*-1,CURRENT_TIMESTAMP(), 1, :id_producto_temporal,
        (SELECT id_cuenta FROM cuentas WHERE id_cuenta = :id_cuenta_temporal))");
        $sentencia->bindParam(":id_producto_temporal", $id_producto_temporal);
        $sentencia->bindParam(":id_cuenta_temporal", $id_cuenta_temporal);
        $sentencia->execute();
      }

    }

    if (isset($_POST["id_entrada_mas"])) {
        //recolectamos los datos del metodo POST
        $id_entrada_mas = (isset($_POST["id_entrada_mas"]) ? $_POST["id_entrada_mas"] : "");
        $id_producto_mas = (isset($_POST["id_producto_mas"]) ? $_POST["id_producto_mas"] : "");

        $sentencia = $conexion->prepare("UPDATE entradas e
      SET cantidad=cantidad-1,
          precio_total=precio_total-(SELECT precio FROM productos WHERE id_producto = :id_producto_mas),
          fecha=CURRENT_TIMESTAMP()
      WHERE id_entrada=:id_entrada_mas");

        //Asignando los valores que vienen del metodo POST ( los que vienen del formulario)
        $sentencia->bindParam(":id_entrada_mas", $id_entrada_mas);
        $sentencia->bindParam(":id_producto_mas", $id_producto_mas);
        $sentencia->execute();
    }

    if (isset($_POST["id_entrada_menos"])) {
        //recolectamos los datos del metodo POST
        $id_entrada_menos = (isset($_POST["id_entrada_menos"]) ? $_POST["id_entrada_menos"] : "");
        $id_producto_menos = (isset($_POST["id_producto_menos"]) ? $_POST["id_producto_menos"] : "");

        $sentencia = $conexion->prepare("SELECT cantidad FROM entradas WHERE id_entrada=" . $id_entrada_menos . "");
        $sentencia->execute();
        $registro_entrada = $sentencia->fetch(PDO::FETCH_LAZY);

        if ($registro_entrada["cantidad"] < -1) {
            $sentencia = $conexion->prepare("UPDATE entradas e
            SET cantidad=cantidad+1,
                precio_total=precio_total+(SELECT precio FROM productos WHERE id_producto = :id_producto_menos),
                fecha=CURRENT_TIMESTAMP()
            WHERE id_entrada=:id_entrada_menos");

            //Asignando los valores que vienen del metodo POST ( los que vienen del formulario)
            $sentencia->bindParam(":id_entrada_menos", $id_entrada_menos);
            $sentencia->bindParam(":id_producto_menos", $id_producto_menos);
            $sentencia->execute();
        }else{
          $sentencia = $conexion->prepare("DELETE FROM entradas WHERE id_entrada=:id_entrada_menos");
          // Asignando los valores que vienen del metodo POST (los que vienen del formulario)
          $sentencia->bindParam(":id_entrada_menos", $id_entrada_menos);
          $sentencia->execute();
        }
    }

    if (isset($_POST["id_entrada_borrar"])) {
      $id_entrada_borrar = (isset($_POST["id_entrada_borrar"]) ? $_POST["id_entrada_borrar"] : "");

      $sentencia = $conexion->prepare("DELETE FROM entradas WHERE id_entrada=:id_entrada_borrar");
          // Asignando los valores que vienen del metodo POST (los que vienen del formulario)
          $sentencia->bindParam(":id_entrada_borrar", $id_entrada_borrar);
          $sentencia->execute();
    }

    if (isset($_POST["id_cuenta_liquidar"])) {

      $id_cuenta_liquidar = (isset($_POST["id_cuenta_liquidar"]) ? $_POST["id_cuenta_liquidar"] : "");

      $sentencia = $conexion->prepare("SELECT tiempo_invertido, c.precio_cuenta, precio_final, estado_liquidado, t.fecha_inicio, t.fecha_fin
      FROM tiempos t
      INNER JOIN cuentas c ON c.id_cuenta = t.id_cuenta
      WHERE t.id_cuenta=:id_cuenta_liquidar AND t.estado_liquidado=1 AND c.id_sede= '" . $_SESSION['id_sede'] . "'");
      $sentencia->bindParam(":id_cuenta_liquidar", $id_cuenta_liquidar);
      $sentencia->execute();
      $lista_tiempos_liquidados = $sentencia->fetch(PDO::FETCH_LAZY);

      $sentencia = $conexion->prepare("SELECT nombre_producto, cantidad, precio, precio_total
        FROM entradas e
        INNER JOIN productos p ON p.id_producto = e.id_producto
        WHERE id_cuenta=:id_cuenta_liquidar AND estado=1 AND p.id_sede= '" . $_SESSION['id_sede'] . "'");
      $sentencia->bindParam(":id_cuenta_liquidar", $id_cuenta_liquidar);
      $sentencia->execute();
      $lista_productos_liquidados = $sentencia->fetchAll(PDO::FETCH_ASSOC);

      $sentencia = $conexion->prepare("SELECT SUM(precio_total) precio_total
        FROM entradas e
        INNER JOIN productos p ON p.id_producto = e.id_producto
        WHERE id_cuenta=:id_cuenta_liquidar AND estado=1 AND p.id_sede= '" . $_SESSION['id_sede'] . "'" .
        " GROUP BY id_cuenta");
      $sentencia->bindParam(":id_cuenta_liquidar", $id_cuenta_liquidar);
      $sentencia->execute();
      $lista_productos_liquidados_agrupados = $sentencia->fetch(PDO::FETCH_LAZY);

      $total = abs($lista_productos_liquidados_agrupados['precio_total']) + $lista_tiempos_liquidados['precio_final'];

      $sentencia = $conexion->prepare("INSERT INTO factura_agrupada (fecha, precio_total, id_cuenta, id_sede)
        VALUES (CURRENT_TIMESTAMP(),
        '{$total}',
        :id_cuenta_liquidar,
        '{$_SESSION['id_sede']}')");
        $sentencia->bindParam(":id_cuenta_liquidar", $id_cuenta_liquidar);
        $sentencia->execute();

        
        // Obtener el ID del registro recién insertado
        $id_registro = $conexion->lastInsertId();

      foreach ($lista_productos_liquidados as $registro) {
        $cantidad = abs($registro['cantidad']);
        $precio_total = abs($registro['precio_total']);
        
        $sentencia = $conexion->prepare("INSERT INTO facturas (fecha, nombre_producto, cantidad, precio_producto, precio_total_producto, nombre_cuenta, id_cuenta, id_facturas)
        VALUES (CURRENT_TIMESTAMP(),
        '{$registro['nombre_producto']}',
        {$cantidad},
        {$registro['precio']},
        {$precio_total},
        (SELECT nombre_cuenta FROM cuentas WHERE id_cuenta = :id_cuenta_liquidar),
        :id_cuenta_liquidar, '{$id_registro}')");
        $sentencia->bindParam(":id_cuenta_liquidar", $id_cuenta_liquidar);
        $sentencia->execute();
      }

      if(isset($lista_tiempos_liquidados['estado_liquidado'])){

        $inicio = date("H:i", strtotime($lista_tiempos_liquidados['fecha_inicio']));
        $fin = date("H:i", strtotime($lista_tiempos_liquidados['fecha_fin']));

        $sentencia = $conexion->prepare("INSERT INTO facturas (fecha, nombre_cuenta, inicio_tiempo, fin_tiempo, precio_tiempo, precio_total_tiempo, tiempo_invertido, id_cuenta, id_facturas)
          VALUES (CURRENT_TIMESTAMP(),
          (SELECT nombre_cuenta FROM cuentas WHERE id_cuenta = :id_cuenta_liquidar),
          '{$inicio}',
          '{$fin}',
          '{$lista_tiempos_liquidados['precio_cuenta']}',
          '{$lista_tiempos_liquidados['precio_final']}',
          '{$lista_tiempos_liquidados['tiempo_invertido']}',        
          :id_cuenta_liquidar, '{$id_registro}')");
          $sentencia->bindParam(":id_cuenta_liquidar", $id_cuenta_liquidar);
          $sentencia->execute();
      }
        

      $sentencia = $conexion->prepare("UPDATE entradas e
            INNER JOIN productos p ON p.id_producto = e.id_producto
            SET estado=0
            WHERE id_cuenta=:id_cuenta_liquidar AND p.id_sede= '" . $_SESSION['id_sede'] . "'");
            //Asignando los valores que vienen del metodo POST ( los que vienen del formulario)
            $sentencia->bindParam(":id_cuenta_liquidar", $id_cuenta_liquidar);
            $sentencia->execute();

      $sentencia = $conexion->prepare("UPDATE tiempos t
            INNER JOIN cuentas c ON c.id_cuenta = t.id_cuenta
            SET estado_liquidado=0
            WHERE t.id_cuenta=:id_cuenta_liquidar AND c.id_sede= '" . $_SESSION['id_sede'] . "'");
            //Asignando los valores que vienen del metodo POST ( los que vienen del formulario)
            $sentencia->bindParam(":id_cuenta_liquidar", $id_cuenta_liquidar);
            $sentencia->execute();


    }     

}

$sentencia = $conexion->prepare("SELECT * FROM categorias WHERE id_sede = " . $_SESSION['id_sede'] . "");
$sentencia->execute();
$lista_categorias = $sentencia->fetchAll(PDO::FETCH_ASSOC);

$sentencia = $conexion->prepare("SELECT * FROM cuentas WHERE estado<>1 AND nombre_cuenta NOT LIKE 'borrada_%' AND id_sede= '" . $_SESSION['id_sede'] . "'");
$sentencia->execute();
$lista_cuentas = $sentencia->fetchAll(PDO::FETCH_ASSOC);

$sentencia = $conexion->prepare("SELECT * FROM cuentas WHERE estado<>0 AND nombre_cuenta NOT LIKE 'borrada_%' AND id_sede= '" . $_SESSION['id_sede'] . "'");
$sentencia->execute();
$lista_cuentas2 = $sentencia->fetchAll(PDO::FETCH_ASSOC);

$sentencia = $conexion->prepare("SELECT * FROM cajas WHERE id_sede='" . $_SESSION['id_sede'] . "'" .
" ORDER BY id_caja DESC LIMIT 1");
$sentencia->execute();
$registro_caja = $sentencia->fetch(PDO::FETCH_LAZY);

//Consulta para contar la cantidad de registros sin liquidar
$sentencia = $conexion->prepare("SELECT COUNT(*) AS cantidad FROM entradas e
INNER JOIN productos p ON p.id_producto=e.id_producto WHERE e.estado=1 AND p.id_sede= '" . $_SESSION['id_sede'] . "'");
$sentencia->execute();
$cantidad_inventario_sin_liquidar = $sentencia->fetch(PDO::FETCH_ASSOC)['cantidad'];

//Consulta para contar la cantidad de tiempos sin detener
$sentencia = $conexion->prepare("SELECT COUNT(*) AS cantidad FROM tiempos t
INNER JOIN cuentas c ON t.id_cuenta=c.id_cuenta WHERE t.estado_liquidado=1  AND c.id_sede= '" . $_SESSION['id_sede'] . "'");
$sentencia->execute();
$cantidad_tiempos_sin_detener = $sentencia->fetch(PDO::FETCH_ASSOC)['cantidad'];

?>

<body>

<table class="table">
  <tr>
    <td>
    <?php if($registro_caja["estado"]==1){ ?>
      <form id="myForm">
        <select name="cuenta" class="form-select-sm" id="cuenta">
          <option value="" selected>Seleccione una Opción</option>
          <?php foreach ($lista_cuentas as $registro) {?>
            <option value="<?php echo str_replace(" ", "_", $registro["nombre_cuenta"]); ?>" 
            data-id="<?php echo $registro['id_cuenta']; ?>" data-estado="<?php echo $registro['estado']; ?>"
            ><?php echo $registro['nombre_cuenta']; ?></option>
          <?php }?>
        </select>
        <button type="button" class="btn btn-success" onClick="agregar()">Agregar</button>
      </form>
      <?php } ?>
    </td>
    <td>
      <?php if($registro_caja["estado"]==0){ ?>
        <button type="submit" class="btn btn-info" id="abrirCaja" onclick="abrirCaja()">Abrir Caja</button>
      <?php } ?>
      <?php if($registro_caja["estado"]==1){ 
        
        if($cantidad_inventario_sin_liquidar > 0 || $cantidad_tiempos_sin_detener > 0){?>
          <a name="" id="" class="btn btn-secondary" href="crear_cuenta.php" role="button">Agregar Cuenta</a>
          <button type="submit" class="btn btn-warning" id="cerrar_caja_sin_liquidar" onclick="cerrar_caja_sin_liquidar()">Cerrar Caja</button>
        <?php }else{?>
          <a name="" id="" class="btn btn-secondary" href="crear_cuenta.php" role="button">Agregar Cuenta</a>
        <button type="submit" class="btn btn-danger" id="cerrarCaja" onclick="cerrarCaja(<?php echo $registro_caja['id_caja']; ?>)">Cerrar Caja</button>
      <?php }} ?>
    </td>
    <td class="text-right">
    <?php if($registro_caja["estado"]==1){ ?>
      <form id="myForm2">
          <select name="cuenta2" class="form-select-sm" id="cuenta2">
            <option value="" selected>Seleccione una Opción</option>
            <?php foreach ($lista_cuentas2 as $registro) {?>
              <option value="<?php echo str_replace(" ", "_", $registro["nombre_cuenta"]); ?>" 
              data-id="<?php echo $registro['id_cuenta']; ?>" data-estado="<?php echo $registro['estado']; ?>"
              ><?php echo $registro['nombre_cuenta']; ?></option>
            <?php }?>
          </select>
          <button type="button" class="btn btn-danger" onClick="quitar()">Quitar</button>
        </form>
      <?php } ?>
    </td>
  </tr>
</table>

  <header>
    <!-- place navbar here -->
  </header>


<nav>
    <?php echo $menu; ?>
</nav>

<?php if($registro_caja["estado"]==1){ ?>
<?php for ($x = -1; $x < count($array_name_cuenta); $x++) {?>

  <div class="table-responsive" id="<?php echo $array_name_cuenta[$x]; ?>">
  <table class="table">
    <thead>
      <tr  class="table-header">
        <th>Categoria</th>
        <th>Productos</th>
        <th>Pedidos</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>
          <form action="" method="post" enctype="multipart/form-data">
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
              <ul class="navbar-nav flex-column">
                <?php foreach ($lista_categorias as $registro): ?>

                    <button type="submit" value="<?php echo $registro['id_categoria']; ?>"
                    id="id_categoria_<?php echo $registro['id_categoria']; ?>_input"
                    name="id_categoria_<?php echo $registro['id_categoria']; ?>_input"
                    class="nav-link text-blue"><?php echo $registro['nombre_categoria'] ?>
                  </button>

                <?php endforeach;?>
              </ul>
            </nav>
          </form>
        </td>
        <td style="white-space: nowrap;">

            <table class="table table-bordered" id="tabla_productos_<?php echo $array_name_cuenta[$x] ?>">
                <thead>
                    <tr valign="middle" align="center">
                        <th scope="col">FOTO</th>
                        <th scope="col">NOMBRE</th>
                        <th scope="col">PRECIO</th>
                        <th scope="col">CANTIDAD</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($lista_productos as $registro) {?>
                    <tr class="" valign="middle" align="center">
                        <?php $sentencia = $conexion->prepare("SELECT id_cuenta FROM cuentas WHERE nombre_cuenta='" . str_replace('_', ' ', $array_name_cuenta[$x]) . "'" .
                          " AND id_sede='" . $_SESSION['id_sede'] . "'");
                          $sentencia->execute();
                          $registro_cuenta = $sentencia->fetch(PDO::FETCH_LAZY);
                        ?>
                        <td>
                          <button type="submit" id="agregar_producto_<?php echo $array_name_cuenta[$x] ?>" value=""
                          style="background-image: url('<?php echo "../productos/" . $registro['image']; ?>');
                              background-repeat: no-repeat;
                              background-position: center center;
                              background-size: cover;
                              width: 50px;
                              height: 50px;"
                          class="img-fluid rounded" alt=""
                          onClick="agregarProductoACuenta('<?php echo $array_name_cuenta[$x] ?>', '<?php echo $registro['id_producto']; ?>',
                          '<?php echo $registro_cuenta['id_cuenta']; ?>', '<?php echo $registro['precio']; ?>')"
                          >
                        </td>
                        <td><?php echo $registro['nombre_producto']; ?></td>
                        <td>$ <?php echo number_format($registro['precio'], 1); ?></td>
                        <td><?php echo $registro['total_inventario']; ?></td>
                    </tr>
                <?php }?>
                </tbody>
            </table>

        </td>
        <td style="text-align: center;">

          <table class="table table-bordered" id="tabla_ventas">
                <thead>
                <?php
                    $sentencia = $conexion->prepare("SELECT * FROM cuentas WHERE nombre_cuenta='" . str_replace('_', ' ', $array_name_cuenta[$x]) . "'" .
                        " AND id_sede='" . $_SESSION['id_sede'] . "'");
                        $sentencia->execute();
                        $registro_cuenta = $sentencia->fetch(PDO::FETCH_LAZY);

                        $sentencia = $conexion->prepare("SELECT * FROM tiempos t
                                              INNER JOIN cuentas c ON t.id_cuenta=c.id_cuenta WHERE c.nombre_cuenta='" . str_replace('_', ' ', $array_name_cuenta[$x]) . "'" .
                                              " AND c.id_sede='" . $_SESSION['id_sede'] . "' ORDER BY t.id_tiempo DESC LIMIT 1");
                        $sentencia->execute();
                        $registro_tiempo = $sentencia->fetch(PDO::FETCH_LAZY);

                        $sentencia = $conexion->prepare("SELECT * FROM tiempos t
                                              INNER JOIN cuentas c ON t.id_cuenta=c.id_cuenta WHERE c.nombre_cuenta='" . str_replace('_', ' ', $array_name_cuenta[$x]) . "'" .
                                              " AND c.id_sede=" . $_SESSION['id_sede'] . " AND estado_liquidado = 1");
                        $sentencia->execute();
                        $registro_tiempo_sin_liquidar = $sentencia->fetch(PDO::FETCH_LAZY);

                        $sentencia = $conexion->prepare("SELECT SUM(e.precio_total) precio_total
                          FROM productos p
                          INNER JOIN entradas e ON p.id_producto = e.id_producto
                          INNER JOIN cuentas c ON c.id_cuenta = e.id_cuenta
                          WHERE p.id_sede= '" . $_SESSION['id_sede'] . "'
                          AND e.estado = 1 AND c.nombre_cuenta ='" . str_replace('_', ' ', $array_name_cuenta[$x]) . "'" .
                          " GROUP BY c.id_cuenta");
                          $sentencia->execute();
                          $lista_precio_productos_agregados = $sentencia->fetch(PDO::FETCH_LAZY);

                          $sentencia = $conexion->prepare("SELECT * FROM factura_agrupada f INNER JOIN cuentas c ON f.id_cuenta = c.id_cuenta
                          WHERE f.id_sede= '" . $_SESSION['id_sede'] . "' AND c.nombre_cuenta ='" . str_replace('_', ' ', $array_name_cuenta[$x]) . "'" . 
                          " ORDER BY f.id_factura DESC LIMIT 1");
                          $sentencia->execute();
                          $lista_factura_agrupada = $sentencia->fetch(PDO::FETCH_LAZY);
                ?>

                    <tr valign="middle" align="center">
                        <th colspan="1"><?php echo $array_name_cuenta[$x]; ?></th>
                        <th colspan="4"><label style="font-size: 2em; color: green;" id="precio_<?php echo $array_name_cuenta[$x] ?>" >$ 
                        <?php if(isset($lista_precio_productos_agregados['precio_total']) && isset($registro_tiempo_sin_liquidar['precio_final'])){
                          echo number_format((abs($lista_precio_productos_agregados['precio_total']) + $registro_tiempo_sin_liquidar['precio_final']), 1);
                        } else if(!isset($lista_precio_productos_agregados['precio_total']) && isset($registro_tiempo_sin_liquidar['precio_final'])){
                          echo number_format(($registro_tiempo_sin_liquidar['precio_final']), 1);
                        } else if(isset($lista_precio_productos_agregados['precio_total']) && !isset($registro_tiempo_sin_liquidar['precio_final'])){
                          echo number_format((abs($lista_precio_productos_agregados['precio_total'])), 1);
                        } else{
                          echo 0;
                        }?>
                        </label></th>
                        <th>
                        
                        <?php if (isset($registro_tiempo['estado_tiempo']) && $registro_tiempo['estado_tiempo'] == 1 && isset($lista_precio_productos_agregados['precio_total'])) {?>
                          <input type="button"
                          style="background-image: url('https://w7.pngwing.com/pngs/184/296/png-transparent-update-button-miscellaneous-angle-trademark-thumbnail.png');
                                background-repeat: no-repeat;
                                background-position: center center;
                                background-size: cover;
                                border: none;
                                width: 35px;
                                height: 40px;"
                            onclick="actualizarTotal('<?php echo $registro_tiempo_sin_liquidar['id_tiempo']; ?>')">
                          </input>
                        <?php }else if(isset($registro_tiempo['estado_tiempo']) && $registro_tiempo['estado_tiempo'] == 1){?>
                          <input type="button"
                          style="background-image: url('https://w7.pngwing.com/pngs/184/296/png-transparent-update-button-miscellaneous-angle-trademark-thumbnail.png');
                                background-repeat: no-repeat;
                                background-position: center center;
                                background-size: cover;
                                border: none;
                                width: 35px;
                                height: 40px;"
                            onclick="actualizarTotal('<?php echo $registro_tiempo_sin_liquidar['id_tiempo']; ?>')">
                          </input>
                        <?php }else if(isset($registro_tiempo['estado_liquidado']) && $registro_tiempo['estado_liquidado'] == 1 && isset($lista_precio_productos_agregados['precio_total'])){?>
                          <input type="button"
                          style="background-image: url('https://w7.pngwing.com/pngs/184/296/png-transparent-update-button-miscellaneous-angle-trademark-thumbnail.png');
                                background-repeat: no-repeat;
                                background-position: center center;
                                background-size: cover;
                                border: none;
                                width: 35px;
                                height: 40px;"
                            onclick="actualizarTotal('<?php echo $registro_tiempo_sin_liquidar['id_tiempo']; ?>')">
                          </input>
                        <?php }else if(isset($registro_tiempo['estado_liquidado']) && $registro_tiempo['estado_liquidado'] == 1 && !isset($lista_precio_productos_agregados['precio_total'])){?>
                          <input type="button"
                          style="background-image: url('https://w7.pngwing.com/pngs/184/296/png-transparent-update-button-miscellaneous-angle-trademark-thumbnail.png');
                                background-repeat: no-repeat;
                                background-position: center center;
                                background-size: cover;
                                border: none;
                                width: 35px;
                                height: 40px;"
                            onclick="actualizarTotal('<?php echo $registro_tiempo_sin_liquidar['id_tiempo']; ?>')">
                          </input>
                        <?php }else if (isset($lista_precio_productos_agregados['precio_total'])){?>
                          <input type="button"
                          style="background-image: url('https://w7.pngwing.com/pngs/184/296/png-transparent-update-button-miscellaneous-angle-trademark-thumbnail.png');
                                background-repeat: no-repeat;
                                background-position: center center;
                                background-size: cover;
                                border: none;
                                width: 35px;
                                height: 40px;"
                            onclick="actualizarTotal('<?php echo $registro_tiempo_sin_liquidar['id_tiempo']; ?>')">
                          </input>
                        <?php }?>
                      </th>
                    </tr>
                </thead>
                <tbody>

                <?php if($registro_cuenta['precio_cuenta']!=0){?>
                    <tr class="" valign="middle" align="center">

                        <td rowspan="2">
                        <?php if (isset($registro_tiempo['estado_tiempo'])) {
                          if($registro_tiempo['estado_tiempo'] == 0 && $registro_tiempo['estado_liquidado'] == 0){?>
                          <button type="submit" value=""
                            id="button_iniciar_<?php echo $array_name_cuenta[$x] ?>"
                            name=""
                            onclick="iniciarTiempo('<?php echo $array_name_cuenta[$x] ?>', '<?php echo $registro_cuenta['id_cuenta'] ?>')"
                            class="btn btn-success">Iniciar
                          </button>
                        <?php }}else{?>
                          <button type="submit" value=""
                            id="button_iniciar_<?php echo $array_name_cuenta[$x] ?>"
                            name=""
                            onclick="iniciarTiempo('<?php echo $array_name_cuenta[$x] ?>', '<?php echo $registro_cuenta['id_cuenta'] ?>')"
                            class="btn btn-success">Iniciar
                          </button>
                        <?php }?>
                        </td>
                        <td><strong>T. Inicio</strong></td>
                        <td><strong>T. Actual</strong></td>
                        <td><strong>Transcurrido</strong></td>
                        <td><strong>Valor</strong></td>
                        <td rowspan="2">
                        <?php if (isset($registro_tiempo['estado_tiempo']) && $registro_tiempo['estado_tiempo'] == 1) {?>
                          <button type="submit" value=""
                            id="button_detener_<?php echo $array_name_cuenta[$x] ?>"
                            name=""
                            onclick="detenerTiempo('<?php echo $array_name_cuenta[$x] ?>', '<?php echo $registro_tiempo['id_cuenta'] ?>', '<?php echo $registro_tiempo['id_tiempo'] ?>')"
                            class="btn btn-danger">Detener
                          </button>
                        <?php }?>
                        <?php if (isset($registro_tiempo['estado_tiempo']) && $registro_tiempo['estado_tiempo'] == 0 && $registro_tiempo['estado_liquidado'] == 1) {?>
                          <button type="submit" value=""
                            id="button_detener_<?php echo $array_name_cuenta[$x] ?>"
                            name=""
                            onclick="continuarTiempo('<?php echo $array_name_cuenta[$x] ?>', '<?php echo $registro_tiempo['id_cuenta'] ?>', '<?php echo $registro_tiempo['id_tiempo'] ?>')"
                            class="btn btn-info">Continuar
                          </button>
                        <?php }?>
                        </td>
                    </tr>
                
                    <tr class="" valign="middle" align="center">

                        <td id="tiempo_inicio_<?php echo $array_name_cuenta[$x] ?>">
                        <?php
                          if (isset($registro_tiempo['fecha_inicio']) && $registro_tiempo['estado_tiempo']==1) {
                            echo date('h:i:s A', strtotime($registro_tiempo['fecha_inicio']));
                          } else if (isset($registro_tiempo['fecha_inicio']) && $registro_tiempo['estado_liquidado']==1) {
                            echo date('h:i:s A', strtotime($registro_tiempo['fecha_inicio']));
                          } else {
                            echo "00:00:00";
                          }
                        ?>
                        </td>
                        <td id="tiempo_actual_<?php echo $array_name_cuenta[$x] ?>">
                        <?php
                          if (isset($registro_tiempo['fecha_fin']) && $registro_tiempo['estado_tiempo']==1) {
                              echo date('h:i:s A', strtotime($registro_tiempo['fecha_fin']));
                          } else if (isset($registro_tiempo['fecha_fin']) && $registro_tiempo['estado_liquidado']==1) {
                            echo date('h:i:s A', strtotime($registro_tiempo['fecha_fin']));
                          } else {
                              echo "00:00:00";
                          }
                        ?>
                        </td>
                        <td id="tiempo_transcurrido_<?php echo $array_name_cuenta[$x] ?>">
                        <?php
                          if (isset($registro_tiempo['tiempo_invertido']) && $registro_tiempo['estado_tiempo']==1) {
                            echo $registro_tiempo['tiempo_invertido'];
                          } else if (isset($registro_tiempo['tiempo_invertido']) && $registro_tiempo['estado_liquidado']==1) {
                            echo $registro_tiempo['tiempo_invertido'];
                          } else {
                              echo "00:00:00";
                          }
                        ?>
                        </td>
                        <td id="valor_tiempo_<?php echo $array_name_cuenta[$x] ?>">$
                        <?php
                          if (isset($registro_tiempo['precio_final']) && $registro_tiempo['estado_tiempo']==1) {
                            echo number_format($registro_tiempo['precio_final'], 1);
                          } else if (isset($registro_tiempo['precio_final']) && $registro_tiempo['estado_liquidado']==1) {
                            echo number_format($registro_tiempo['precio_final'], 1);
                          } else {
                              echo "0";
                          }
                        ?>
                        </td>
                    </tr>
                  <?php }?>
                </tbody>
            </table>

            <table class="table table-bordered" id="tabla_id">
              <?php
                $sentencia = $conexion->prepare("SELECT p.image, p.nombre_producto, SUM(e.precio_total) precio, SUM(cantidad) cantidad, p.id_producto, e.id_entrada
                FROM productos p
                INNER JOIN entradas e ON p.id_producto = e.id_producto
                INNER JOIN cuentas c ON c.id_cuenta = e.id_cuenta
                WHERE p.id_sede= '" . $_SESSION['id_sede'] . "'
                AND e.estado = 1 AND c.nombre_cuenta ='" . str_replace('_', ' ', $array_name_cuenta[$x]) . "'" .
                " GROUP BY p.id_producto");
                $sentencia->execute();
                $lista_productos2 = $sentencia->fetchAll(PDO::FETCH_ASSOC);
              ?>
                <thead>
                    <tr valign="middle" align="center">
                        <th scope="col">FOTO</th>
                        <th scope="col">NOMBRE</th>
                        <th scope="col">PRECIO</th>
                        <th scope="col">CANTIDAD</th>
                        <th scope="col">ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($lista_productos2 as $registro) {?>
                    <tr class="" valign="middle" align="center">
                        <td>
                          <img width="60"
                          src="<?php echo "../productos/" . $registro['image']; ?>"
                          class="img-fluid rounded" alt=""
                          style="background-repeat: no-repeat;
                              background-position: center center;
                              background-size: cover;
                              width: 50px;
                              height: 50px;">
                        </td>
                        <td id="nombre_p_<?php echo $array_name_cuenta[$x];?>_<?php echo $registro['id_producto'];?>"><?php echo $registro['nombre_producto']; ?></td>
                        <td id="precio_p_<?php echo $array_name_cuenta[$x];?>_<?php echo $registro['id_producto'];?>">$ <?php echo number_format(abs($registro['precio']), 1); ?></td>
                        <td id="cantidad_p_<?php echo $array_name_cuenta[$x];?>_<?php echo $registro['id_producto'];?>"><?php echo abs($registro['cantidad']); ?></td>
                        <td>
                          <button style="background-color: red; width: 30px; height: 30px; display: inline-block;"
                            onClick="agregarMenosProductoACuenta('<?php echo $registro['id_entrada']; ?>',
                            '<?php echo $registro['id_producto']; ?>')" class="nav-link text-green">-</button>

                          <button style="background-color: green; width: 30px; height: 30px; display: inline-block;"
                            onClick="agregarMasProductoACuenta('<?php echo $registro['id_entrada']; ?>',
                            '<?php echo $registro['id_producto']; ?>')" class="nav-link text-red2">+</button>

                            <button style="background-image: url('https://cdn-icons-png.flaticon.com/512/25/25008.png'); 
                            background-size: cover; width: 30px; height: 30px; display: inline-block;"
                              onClick="eliminarProducto('<?php echo $registro['id_entrada']; ?>',
                              '<?php echo $registro['id_producto']; ?>')" 
                              >.</button>
                        </td>
                    </tr>
                <?php }?>
                </tbody>
                
            </table>
            <?php 
              $sentencia = $conexion->prepare("SELECT * FROM tiempos t
              INNER JOIN cuentas c ON t.id_cuenta=c.id_cuenta WHERE c.nombre_cuenta='" . str_replace('_', ' ', $array_name_cuenta[$x]) . "'" .
              " AND c.id_sede='" . $_SESSION['id_sede'] . "' ORDER BY t.id_tiempo DESC LIMIT 1");
              $sentencia->execute();
              $registro_tiempo = $sentencia->fetch(PDO::FETCH_LAZY);

              $sentencia = $conexion->prepare("SELECT id_cuenta FROM cuentas WHERE nombre_cuenta='" . str_replace('_', ' ', $array_name_cuenta[$x]) . "'" .
                  " AND id_sede='" . $_SESSION['id_sede'] . "'");
                  $sentencia->execute();
                  $registro_cuenta = $sentencia->fetch(PDO::FETCH_LAZY);
                  
              $sentencia = $conexion->prepare("SELECT * FROM facturas f INNER JOIN cuentas c ON f.id_cuenta=c.id_cuenta 
              WHERE f.nombre_cuenta='" . str_replace('_', ' ', $array_name_cuenta[$x]) . "'" .
                  " AND c.id_sede='" . $_SESSION['id_sede'] . "' ORDER BY f.id_factura DESC LIMIT 1");
                  $sentencia->execute();
                  $registro_detalle_factura = $sentencia->fetch(PDO::FETCH_LAZY);

              $sentencia = $conexion->prepare("SELECT COUNT(*) AS cantidad FROM entradas e
              INNER JOIN productos p ON p.id_producto=e.id_producto WHERE e.estado=1 AND e.id_cuenta='" . $registro_cuenta['id_cuenta'] . "'" .
              " AND p.id_sede= '" . $_SESSION['id_sede'] . "'");
              $sentencia->execute();
              $cantidad_inventario_sin_liquidar = $sentencia->fetch(PDO::FETCH_ASSOC)['cantidad'];  
              ?>            
              <script>

                function liquidar2(id_cuenta_liquidar) {
                  Swal.fire({
                      title: '¿Estás seguro de liquidar?',
                      text: 'Esta acción no se puede deshacer',
                      icon: 'warning',
                      showCancelButton: true,
                      confirmButtonColor: '#3085d6',
                      cancelButtonColor: '#d33',
                      confirmButtonText: 'Sí, liquidar',
                      cancelButtonText: 'Cancelar'
                  }).then((result) => {
                      if (result.isConfirmed) {
                          $.ajax({
                              url: 'index.php',
                              method: 'POST',
                              data: { id_cuenta_liquidar: id_cuenta_liquidar },
                              success: function(response) {
                                  var urlpdf = "generar_factura.php?txtID=" + id_cuenta_liquidar;
                                  var urlindex = 'index.php';
                                  setTimeout(function() {
                                    window.open(urlpdf, "_blank");
                                  }, 2000);    
                                  setTimeout(function() {
                                    window.location.href = urlindex;
                                  }, 3000);
                                  //var urlpdf = "consulta.php?txtID=" + id_cuenta_liquidar;
                                  //window.open(urlpdf, "_blank");
                              },
                              error: function(xhr, textStatus, errorThrown) {
                                  console.log(xhr.responseText);
                              }
                          });
                      }
                  });
              }

              </script>
              <?php if(isset($registro_tiempo['estado_tiempo']) && $registro_tiempo['estado_tiempo']==1){?>

                <button type="button" class="btn btn-dark" id="liquidar_sin_tiempo_detenido_<?php echo $array_name_cuenta[$x] ?>" 
                 onclick="liquidar_sin_tiempo_detenido(<?php echo $registro_cuenta['id_cuenta']; ?>)">Liquidar</button>

              <?php }else if (isset($registro_tiempo['estado_liquidado']) && $registro_tiempo['estado_liquidado']==1){?>

                <button type="button" class="btn btn-dark" id="liquidar_<?php echo $array_name_cuenta[$x] ?>" 
                role="button" onclick="liquidar2('<?php echo $registro_cuenta['id_cuenta'];?>')">Liquidar</button>

              <?php }else if ($cantidad_inventario_sin_liquidar>0){?>

                <button type="button" class="btn btn-dark" id="liquidar_<?php echo $array_name_cuenta[$x] ?>" 
                role="button" onclick="liquidar2('<?php echo $registro_cuenta['id_cuenta'];?>')">Liquidar</button>

            <?php }?>
            
        </td>

      </tr>
    </tbody>
  </table>
</div>
<?php }?>
<?php }?>

<style>
  nav {
    padding: 10px;
  }
  .text-blue {
    color: red;
    background: white;
  }
  .text-red2 {
    color: white;
    background: red;
  }
  .text-green {
    color: white;
    background: green;
  }
  .nav-item.tab-6 {
    border: 1px solid black;
  }
  .table-header {
    text-align: center;
  }
  .my-td {
    border: 3px solid black;
  }
</style>


<?php include "../../templates/footer.php";?>

<script>

var tabId = "";

<?php echo $menu3; ?>

function changeActiveTab(Id, urlBase) {

  // Eliminar la clase "active" de todas las pestañas
  var tabs = document.querySelectorAll(".nav-link");

  for (var i = 0; i < tabs.length; i++) {
    tabs[i].classList.remove("active");
  }

  window.location.href = urlBase;

  tabId = Id;
  localStorage.setItem("tabId", tabId);

  document.getElementById(tabId).classList.add("active");
  document.getElementById("nav-cajas-tab").classList.add("active");

}

window.onhashchange = function() {
  // Obtener el nuevo valor del fragmento de la URL
  var nuevaSeccion = window.location.hash.substring(1); // Eliminar el símbolo '#' del inicio

  // Ocultar todas las secciones
  var secciones = document.getElementsByTagName("div");
  for (var i = 0; i < secciones.length; i++) {
    secciones[i].style.display = "none";
  }

  // Mostrar la sección correspondiente al nuevo valor del fragmento de la URL
  var seccionActual = document.getElementById(nuevaSeccion);
  if (seccionActual) {
    seccionActual.style.display = "block";
  }
}

window.onhashchange();

</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>

  function agregar() {
      var cuenta = $("#cuenta").val();
      var idCuenta = $("#cuenta option:selected").data("id");
      var estado = $('#cuenta option:selected').data('estado');
      $.ajax({
        url: 'index.php',
        method: 'POST',
        data: { estado: 1, idCuenta: idCuenta },
        success: function(response) {
          $('#nav-' + cuenta + '-tab').show();
          Swal.fire({
            title: 'Se agrego correctamente!',
            icon: 'success',
            timer: 2000,
            timerProgressBar: true,
            didClose: () => {
              location.reload();
            }
          });
        },
        error: function(xhr, textStatus, errorThrown) {
          console.log(xhr.responseText);
        }
      });
  }
  function quitar() {
      var cuenta2 = $("#cuenta2").val();
      var idCuenta = $("#cuenta2 option:selected").data("id");
      var estado = $('#cuenta2 option:selected').data('estado');
      $.ajax({
        url: 'index.php',
        method: 'POST',
        data: { estado: 0, idCuenta: idCuenta },
        success: function(response) {
          $('#nav-' + cuenta2 + '-tab').hide();
          Swal.fire({
            title: 'Se quito correctamente!',
            icon: 'success',
            timer: 2000,
            timerProgressBar: true,
            didClose: () => {
              location.reload();
            }
          });
        },
        error: function(xhr, textStatus, errorThrown) {
          console.log(xhr.responseText);
        }
      });
  }

  function abrirCaja() {
    var abrir_caja = ""
    $.ajax({
      url: 'index.php',
      method: 'POST',
      data: { abrir_caja: abrir_caja },
      success: function(response) {
        Swal.fire({
          title: 'Caja abierta!',
          icon: 'success',
          timer: 2000,
          timerProgressBar: true,
          didClose: () => {
            location.reload();
          }
        });
        //location.reload();
      },
      error: function(xhr, textStatus, errorThrown) {
        console.log(xhr.responseText);
      }
    });
  }

  function cerrarCaja(id_caja) {
  Swal.fire({
    title: '¿Está seguro de que desea cerrar la caja?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí',
    cancelButtonText: 'Cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: 'index.php',
        method: 'POST',
        data: { id_caja: id_caja },
        success: function(response) {
          var urlpdf = "factura_del_dia.php";
          Swal.fire({
            title: 'Caja cerrada!',
            icon: 'success',
            timer: 2000,
            timerProgressBar: true,
            didClose: () => {
              location.reload();
              window.open(urlpdf, "_blank");
            }
          });
        },
        error: function(xhr, textStatus, errorThrown) {
          console.log(xhr.responseText);
        }
      });
    }
  });
}


  function cerrar_caja_sin_liquidar() {
      Swal.fire({
        title: 'No se puede cerrar la caja hasta que se hayan liquidado todos los producto y detenido todos los tiempos de las mesas.',
        icon: 'error',
        timer: 5000,
        timerProgressBar: true,
        didClose: () => {
          location.reload();
      }
    });

  }

  function agregarProductoACuenta(nombre_cuenta_temporal, id_producto_temporal, id_cuenta_temporal, precio) {
    var nombreProducto = document.getElementById("nombre_p_" + nombre_cuenta_temporal + "_" + id_producto_temporal); 
    var precioProducto = document.getElementById("precio_p_" + nombre_cuenta_temporal + "_" + id_producto_temporal); 
    var cantidadProducto = document.getElementById("cantidad_p_" + nombre_cuenta_temporal + "_" + id_producto_temporal);
    var precioTotal = document.getElementById("precio_" + nombre_cuenta_temporal); 
    
    if (nombreProducto) {
      $.ajax({
        url: 'index.php',
        method: 'POST',
        data: { id_cuenta_temporal: id_cuenta_temporal, id_producto_temporal: id_producto_temporal },
        success: function(response) {
          var precioActual = parseFloat(precioProducto.innerHTML.replace(/[^0-9.]/g, ''));
          var precioActualTotal = parseFloat(precioTotal.innerHTML.replace(/[^0-9.]/g, ''));
          if (!isNaN(precioActual)) {
          var nuevoPrecio = precioActual + parseInt(precio);
          var nuevoPrecioTotal = precioActualTotal + parseInt(precio);
          precioProducto.innerHTML = "$ " + nuevoPrecio.toLocaleString(undefined, { minimumFractionDigits: 1 }).replace(/\./g, "@").replace(/,/g, ".").replace(/@/g, ",");
          precioTotal.innerHTML = "$ " + nuevoPrecioTotal.toLocaleString(undefined, { minimumFractionDigits: 1 }).replace(/\./g, "@").replace(/,/g, ".").replace(/@/g, ",");
          } else {
            console.log("El contenido de precioProducto no es un número válido.");
          }
          var cantidadActual = parseInt(cantidadProducto.innerHTML);
          if (!isNaN(cantidadActual)) {
            cantidadProducto.innerHTML = cantidadActual + 1;
          } else {
            console.log("El contenido de cantidadProducto no es un número válido.");
          }
        },
        error: function(xhr, textStatus, errorThrown) {
          console.log(xhr.responseText);
        }
      });
    } else {
      $.ajax({
        url: 'index.php',
        method: 'POST',
        data: { id_cuenta_temporal: id_cuenta_temporal, id_producto_temporal: id_producto_temporal },
        success: function(response) {
          setTimeout(function(){
            location.reload();
          }, 500);
        },
        error: function(xhr, textStatus, errorThrown) {
          console.log(xhr.responseText);
        }
      });
    }
  }

  function agregarMasProductoACuenta(id_entrada_mas, id_producto_mas) {
    $.ajax({
      url: 'index.php',
      method: 'POST',
      data: { id_entrada_mas: id_entrada_mas, id_producto_mas: id_producto_mas },
      success: function(response) {
        setTimeout(function(){
          location.reload();
        }, 500);
      },
      error: function(xhr, textStatus, errorThrown) {
        console.log(xhr.responseText);
      }
    });
  }

  function agregarMenosProductoACuenta(id_entrada_menos, id_producto_menos) {
    $.ajax({
      url: 'index.php',
      method: 'POST',
      data: { id_entrada_menos: id_entrada_menos, id_producto_menos: id_producto_menos },
      success: function(response) {
        setTimeout(function(){
          location.reload();
        }, 500);
      },
      error: function(xhr, textStatus, errorThrown) {
        console.log(xhr.responseText);
      }
    });
  }

  function eliminarProducto(id_entrada_borrar) {
    if (confirm("¿Está seguro de que desea eliminar este producto?")) {
        $.ajax({
            url: 'index.php',
            method: 'POST',
            data: { id_entrada_borrar: id_entrada_borrar },
            success: function(response) {
                location.reload();
            },
            error: function(xhr, textStatus, errorThrown) {
                console.log(xhr.responseText);
            }
        });
    }
  }

  function iniciarTiempo(nombre_cuenta, id_cuenta) {

    var fechaActual = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit', second: '2-digit'});

    $.ajax({
      url: 'index.php',
      method: 'POST',
      data: { fechaActual: fechaActual, id_cuenta: id_cuenta },
      success: function(response) {
        setTimeout(function(){
          location.reload();
        }, 500);
      },
      error: function(xhr, textStatus, errorThrown) {
        console.log(xhr.responseText);
      }
    });

  }

  function detenerTiempo(nombre_cuenta, id_cuenta_end, id_tiempo) {
    $.ajax({
      url: 'index.php',
      method: 'POST',
      data: { id_cuenta_end: id_cuenta_end, id_tiempo: id_tiempo },
      success: function(response) {
        setTimeout(function(){
          location.reload();
        }, 500);
      },
      error: function(xhr, textStatus, errorThrown) {
        console.log(xhr.responseText);
      }
    });

  }

  
  function actualizarTotal(actualizar_id_tiempo) {
    $.ajax({
      url: 'index.php',
      method: 'POST',
      data: { actualizar_id_tiempo: actualizar_id_tiempo },
      success: function(response) {
        setTimeout(function(){
          location.reload();
        }, 500);
      },
      error: function(xhr, textStatus, errorThrown) {
        console.log(xhr.responseText);
      }
    });

  }

   function continuarTiempo(nombre_cuenta, id_cuenta_continuar, id_tiempo) {
    $.ajax({
      url: 'index.php',
      method: 'POST',
      data: { id_cuenta_continuar: id_cuenta_continuar, id_tiempo: id_tiempo },
      success: function(response) {
        setTimeout(function(){
          location.reload();
        }, 500);
      },
      error: function(xhr, textStatus, errorThrown) {
        console.log(xhr.responseText);
      }
    });

  }

  function actualizarTiempo(id_nombre_cuenta, fecha_inicial, precio_final, precio_cuenta, precio_productos) {
    
    var precioTotal = 0;
    var tdTiempo = document.getElementById("tiempo_actual_" + id_nombre_cuenta);
    var tdTranscurrido = document.getElementById("tiempo_transcurrido_" + id_nombre_cuenta);
    var tdValorTiempo = document.getElementById("valor_tiempo_" + id_nombre_cuenta);
    var tdPrecio = document.getElementById("precio_" + id_nombre_cuenta);

    if(precio_final!=0){
      precioTotal=precio_final;
    }else if(fecha_inicial == 0){
      precioTotal=0;
    }else {
      var fecha_actual = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit', second: '2-digit'});
      var fechaActual = obtenerFechaActual();

      var date1 = new Date(fecha_inicial);
      var date2 = new Date(fechaActual);

      var diff = Math.abs(date1 - date2);

      var hours = Math.floor(diff / 3600000);
      var minutes = Math.floor((diff % 3600000) / 60000);
      var seconds = Math.floor(((diff % 3600000) % 60000) / 1000);

      hours = ("0" + hours).slice(-2);
      minutes = ("0" + minutes).slice(-2);
      seconds = ("0" + seconds).slice(-2);

      var tiempoTranscurrido = hours + ":" + minutes + ":" + seconds;
      var tiempoEnSegundos = ((hours * 3600 + minutes * 60 + seconds) / 3600);

      var partes = tiempoTranscurrido.split(":");
      var horas = parseInt(partes[0]) + parseInt(partes[1])/60 + parseInt(partes[2])/3600;

      //var tiemporecorrido = parseFloat(hours + "." + minutes + seconds);
      precioTotal = (horas * precio_cuenta);
    }

    $.ajax({
      url: 'index.php',
      method: 'POST',
      data: { id_nombre_cuenta: id_nombre_cuenta },
      success: function(response) {
        if(precio_final!=0){
          tdPrecio.innerHTML = "$ " + (parseFloat(precioTotal) + Math.abs(parseFloat(precio_productos))).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }else if(fecha_inicial == 0){
          tdPrecio.innerHTML = "$ " + (Math.abs(parseFloat(precio_productos))).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }else{
          tdTiempo.innerHTML = fecha_actual;
          tdTranscurrido.innerHTML = tiempoTranscurrido;
          tdValorTiempo.innerHTML = "$ " + parseFloat(precioTotal.toFixed(1));
          tdPrecio.innerHTML = "$ " + (parseFloat(precioTotal.toFixed(1)) + Math.abs(parseFloat(precio_productos))).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }
      },
      error: function(xhr, textStatus, errorThrown) {
        console.log(xhr.responseText);
      }
    });
  }

  function obtenerFechaActual() {
    let now = new Date();
    let year = now.getFullYear();
    let month = now.getMonth() + 1;
    let day = now.getDate();
    let hour = now.getHours();
    let minute = now.getMinutes();
    let second = now.getSeconds();
    let timezoneOffset = now.getTimezoneOffset() / 60;

    let timezoneOffsetFormatted = Math.abs(timezoneOffset).toLocaleString('en-US', {
      minimumIntegerDigits: 2,
      useGrouping: false
    });

    let timezoneOffsetSign = timezoneOffset >= 0 ? '-' : '+';

    let timezoneOffsetFormattedFull = timezoneOffsetSign + timezoneOffsetFormatted + ':00';

    let fechaActual = year + '-' +
      ('0' + month).slice(-2) + '-' +
      ('0' + day).slice(-2) + ' ' +
      ('0' + hour).slice(-2) + ':' +
      ('0' + minute).slice(-2) + ':' +
      ('0' + second).slice(-2);

    return fechaActual;
  }

  function liquidar_sin_tiempo_detenido(id_cuenta_liquidar) {
    Swal.fire({
        title: 'Debe Detener el tiempo de la mesa antes de liquidar',
        icon: 'error',
        timer: 5000,
        timerProgressBar: true,
        didClose: () => {
          location.reload();
      }
    });
  }
  
</script>

</body>

