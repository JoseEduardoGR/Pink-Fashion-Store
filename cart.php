<?php
require_once 'includes/auth.php';
require_once 'includes/cart.php';


requireLogin();

$cart = new Cart();
$cart_items = $cart->getCartItems($_SESSION['user_id']);
$cart_total = $cart->getCartTotal($_SESSION['user_id']);
$cart_count = $cart->getCartCount($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras - Pink Fashion Store</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <div class="nav-container">
                <div class="nav-logo">
                    <i class="fas fa-heart"></i>
                    <h1><a href="index.php">Pink Fashion</a></h1>
                </div>
                <ul class="nav-menu">
                    <li><a href="index.php"><i class="fas fa-home"></i> Inicio</a></li>
                    <li><a href="cart.php" class="active"><i class="fas fa-shopping-cart"></i> Carrito</a></li>
                    <li><a href="dashboard.php"><i class="fas fa-user"></i> Mi Cuenta</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
                </ul>
                <div class="hamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </nav>
    </header>

    <main class="cart-main">
        <div class="container">
            <div class="cart-header">
                <h1><i class="fas fa-shopping-cart"></i> Mi Carrito</h1>
                <p>Revisa tus productos antes de finalizar la compra</p>
            </div>

            <?php if (empty($cart_items)): ?>
                <div class="empty-cart">
                    <div class="empty-cart-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h2>Tu carrito está vacío</h2>
                    <p>¡Agrega algunos productos increíbles a tu carrito!</p>
                    <a href="index.php#productos" class="btn-primary">
                        <i class="fas fa-shopping-bag"></i>
                        Continuar Comprando
                    </a>
                </div>
            <?php else: ?>
                <div class="cart-content">
                    <div class="cart-items">
                        <div class="cart-items-header">
                            <h2>Productos en tu carrito (<?php echo $cart_count; ?>)</h2>
                        </div>
                        
                        <div class="cart-items-list">
                            <?php foreach ($cart_items as $item): ?>
                                <div class="cart-item" data-product-id="<?php echo $item['product_id']; ?>">
                                    <div class="item-image">
                                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                                             alt="<?php echo htmlspecialchars($item['name']); ?>">
                                    </div>
                                    
                                    <div class="item-details">
                                        <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                                        <p class="item-price">$<?php echo number_format($item['price'], 2); ?></p>
                                        <p class="item-stock">Stock disponible: <?php echo $item['stock']; ?></p>
                                    </div>
                                    
                                    <div class="item-quantity">
                                        <label>Cantidad:</label>
                                        <div class="quantity-controls">
                                            <button class="quantity-btn minus" data-action="decrease">
                                                <i class="fas fa-minus"></i>
                                            <button class="quantity-btn minus" data-action="decrease">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <input type="number" class="quantity-input" 
                                                value="<?php echo $item['quantity']; ?>" 
                                                min="1" max="<?php echo $item['stock']; ?>">
                                            <button class="quantity-btn plus" data-action="increase">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="item-subtotal">
                                        <p class="subtotal-label">Subtotal:</p>
                                        <p class="subtotal-amount">$<?php echo number_format($item['subtotal'], 2); ?></p>
                                    </div>
                                    
                                    <div class="item-actions">
                                        <button class="btn-remove" data-product-id="<?php echo $item['product_id']; ?>">
                                            <i class="fas fa-trash"></i>
                                            Eliminar
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="cart-summary">
                        <div class="summary-card">
                            <h3><i class="fas fa-receipt"></i> Resumen del Pedido</h3>
                            
                            <div class="summary-details">
                                <div class="summary-row">
                                    <span>Subtotal:</span>
                                    <span id="cartSubtotal">$<?php echo number_format($cart_total, 2); ?></span>
                                </div>
                                <div class="summary-row">
                                    <span>Envío:</span>
                                    <span class="free-shipping">Gratis</span>
                                </div>
                                <div class="summary-row">
                                    <span>Impuestos:</span>
                                    <span>$<?php echo number_format($cart_total * 0.08, 2); ?></span>
                                </div>
                                <div class="summary-divider"></div>
                                <div class="summary-row total">
                                    <span>Total:</span>
                                    <span id="cartTotal">$<?php echo number_format($cart_total * 1.08, 2); ?></span>
                                </div>
                            </div>
                            
                            <div class="summary-actions">
                                <button class="btn-primary btn-full checkout-btn">
                                    <i class="fas fa-credit-card"></i>
                                    Proceder al Pago
                                </button>
                                <a href="index.php#productos" class="btn-outline btn-full">
                                    <i class="fas fa-arrow-left"></i>
                                    Continuar Comprando
                                </a>
                            </div>
                            
                            <div class="payment-methods">
                                <p>Métodos de pago aceptados:</p>
                                <div class="payment-icons">
                                    <i class="fab fa-cc-visa"></i>
                                    <i class="fab fa-cc-mastercard"></i>
                                    <i class="fab fa-cc-paypal"></i>
                                    <i class="fab fa-cc-apple-pay"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Toast Notifications -->
    <div id="toast-container"></div>

    <script src="js/script.js"></script>
</body>
</html>
