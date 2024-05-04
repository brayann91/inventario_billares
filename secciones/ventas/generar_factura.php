<?php

    require_once '../../libs/dompdf/vendor/autoload.php'; // carga la biblioteca DOMPDF

    ob_start();
    
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <title>Factura</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                font-size: 12px;
                margin: 0;
            }
            @page {
                size: 70mm 150mm;
                margin: 0.5cm;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                border: none;
            }
            td, th {
                padding: 2px;
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
        </style>
    </head>
    <body>

    <?php 
    
    include "../../bd.php";
    session_start();

    $txtID = (isset($_GET['txtID'])) ? $_GET['txtID'] : "";

    $sentencia = $conexion->prepare("SELECT * FROM factura_agrupada WHERE id_sede=" . $_SESSION['id_sede'] . " 
    AND id_cuenta=:id_cuenta ORDER BY id_factura DESC LIMIT 1");
    $sentencia->bindParam(":id_cuenta", $txtID);
    $sentencia->execute();
    $registro_factura_agrupada = $sentencia->fetch(PDO::FETCH_LAZY);

    $ID = $registro_factura_agrupada['id_factura'];

    $sentencia = $conexion->prepare("SELECT * FROM cuentas WHERE id_cuenta=:id_cuenta");
    $sentencia->bindParam(":id_cuenta", $txtID);
    $sentencia->execute();
    $registro_cuenta = $sentencia->fetch(PDO::FETCH_LAZY);

    $sentencia = $conexion->prepare("SELECT * FROM facturas WHERE id_facturas=:id_facturas");
    $sentencia->bindParam(":id_facturas", $ID);
    $sentencia->execute();
    $lista_facturas = $sentencia->fetchAll(PDO::FETCH_ASSOC);

    $sentencia = $conexion->prepare("SELECT * FROM sedes WHERE id_sede='" . $_SESSION['id_sede'] . "'");
    $sentencia->execute();
    $registro_sedes = $sentencia->fetch(PDO::FETCH_LAZY);

    $sentencia = $conexion->prepare("SELECT * FROM factura_agrupada WHERE id_factura=:id_facturas");
    $sentencia->bindParam(":id_facturas", $ID);
    $sentencia->execute();
    $lista_factura_agrupada = $sentencia->fetch(PDO::FETCH_LAZY);

    $id_cuenta_factura = $lista_factura_agrupada['id_cuenta'];
    $id_factura = $lista_factura_agrupada['id_factura'];

    $sentencia = $conexion->prepare("UPDATE cuentas SET nombre_cuenta = CONCAT('borrada_', nombre_cuenta), estado=0, estado_cuenta=0 WHERE id_cuenta=:idCuenta AND nombre_cuenta NOT LIKE 'MESA%' AND nombre_cuenta NOT LIKE 'CLIENTE'");
    $sentencia->bindParam(":idCuenta", $id_cuenta_factura);
    $sentencia->execute();

    ?>


    <div class="header">
        <h3>Billar <?php echo $registro_sedes["nombre_sede"]; ?></h3>
        <p>Direccion <?php echo $registro_sedes["direccion_sede"]; ?></p>
        <p>Teléfono: <?php echo $registro_sedes["telefono_sede"]; ?></p>
        <p><?php 
        $ultimo_registro = end($lista_facturas);
        echo $ultimo_registro["fecha"];
        ?></p>
        <p><?php echo $registro_cuenta['nombre_cuenta']; ?></p>
        <p>Persona Natural</p>
        <p>Factura #: <?php echo $id_factura; ?> </p>
    </div>
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lista_facturas as $registro) { ?>
                    <tr>
                        <td><?php if($registro['nombre_producto'] != ""){ 
                                echo $registro['nombre_producto'];
                            }else {
                                echo "Tiempo<br>";
                                echo $registro['inicio_tiempo'] . "-" . $registro['fin_tiempo'];
                            }?></td>
                        <td class="text-center align-middle cantidad"><?php if($registro['nombre_producto'] != ""){
                                echo $registro['cantidad'];
                            } else{
                                echo $registro['tiempo_invertido'];
                                
                            }?></td>
                        <td class="text-right">$<?php if($registro['nombre_producto'] != ""){ 
                                echo number_format($registro['precio_total_producto'], 0);
                            }else{
                                echo number_format($registro['precio_total_tiempo'], 0);
                            }?></td>
                    </tr>
                <?php }?>
            </tbody></br>
            <tfoot>
                <tr>
                    <td colspan="2">Total:</td>
                    <td class="text-right">$ <?php echo number_format($lista_factura_agrupada['precio_total'], 2); ?></td>
                </tr>
            </tfoot>
        </table>
        <div class="footer">
            <table>
                <tr>
                    <td>Gracias por su compra</td>
                    <td class="text-right"></td>
                </tr>
            </table>
        </div>
    </body>
    </html>

    <?php 
    
    $html=ob_get_clean();
    // Crea una nueva instancia de DOMPDF
    $dompdf = new Dompdf\Dompdf();

    // Configura el tamaño de página y los márgenes
    $dompdf->setPaper('70mm', 'auto', 'left', 'top');

    // Establece el contenido HTML de la factura
    $dompdf->loadHtml($html);

    // Renderiza la factura a un archivo PDF
    $dompdf->render();

    // Envía el archivo PDF al navegador para su descarga
    $dompdf->stream('factura_' . $id_factura . '.pdf', array('Attachment' => false));

    $nombrePDF = 'factura_' . $id_factura . '.pdf';

    // Guarda el archivo PDF en una ubicación temporal en el servidor
    $rutaTemporal = '../../pdf/' . $nombrePDF;
    file_put_contents($rutaTemporal, $dompdf->output());

    // Decodifica el contenido base64 a datos binarios
    $contenidoPDF = file_get_contents($rutaTemporal);
    $contenidoPDFBase64 = base64_encode($contenidoPDF);

    // Guarda el contenido del archivo PDF en la base de datos
    $sentencia = $conexion->prepare("UPDATE factura_agrupada SET pdf=:pdf WHERE id_factura=:id_facturas");
    $sentencia->bindParam(":id_facturas", $ID);
    $sentencia->bindParam(":pdf", $contenidoPDFBase64);
    $sentencia->execute();

    // Renombra el archivo temporal con la extensión .pdf
    $rutaFinal = '../../pdf/factura_' . $id_factura . '.pdf';
    rename($rutaTemporal, $rutaFinal);
?>
