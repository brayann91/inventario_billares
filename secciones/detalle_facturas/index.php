<?php

include "../../bd.php";

include "../../templates/header.php";

$sentencia = $conexion->prepare("SELECT * FROM facturas t INNER JOIN cuentas c 
WHERE c.id_cuenta=t.id_cuenta and c.id_sede = " . $_SESSION['id_sede'] . " ORDER BY DESC");
$sentencia->execute();
$lista_facturas = $sentencia->fetchAll(PDO::FETCH_ASSOC);

?>

<br/>

  <h4>DETALLE DE FACTURAS:</h4>

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
                    <th scope="col">CUENTA</th>
                    <th scope="col">FECHA</th>
                    <th scope="col">PRODUCTO</th>
                    <th scope="col">CANTIDAD</th>
                    <th scope="col">PRECIO PRO.</th>
                    <th scope="col">VALOR TOTAL P.</th>
                    <th scope="col">PRECIO TIE.</th>
                    <th scope="col">VALOR TOTAL T.</th>
                    <th scope="col">T. INVERTIDO</th>
                    <th scope="col">FACTURA #</th>
                </tr>
            </thead>
            <tbody>

            <?php foreach ($lista_facturas as $registro) {?>
                <tr class="" valign="middle" align="center">
                    <td scope="row"><?php echo $registro['id_factura']; ?></td>
                    <td><?php echo $registro['nombre_cuenta']; ?></td>
                    <td scope="row"><?php echo date('M-d H:i:s', strtotime($registro['fecha'])); ?></td>
                    <td scope="row"><?php echo $registro['nombre_producto']; ?></td>
                    <td scope="row"><?php echo $registro['cantidad']; ?></td>
                    <td>$ <?php echo number_format($registro['precio_producto'], 1); ?></td>
                    <td>$ <?php echo number_format($registro['precio_total_producto'], 1); ?></td>
                    <td>$ <?php echo number_format($registro['precio_tiempo'], 1); ?></td>
                    <td>$ <?php echo number_format($registro['precio_total_tiempo'], 1); ?></td>
                    <td><?php echo $registro['tiempo_invertido']; ?></td>
                    <td><?php echo $registro['id_facturas']; ?></td>
                </tr>
            <?php }?>
            </tbody>
        </table>
    </div>


    </div>

  </div>


<?php include "../../templates/footer.php";?>