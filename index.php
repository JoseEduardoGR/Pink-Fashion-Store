<?php
require_once 'includes/auth.php';
require_once 'includes/cart.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Obtener productos destacados
$query = "SELECT * FROM products ORDER BY created_at DESC LIMIT 12";
$stmt = $db->prepare($query);
$stmt->execute();
$products = $stmt->fetchAll();

// Obtener contador del carrito si está logueado
$cart_count = 0;
if (isLoggedIn()) {
    $cart = new Cart();
    $cart_count = $cart->getCartCount($_SESSION['user_id']);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pink Fashion Store - Tienda de Ropa Femenina</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <div class="nav-container">
                <div class="nav-logo">
                    <i class="fas fa-heart"></i>
                    <h1>Pink Fashion</h1>
                </div>
                <ul class="nav-menu">
                    <li><a href="index.php" class="active"><i class="fas fa-home"></i> Inicio</a></li>
                    <li><a href="#productos"><i class="fas fa-shopping-bag"></i> Productos</a></li>
                    <li><a href="#contacto"><i class="fas fa-envelope"></i> Contacto</a></li>
                    <?php if (isLoggedIn()): ?>
                        <li><a href="cart.php" class="cart-link">
                            <i class="fas fa-shopping-cart"></i> 
                            Carrito 
                            <span class="cart-badge" id="cartBadge"><?php echo $cart_count; ?></span>
                        </a></li>
                        <li><a href="dashboard.php"><i class="fas fa-user"></i> Mi Cuenta</a></li>
                        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
                    <?php else: ?>
                        <li><a href="login.php"><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</a></li>
                        <li><a href="register.php"><i class="fas fa-user-plus"></i> Registrarse</a></li>
                    <?php endif; ?>
                </ul>
                <div class="hamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <section class="hero">
            <div class="hero-background">
                <div class="hero-overlay"></div>
            </div>
            <div class="hero-content">
                <div class="hero-text">
                    <h2>Descubre tu Estilo Rosa</h2>
                    <p>La moda más elegante y femenina te espera. Encuentra piezas únicas que reflejen tu personalidad.</p>
                    <div class="hero-buttons">
                        <a href="#productos" class="btn-primary">
                            <i class="fas fa-shopping-bag"></i>
                            Ver Colección
                        </a>
                        <a href="#contacto" class="btn-outline">
                            <i class="fas fa-info-circle"></i>
                            Conoce Más
                        </a>
                    </div>
                </div>
                <div class="hero-stats">
                    <div class="stat">
                        <h3>500+</h3>
                        <p>Productos</p>
                    </div>
                    <div class="stat">
                        <h3>1000+</h3>
                        <p>Clientes Felices</p>
                    </div>
                    <div class="stat">
                        <h3>24/7</h3>
                        <p>Soporte</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="features">
            <div class="container">
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-shipping-fast"></i>
                        </div>
                        <h3>Envío Gratis</h3>
                        <p>En compras mayores a $50</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-undo"></i>
                        </div>
                        <h3>Devoluciones</h3>
                        <p>30 días para cambios</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <h3>Soporte 24/7</h3>
                        <p>Atención personalizada</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3>Compra Segura</h3>
                        <p>Pagos protegidos</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="productos" class="products-section">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">Productos Destacados</h2>
                    <p class="section-subtitle">Descubre nuestra colección más exclusiva</p>
                </div>
                
                <div class="products-filter">
                    <button class="filter-btn active" data-filter="all">Todos</button>
                    <button class="filter-btn" data-filter="vestidos">Vestidos</button>
                    <button class="filter-btn" data-filter="blusas">Blusas</button>
                    <button class="filter-btn" data-filter="faldas">Faldas</button>
                    <button class="filter-btn" data-filter="accesorios">Accesorios</button>
                </div>

                <div class="products-grid">
                    <?php foreach ($products as $product): ?>
                        <div class="product-card" data-category="<?php echo htmlspecialchars($product['category']); ?>">
                            <div class="product-image">
                                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>">
                                <div class="product-overlay">
                                    <?php if (isLoggedIn()): ?>
                                        <button class="btn-add-cart" data-product-id="<?php echo $product['id']; ?>">
                                            <i class="fas fa-shopping-cart"></i>
                                            Agregar al Carrito
                                        </button>
                                    <?php else: ?>
                                        <a href="login.php" class="btn-add-cart">
                                            <i class="fas fa-sign-in-alt"></i>
                                            Iniciar Sesión
                                        </a>
                                    <?php endif; ?>
                                    <button class="btn-quick-view" data-product-id="<?php echo $product['id']; ?>">
                                        <i class="fas fa-eye"></i>
                                        Vista Rápida
                                    </button>
                                </div>
                                <?php if ($product['stock'] < 5): ?>
                                    <div class="product-badge">¡Últimas piezas!</div>
                                <?php endif; ?>
                            </div>
                            <div class="product-info">
                                <div class="product-category"><?php echo htmlspecialchars($product['category']); ?></div>
                                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                                <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                                <div class="product-details">
                                    <div class="product-price">
                                        <span class="current-price">$<?php echo number_format($product['price'], 2); ?></span>
                                    </div>
                                    <div class="product-meta">
                                        <span class="product-size">Talla: <?php echo htmlspecialchars($product['size']); ?></span>
                                        <span class="product-stock">Stock: <?php echo $product['stock']; ?></span>
                                    </div>
                                </div>
                                <div class="product-rating">
                                    <div class="stars">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <span class="rating-text">(4.8)</span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="newsletter">
            <div class="container">
                <div class="newsletter-content">
                    <div class="newsletter-text">
                        <h2>¡Mantente al día con Pink Fashion!</h2>
                        <p>Suscríbete y recibe ofertas exclusivas, nuevos productos y tendencias de moda.</p>
                    </div>
                    <form class="newsletter-form">
                        <div class="input-group">
                            <input type="email" placeholder="Tu email aquí..." required>
                            <button type="submit" class="btn-primary">
                                <i class="fas fa-paper-plane"></i>
                                Suscribirse
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <section id="contacto" class="contact-section">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">Contacto</h2>
                    <p class="section-subtitle">Estamos aquí para ayudarte</p>
                </div>
                <div class="contact-grid">
                    <div class="contact-info">
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="contact-details">
                                <h4>Email</h4>
                                <p>info@pinkfashion.com</p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="contact-details">
                                <h4>Teléfono</h4>
                                <p>+1 234 567 8900</p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="contact-details">
                                <h4>Dirección</h4>
                                <p>123 Fashion Street, Style City</p>
                            </div>
                        </div>
                    </div>
                    <div class="contact-form">
                        <form>
                            <div class="form-group">
                                <input type="text" placeholder="Tu nombre" required>
                            </div>
                            <div class="form-group">
                                <input type="email" placeholder="Tu email" required>
                            </div>
                            <div class="form-group">
                                <textarea placeholder="Tu mensaje" rows="5" required></textarea>
                            </div>
                            <button type="submit" class="btn-primary">
                                <i class="fas fa-paper-plane"></i>
                                Enviar Mensaje
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="footer-logo">
                        <i class="fas fa-heart"></i>
                        <h3>Pink Fashion</h3>
                    </div>
                    <p>Tu destino para la moda rosa más elegante y femenina.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-pinterest"></i></a>
                    </div>
                </div>
                <div class="footer-section">
                    <h4>Enlaces Rápidos</h4>
                    <ul>
                        <li><a href="#productos">Productos</a></li>
                        <li><a href="#contacto">Contacto</a></li>
                        <li><a href="#">Términos y Condiciones</a></li>
                        <li><a href="#">Política de Privacidad</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Categorías</h4>
                    <ul>
                        <li><a href="#">Vestidos</a></li>
                        <li><a href="#">Blusas</a></li>
                        <li><a href="#">Faldas</a></li>
                        <li><a href="#">Accesorios</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Atención al Cliente</h4>
                    <ul>
                        <li><a href="#">Centro de Ayuda</a></li>
                        <li><a href="#">Envíos y Devoluciones</a></li>
                        <li><a href="#">Guía de Tallas</a></li>
                        <li><a href="#">Cuidado de Prendas</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 Pink Fashion Store. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- Toast Notifications -->
    <div id="toast-container"></div>

    <!-- Quick View Modal -->
    <div id="quickViewModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div id="quickViewContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>

    <script src="js/script.js"></script>
</body>
</html>
