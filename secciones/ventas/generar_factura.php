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

    $sentencia = $conexion->prepare("SELECT * FROM facturas WHERE id_facturas=:id_facturas");
    $sentencia->bindParam(":id_facturas", $txtID);
    $sentencia->execute();
    $lista_facturas = $sentencia->fetchAll(PDO::FETCH_ASSOC);

    $sentencia = $conexion->prepare("SELECT * FROM sedes WHERE id_sede='" . $_SESSION['id_sede'] . "'");
    $sentencia->execute();
    $registro_sedes = $sentencia->fetch(PDO::FETCH_LAZY);

    $sentencia = $conexion->prepare("SELECT * FROM factura_agrupada WHERE id_factura=:id_facturas");
    $sentencia->bindParam(":id_facturas", $txtID);
    $sentencia->execute();
    $lista_factura_agrupada = $sentencia->fetch(PDO::FETCH_LAZY);

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
                    <td class="text-right">IVA incluido</td>
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
    $dompdf->stream('factura.pdf', array('Attachment' => false));

?>
