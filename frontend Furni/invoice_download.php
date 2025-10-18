<?php
// furniture/invoice_download.php
// Minimal invoice download handler: verify auth and send a simple PDF/HTML invoice.
// For real apps generate PDF via library (TCPDF, Dompdf).

require_once __DIR__ . '/includes/middleware/auth.php';

$orderId = (int)($_GET['order_id'] ?? 0);
if (!$orderId) {
    die('Order ID required.');
}

// For demo, send a small HTML invoice as download:
$invoiceHtml = "<h1>Invoice #$orderId</h1><p>Sample invoice content.</p>";
header('Content-type: application/octet-stream');
header('Content-Disposition: attachment; filename="invoice-'.$orderId.'.html"');
echo $invoiceHtml;
exit;
