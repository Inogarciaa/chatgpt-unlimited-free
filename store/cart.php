<?php
session_start();
require '../config.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
$cart_count = array_sum($_SESSION['cart']);

// Fetch cart items
$cart_items = [];
$total = 0;

if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_keys($_SESSION['cart']));
    $stmt = $pdo->query("SELECT * FROM products WHERE product_id IN ($ids)");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $qty = $_SESSION['cart'][$row['product_id']];
        
        // Ensure cart qty does not exceed stock in case it changed
        if ($qty > $row['stock_quantity']) {
            $qty = max(0, $row['stock_quantity']);
            $_SESSION['cart'][$row['product_id']] = $qty;
            if($qty == 0) unset($_SESSION['cart'][$row['product_id']]);
        }
        
        if ($qty > 0) {
            $row['cart_qty'] = $qty;
            $row['subtotal'] = $qty * $row['price'];
            $total += $row['subtotal'];
            $cart_items[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - Sugarlandia Barquillios</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Shared Styles */
        :root {
            --primary: #d97706;
            --primary-dark: #b45309;
            --secondary: #451a03;
            --dark: #1c1917;
            --light: #fefce8;
            --gray: #57534e;
            --danger: #ef4444;
            --success: #10b981;
            --glass-bg: rgba(255, 255, 255, 0.85);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Outfit', sans-serif; }
        body { background-color: var(--light); color: var(--dark); }
        h1, h2, h3, .navbar-brand { font-family: 'Playfair Display', serif; }
        .navbar { background: var(--glass-bg); backdrop-filter: blur(12px); position: sticky; top: 0; z-index: 100; border-bottom: 1px solid rgba(255,255,255,0.3); padding: 1.5rem 5%; display: flex; justify-content: space-between; align-items: center; }
        .navbar-brand { font-size: 1.8rem; font-weight: 700; color: var(--secondary); text-decoration: none; letter-spacing: -0.5px; }
        .navbar-brand span { color: var(--primary); }
        .cart-btn { position: relative; background: var(--primary); padding: 0.7rem 1.5rem; border-radius: 50px; text-decoration: none; color: white; font-weight: 600; display: flex; align-items: center; gap: 0.5rem; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(217, 119, 6, 0.3); border: none; }
        .cart-btn:hover { background: var(--primary-dark); transform: translateY(-2px); box-shadow: 0 6px 20px rgba(217, 119, 6, 0.4); color: white; }
        .cart-badge { background: white; color: var(--primary); font-size: 0.75rem; font-weight: 800; padding: 0.1rem 0.5rem; border-radius: 9999px; margin-left: 4px; }

        /* Cart specific */
        .container { max-width: 900px; margin: 3rem auto; padding: 0 1.5rem; }
        .page-title { font-size: 2rem; margin-bottom: 2rem; }
        
        .cart-box { background: white; border-radius: 16px; padding: 2rem; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); }
        .cart-item { display: flex; align-items: center; gap: 1.5rem; padding: 1.5rem 0; border-bottom: 1px solid #e2e8f0; }
        .cart-item:last-child { border-bottom: none; }
        
        .item-img { width: 80px; height: 80px; background: #f1f5f9; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 2rem; }
        .item-info { flex-grow: 1; }
        .item-name { font-size: 1.1rem; font-weight: 600; margin-bottom: 0.25rem; }
        .item-variant { font-size: 0.85rem; color: var(--gray); }
        .item-price { font-weight: 600; color: var(--primary); }
        
        .qty-controls { display: flex; align-items: center; gap: 0.5rem; }
        .qty-btn { width: 32px; height: 32px; border-radius: 6px; border: 1px solid #cbd5e1; background: white; cursor: pointer; font-weight: bold; transition: all 0.2s; }
        .qty-btn:hover { background: #f1f5f9; }
        .qty-input { width: 50px; text-align: center; border: 1px solid #cbd5e1; border-radius: 6px; padding: 0.25rem; font-family: inherit; }
        
        .item-subtotal { font-weight: 700; width: 100px; text-align: right; }
        .btn-remove { background: none; border: none; color: var(--danger); cursor: pointer; padding: 0.5rem; font-size: 1.2rem; transition: transform 0.2s; }
        .btn-remove:hover { transform: scale(1.1); }

        .cart-summary { margin-top: 2rem; border-top: 2px dashed #e2e8f0; padding-top: 2rem; display: flex; justify-content: space-between; align-items: flex-end; }
        .total-label { font-size: 1.2rem; color: var(--gray); }
        .total-amount { font-size: 2.5rem; font-weight: 700; color: var(--dark); }
        
        .checkout-btn { display: inline-block; background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: white; text-decoration: none; padding: 1rem 3rem; border-radius: 8px; font-weight: 600; font-size: 1.1rem; transition: all 0.3s; box-shadow: 0 4px 12px rgba(99,102,241,0.3); }
        .checkout-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(99,102,241,0.4); }
        
        .empty-cart { text-align: center; padding: 3rem; color: var(--gray); }
        .empty-cart a { color: var(--primary); font-weight: 600; text-decoration: none; margin-top: 1rem; display: inline-block; }

        @media (max-width: 600px) {
            .navbar { padding: 1rem; flex-direction: column; gap: 1rem; }
            .back-link { display: none; }
            .cart-item { flex-direction: column; text-align: center; gap: 1rem; }
            .qty-controls { justify-content: center; }
            .item-subtotal { text-align: center; width: 100%; margin-top: 0.5rem; }
            .cart-summary { flex-direction: column; gap: 1.5rem; align-items: center; text-align: center; }
            .cart-summary > div { text-align: center !important; }
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="index.php" class="navbar-brand">Sugar<span>landia</span></a>
        <div style="display:flex; align-items:center; gap: 1.5rem;">
            <a href="./index.php" class="back-link" style="text-decoration:none; color:var(--text-main); font-weight:500;">&larr; Back</a>
            <a href="cart.php" class="cart-btn">
                <i class="fa-solid fa-cart-shopping"></i> Cart <span class="cart-badge" id="cart-count"><?= $cart_count ?></span>
            </a>
        </div>
    </nav>

    <div class="container">
        <h1 class="page-title">Your Cart</h1>
        
        <div class="cart-box">
            <?php if (empty($cart_items)): ?>
                <div class="empty-cart">
                    <div style="font-size:4rem;margin-bottom:1rem;">🛒</div>
                    <h2>Your cart is empty</h2>
                    <a href="index.php">← Continue Shopping</a>
                </div>
            <?php else: ?>
                <?php foreach ($cart_items as $item): ?>
                <div class="cart-item" id="item-<?= $item['product_id'] ?>">
                    <div class="item-img">🍪</div>
                    <div class="item-info">
                        <div class="item-name"><?= htmlspecialchars($item['product_name']) ?></div>
                        <div class="item-variant"><?= htmlspecialchars($item['product_variant']) ?></div>
                        <div class="item-price">₱<?= number_format($item['price'], 2) ?></div>
                    </div>
                    
                    <div class="qty-controls">
                        <button class="qty-btn" onclick="updateQty(<?= $item['product_id'] ?>, -1)">-</button>
                        <input type="number" class="qty-input" id="qty-<?= $item['product_id'] ?>" value="<?= $item['cart_qty'] ?>" readonly>
                        <button class="qty-btn" onclick="updateQty(<?= $item['product_id'] ?>, 1)">+</button>
                    </div>
                    
                    <div class="item-subtotal">₱<span id="sub-<?= $item['product_id'] ?>"><?= number_format($item['subtotal'], 2) ?></span></div>
                    
                    <button class="btn-remove" onclick="removeItem(<?= $item['product_id'] ?>)">🗑️</button>
                </div>
                <?php endforeach; ?>
                
                <div class="cart-summary">
                    <div>
                        <a href="index.php" style="color:var(--gray);text-decoration:none;font-weight:500;">← Continue Shopping</a>
                    </div>
                    <div style="text-align:right;">
                        <div class="total-label">Total Amount</div>
                        <div class="total-amount">₱<span id="cart-total"><?= number_format($total, 2) ?></span></div>
                        <br>
                        <a href="checkout.php" class="checkout-btn">Proceed to Checkout →</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function updateQty(id, change) {
            const input = document.getElementById('qty-' + id);
            let newVal = parseInt(input.value) + change;
            if (newVal < 1) newVal = 1;
            
            fetch('ajax_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=update&product_id=' + id + '&quantity=' + newVal
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    location.reload(); // Quickest way to update totals accurately
                } else {
                    alert(data.error || 'Could not update quantity.');
                }
            });
        }

        function removeItem(id) {
            if(!confirm('Remove this item?')) return;
            fetch('ajax_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=remove&product_id=' + id
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) location.reload();
            });
        }
    </script>
</body>
</html>
