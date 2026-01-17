<?php
require_once 'includes/auth.php';
require_once 'config/database.php';

requireLogin();

$database = new Database();
$db = $database->getConnection();

$success = '';
$error = '';

if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'login':
            $success = 'Bienvenido/a, ' . $_SESSION['full_name'] . '!';
            break;
        case 'profile_updated':
            $success = 'Perfil actualizado correctamente.';
            break;
    }
}

// Obtener datos del usuario
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Actualizar perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    if (!validateCSRFToken($_POST['csrf_token'])) {
        $error = 'Token de seguridad inválido.';
    } else {
        $full_name = sanitizeInput($_POST['full_name']);
        $email = sanitizeInput($_POST['email']);
        $phone = sanitizeInput($_POST['phone']);
        $address = sanitizeInput($_POST['address']);

        if (empty($full_name) || empty($email)) {
            $error = 'El nombre y email son obligatorios.';
        } elseif (!validateEmail($email)) {
            $error = 'El email no es válido.';
        } else {
            // Verificar si el email ya existe (excepto el actual)
            $query = "SELECT id FROM users WHERE email = ? AND id != ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$email, $_SESSION['user_id']]);
            
            if ($stmt->fetch()) {
                $error = 'Este email ya está registrado por otro usuario.';
            } else {
                $query = "UPDATE users SET full_name = ?, email = ?, phone = ?, address = ? WHERE id = ?";
                $stmt = $db->prepare($query);
                
                if ($stmt->execute([$full_name, $email, $phone, $address, $_SESSION['user_id']])) {
                    $_SESSION['full_name'] = $full_name;
                    header('Location: dashboard.php?success=profile_updated');
                    exit();
                } else {
                    $error = 'Error al actualizar el perfil.';
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Cuenta - Pink Fashion Store</title>
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
                    <li><a href="cart.php"><i class="fas fa-shopping-cart"></i> Carrito</a></li>
                    <li><a href="dashboard.php" class="active"><i class="fas fa-user"></i> Mi Cuenta</a></li>
                    <?php if (isAdmin()): ?>
                        <li><a href="products.php"><i class="fas fa-cogs"></i> Gestionar Productos</a></li>
                    <?php endif; ?>
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

    <main class="dashboard-main">
        <div class="container">
            <div class="dashboard-header">
                <div class="user-welcome">
                    <div class="user-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="user-info">
                        <h1>¡Hola, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</h1>
                        <p>Gestiona tu cuenta y pedidos desde aquí</p>
                    </div>
                </div>
                <div class="dashboard-stats">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                        <div class="stat-info">
                            <h3>0</h3>
                            <p>Pedidos</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <div class="stat-info">
                            <h3>0</h3>
                            <p>Favoritos</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="stat-info">
                            <h3>5.0</h3>
                            <p>Rating</p>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <div class="dashboard-content">
                <div class="dashboard-sidebar">
                    <ul class="dashboard-menu">
                        <li>
                            <a href="#profile" class="menu-link active" data-section="profile">
                                <i class="fas fa-user"></i>
                                <span>Mi Perfil</span>
                            </a>
                        </li>
                        <li>
                            <a href="#orders" class="menu-link" data-section="orders">
                                <i class="fas fa-shopping-bag"></i>
                                <span>Mis Pedidos</span>
                            </a>
                        </li>
                        <li>
                            <a href="#favorites" class="menu-link" data-section="favorites">
                                <i class="fas fa-heart"></i>
                                <span>Favoritos</span>
                            </a>
                        </li>
                        <li>
                            <a href="#settings" class="menu-link" data-section="settings">
                                <i class="fas fa-cog"></i>
                                <span>Configuración</span>
                            </a>
                        </li>
                        <?php if (isAdmin()): ?>
                            <li>
                                <a href="products.php" class="menu-link">
                                    <i class="fas fa-cogs"></i>
                                    <span>Gestionar Productos</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>

                <div class="dashboard-main-content">
                    <div id="profile" class="dashboard-section active">
                        <div class="section-header">
                            <h2><i class="fas fa-user"></i> Información Personal</h2>
                            <p>Actualiza tu información personal</p>
                        </div>
                        
                        <div class="profile-card">
                            <form method="POST" class="profile-form">
                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                <input type="hidden" name="update_profile" value="1">

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="full_name">
                                            <i class="fas fa-user"></i>
                                            Nombre Completo
                                        </label>
                                        <input type="text" id="full_name" name="full_name" 
                                               value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="username">
                                            <i class="fas fa-at"></i>
                                            Usuario
                                        </label>
                                        <input type="text" id="username" name="username" 
                                               value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="email">
                                            <i class="fas fa-envelope"></i>
                                            Email
                                        </label>
                                        <input type="email" id="email" name="email" 
                                               value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="phone">
                                            <i class="fas fa-phone"></i>
                                            Teléfono
                                        </label>
                                        <input type="tel" id="phone" name="phone" 
                                               value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="address">
                                        <i class="fas fa-map-marker-alt"></i>
                                        Dirección
                                    </label>
                                    <textarea id="address" name="address" rows="3"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                                </div>

                                <div class="form-actions">
                                    <button type="submit" class="btn-primary">
                                        <i class="fas fa-save"></i>
                                        Actualizar Perfil
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div id="orders" class="dashboard-section">
                        <div class="section-header">
                            <h2><i class="fas fa-shopping-bag"></i> Mis Pedidos</h2>
                            <p>Historial de tus compras</p>
                        </div>
                        
                        <div class="orders-container">
                            <div class="empty-state">
                                <i class="fas fa-shopping-bag"></i>
                                <h3>No tienes pedidos aún</h3>
                                <p>¡Explora nuestros productos y realiza tu primera compra!</p>
                                <a href="index.php#productos" class="btn-primary">
                                    <i class="fas fa-shopping-bag"></i>
                                    Ver Productos
                                </a>
                            </div>
                        </div>
                    </div>

                    <div id="favorites" class="dashboard-section">
                        <div class="section-header">
                            <h2><i class="fas fa-heart"></i> Mis Favoritos</h2>
                            <p>Productos que te encantan</p>
                        </div>
                        
                        <div class="favorites-container">
                            <div class="empty-state">
                                <i class="fas fa-heart"></i>
                                <h3>No tienes favoritos aún</h3>
                                <p>Guarda tus productos favoritos para encontrarlos fácilmente</p>
                                <a href="index.php#productos" class="btn-primary">
                                    <i class="fas fa-heart"></i>
                                    Explorar Productos
                                </a>
                            </div>
                        </div>
                    </div>

                    <div id="settings" class="dashboard-section">
                        <div class="section-header">
                            <h2><i class="fas fa-cog"></i> Configuración</h2>
                            <p>Personaliza tu experiencia</p>
                        </div>
                        
                        <div class="settings-container">
                            <div class="settings-card">
                                <h3><i class="fas fa-bell"></i> Notificaciones</h3>
                                <div class="setting-item">
                                    <label class="switch">
                                        <input type="checkbox" checked>
                                        <span class="slider"></span>
                                    </label>
                                    <div class="setting-info">
                                        <h4>Ofertas y promociones</h4>
                                        <p>Recibe notificaciones sobre ofertas especiales</p>
                                    </div>
                                </div>
                                <div class="setting-item">
                                    <label class="switch">
                                        <input type="checkbox" checked>
                                        <span class="slider"></span>
                                    </label>
                                    <div class="setting-info">
                                        <h4>Nuevos productos</h4>
                                        <p>Entérate de los últimos productos</p>
                                    </div>
                                </div>
                            </div>

                            <div class="settings-card">
                                <h3><i class="fas fa-shield-alt"></i> Privacidad</h3>
                                <div class="setting-item">
                                    <label class="switch">
                                        <input type="checkbox">
                                        <span class="slider"></span>
                                    </label>
                                    <div class="setting-info">
                                        <h4>Perfil público</h4>
                                        <p>Permite que otros usuarios vean tu perfil</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="js/script.js"></script>
</body>
</html>
