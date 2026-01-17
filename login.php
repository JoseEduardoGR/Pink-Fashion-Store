<?php
require_once 'includes/auth.php';
require_once 'config/database.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
$success = '';

if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'access_denied':
            $error = 'Debes iniciar sesión para acceder a esta página.';
            break;
        case 'invalid_credentials':
            $error = 'Usuario o contraseña incorrectos.';
            break;
    }
}

if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'registered':
            $success = 'Registro exitoso. Ahora puedes iniciar sesión.';
            break;
        case 'logout':
            $success = 'Sesión cerrada correctamente.';
            break;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'])) {
        $error = 'Token de seguridad inválido.';
    } else {
        $username = sanitizeInput($_POST['username']);
        $password = $_POST['password'];

        if (empty($username) || empty($password)) {
            $error = 'Todos los campos son obligatorios.';
        } else {
            $database = new Database();
            $db = $database->getConnection();

            $query = "SELECT id, username, password, full_name, role FROM users WHERE username = ? OR email = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];
                
                header('Location: dashboard.php?success=login');
                exit();
            } else {
                $error = 'Usuario o contraseña incorrectos.';
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
    <title>Iniciar Sesión - Pink Fashion Store</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="auth-body">
    <div class="auth-container">
        <div class="auth-background">
            <div class="auth-shapes">
                <div class="shape shape-1"></div>
                <div class="shape shape-2"></div>
                <div class="shape shape-3"></div>
            </div>
        </div>
        
        <div class="auth-content">
            <div class="auth-card">
                <div class="auth-header">
                    <div class="auth-logo">
                        <i class="fas fa-heart"></i>
                        <h1>Pink Fashion</h1>
                    </div>
                    <h2>¡Bienvenida de vuelta!</h2>
                    <p>Inicia sesión para continuar con tu experiencia de compra</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>

                <form class="auth-form" method="POST" id="loginForm">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    
                    <div class="form-group">
                        <div class="input-wrapper">
                            <i class="fas fa-user input-icon"></i>
                            <input type="text" id="username" name="username" placeholder="Usuario o Email" required>
                        </div>
                        <span class="error-message" id="usernameError"></span>
                    </div>

                    <div class="form-group">
                        <div class="input-wrapper">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" id="password" name="password" placeholder="Contraseña" required>
                            <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <span class="error-message" id="passwordError"></span>
                    </div>

                    <div class="form-options">
                        <label class="checkbox-wrapper">
                            <input type="checkbox" name="remember">
                            <span class="checkmark"></span>
                            Recordarme
                        </label>
                        <a href="#" class="forgot-password">¿Olvidaste tu contraseña?</a>
                    </div>

                    <button type="submit" class="btn-primary btn-full">
                        <i class="fas fa-sign-in-alt"></i>
                        Iniciar Sesión
                    </button>
                </form>

                <div class="auth-divider">
                    <span>o continúa con</span>
                </div>

                <div class="social-login">
                    <button class="btn-social btn-google">
                        <i class="fab fa-google"></i>
                        Google
                    </button>
                    <button class="btn-social btn-facebook">
                        <i class="fab fa-facebook-f"></i>
                        Facebook
                    </button>
                </div>

                <div class="auth-footer">
                    <p>¿No tienes cuenta? <a href="register.php">Regístrate aquí</a></p>
                    <p><a href="index.php"><i class="fas fa-arrow-left"></i> Volver al inicio</a></p>
                </div>
            </div>
        </div>
    </div>

    <script src="js/script.js"></script>
</body>
</html>
