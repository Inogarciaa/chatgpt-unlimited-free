<?php
require 'config.php';

header('Content-Type: application/json');

$last_id = isset($_GET['last_id']) ? (int)$_GET['last_id'] : 0;

$stmt = $pdo->prepare("SELECT * FROM reservations WHERE reservation_id > ? ORDER BY created_at DESC");
$stmt->execute([$last_id]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$html = '';
$max_id = $last_id;

foreach ($rows as $r) {
    if ($r['reservation_id'] > $max_id) $max_id = $r['reservation_id'];
    
    $badgeClass = '';
    if ($r['status'] == 'Pending') $badgeClass = 'background:#fef3c7;color:#92400e;';
    if ($r['status'] == 'Confirmed') $badgeClass = 'background:#dbeafe;color:#1e40af;';
    if ($r['status'] == 'Completed') $badgeClass = 'background:#d1fae5;color:#065f46;';
    if ($r['status'] == 'Cancelled') $badgeClass = 'background:#fee2e2;color:#b91c1c;';

    $paymentSelectHtml = '';
    $paymentSelectHtml .= '<select name="payment_status" onchange="this.form.submit()" style="padding:2px 4px;border-radius:4px;border:1px solid #ccc;font-size:11px;cursor:pointer; width:90px; ' . ($r['payment_status']=='Paid' ? 'background:#d1fae5;color:#065f46;font-weight:bold;' : 'background:#fee2e2;color:#b91c1c;font-weight:bold;') . '">';
    $paymentSelectHtml .= '<option value="Unpaid" ' . ($r['payment_status']=='Unpaid'?'selected':'') . '>Unpaid</option>';
    $paymentSelectHtml .= '<option value="Paid" ' . ($r['payment_status']=='Paid'?'selected':'') . '>Paid</option>';
    $paymentSelectHtml .= '</select>';

    $statusSelectHtml = '';
    $statusSelectHtml .= '<select name="status" onchange="this.form.submit()" style="padding:2px 4px;border-radius:4px;border:1px solid #ccc;font-size:11px;cursor:pointer; width:90px;">';
    $statusSelectHtml .= '<option value="Pending" ' . ($r['status']=='Pending'?'selected':'') . '>Pending</option>';
    $statusSelectHtml .= '<option value="Confirmed" ' . ($r['status']=='Confirmed'?'selected':'') . '>Confirmed</option>';
    $statusSelectHtml .= '<option value="Completed" ' . ($r['status']=='Completed'?'selected':'') . '>Completed</option>';
    $statusSelectHtml .= '<option value="Cancelled" ' . ($r['status']=='Cancelled'?'selected':'') . '>Cancelled</option>';
    $statusSelectHtml .= '</select>';

    // We add a special class `new-res-row`
    $html .= '<tr class="new-res-row" onmouseenter="this.classList.remove(\'new-res-row\')" style="transition: background-color 0.5s;">';
    $html .= '<td>#' . str_pad($r['reservation_id'], 5, '0', STR_PAD_LEFT) . '</td>';
    $html .= '<td>' . htmlspecialchars($r['customer_name']) . '</td>';
    $html .= '<td>' . htmlspecialchars($r['customer_phone']) . '</td>';
    $html .= '<td>' . htmlspecialchars($r['pickup_date']) . '</td>';
    $html .= '<td style="font-weight:bold;color:#27ae60;">₱' . number_format($r['total_amount'], 2) . '</td>';
    $html .= '<td><div style="font-size:12px;"><strong>' . htmlspecialchars($r['payment_method']) . '</strong>';
    if (!empty($r['payment_reference'])) {
        $html .= '<br><span style="color:#64748b;">Ref: ' . htmlspecialchars($r['payment_reference']) . '</span>';
    }
    $html .= '</div></td>';
    $html .= '<td><span style="' . $badgeClass . 'padding:4px 8px;border-radius:12px;font-size:12px;font-weight:600;">' . $r['status'] . '</span></td>';
    
    $html .= '<td><div class="actions-cell">';
    $html .= '<form method="POST" style="display:inline; display:flex; gap: 4px; flex-direction:column;">';
    $html .= '<input type="hidden" name="_action" value="update_reservation">';
    $html .= '<input type="hidden" name="reservation_id" value="' . $r['reservation_id'] . '">';
    $html .= '<div style="display:flex; align-items:center; gap: 4px; justify-content:space-between;"><span style="font-size:10px; color:#888;">Order:</span>' . $statusSelectHtml . '</div>';
    $html .= '<div style="display:flex; align-items:center; gap: 4px; justify-content:space-between;"><span style="font-size:10px; color:#888;">Payment:</span>' . $paymentSelectHtml . '</div>';
    $html .= '</form>';
    $html .= '<button type="button" class="btn btn-edit" style="width:100%; margin-top:4px;" onclick="openOrderDetails(' . $r['reservation_id'] . ', \'' . htmlspecialchars(addslashes($r['customer_name'])) . '\')"><i class="fa-solid fa-eye"></i> View Items</button>';
    $html .= '</div></td>';
    $html .= '</tr>';
}

echo json_encode([
    'count' => count($rows),
    'max_id' => $max_id,
    'html' => $html
]);
