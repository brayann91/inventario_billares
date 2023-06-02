<?php

include "../../bd.php";

include "../../templates/header.php";

$sentencia = $conexion->prepare("SELECT SUM(cantidad) cantidad, e.id_entrada, p.image, p.nombre_producto, p.precio, e.precio_total, e.fecha
FROM productos p INNER JOIN entradas e Where e.id_producto=p.id_producto and p.id_sede= " . $_SESSION['id_sede'] . " GROUP BY p.id_producto");
$sentencia->execute();
$lista_entradas = $sentencia->fetchAll(PDO::FETCH_ASSOC);

?>

  <h4>INVENTARIO:</h4>

  <br/>
  <script src="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/jquery.table2excel.min.js"></script>
  <div class="card">
    <div class="card-header">

    <?php if ($_SESSION['id_cargo'] != 2) {?>
        <a name="" id="" class="btn btn-primary"
        href="crear.php"
        role="button">
        Agregar Stock</a>

        <a name="" id="" class="btn btn-danger"
        href="quitar.php"
        role="button">
        Quitar Stock</a>

        <button id="exportButton" type="submit" class="btn btn-success">Exportar a Excel</button>
    <?php }?>

    </div>
    <div class="card-body">

    <div class="table-responsive-sm">
        <table class="table" id="tabla_id">
            <thead>
                <tr valign="middle" align="center">
                    <th scope="col">ID</th>
                    <th scope="col">FOTO</th>
                    <th scope="col">NOMBRE</th>
                    <th scope="col">CANTIDAD</th>
                    <th scope="col">PRECIO UNITARIO</th>
                    <th scope="col">FECHA DE ENTRADA</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($lista_entradas as $registro) {?>
                <tr class="" valign="middle" align="center">
                    <td scope="row"><?php echo $registro['id_entrada']; ?></td>
                    <td>
                        <img width="60"
                        src="<?php echo "../productos/" . $registro['image']; ?>"
                        class="img-fluid rounded" alt="">
                    </td>
                    <td><?php echo $registro['nombre_producto']; ?></td>
                    <td><?php echo $registro['cantidad']; ?></td>
                    <td>$ <?php echo number_format($registro['precio'], 1); ?></td>
                    <!-- <td>$ <?php echo number_format($registro['precio_total'], 1); ?></td> -->
                    <td><?php echo $registro['fecha']; ?></td>

                </tr>
            <?php }?>
            </tbody>
        </table>
    </div>


    </div>

  </div>

  


<!-- Resto del código HTML -->

<script>
$(function() {
        $("#exportButton").click(function(e){
          var table = $("#tabla_id");
          if(table && table.length){
            $(table).table2excel({
              exclude: ".noExl",
              name: "Excel Document Name",
              filename: "Inventario" + new Date().toISOString().replace(/[\-\:\.]/g, "") + ".xls",
              fileext: ".xls",
              exclude_img: true,
              exclude_links: true,
              exclude_inputs: true,
              preserveColors: false
            });
          }
        });
        
      });
</script>


<?php include "../../templates/footer.php";?>
