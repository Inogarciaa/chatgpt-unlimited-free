<?php
require 'config.php';

if (!isset($_GET['id'])) {
    echo "No reservation ID provided.";
    exit;
}

$id = (int)$_GET['id'];

$stmt = $pdo->prepare("
    SELECT ri.*, p.product_name, p.product_variant 
    FROM reservation_items ri
    JOIN products p ON ri.product_id = p.product_id
    WHERE ri.reservation_id = ?
");
$stmt->execute([$id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$items) {
    echo "<div style='padding: 20px; text-align: center; color: #64748b;'>No items found for this reservation.</div>";
    exit;
}

echo '<table style="width: 100%; border-collapse: collapse; font-size: 14px;">';
echo '<thead><tr style="background: #f8fafc; color: #64748b; font-size: 12px; text-transform: uppercase;"><th style="padding: 12px; text-align: left; border-bottom: 1px solid #e2e8f0;">Item</th><th style="padding: 12px; text-align: center; border-bottom: 1px solid #e2e8f0;">Quantity</th><th style="padding: 12px; text-align: right; border-bottom: 1px solid #e2e8f0;">Price</th><th style="padding: 12px; text-align: right; border-bottom: 1px solid #e2e8f0;">Subtotal</th></tr></thead>';
echo '<tbody>';

$total = 0;
foreach ($items as $item) {
    $subtotal = $item['quantity'] * $item['price'];
    $total += $subtotal;
    echo '<tr>';
    echo '<td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">';
    echo '<strong>' . htmlspecialchars($item['product_name']) . '</strong><br>';
    echo '<span style="font-size: 12px; color: #64748b;">' . htmlspecialchars($item['product_variant']) . '</span>';
    echo '</td>';
    echo '<td style="padding: 12px; text-align: center; border-bottom: 1px solid #e2e8f0;">' . $item['quantity'] . '</td>';
    echo '<td style="padding: 12px; text-align: right; border-bottom: 1px solid #e2e8f0;">₱' . number_format($item['price'], 2) . '</td>';
    echo '<td style="padding: 12px; text-align: right; border-bottom: 1px solid #e2e8f0; font-weight: 600;">₱' . number_format($subtotal, 2) . '</td>';
    echo '</tr>';
}

echo '</tbody>';
echo '<tfoot>';
echo '<tr>';
echo '<td colspan="3" style="padding: 16px 12px; text-align: right; font-weight: 600;">Total Amount:</td>';
echo '<td style="padding: 16px 12px; text-align: right; font-weight: 700; color: #d97706; font-size: 16px;">₱' . number_format($total, 2) . '</td>';
echo '</tr>';
echo '</tfoot>';
echo '</table>';
?>
