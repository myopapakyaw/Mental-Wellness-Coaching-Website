<?php
session_start();
include 'Connect.php';
include("Header.php");
include("Nav.php");

try {
    if (!isset($_SESSION['user_id'])) {
        header("Location: Login.php?message=Please login to view your wishlist");
        exit();
    }

    $user_id = $_SESSION['user_id'];

    $query = "
        SELECT w.wishlist_id, w.added_at, s.service_id, s.service_name, 
               s.service_category, s.service_description, s.service_price, s.duration
        FROM wishlist w
        JOIN services s ON w.service_id = s.service_id
        WHERE w.user_id = ?
        ORDER BY w.added_at DESC
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute([$user_id]);
    $wishlist_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $message = '';
    if (isset($_GET['success'])) {
        $message = htmlspecialchars($_GET['success']);
    } elseif (isset($_GET['error'])) {
        $message = htmlspecialchars($_GET['error']);
    }

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wishlist</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        /* Global Styles */
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f0f4f8, #d9e2ec);
            margin: 0;
            padding: 0;
            line-height: 1.6;
            color: #333;
        }

        .wishlist-container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 30px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .wishlist-container h1 {
            text-align: center;
            color: #2d6a4f;
            font-size: 2.5rem;
            font-weight: 600;
            margin-bottom: 30px;
            position: relative;
        }

        wishlist-container h1::after {
            content: '';
            width: 60px;
            height: 4px;
            background: #74c69d;
            display: block;
            margin: 10px auto;
            border-radius: 2px;
        }

        /* Message Styles */
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            text-align: center;
            font-size: 1rem;
            font-weight: 400;
            animation: fadeIn 0.5s ease-in;
        }

        .success {
            background-color: #dff0d8;
            color: #3c763d;
            border: 1px solid #d6e9c6;
        }

        .error {
            background-color: #f2dede;
            color: #a94442;
            border: 1px solid #ebccd1;
        }

        /* Wishlist Item Styles */
        .wishlist-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 25px;
        }

        .wishlist-item {
            background: #ffffff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid rgba(116, 198, 157, 0.2);
        }

        .wishlist-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .wishlist-item h3 {
            color: #1a3c34;
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0 0 10px 0;
        }

        .wishlist-item p {
            margin: 5px 0;
            font-size: 0.95rem;
            color: #6b7280;
        }

        .wishlist-item .price {
            font-weight: 700;
            color: #2d6a4f;
            font-size: 1.2rem;
            margin: 10px 0;
        }

        .wishlist-item .duration {
            color: #40916c;
            font-weight: 500;
            font-size: 1rem;
        }

        .wishlist-item .added {
            font-style: italic;
            color: #95a5a6;
            font-size: 0.9rem;
        }

        /* Button Styles (Matched with ServicePage.php) */
        .button-group {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-top: 20px;
        }

        .button-group a {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            border: none;
            font-size: 1.2rem;
            color: #fff;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .remove-btn {
            background: linear-gradient(135deg, #ef4444, #dc2626);
        }

        .remove-btn:hover {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            transform: scale(1.1);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }

        .add-to-cart-btn {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
        }

        .add-to-cart-btn:hover {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            transform: scale(1.1);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }

        /* Empty Message */
        .empty-message {
            text-align: center;
            padding: 40px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            color: #6b7280;
            font-size: 1.2rem;
        }

        .empty-message a {
            color: #74c69d;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .empty-message a:hover {
            color: #40916c;
        }

        /* Continue Browsing */
        .continue-browsing {
            text-align: center;
            margin-top: 30px;
        }

        .continue-browsing a {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #74c69d, #40916c);
            color: #fff;
            border-radius: 25px;
            text-decoration: none;
            font-size: 1rem;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .continue-browsing a:hover {
            background: linear-gradient(135deg, #40916c, #2d6a4f);
            transform: scale(1.05);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .wishlist-item {
            animation: fadeIn 0.5s ease-in;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .wishlist-container {
                margin: 20px;
                padding: 20px;
            }

            .button-group {
                gap: 10px;
            }
        }

        .button-group form {
            display: inline;
            margin: 0;
            padding: 0;
        }

        .button-group button {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            border: none;
            font-size: 1.2rem;
            color: #fff;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            background: linear-gradient(135deg, #3b82f6, #2563eb);
        }

        .button-group button:hover {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            transform: scale(1.1);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>

<body>
    <div class="wishlist-container">
        <h1>My Wishlist</h1>

        <?php if (!empty($message)): ?>
            <div class="message <?php echo isset($_GET['success']) ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if (empty($wishlist_items)): ?>
            <div class="empty-message">
                Your wishlist is empty. <a href="ServicePage.php">Browse services</a> to add items!
            </div>
        <?php else: ?>
            <div class="wishlist-grid">
                <?php foreach ($wishlist_items as $item): ?>
                    <div class="wishlist-item">
                        <h3><?php echo htmlspecialchars($item['service_name']); ?></h3>
                        <p>Category: <?php echo htmlspecialchars($item['service_category']); ?></p>
                        <p><?php echo htmlspecialchars($item['service_description']); ?></p>
                        <p class="price"><?php echo number_format($item['service_price'], 2); ?> MMK</p>
                        <p class="duration"><?php echo htmlspecialchars($item['duration']); ?></p>
                        <p class="added">Added: <?php echo date('F j, Y, g:i a', strtotime($item['added_at'])); ?></p>
                        <div class="button-group">
                            <a href="Add_to_wishlist.php?id=<?php echo $item['service_id']; ?>" class="remove-btn">
                                <i class="fas fa-trash"></i>
                            </a>
                            <form action="Add_to_cart.php" method="post" style="display: inline;">
                                <input type="hidden" name="service_id" value="<?php echo $item['service_id']; ?>">
                                <button type="submit" class="add-to-cart-btn">
                                    <i class="fas fa-shopping-cart"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="continue-browsing">
            <a href="ServicePage.php">Continue Browsing</a>
        </div>
    </div>
</body>

</html>