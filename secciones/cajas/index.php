<?php

include "../../bd.php";

include "../../templates/header.php";

if($_SESSION['id_cargo']!=2){ 
  $sentencia = $conexion->prepare("SELECT * FROM cajas 
  WHERE id_sede = " . $_SESSION['id_sede'] . "");
  $sentencia->execute();
  $lista_facturas = $sentencia->fetchAll(PDO::FETCH_ASSOC);
}else{
  $sentencia = $conexion->prepare("SELECT * FROM cajas 
  WHERE valor>=0 AND id_sede = " . $_SESSION['id_sede'] . "");
  $sentencia->execute();
  $lista_facturas = $sentencia->fetchAll(PDO::FETCH_ASSOC);
}
?>

<br/>

  <h4>REGISTRO DE LA CAJA:</h4>

  <br/>

  <div class="card">
    <div class="card-header">
     <?php if($_SESSION['id_cargo']!=2){ ?>
        <a name="" id="" class="btn btn-success" 
        href="crear.php" 
        role="button">
        Agregar Dinero a Caja</a>

        <a name="" id="" class="btn btn-danger" 
        href="quitar.php" 
        role="button">
        Retirar Dinero de Caja</a>
    <?php } ?>
     </div>
    <div class="card-body">

    <div class="table-responsive-sm">
        <table class="table" id="tabla_id">
            <thead>
                <tr valign="middle" text-align="center">
                    <th scope="col">ID</th>
                    <th scope="col">FECHA APERTURA</th>
                    <th scope="col">FECHA CIERRE</th>
                    <th scope="col">REGISTRO DE VALOR</th>
                    <?php if($_SESSION['id_cargo']!=2){ ?>
                      <th scope="col">CANTIDAD EN CAJA</th>
                    <?php } ?>
                    <th scope="col">ESTADO CAJA</th>
                    <th scope="col">FACTURA CAJA</th>
                </tr>
            </thead>
            <tbody>

            <?php foreach ($lista_facturas as $registro) {?>
                <tr class="" valign="middle" align="center">
                    <td scope="row"><?php echo $registro['id_caja']; ?></td>
                    <td><?php echo $registro['fecha_apertura']; ?></td>
                    <td><?php echo $registro['fecha_cierre']; ?></td>
                    <td>$ <?php echo number_format($registro['valor'], 1); ?></td>
                    <?php if($_SESSION['id_cargo']!=2){ ?>
                      <td>$ <?php echo number_format($registro['valor_acomulado'], 1); ?></td>
                    <?php } ?>
                    <td>
                        <?php 
                          if ($registro['estado'] == 1){
                            echo "Abierta";
                          }else{
                            echo "Cerrada";
                          }
                        ?>
                    </td>
                    <td>
                      <?php if($registro['pdf_caja']<>""){ ?>
                        <a class="btn btn-info" href="../../pdf/factura_caja_<?php echo $registro['id_caja']; ?>.pdf" target="_blank">Factura</a>
                      <?php }?>
                    </td>
                </tr>
            <?php }?>
            </tbody>
        </table>
    </div>


    </div>

  </div>


<?php include "../../templates/footer.php";?>