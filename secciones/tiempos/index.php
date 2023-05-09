<?php

include "../../bd.php";

include "../../templates/header.php";

$sentencia = $conexion->prepare("SELECT * FROM tiempos t INNER JOIN cuentas c 
WHERE c.id_cuenta=t.id_cuenta and c.id_sede = " . $_SESSION['id_sede'] . " GROUP BY t.id_tiempo");
$sentencia->execute();
$lista_entradas = $sentencia->fetchAll(PDO::FETCH_ASSOC);

?>

<br/>

  <h4>TIEMPOS VENDIDOS POR CUENTA:</h4>

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
                    <th scope="col">NOMBRE CUENTA</th>
                    <th scope="col">PRECIO POR HORA</th>
                    <th scope="col">FECHA INICIO</th>
                    <th scope="col">FECHA FIN</th>
                    <th scope="col">T. EN HORAS</th>
                    <th scope="col">PRECIO FINAL</th>
                    <th scope="col">ESTADO</th>
                    <!-- <?php if ($_SESSION['id_cargo'] != 2) {?>
                        <th scope="col">ACCIONES</th>
                    <?php }?> -->
                </tr>
            </thead>
            <tbody>

            <?php foreach ($lista_entradas as $registro) {?>
                <tr class="" valign="middle" align="center">
                    <td scope="row"><?php echo $registro['id_tiempo']; ?></td>
                    <td><?php echo $registro['nombre_cuenta']; ?></td>
                    <td>$ <?php echo number_format($registro['precio_cuenta'], 1); ?></td>
                    <td><?php echo date('M-d H:i:s', strtotime($registro['fecha_inicio'])); ?></td>
                    <td><?php echo date('M-d H:i:s', strtotime($registro['fecha_fin'])); ?></td>
                    <td><?php echo $registro['tiempo_invertido']; ?></td>
                    <td>$ <?php echo number_format($registro['precio_final'], 1); ?></td>
                    <td>
                        <?php 
                          if ($registro['estado_tiempo'] == 1){
                            echo "Abierto";
                          }else{
                            echo "Cerrado";
                          }
                        ?>
                     </td>
                    <?php if ($_SESSION['id_cargo'] != 2) {?>
                        <!-- <td>
                            <a name="" id="editar_tiempo" class="btn btn-info" href="editar.php?txtID=<?php echo $registro['id_tiempo']; ?>" role="button">Editar</a>
                            <a name="" id="eliminar_tiempo" class="btn btn-danger" href="javascript:borrar(<?php echo $registro['id_tiempo']; ?>);" role="button">Borrar</a>
                        </td> -->
                    <?php }?>
                </tr>
            <?php }?>
            </tbody>
        </table>
    </div>


    </div>

  </div>


<?php include "../../templates/footer.php";?>