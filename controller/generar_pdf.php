<?php
require_once '../vendor/dompdf/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options;

function generarPDF($factura) {
    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $dompdf = new Dompdf($options);

    $html = '
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; font-size: 14px; }
            h1, h3 { color: #2E7D32; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #4CAF50; color: white; }
            .totales { font-size: 16px; font-weight: bold; }
        </style>
    </head>
    <body>
        <h1 style="text-align:center;">Plaza Móvil</h1>
        <p style="text-align:center;">Factura No. ' . htmlspecialchars($factura['id_pago']) . '</p>
        <p style="text-align:center;">Fecha: ' . htmlspecialchars($factura['fecha_pago']) . '</p>
        <hr>

        <h3>Cliente</h3>
        <p>Nombre: ' . htmlspecialchars($factura['cliente']) . '<br>
        Email: ' . htmlspecialchars($factura['cliente_email']) . '<br>
        Teléfono: ' . htmlspecialchars($factura['cliente_telefono']) . '</p>

        <h3>Agricultor</h3>
        <p>Nombre: ' . htmlspecialchars($factura['agricultor']) . '<br>
        Email: ' . htmlspecialchars($factura['agricultor_email']) . '<br>
        Teléfono: ' . htmlspecialchars($factura['agricultor_telefono']) . '</p>

        <h3>Detalle</h3>
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>' . htmlspecialchars($factura['producto']) . '</td>
                    <td>' . intval($factura['cantidad']) . '</td>
                    <td>$' . number_format($factura['precio_unitario'], 2) . '</td>
                    <td>$' . number_format($factura['cantidad'] * $factura['precio_unitario'], 2) . '</td>
                </tr>
            </tbody>
        </table>

        <p class="totales">Total Pagado: $' . number_format($factura['monto_total'], 2) . '</p>
        <p>Método de pago: ' . htmlspecialchars($factura['metodo']) . '</p>
    </body>
    </html>';

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream("factura_" . $factura['id_pago'] . ".pdf", ["Attachment" => false]);
}
?>