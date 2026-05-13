<?php
require '../config.php';

$error = '';
$reservation = null;
$items = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if ($full_name && $phone) {
        $stmt = $pdo->prepare("SELECT * FROM reservations WHERE customer_name LIKE ? AND customer_phone = ? ORDER BY created_at DESC LIMIT 1");
        $stmt->execute(['%' . $full_name . '%', $phone]);
        $reservation = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($reservation) {
            $res_id = $reservation['reservation_id'];
            $item_stmt = $pdo->prepare("
                SELECT ri.*, p.product_name, p.product_variant 
                FROM reservation_items ri
                JOIN products p ON ri.product_id = p.product_id
                WHERE ri.reservation_id = ?
            ");
            $item_stmt->execute([$res_id]);
            $items = $item_stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $error = "No reservation found with that name and phone number.";
        }
    } else {
        $error = "Please enter both Full Name and Phone Number.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Reservation - Sugarlandia Barquillios</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;1,600&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #d97706; --primary-dark: #b45309; --secondary: #451a03; --dark: #1c1917; --light: #fefce8; --gray: #57534e; --danger: #ef4444; --success: #10b981; --glass-bg: rgba(255, 255, 255, 0.85); }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Outfit', sans-serif; }
        body { background-color: var(--light); color: var(--dark); }
        h1, h2, h3, .navbar-brand { font-family: 'Playfair Display', serif; }
        .navbar { background: var(--glass-bg); backdrop-filter: blur(12px); position: sticky; top: 0; z-index: 100; border-bottom: 1px solid rgba(255, 255, 255, 0.3); padding: 1.5rem 5%; display: flex; justify-content: space-between; align-items: center; }
        .navbar-brand { font-size: 1.8rem; font-weight: 700; color: var(--secondary); text-decoration: none; letter-spacing: -0.5px; }
        .navbar-brand span { color: var(--primary); }
        
        .container { max-width: 600px; margin: 4rem auto; padding: 0 1.5rem; }
        .track-box { background: white; border-radius: 16px; padding: 2.5rem; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); }
        .page-title { font-size: 2rem; margin-bottom: 1.5rem; text-align: center; }
        
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; font-weight: 500; margin-bottom: 0.5rem; color: #334155; }
        .form-control { width: 100%; padding: 0.75rem 1rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 1rem; transition: border-color 0.2s; outline: none; font-family: inherit; }
        .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(99,102,241,0.1); }
        
        .submit-btn { width: 100%; background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: white; border: none; padding: 1rem; border-radius: 8px; font-weight: 600; font-size: 1.1rem; cursor: pointer; transition: all 0.3s; }
        .submit-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(217,119,6,0.3); }
        
        .error-msg { background: #fef2f2; border-left: 4px solid var(--danger); padding: 1rem; border-radius: 4px; color: #991b1b; margin-bottom: 1.5rem; }
        
        .result-box { margin-top: 2rem; padding-top: 2rem; border-top: 2px dashed #e2e8f0; }
        .status-badge { display: inline-block; padding: 0.5rem 1rem; border-radius: 20px; font-weight: 700; font-size: 1.1rem; margin-bottom: 1.5rem; }
        .status-Pending { background: #fef3c7; color: #92400e; }
        .status-Confirmed { background: #dbeafe; color: #1e40af; }
        .status-Completed { background: #d1fae5; color: #065f46; }
        .status-Cancelled { background: #fee2e2; color: #b91c1c; }
        
        .info-row { display: flex; justify-content: space-between; margin-bottom: 0.75rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.5rem; }
        .info-label { color: var(--gray); }
        .info-val { font-weight: 600; }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="index.php" class="navbar-brand">Sugar<span>landia</span></a>
        <a href="index.php" style="color:var(--dark); text-decoration:none; font-weight:500;">&larr; Back to Store</a>
    </nav>

    <div class="container">
        <div class="track-box">
            <h1 class="page-title">Track Reservation</h1>
            
            <?php if($error): ?>
                <div class="error-msg"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" class="form-control" required placeholder="e.g. Juan Dela Cruz" value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="tel" name="phone" class="form-control" required placeholder="09123456789" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                </div>
                
                <button type="submit" class="submit-btn">Check Status</button>
            </form>

            <?php if ($reservation): ?>
                <div class="result-box">
                    <div style="text-align: center;">
                        <span class="status-badge status-<?= htmlspecialchars($reservation['status']) ?>">
                            Status: <?= htmlspecialchars($reservation['status']) ?>
                        </span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Customer Name</span>
                        <span class="info-val"><?= htmlspecialchars($reservation['customer_name']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Pickup Date</span>
                        <span class="info-val"><?= htmlspecialchars($reservation['pickup_date']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Payment Method</span>
                        <span class="info-val"><?= htmlspecialchars($reservation['payment_method']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Payment Status</span>
                        <span class="info-val" style="color: <?= $reservation['payment_status'] === 'Paid' ? 'var(--success)' : 'var(--danger)' ?>;">
                            <?= htmlspecialchars($reservation['payment_status']) ?>
                        </span>
                    </div>
                    <div class="info-row" style="margin-top: 1rem;">
                        <span class="info-label">Total Amount</span>
                        <span class="info-val" style="font-size: 1.2rem; color: var(--primary);">₱<?= number_format($reservation['total_amount'], 2) ?></span>
                    </div>
                    
                    <div style="text-align: center; margin-top: 1.5rem;">
                        <a href="success.php?id=<?= $reservation['reservation_id'] ?>" style="color: var(--primary); font-weight: 600; text-decoration: none;">View Full Receipt</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
