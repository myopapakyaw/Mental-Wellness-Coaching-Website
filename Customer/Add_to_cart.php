<?php

use const Dom\NOT_FOUND_ERR;

session_start();
require_once 'Connect.php';

class Cart {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function addToCart($user_id, $service_id, $quantity = 1) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM add_to_cart WHERE user_id = ? AND service_id = ?");
            $stmt->execute([$user_id, $service_id]);
            
            if ($stmt->rowCount() > 0) {
                $stmt = $this->pdo->prepare("UPDATE add_to_cart SET quantity = quantity + ? WHERE user_id = ? AND service_id = ?");
                return $stmt->execute([$quantity, $user_id, $service_id]);
            } else {
                $stmt = $this->pdo->prepare("INSERT INTO add_to_cart (user_id, service_id, quantity) VALUES (?, ?, ?)");
                return $stmt->execute([$user_id, $service_id, $quantity]);
            }
        } catch (PDOException $e) {
            error_log("Error adding to cart: " . $e->getMessage());
            return false;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['service_id']) && !empty($_POST['service_id'])) {
    $cart = new Cart($pdo);
    $user_id = $_SESSION['user_id'] ?? 1; // Default to 1 if not logged in; adjust for guest logic if needed
    $service_id = $_POST['service_id'];
    $quantity = $_POST['quantity'] ?? 1;

    if ($cart->addToCart($user_id, $service_id, $quantity)) {
        header("Location: Servicepage.php?message=Service added to cart");
    } else {
        header("Location: Servicepage.php?error=Failed to add to cart");
    }
    exit();
} else {
    header("Location: Servicepage.php?error=Invalid request");
    exit();
}


?>
