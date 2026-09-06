<?php
session_start();

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit();
}

// Database connection
include 'Connect.php';

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("Fail to connect: " . $e->getMessage());
}

// Fetch user details
$stmt = $pdo->prepare("SELECT username, email, phone, profile_picture FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

// Fetch user appointments
$stmt = $pdo->prepare("
    SELECT a.appointment_id, s.service_name, a.appointment_date, a.status 
    FROM appointments a
    JOIN services s ON a.service_id = s.service_id
    WHERE a.user_id = ?
    ORDER BY a.appointment_date DESC
");
$stmt->execute([$_SESSION['user_id']]);
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch user wishlist
$stmt = $pdo->prepare("
    SELECT w.wishlist_id, w.added_at, s.service_id, s.service_name, 
           s.service_category, s.service_description, s.service_price, s.duration
    FROM wishlist w
    JOIN services s ON w.service_id = s.service_id
    WHERE w.user_id = ?
    ORDER BY w.added_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$wishlist_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle profile picture upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
    $profile_picture = file_get_contents($_FILES['profile_picture']['tmp_name']);
    $stmt = $pdo->prepare("UPDATE users SET profile_picture = ? WHERE user_id = ?");
    $stmt->execute([$profile_picture, $_SESSION['user_id']]);
    header("Location: Profile.php"); // Refresh the page
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .back-btn {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .back-btn:hover {
            transform: translateX(-4px);
        }
        .back-btn::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.4s ease, height 0.4s ease;
        }
        .back-btn:hover::after {
            width: 200px;
            height: 200px;
        }

        /* Wishlist Styles */
        .wishlist-item {
            background: #ffffff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid rgba(116, 198, 157, 0.2);
            margin-bottom: 25px;
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

        /* Button Group Styles (Matched with ServicePage.php) */
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
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <!-- Back to Home Button -->
        <div class="mb-6">
            <a href="Index.php" class="back-btn inline-flex items-center bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                <i class="fas fa-arrow-left mr-2"></i>
                <span>Back to Home</span>
            </a>
        </div>

        <h1 class="text-2xl font-bold mb-4">Profile</h1>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <!-- Profile Picture Section -->
            <div class="flex items-center space-x-4 mb-6">
                <?php if ($user['profile_picture']): ?>
                    <img src="data:image/jpeg;base64,<?= base64_encode($user['profile_picture']) ?>" alt="Profile Picture" class="w-24 h-24 rounded-full">
                <?php else: ?>
                    <i class="fas fa-user-circle fa-5x text-gray-400"></i>
                <?php endif; ?>
                <form method="POST" enctype="multipart/form-data" class="flex items-center space-x-2">
                    <input type="file" name="profile_picture" accept="image/*" class="border p-2 rounded hover:border-blue-500 transition-colors">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors">
                        <i class="fas fa-upload mr-2"></i>Upload
                    </button>
                </form>
            </div>

            <!-- User Details -->
            <div class="space-y-2 mb-6">
                <p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                <p><strong>Phone:</strong> <?= htmlspecialchars($user['phone']) ?></p>
            </div>

            <!-- Edit Profile and Logout Buttons -->
            <div class="flex space-x-4">
                <a href="EditProfile.php" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors">
                    <i class="fas fa-edit mr-2"></i>Edit Profile
                </a>
                <a href="Logout.php" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition-colors">
                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                </a>
            </div>
        </div>

        <!-- Appointments Section -->
        <div class="mt-8">
            <h2 class="text-xl font-bold mb-4">Your Appointments</h2>
            <?php if (empty($appointments)): ?>
                <p class="text-gray-600">You have no appointments.</p>
            <?php else: ?>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-2">Service</th>
                                <th class="text-left py-2">Date</th>
                                <th class="text-left py-2">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($appointments as $appointment): ?>
                                <tr class="border-b">
                                    <td class="py-2"><?= htmlspecialchars($appointment['service_name']) ?></td>
                                    <td class="py-2"><?= htmlspecialchars($appointment['appointment_date']) ?></td>
                                    <td class="py-2"><?= htmlspecialchars($appointment['status']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Wishlist Section -->
        <div class="mt-8">
            <h2 class="text-xl font-bold mb-4">Your Wishlist</h2>
            <?php if (empty($wishlist_items)): ?>
                <p class="text-gray-600">Your wishlist is empty. <a href="ServicePage.php" class="text-blue-500 hover:underline">Browse services</a> to add items!</p>
            <?php else: ?>
                <div class="wishlist-grid">
                    <?php foreach ($wishlist_items as $item): ?>
                        <div class="wishlist-item">
                            <h3><?= htmlspecialchars($item['service_name']) ?></h3>
                            <p>Category: <?= htmlspecialchars($item['service_category']) ?></p>
                            <p><?= htmlspecialchars($item['service_description']) ?></p>
                            <p class="price"><?= number_format($item['service_price'], 2) ?> MMK</p>
                            <p class="duration"><?= htmlspecialchars($item['duration']) ?></p>
                            <p class="added">Added: <?= date('F j, Y, g:i a', strtotime($item['added_at'])) ?></p>
                            <div class="button-group">
                                <a href="Add_to_wishlist.php?id=<?= $item['service_id'] ?>" class="remove-btn">
                                    <i class="fas fa-trash"></i>
                                </a>
                                <a href="Add_to_cart.php?id=<?= $item['service_id'] ?>" class="add-to-cart-btn">
                                    <i class="fas fa-shopping-cart"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>