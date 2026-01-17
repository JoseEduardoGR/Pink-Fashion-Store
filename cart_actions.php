<?php
require_once 'includes/auth.php';
require_once 'includes/cart.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';
$product_id = intval($input['product_id'] ?? 0);
$quantity = intval($input['quantity'] ?? 1);

$cart = new Cart();
$user_id = $_SESSION['user_id'];

switch ($action) {
    case 'add':
        $success = $cart->addToCart($user_id, $product_id, $quantity);
        $count = $cart->getCartCount($user_id);
        echo json_encode([
            'success' => $success,
            'message' => $success ? 'Producto agregado al carrito' : 'Error al agregar producto',
            'cart_count' => $count
        ]);
        break;
        
    case 'remove':
        $success = $cart->removeFromCart($user_id, $product_id);
        $count = $cart->getCartCount($user_id);
        $total = $cart->getCartTotal($user_id);
        echo json_encode([
            'success' => $success,
            'message' => $success ? 'Producto eliminado del carrito' : 'Error al eliminar producto',
            'cart_count' => $count,
            'cart_total' => $total
        ]);
        break;
        
    case 'update':
        $success = $cart->updateQuantity($user_id, $product_id, $quantity);
        $count = $cart->getCartCount($user_id);
        $total = $cart->getCartTotal($user_id);
        echo json_encode([
            'success' => $success,
            'message' => $success ? 'Cantidad actualizada' : 'Error al actualizar cantidad',
            'cart_count' => $count,
            'cart_total' => $total
        ]);
        break;
        
    case 'get_count':
        $count = $cart->getCartCount($user_id);
        echo json_encode(['success' => true, 'cart_count' => $count]);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
        break;
}
?>
