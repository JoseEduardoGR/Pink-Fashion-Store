<?php
require_once 'includes/auth.php';
require_once 'config/database.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'])) {
        $error = 'Token de seguridad inválido.';
    } else {
        $username = sanitizeInput($_POST['username']);
        $email = sanitizeInput($_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $full_name = sanitizeInput($_POST['full_name']);
        $phone = sanitizeInput($_POST['phone']);

        // Validaciones
        if (empty($username) || empty($email) || empty($password) || empty($full_name)) {
            $error = 'Todos los campos obligatorios deben ser completados.';
        } elseif (!validateEmail($email)) {
            $error = 'El email no es válido.';
        } elseif (strlen($password) < 6) {
            $error = 'La contraseña debe tener al menos 6 caracteres.';
        } elseif ($password !== $confirm_password) {
            $error = 'Las contraseñas no coinciden.';
        } else {
            $database = new Database();
            $db = $database->getConnection();

            // Verificar si el usuario o email ya existen
            $query = "SELECT id FROM users WHERE username = ? OR email = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$username, $email]);
            
            if ($stmt->fetch()) {
                $error = 'El usuario o email ya están registrados.';
            } else {
                // Crear nuevo usuario
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $query = "INSERT INTO users (username, email, password, full_name, phone) VALUES (?, ?, ?, ?, ?)";
                $stmt = $db->prepare($query);
                
                if ($stmt->execute([$username, $email, $hashed_password, $full_name, $phone])) {
                    header('Location: login.php?success=registered');
                    exit();
                } else {
                    $error = 'Error al crear la cuenta. Inténtalo de nuevo.';
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
    <title>Registrarse - Pink Fashion Store</title>
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
            <div class="auth-card register-card">
                <div class="auth-header">
                    <div class="auth-logo">
                        <i class="fas fa-heart"></i>
                        <h1>Pink Fashion</h1>
                    </div>
                    <h2>¡Únete a Pink Fashion!</h2>
                    <p>Crea tu cuenta y descubre un mundo de moda rosa</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form class="auth-form" method="POST" id="registerForm">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <div class="input-wrapper">
                                <i class="fas fa-user input-icon"></i>
                                <input type="text" id="full_name" name="full_name" placeholder="Nombre Completo *" required>
                            </div>
                            <span class="error-message" id="fullNameError"></span>
                        </div>

                        <div class="form-group">
                            <div class="input-wrapper">
                                <i class="fas fa-at input-icon"></i>
                                <input type="text" id="username" name="username" placeholder="Usuario *" required>
                            </div>
                            <span class="error-message" id="usernameError"></span>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <div class="input-wrapper">
                                <i class="fas fa-envelope input-icon"></i>
                                <input type="email" id="email" name="email" placeholder="Email *" required>
                            </div>
                            <span class="error-message" id="emailError"></span>
                        </div>

                        <div class="form-group">
                            <div class="input-wrapper">
                                <i class="fas fa-phone input-icon"></i>
                                <input type="tel" id="phone" name="phone" placeholder="Teléfono">
                            </div>
                            <span class="error-message" id="phoneError"></span>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <div class="input-wrapper">
                                <i class="fas fa-lock input-icon"></i>
                                <input type="password" id="password" name="password" placeholder="Contraseña *" required>
                                <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <span class="error-message" id="passwordError"></span>
                        </div>

                        <div class="form-group">
                            <div class="input-wrapper">
                                <i class="fas fa-lock input-icon"></i>
                                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirmar Contraseña *" required>
                                <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <span class="error-message" id="confirmPasswordError"></span>
                        </div>
                    </div>

                    <div class="form-options">
                        <label class="checkbox-wrapper">
                            <input type="checkbox" name="terms" required>
                            <span class="checkmark"></span>
                            Acepto los <a href="#">términos y condiciones</a>
                        </label>
                    </div>

                    <button type="submit" class="btn-primary btn-full">
                        <i class="fas fa-user-plus"></i>
                        Crear Cuenta
                    </button>
                </form>

                <div class="auth-divider">
                    <span>o regístrate con</span>
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
                    <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a></p>
                    <p><a href="index.php"><i class="fas fa-arrow-left"></i> Volver al inicio</a></p>
                </div>
            </div>
        </div>
    </div>

    <script src="js/script.js"></script>
</body>
</html>
