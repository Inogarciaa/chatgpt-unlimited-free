<?php
session_start();
require '../config.php';

if (empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit;
}

$error = '';
$cart_count = array_sum($_SESSION['cart']);

// Calculate total
$total = 0;
$ids = implode(',', array_keys($_SESSION['cart']));
$stmt = $pdo->query("SELECT * FROM products WHERE product_id IN ($ids)");
$cart_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($cart_products as $row) {
    $qty = $_SESSION['cart'][$row['product_id']];
    $total += $qty * $row['price'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $date = trim($_POST['pickup_date'] ?? '');

    if (!$name || !$phone || !$date) {
        $error = 'Name, Phone, and Pickup Date are required.';
    } else {
        try {
            $pdo->beginTransaction();

            $payment_method = trim($_POST['payment_method'] ?? 'Cash');
            $payment_reference = trim($_POST['payment_reference'] ?? '');
            $payment_status = ($payment_method === 'Online Payment' && !empty($payment_reference)) ? 'Paid' : 'Unpaid';
            $reservation_status = ($payment_status === 'Paid') ? 'Confirmed' : 'Pending';

            // Create Reservation
            $stmt = $pdo->prepare("INSERT INTO reservations (customer_name, customer_phone, customer_email, pickup_date, total_amount, payment_method, payment_reference, payment_status, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $phone, $email, $date, $total, $payment_method, $payment_reference, $payment_status, $reservation_status]);
            $res_id = $pdo->lastInsertId();

            // Insert Items and Update Stock
            $item_stmt = $pdo->prepare("INSERT INTO reservation_items (reservation_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stock_stmt = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE product_id = ?");

            foreach ($cart_products as $row) {
                $pid = $row['product_id'];
                $qty = $_SESSION['cart'][$pid];
                
                // Double check stock
                $check_stock = $pdo->prepare("SELECT stock_quantity FROM products WHERE product_id = ? FOR UPDATE");
                $check_stock->execute([$pid]);
                $curr_stock = $check_stock->fetchColumn();

                if ($curr_stock < $qty) {
                    throw new Exception("Not enough stock for " . $row['product_name']);
                }

                $item_stmt->execute([$res_id, $pid, $qty, $row['price']]);
                $stock_stmt->execute([$qty, $pid]);
            }

            $pdo->commit();
            $_SESSION['cart'] = []; // clear cart
            
            header("Location: success.php?id=" . $res_id);
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $error = $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Sugarlandia Barquillios</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Shared */
        :root { --primary: #d97706; --primary-dark: #b45309; --secondary: #451a03; --dark: #1c1917; --light: #fefce8; --gray: #57534e; --danger: #ef4444; --success: #10b981; --glass-bg: rgba(255, 255, 255, 0.85); }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Outfit', sans-serif; }
        body { background-color: var(--light); color: var(--dark); }
        h1, h2, h3, .navbar-brand { font-family: 'Playfair Display', serif; }
        .navbar { background: var(--glass-bg); backdrop-filter: blur(12px); position: sticky; top: 0; z-index: 100; border-bottom: 1px solid rgba(255, 255, 255, 0.3); padding: 1.5rem 5%; display: flex; justify-content: space-between; align-items: center; }
        .navbar-brand { font-size: 1.8rem; font-weight: 700; color: var(--secondary); text-decoration: none; letter-spacing: -0.5px; }
        .navbar-brand span { color: var(--primary); }
        
        .container { max-width: 800px; margin: 3rem auto; padding: 0 1.5rem; }
        .checkout-box { background: white; border-radius: 16px; padding: 2.5rem; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); }
        
        .page-title { font-size: 2rem; margin-bottom: 1.5rem; border-bottom: 2px solid #f1f5f9; padding-bottom: 1rem; }
        
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; font-weight: 500; margin-bottom: 0.5rem; color: #334155; }
        .form-control { width: 100%; padding: 0.75rem 1rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 1rem; transition: border-color 0.2s; outline: none; font-family: inherit; }
        .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(99,102,241,0.1); }
        
        .order-summary { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem; margin-top: 2rem; }
        .order-summary h3 { margin-bottom: 1rem; font-size: 1.2rem; }
        .summary-item { display: flex; justify-content: space-between; margin-bottom: 0.5rem; color: var(--gray); font-size: 0.95rem; }
        .summary-total { display: flex; justify-content: space-between; font-weight: 700; font-size: 1.25rem; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #cbd5e1; color: var(--dark); }
        
        .submit-btn { width: 100%; background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: white; border: none; padding: 1rem; border-radius: 8px; font-weight: 600; font-size: 1.1rem; cursor: pointer; transition: all 0.3s; margin-top: 2rem; }
        .submit-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(99,102,241,0.4); }
        
        .error-msg { background: #fef2f2; border-left: 4px solid var(--danger); padding: 1rem; border-radius: 4px; color: #991b1b; margin-bottom: 1.5rem; }

        @media (max-width: 600px) {
            .navbar { padding: 1rem; flex-direction: column; gap: 1rem; text-align: center; }
            .checkout-box { padding: 1.5rem; }
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="index.php" class="navbar-brand">Sugar<span>landia</span></a>
        <a href="cart.php" style="color:var(--dark); text-decoration:none; font-weight:500;">&larr; Back to Cart</a>
    </nav>

    <div class="container">
        <div class="checkout-box">
            <h1 class="page-title">Checkout Details</h1>
            
            <?php if($error): ?>
                <div class="error-msg"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" name="name" class="form-control" required placeholder="Juan Dela Cruz">
                </div>
                
                <div class="form-group">
                    <label>Phone Number *</label>
                    <input type="tel" name="phone" class="form-control" required placeholder="09123456789">
                </div>
                
                <div class="form-group">
                    <label>Email Address (Optional)</label>
                    <input type="email" name="email" class="form-control" placeholder="juan@example.com">
                </div>
                
                <div class="form-group">
                    <label>Preferred Pick-up Date *</label>
                    <input type="date" name="pickup_date" class="form-control" required min="<?= date('Y-m-d') ?>">
                </div>

                <div class="form-group">
                    <label>Payment Method *</label>
                    <select name="payment_method" id="payment_method" class="form-control" required>
                        <option value="Cash">Cash (Pay in Store)</option>
                        <option value="Online Payment">Online Payment (GCash)</option>
                    </select>
                </div>

                <div class="form-group" id="ref_group" style="display: none; background: #f8fafc; padding: 1.5rem; border-radius: 8px; border: 1px solid #e2e8f0; text-align: center;">
                    <div style="display: flex; gap: 2rem; justify-content: center; margin-bottom: 1.5rem; flex-wrap: wrap;">
                        <div>
                            <strong style="display: block; margin-bottom: 0.5rem; color: #334155;">GCash Details</strong>
                            <img src="gcash_qr.jpg" alt="GCash QR Code" style="max-width: 200px; border-radius: 8px; border: 2px solid #005ce6; padding: 5px; background: white; margin-bottom: 0.5rem;">
                            <div style="font-weight: 600; color: #005ce6;">0946 564 ....</div>
                            <div style="font-size: 0.85rem; color: #64748b;">IN******O G.</div>
                        </div>
                    </div>
                    <label style="text-align: left; font-weight: 600;">Payment Reference Number *</label>
                    <input type="text" name="payment_reference" id="payment_reference" class="form-control" placeholder="Enter GCash Ref No.">
                    <small style="color: #64748b; margin-top: 8px; display: block; text-align: left;">Please scan the QR code, pay via GCash, then enter the reference number here to automatically confirm your reservation.</small>
                </div>

                <script>
                    const pmethod = document.getElementById('payment_method');
                    const refGroup = document.getElementById('ref_group');
                    const refInput = document.getElementById('payment_reference');
                    const submitBtn = document.querySelector('button[type="submit"]');

                    function validateSubmitBtn() {
                        if (pmethod.value === 'Online Payment' && refInput.value.trim() === '') {
                            submitBtn.style.backgroundColor = '#94a3b8'; // gray out
                            submitBtn.style.cursor = 'not-allowed';
                            submitBtn.disabled = true;
                        } else {
                            submitBtn.style.backgroundColor = ''; // restore original
                            submitBtn.style.cursor = 'pointer';
                            submitBtn.disabled = false;
                        }
                    }

                    pmethod.addEventListener('change', function() {
                        if (this.value === 'Online Payment') {
                            refGroup.style.display = 'block';
                            refInput.required = true;
                        } else {
                            refGroup.style.display = 'none';
                            refInput.required = false;
                        }
                        validateSubmitBtn();
                    });

                    refInput.addEventListener('input', validateSubmitBtn);

                    // Initialize on load
                    validateSubmitBtn();
                </script>

                <div class="order-summary">
                    <h3>Order Summary</h3>
                    <?php foreach($cart_products as $row): 
                        $qty = $_SESSION['cart'][$row['product_id']];
                    ?>
                        <div class="summary-item">
                            <span><?= $qty ?>x <?= htmlspecialchars($row['product_name']) ?></span>
                            <span>₱<?= number_format($row['price'] * $qty, 2) ?></span>
                        </div>
                    <?php endforeach; 
                        $vatable_sales = $total / 1.12;
                        $vat_amount = $total - $vatable_sales;
                    ?>
                    <div class="summary-item" style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e2e8f0;">
                        <span>Vatable Sales</span>
                        <span>₱<?= number_format($vatable_sales, 2) ?></span>
                    </div>
                    <div class="summary-item">
                        <span>VAT (12%)</span>
                        <span>₱<?= number_format($vat_amount, 2) ?></span>
                    </div>
                    <div class="summary-total">
                        <span>Total to Pay</span>
                        <span>₱<?= number_format($total, 2) ?></span>
                    </div>
                </div>

                <button type="submit" class="submit-btn">Confirm Reservation</button>
            </form>
        </div>
    </div>

</body>
</html>
