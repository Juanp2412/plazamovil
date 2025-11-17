<?php
require_once '../config/conexion.php';
require_once __DIR__ . '/../fpdf186/fpdf.php';

if (!isset($_GET['id_pago'])) {
    die('No se especificó el pago.');
}

$id_pago = $_GET['id_pago'];
$stmt = $pdo->prepare("
    SELECT p.*, u.nombre_completo AS cliente, pe.id_pedido
    FROM pagos p
    JOIN pedidos pe ON pe.id_pedido = p.id_pedido
    JOIN usuarios u ON u.id_usuario = pe.id_usuario
    WHERE p.id_pago = ?
");
$stmt->execute([$id_pago]);
$pago = $stmt->fetch(PDO::FETCH_ASSOC);

$pdf = new FPDF();
$pdf->AddPage();

// Logo y encabezado principal
$logo_path = '../img/logoplaza_movil.png';
if (file_exists($logo_path)) {
    $pdf->Image($logo_path, 80, 10, 50); // Centrado arriba
}
$pdf->Ln(30);
$pdf->SetFont('Arial', 'B', 22);
$pdf->SetTextColor(56, 142, 60);
$pdf->Cell(0, 15, utf8_decode('Factura de Compra'), 0, 1, 'C');
$pdf->SetTextColor(0,0,0);
$pdf->SetFont('Arial', '', 13);
$pdf->Cell(0, 8, utf8_decode('Plaza Móvil - Tu mercado de confianza'), 0, 1, 'C');
$pdf->Ln(5);

// Línea separadora
$pdf->SetDrawColor(120, 180, 120);
$pdf->SetLineWidth(0.8);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(8);

// Datos del cliente y del pago (sin fecha de pago)
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 8, utf8_decode('N° de Factura:'), 0, 0, 'L');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(60, 8, $pago['id_pago'], 0, 1, 'L');

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 8, 'Cliente:', 0, 0, 'L');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(60, 8, utf8_decode($pago['cliente']), 0, 1, 'L');

// (Elimina la fila de fecha de pago)
//$pdf->SetFont('Arial', 'B', 12);
//$pdf->Cell(40, 8, 'Fecha de pago:', 0, 0, 'L');
//$pdf->SetFont('Arial', '', 12);
//$pdf->Cell(60, 8, $pago['fecha_pago'], 0, 1, 'L');

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 8, 'Metodo de pago:', 0, 0, 'L');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(60, 8, ucfirst(utf8_decode($pago['metodo'])), 0, 1, 'L');

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 8, 'Estado:', 0, 0, 'L');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(60, 8, ucfirst($pago['estado']), 0, 1, 'L');

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 8, 'Monto total:', 0, 0, 'L');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(60, 8, "$" . number_format($pago['monto'], 2, ',', '.'), 0, 1, 'L');

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 8, utf8_decode('Transacción:'), 0, 0, 'L');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(60, 8, $pago['transaccion_id'], 0, 1, 'L');

$pdf->Ln(15);

// Mensaje de agradecimiento
$pdf->SetFont('Arial', 'I', 13);
$pdf->SetTextColor(56, 142, 60);
$pdf->MultiCell(0, 10, utf8_decode("¡Gracias por confiar en Plaza Móvil!\nSi tienes dudas sobre tu compra, contáctanos a soporte@plazamovil.com"), 0, 'C');
$pdf->SetTextColor(0,0,0);

// Pie de página
$pdf->SetY(-30);
$pdf->SetFont('Arial', 'I', 10);
$pdf->SetTextColor(120, 180, 120);
$pdf->Cell(0, 10, utf8_decode('Factura generada automáticamente - Plaza Móvil'), 0, 1, 'C');
$pdf->SetTextColor(0,0,0);

$pdf->Output("I", "factura_pago_{$pago['id_pago']}.pdf");
exit;
?>