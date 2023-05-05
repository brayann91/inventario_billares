<?php
include "../../bd.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  
    require_once '../../libs/dompdf/vendor/autoload.php'; // carga la biblioteca DOMPDF

    // Contenido HTML y CSS de la factura
    $html = '<html>
    <head>
        <title>Factura</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                font-size: 12px;
                margin: 0;
            }
            @page {
                size: auto;
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
        </style>
    </head>
    <body>
        <div class="header">
            <p><?php echo date("d/m/Y") ?></p>
            <h3>Nombre Empresa</h3>
            <p>Teléfono: telefono</p>
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
                <tr>
                    <td>Producto 1</td>
                    <td class="text-right">3</td>
                    <td class="text-right">$20.00</td>
                </tr>
                <tr>
                    <td>Producto 2</td>
                    <td class="text-right">10</td>
                    <td class="text-right">$40.00</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2">Total:</td>
                    <td class="text-right">$60.00</td>
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
    ';

    // Crea una nueva instancia de DOMPDF
    $dompdf = new Dompdf\Dompdf();

    // Configura el tamaño de página y los márgenes
    $dompdf->setPaper('70mm', '150mm', 'left', 'top');

    // Establece el contenido HTML de la factura
    $dompdf->loadHtml($html);

    // Renderiza la factura a un archivo PDF
    $dompdf->render();

    // Envía el archivo PDF al navegador para su descarga
    $dompdf->stream('factura.pdf', array('Attachment' => false));
}
?>
