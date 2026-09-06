<?php
ob_start();
session_start();
require_once 'Connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cart = new Cart($pdo);
    $user_id = $_SESSION['user_id'] ?? 1;

    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $service_id = $_POST['service_id'] ?? 0;
                $quantity = $_POST['quantity'] ?? 1;
                if ($cart->addToCart($user_id, $service_id, $quantity)) {
                    header("Location: Servicepage.php?message=Service added to cart");
                    exit();
                } else {
                    header("Location: Servicepage.php?error=Failed to add to cart");
                    exit();
                }
            case 'update':
                $cart_id = $_POST['cart_id'] ?? 0;
                $quantity = $_POST['quantity'] ?? 1;
                $cart->updateQuantity($cart_id, $quantity);
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            case 'remove':
                $cart_id = $_POST['cart_id'] ?? 0;
                $cart->removeFromCart($cart_id);
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
        }
    }
}

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
    
    public function getCartItems($user_id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT c.cart_id, c.service_id, c.quantity, c.added_at,
                       s.service_name, s.service_price, s.service_description, s.duration
                FROM add_to_cart c
                JOIN services s ON c.service_id = s.service_id
                WHERE c.user_id = ?
            ");
            $stmt->execute([$user_id]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $items ?: [];
        } catch (PDOException $e) {
            error_log("Error fetching cart items: " . $e->getMessage());
            return [];
        }
    }
    
    public function updateQuantity($cart_id, $quantity) {
        try {
            $stmt = $this->pdo->prepare("UPDATE add_to_cart SET quantity = ? WHERE cart_id = ?");
            return $stmt->execute([$quantity, $cart_id]);
        } catch (PDOException $e) {
            error_log("Error updating quantity: " . $e->getMessage());
            return false;
        }
    }
    
    public function removeFromCart($cart_id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM add_to_cart WHERE cart_id = ?");
            return $stmt->execute([$cart_id]);
        } catch (PDOException $e) {
            error_log("Error removing item: " . $e->getMessage());
            return false;
        }
    }
    
    public function getCartTotal($user_id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT SUM(s.service_price * c.quantity) as total
                FROM add_to_cart c
                JOIN services s ON c.service_id = s.service_id
                WHERE c.user_id = ?
            ");
            $stmt->execute([$user_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Error calculating total: " . $e->getMessage());
            return 0;
        }
    }
    
    public function getCartItemCount($user_id) {
        try {
            $stmt = $this->pdo->prepare("SELECT SUM(quantity) as total_sessions FROM add_to_cart WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total_sessions'] ?? 0;
        } catch (PDOException $e) {
            error_log("Error getting cart item count: " . $e->getMessage());
            return 0;
        }
    }
}

$cart = new Cart($pdo);
$user_id = $_SESSION['user_id'] ?? 1;
$cart_items = $cart->getCartItems($user_id);
$total = $cart->getCartTotal($user_id);

include("Header.php");
include("Nav.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        .main-content {
            font-family: 'Poppins', sans-serif;
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
            /* background: linear-gradient(135deg,rgb(144, 247, 118) 0%, #e6eef6 100%); */
            min-height: calc(100vh - 80px);
        }
        h1 {
            color: #1a3c5e;
            text-align: center;
            margin-bottom: 40px;
            font-size: 2.5em;
            font-weight: 600;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }
        .cart-container {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            padding: 30px;
            transition: transform 0.3s ease;
        }
        .cart-container:hover {
            transform: translateY(-5px);
        }
        .cart-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
            margin-bottom: 30px;
        }
        .cart-table th {
            background: linear-gradient(90deg,rgb(6, 118, 21),rgb(45, 205, 120));
            color: white;
            text-transform: uppercase;
            font-size: 14px;
            padding: 15px 20px;
            border-bottom: 3px solid #1f6391;
        }
        .cart-table td {
            padding: 20px;
            background: #fff;
            border-bottom: 1px solid #eef2f7;
            vertical-align: middle;
            transition: background 0.3s;
        }
        .cart-table tr:hover td {
            background: #f8fafc;
        }
        .quantity-form {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .quantity-form input[type="number"] {
            width: 70px;
            padding: 8px;
            border: 2px solid #e0e6ed;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        .quantity-form input[type="number"]:focus {
            border-color: #3498db;
            outline: none;
        }
        .botton {
            padding: 10px 20px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        .btn-update {
            background: #27ae60;
            color: white;
        }
        .btn-update:hover {
            background: #219653;
            transform: translateY(-2px);
        }
        .btn-remove {
            background: #e74c3c;
            color: white;
        }
        .btn-remove:hover {
            background: #c0392b;
            transform: translateY(-2px);
        }
        .total-row td {
            font-weight: 700;
            font-size: 20px;
            padding: 20px;
            color: #1a3c5e;
        }
        .empty-cart {
            text-align: center;
            padding: 60px 20px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }
        .empty-cart p {
            font-size: 20px;
            margin-bottom: 30px;
            color: #666;
            font-weight: 300;
        }
        .btn-continue, .btn-checkout {
            display: inline-block;
            padding: 14px 30px;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .btn-continue {
            background: #7f8c8d;
            color: white;
            margin-right: 15px;
        }
        .btn-continue:hover {
            background: #6c7778;
            transform: translateY(-3px);
        }
        .btn-checkout {
            background: linear-gradient(90deg,rgb(14, 239, 104), #3498db);
            color: white;
        }
        .btn-checkout:hover {
            background: linear-gradient(90deg, #2573a7, #2e86c1);
            transform: translateY(-3px);
        }
        .actions {
            margin-top: 30px;
            text-align: right;
        }
        @media (max-width: 768px) {
            .main-content {
                margin: 20px auto;
                padding: 10px;
            }
            h1 {
                font-size: 2em;
            }
            .cart-table th, .cart-table td {
                padding: 12px;
                font-size: 14px;
            }
            .quantity-form {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            .btn {
                width: 100%;
                padding: 12px;
            }
            .actions {
                text-align: center;
            }
            .btn-continue, .btn-checkout {
                display: block;
                margin: 15px 0;
                width: 100%;
            }
            .empty-cart {
                padding: 40px 10px;
            }
        }

        body.dark-mode {
            background-color: #121212;
            color: #f8f8f2;
        }

        body.dark-mode .main-content h1 {
            color: #f8f8f2;
        }

        body.dark-mode .cart-container {
            background: #1e1e1e;
            box-shadow: 0 4px 12px rgba(255, 255, 255, 0.05);
        }

        body.dark-mode .cart-table th {
            /* background: #2980b9; */
            background: linear-gradient(90deg,rgb(45, 205, 120), rgb(6, 118, 21));
            color:rgb(14, 14, 13);
        }

        body.dark-mode .cart-table td {
            color:rgb(22, 22, 21);
            border-bottom: 1px solid #333;
        }

        body.dark-mode .cart-table small {
            color:rgb(15, 15, 15);
        }

        body.dark-mode .quantity-form input[type="number"] {
            background:rgb(174, 172, 172);
            border-color: #444;
            color:rgb(11, 11, 11);
        }

        body.dark-mode .total-section {
            background:rgb(238, 233, 233);
        }

        body.dark-mode .total-amount {
            color:rgb(14, 14, 14);
        }

        body.dark-mode .empty-cart {
            background: #1e1e1e;
        }

        body.dark-mode .empty-cart p {
            color:rgb(17, 16, 16);
        }

        @media (max-width: 768px) {
            .cart-table {
                display: block;
                overflow-x: auto;
            }
            
            .quantity-form {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .actions {
                flex-direction: column;
            }
            
            .btn-action {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="main-content">
        <h1>Your Wellness Cart</h1>
        <div class="cart-container">
            <?php if (empty($cart_items)): ?>
                <div class="empty-cart">
                    <p>Your cart is empty. Discover our wellness services now!</p>
                    <a href="Servicepage.php" class="btn-continue">Continue Shopping</a>
                </div>
            <?php else: ?>
                <table class="cart-table">
                    <tr>
                        <th>Service</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Sessions</th>
                        <th>Subtotal</th>
                        <th>Action</th>
                    </tr>
                    <?php foreach ($cart_items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['service_name']); ?></td>
                            <td><?php echo htmlspecialchars($item['service_description']); ?><br>
                                <small>Duration: <?php echo htmlspecialchars($item['duration']); ?></small>
                            </td>
                            <td><?php echo number_format($item['service_price'], 0); ?> MMK</td>
                            <td>
                                <form method="post" class="quantity-form">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                    <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1">
                                    <button type="submit" class="botton btn-update">Update</button>
                                </form>
                            </td>
                            <td><?php echo number_format($item['service_price'] * $item['quantity'], 0); ?> MMK</td>
                            <td>
                                <form method="post">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                    <button type="submit" class="botton btn-remove">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="total-row">
                        <td colspan="4">Total:</td>
                        <td><?php echo number_format($total, 0); ?> MMK</td>
                        <td></td>
                    </tr>
                </table>
                <div class="actions">
                    <a href="Servicepage.php" class="btn-continue">Continue Shopping</a>
                    <a href="Checkout.php" class="btn-checkout">Proceed to Checkout</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
<?php ob_end_flush(); ?>