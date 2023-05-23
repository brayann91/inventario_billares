<?php 

include "../../bd.php";

include "../../templates/header.php";

if(isset($_GET['txtID'])){

    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";

    $sentencia = $conexion->prepare("SELECT p.nombre_producto, e.cantidad, e.precio_total FROM entradas e INNER JOIN productos p ON p.id_producto=e.id_producto WHERE e.estado=1 AND e.id_cuenta=:id_cuenta");
    $sentencia->bindParam(":id_cuenta", $txtID);
    $sentencia->execute();
    $inventario_sin_liquidar = $sentencia->fetchAll(PDO::FETCH_ASSOC);

    $sentencia = $conexion->prepare("SELECT * FROM sedes WHERE id_sede='" . $_SESSION['id_sede'] . "'");
    $sentencia->execute();
    $registro_sedes = $sentencia->fetch(PDO::FETCH_LAZY);

    $sentencia = $conexion->prepare("SELECT nombre_cuenta FROM cuentas WHERE id_cuenta=:id_cuenta");
    $sentencia->bindParam(":id_cuenta", $txtID);
    $sentencia->execute();
    $registro_cuentas = $sentencia->fetch(PDO::FETCH_LAZY);
                
    $sentencia = $conexion->prepare("SELECT * FROM tiempos t
    INNER JOIN cuentas c ON t.id_cuenta=c.id_cuenta WHERE c.id_cuenta=:id_cuenta
    AND c.id_sede=" . $_SESSION['id_sede'] . " AND estado_liquidado = 1");
    $sentencia->bindParam(":id_cuenta", $txtID);
    $sentencia->execute();
    $tiempo_sin_liquidar = $sentencia->fetch(PDO::FETCH_LAZY);

    $total = 0;
    $tiempo = 0;

    foreach ($inventario_sin_liquidar as $registro) {
        $total += abs($registro["precio_total"]);
    }

    if(isset($tiempo_sin_liquidar["precio_final"])){
        $total += $tiempo_sin_liquidar["precio_final"];
    }
}

if ($_POST) {
    $txtID = (isset($_GET['txtID'])) ? $_GET['txtID'] : "";

    $sentencia = $conexion->prepare("SELECT * FROM facturas f INNER JOIN cuentas c ON f.id_cuenta=c.id_cuenta 
    WHERE f.id_cuenta=:id_cuenta" .
    " AND c.id_sede='" . $_SESSION['id_sede'] . "' ORDER BY f.id_factura DESC LIMIT 1");
    $sentencia->bindParam(":id_cuenta", $txtID);
    $sentencia->execute();
    $registro_detalle_factura = $sentencia->fetch(PDO::FETCH_LAZY);
    ?>

    <script>
        function abrirPDF() {
            var url_pdf = '<?php echo $registro_detalle_factura['id_facturas']; ?>';
            window.open("generar_factura.php?txtID=" + url_pdf, '_blank');
        }
    </script>
<?php } ?>


<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
    }.card {
        margin: 0 0px; /* Márgenes a la izquierda y derecha */
    }
    table {
        width: 100%;
        border-collapse: collapse;
        border: none;
    }
    td, th {
        padding: 2px;
        font-size: 12px; /* Tamaño de fuente por defecto */
    }
    .text-right {
        text-align: right;
    }
    .header {
        margin-bottom: 10px;
        text-align: center;
    }
    .header p {
        margin: 0;
    }
    .header h3 {
        margin: 5px 0;
    }
    .footer {
        margin-top: 10px;
    }
    .footer td {
        padding-top: 5px;
        border-top: 1px solid black;
    }
    .footer .text-right {
        font-weight: bold;
    }
    .cantidad {
        text-align: center;
    }
    /* Estilo para el tamaño de fuente aumentado */
    .large-font {
        font-size: 22px;
    }
</style>

<script>

function liquidar(id_cuenta_liquidar) {
    var cuenta = '<?php echo str_replace(" ", "_", $registro_cuentas['nombre_cuenta']); ?>';
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
                    var url = 'index.php';
                    setTimeout(function() {
                        window.location.href = url;
                    }, 3000);
                    pdf(id_cuenta_liquidar);
                },
                error: function(xhr, textStatus, errorThrown) {
                    console.log(xhr.responseText);
                }
            });
        }
    });
}

function pdf(txtID) {
  var url = "generar_factura.php?txtID=" + txtID;
  setTimeout(function() {
    window.open(url, "_blank");
  }, 4000);
}

</script>

<div class="header">
    <h3 style="font-size: 34px;">Billar <?php echo $registro_sedes["nombre_sede"]; ?></h3>
    <p style="font-size: 28px;">Dirección <?php echo $registro_sedes["direccion_sede"]; ?></p>
    <p style="font-size: 28px;">Teléfono: <?php echo $registro_sedes["telefono_sede"]; ?></p>
    </br></br>        
</div>

<div class="card">
    <div class="card-header">
        Liquidar
    </div>
    <div class="card-body">
        <form action="" method="post" enctype="multipart/form-data">
            <table>
                <thead>
                    <tr>
                        <th class="large-font">Producto<br><br></th>
                        <th class="text-center align-middle cantidad large-font">Cantidad<br><br></th>
                        <th class="text-right large-font">Precio<br><br></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($inventario_sin_liquidar as $registro){ ?>
                        <tr>
                            <td class="large-font"><?php echo $registro["nombre_producto"] ?></td>
                            <td class="text-center align-middle cantidad large-font"><?php echo abs($registro["cantidad"]) ?></td>
                            <td class="text-right large-font">$ <?php echo number_format(abs($registro["precio_total"]), 0) ?></td>
                        </tr>
                    <?php } ?>
                    <?php if(isset($tiempo_sin_liquidar["tiempo_invertido"])) {?>
                        <tr>
                            <td class="large-font"><br>Tiempo:</td>
                            <td class="text-center align-middle cantidad large-font"><br><?php echo $tiempo_sin_liquidar["tiempo_invertido"]; ?></td>
                            <td class="text-right large-font"><br>$ <?php echo number_format(abs($tiempo_sin_liquidar["precio_final"]), 0); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" class="large-font"><br>Total:</td>
                        <td class="text-right large-font" style="font-weight: bold;"><br>$ <?php echo number_format(abs($total), 0); ?></td>
                    </tr>
                </tfoot>
            </table>
            <br>
            <br>
            <div style="text-align: center;">
                <button type="button" class="btn btn-success" onclick="liquidar('<?php echo $txtID;?>')">Liquidar</button>
                <a name="" id="" class="btn btn-secondary" href="index.php" role="button">Cancelar</a>
            </div>
            <div class="mb-3">
                <h5 style="display: none;" name="txtID" id="txtID"><?php echo $txtID;?></h5>
            </div>
        </form>
    </div>
</div>

<div class="footer">
    <table>
        <tr>
            <td>Gracias por su compra</td>
            <td class="text-right">IVA incluido</td>
        </tr>
    </table>
</div>



<?php include "../../templates/footer.php";?>