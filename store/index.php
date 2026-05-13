<?php
session_start();
require '../config.php';

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cart_count = array_sum($_SESSION['cart']);

// Fetch all products
$products = $pdo->query("SELECT * FROM products ORDER BY product_id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sugarlandia Barquillios - Online Store</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #d97706; /* Caramel/Gold */
            --primary-dark: #b45309;
            --secondary: #451a03; /* Deep Chocolate */
            --dark: #1c1917;
            --light: #fefce8;
            --gray: #57534e;
            --danger: #ef4444;
            --success: #10b981;
            --glass-bg: rgba(255, 255, 255, 0.85);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background-color: var(--light);
            color: var(--dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        h1, h2, h3, .navbar-brand { font-family: 'Playfair Display', serif; }

        /* Navbar */
        .navbar {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
            padding: 1.5rem 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar-brand {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--secondary);
            text-decoration: none;
            letter-spacing: -0.5px;
        }
        .navbar-brand span { color: var(--primary); }

        .cart-btn {
            position: relative;
            background: var(--primary);
            padding: 0.7rem 1.5rem;
            border-radius: 50px;
            text-decoration: none;
            color: white;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(217, 119, 6, 0.3);
            border: none;
        }

        .cart-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(217, 119, 6, 0.4);
            color: white;
        }

        .cart-badge {
            background: white;
            color: var(--primary);
            font-size: 0.75rem;
            font-weight: 800;
            padding: 0.1rem 0.5rem;
            border-radius: 9999px;
            margin-left: 4px;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #fefce8 0%, #fef3c7 100%);
            padding: 4rem 5%;
            text-align: center;
            border-bottom: 1px solid #fde68a;
        }

        .hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: var(--secondary);
        }

        .hero p {
            color: var(--gray);
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Responsive Styles */
        @media (max-width: 600px) {
            .navbar { padding: 1rem; flex-direction: column; gap: 1rem; text-align: center; }
            .hero { padding: 2rem 1rem; }
            .hero h1 { font-size: 2rem; }
            .back-link { display: none; }
        }

        /* Product Grid */
        .container {
            padding: 4rem 5%;
            flex-grow: 1;
        }

        .section-title {
            font-size: 2rem;
            margin-bottom: 2rem;
            text-align: center;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
        }

        .product-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            display: flex;
            flex-direction: column;
        }

        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .product-image {
            height: 200px;
            background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
        }

        .product-content {
            padding: 1.5rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .product-name {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .product-variant {
            color: var(--gray);
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .product-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 1rem;
        }

        .product-stock {
            font-size: 0.85rem;
            margin-bottom: 1rem;
            font-weight: 500;
        }

        .stock-in { color: var(--success); }
        .stock-out { color: var(--danger); }

        .btn-add {
            margin-top: auto;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            padding: 0.75rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn-add:hover:not(:disabled) {
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
            transform: scale(1.02);
        }

        .btn-add:disabled {
            background: #cbd5e1;
            cursor: not-allowed;
            color: #64748b;
        }

        /* Toast Notification */
        .toast {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            background: white;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 1rem;
            transform: translateY(150%);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1000;
        }

        .toast.show {
            transform: translateY(0);
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="../portfolio.php" class="navbar-brand">Sugar<span>landia</span></a>
        <div style="display:flex; align-items:center; gap: 1.5rem;">
            <a href="../portfolio.php" class="back-link" style="text-decoration:none; color:var(--text-main); font-weight:500;">&larr; Back to Home</a>
            <a href="track.php" style="text-decoration:none; color:var(--dark); font-weight:500; display:flex; align-items:center; gap:0.5rem;"><i class="fa-solid fa-magnifying-glass"></i> Track Order</a>
            <a href="cart.php" class="cart-btn">
                <i class="fa-solid fa-cart-shopping"></i> Cart <span class="cart-badge" id="cart-count"><?= $cart_count ?></span>
            </a>
        </div>
    </nav>

    <div class="hero">
        <h1>Welcome to Sugarlandia!</h1>
        <p>Discover our delicious, handcrafted barquillios. Reserve your favorites online and pick them up fresh.</p>
    </div>

    <div class="container">
        <h2 class="section-title">Our Products</h2>
        <div class="product-grid">
            <?php foreach ($products as $p): 
                $in_stock = $p['stock_quantity'] > 0;
            ?>
            <div class="product-card">
                <div class="product-image">
                    🍪
                </div>
                <div class="product-content">
                    <h3 class="product-name"><?= htmlspecialchars($p['product_name']) ?></h3>
                    <div class="product-variant"><?= htmlspecialchars($p['product_variant']) ?></div>
                    <div class="product-price">₱<?= number_format($p['price'], 2) ?></div>
                    
                    <div class="product-stock <?= $in_stock ? 'stock-in' : 'stock-out' ?>">
                        <?= $in_stock ? $p['stock_quantity'] . ' in stock' : 'Out of Stock' ?>
                    </div>

                    <button class="btn-add" 
                            onclick="addToCart(<?= $p['product_id'] ?>)" 
                            <?= !$in_stock ? 'disabled' : '' ?>>
                        <?= $in_stock ? 'Add to Cart' : 'Unavailable' ?>
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="toast" id="toast">
        <span style="color: var(--success); font-size: 1.25rem;">✅</span>
        <span id="toast-msg">Added to cart!</span>
    </div>

    <script>
        function addToCart(productId) {
            fetch('ajax_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=add&product_id=' + productId
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    document.getElementById('cart-count').textContent = data.cart_count;
                    showToast('Item added to cart!');
                } else {
                    showToast(data.error || 'Could not add item.', true);
                }
            });
        }

        function showToast(msg, isError = false) {
            const toast = document.getElementById('toast');
            document.getElementById('toast-msg').textContent = msg;
            toast.style.borderLeft = isError ? '4px solid var(--danger)' : '4px solid var(--success)';
            toast.classList.add('show');
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }
    </script>
</body>
</html>
