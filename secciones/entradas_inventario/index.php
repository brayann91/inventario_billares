<?php

include "../../bd.php";

include "../../templates/header.php";

$sentencia = $conexion->prepare("SELECT e.cantidad, e.id_entrada, p.image, p.nombre_producto, p.precio, e.precio_total, e.fecha
FROM productos p INNER JOIN entradas e Where e.id_producto=p.id_producto AND p.id_sede= " . $_SESSION['id_sede'] . " AND e.cantidad >= 0");
$sentencia->execute();
$lista_entradas = $sentencia->fetchAll(PDO::FETCH_ASSOC);

?>

  <h4>INVENTARIO:</h4>

  <br/>

  <div class="card">
    <div class="card-header">
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


<?php include "../../templates/footer.php";?>