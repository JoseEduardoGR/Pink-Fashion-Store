<?php
require_once 'auth.php';
require_once 'config/database.php';

class Cart {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    public function addToCart($user_id, $product_id, $quantity = 1) {
        // Verificar si el producto ya está en el carrito
        $query = "SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$user_id, $product_id]);
        $existing = $stmt->fetch();
        
        if ($existing) {
            // Actualizar cantidad
            $new_quantity = $existing['quantity'] + $quantity;
            $query = "UPDATE cart SET quantity = ? WHERE id = ?";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([$new_quantity, $existing['id']]);
        } else {
            // Agregar nuevo item
            $query = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([$user_id, $product_id, $quantity]);
        }
    }
    
    public function removeFromCart($user_id, $product_id) {
        $query = "DELETE FROM cart WHERE user_id = ? AND product_id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$user_id, $product_id]);
    }
    
    public function updateQuantity($user_id, $product_id, $quantity) {
        if ($quantity <= 0) {
            return $this->removeFromCart($user_id, $product_id);
        }
        
        $query = "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$quantity, $user_id, $product_id]);
    }
    
    public function getCartItems($user_id) {
        $query = "SELECT c.*, p.name, p.price, p.image_url, p.stock, (c.quantity * p.price) as subtotal 
                  FROM cart c 
                  JOIN products p ON c.product_id = p.id 
                  WHERE c.user_id = ? 
                  ORDER BY c.created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }
    
    public function getCartCount($user_id) {
        $query = "SELECT SUM(quantity) as count FROM cart WHERE user_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$user_id]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }
    
    public function getCartTotal($user_id) {
        $query = "SELECT SUM(c.quantity * p.price) as total 
                  FROM cart c 
                  JOIN products p ON c.product_id = p.id 
                  WHERE c.user_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$user_id]);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }
    
    public function clearCart($user_id) {
        $query = "DELETE FROM cart WHERE user_id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$user_id]);
    }
}
?>
