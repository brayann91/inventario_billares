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

    $sentencia = $conexion->prepare("SELECT * FROM cajas WHERE id_sede=" . $_SESSION['id_sede'] . " 
    ORDER BY id_caja DESC LIMIT 1");
    $sentencia->execute();
    $registro_caja = $sentencia->fetch(PDO::FETCH_LAZY);

    $fecha_apertura_caja = $registro_caja['fecha_apertura'];
    $fecha_cierre_caja = $registro_caja['fecha_cierre'];
    $ID = $registro_caja['id_caja'];

    $sentencia = $conexion->prepare("SELECT f.id_cuenta, c.nombre_cuenta, f.nombre_producto, f.cantidad, f.fecha,
    SUM(f.precio_total_producto) precio_total_producto, SUBSTRING_INDEX(SEC_TO_TIME(SUM(TIME_TO_SEC(f.tiempo_invertido))), '.', 1) tiempo_invertido,
    SUM(f.precio_total_tiempo) precio_total_tiempo, SUM(f.cantidad) cantidad
    FROM facturas f 
    INNER JOIN cuentas c ON c.id_cuenta=f.id_cuenta 
    WHERE c.id_sede=" . $_SESSION['id_sede'] . " AND f.fecha BETWEEN :fecha_apertura AND :fecha_cierre GROUP BY f.nombre_producto");
    $sentencia->bindParam(":fecha_apertura", $fecha_apertura_caja);
    $sentencia->bindParam(":fecha_cierre", $fecha_cierre_caja);
    $sentencia->execute();
    $lista_facturas = $sentencia->fetchAll(PDO::FETCH_ASSOC);

    $sentencia = $conexion->prepare("SELECT * FROM sedes WHERE id_sede='" . $_SESSION['id_sede'] . "'");
    $sentencia->execute();
    $registro_sedes = $sentencia->fetch(PDO::FETCH_LAZY);

    ?>


    <div class="header">
        <h3>Billar <?php echo $registro_sedes["nombre_sede"]; ?></h3>
        <p>Direccion <?php echo $registro_sedes["direccion_sede"]; ?></p>
        <p>Teléfono: <?php echo $registro_sedes["telefono_sede"]; ?></p>
        <p><?php 
        $ultimo_registro = end($lista_facturas);
        echo $ultimo_registro["fecha"];
        ?></p>
        <p>Persona Natural</p>        
    </div>
        <table>
            <thead>
                <tr>
                    <th>Producto<br><br></th>
                    <th>Cantidad<br><br></th>
                    <th>Precio<br><br></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($lista_facturas as $registro) { ?>
                  <tr>
                      <td><?php if($registro['nombre_producto'] != ""){ 
                              echo $registro['nombre_producto'];
                          }else {
                              echo "Tiempo";
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
              <?php } ?>

            </tbody></br>
            <tfoot>
                <tr>
                    <td colspan="2">Total:</td>
                    <td class="text-right">$ <?php echo number_format($registro_caja['valor'], 2); ?></td>
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
    $dompdf->stream('factura_caja_' . $ID . '.pdf', array('Attachment' => false));

    $nombrePDF = 'factura_caja_' . $ID . '.pdf';

    // Guarda el archivo PDF en una ubicación temporal en el servidor
    $rutaTemporal = '../../pdf/' . $nombrePDF;
    file_put_contents($rutaTemporal, $dompdf->output());

    // Decodifica el contenido base64 a datos binarios
    $contenidoPDF = file_get_contents($rutaTemporal);
    $contenidoPDFBase64 = base64_encode($contenidoPDF);

    // Guarda el contenido del archivo PDF en la base de datos
    $sentencia = $conexion->prepare("UPDATE cajas SET pdf_caja=:pdf WHERE id_caja=:id_caja");
    $sentencia->bindParam(":id_caja", $ID);
    $sentencia->bindParam(":pdf", $contenidoPDFBase64);
    $sentencia->execute();

    // Renombra el archivo temporal con la extensión .pdf
    $rutaFinal = '../../pdf/factura_caja_' . $ID . '.pdf';
    rename($rutaTemporal, $rutaFinal);
?>
