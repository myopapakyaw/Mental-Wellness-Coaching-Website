<?php
session_start();
include("Header.php");
include("Nav.php");
include 'Connect.php'; 

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("Fail to connect: " . $e->getMessage());
}

// Check if service_id is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid service ID.");
}

$service_id = (int) $_GET['id'];

// Fetch service details
$stmt = $pdo->prepare("SELECT * FROM services WHERE service_id = ?");
$stmt->execute([$service_id]);
$service = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$service) {
    die("Service not found.");
}

// Convert BLOB to base64 for image display
$imageData = $service['service_image'];
$base64Image = base64_encode($imageData);
$imageSrc = "data:image/jpeg;base64," . $base64Image;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($service['service_name']); ?> - Details</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        /* Scoped Styles */
        .service-details-page {
            font-family: 'Poppins', sans-serif;
            background: radial-gradient(circle at center, #e8f5e9 0%, #c8e6c9 100%);
            background-image: url('data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"%3E%3Cg fill="%23ffffff" fill-opacity="0.05"%3E%3Ccircle cx="50" cy="50" r="50"/%3E%3C/g%3E%3C/svg%3E');
            background-size: 100px 100px;
            margin: 0;
            padding: 0;
            color: #2c3e50;
            line-height: 1.6;
        }

        .service-details-page .container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 20px;
        }

        .service-details-page .service-detail {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            padding: 25px;
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            gap: 25px;
            position: relative;
            overflow: hidden;
        }

        .service-details-page .service-detail::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #6ab04c, #badc58);
            transition: height 0.3s ease;
        }

        .service-details-page .service-detail:hover::before {
            height: 8px;
        }

        .service-details-page .service-image img {
            width: 100%;
            height: 280px;
            object-fit: cover;
            margin-top: 50px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .service-details-page .service-image:hover img {
            transform: scale(1.04);
        }

        .service-details-page .service-info {
            padding: 10px 0;
        }

        .service-details-page .service-info h1 {
            font-size: 2rem;
            color: #2d6a4f;
            margin: 0 0 10px;
            font-weight: 600;
            line-height: 1.2;
        }

        .service-details-page .service-info .category {
            font-size: 0.85rem;
            color: #fff;
            background: #6ab04c;
            padding: 4px 12px;
            border-radius: 20px;
            display: inline-block;
            margin-bottom: 12px;
            font-weight: 500;
        }

        .service-details-page .service-info .price {
            font-size: 1.5rem;
            color: #2d6a4f;
            font-weight: 600;
            margin-bottom: 8px; /* Fixed typo from ８px */
        }

        .service-details-page .service-info .duration {
            font-size: 1rem;
            color: #7f8c8d;
            font-weight: 500;
            margin-bottom: 15px;
        }

        .service-details-page .service-info .duration i {
            margin-right: 6px;
            color: #6ab04c;
        }

        .service-details-page .service-info .description {
            font-size: 0.95rem;
            color: #34495e;
            margin-bottom: 20px;
            line-height: 1.7;
        }

        .service-details-page .action-buttons {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: nowrap; 
        }

        .service-details-page .action-buttons a,
        .service-details-page .action-buttons form button {
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
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            padding: 0;
            margin: 0; 
        }

        .service-details-page .action-buttons form {
            display: inline-flex; 
            margin: 0; 
            padding: 0; 
        }

        .service-details-page .view-details {
            background: #6ab04c;
        }

        .service-details-page .view-details:hover {
            background: #5d9b42;
            transform: scale(1.08);
        }

        .service-details-page .add-to-cart {
            background: #3498db;
        }

        .service-details-page .add-to-cart:hover {
            background: #2980b9;
            transform: scale(1.08);
        }

        .service-details-page .add-to-wishlist {
            background: rgb(11, 164, 32);
        }

        .service-details-page .add-to-wishlist:hover {
            background: #c0392b;
            transform: scale(1.08);
        }

        .service-details-page .back-button {
            display: inline-flex;
            align-items: center;
            margin-top: 20px;
            padding: 10px 20px;
            background: linear-gradient(135deg, #badc58, #6ab04c);
            color: #fff;
            text-decoration: none;
            border-radius: 25px;
            font-size: 0.95rem;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }

        .service-details-page .back-button:hover {
            background: linear-gradient(135deg, #6ab04c, #5d9b42);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        }

        .service-details-page .back-button i {
            margin-right: 8px;
        }

        @media (max-width: 768px) {
            .service-details-page .service-detail {
                grid-template-columns: 1fr;
                padding: 20px;
            }

            .service-details-page .service-image img {
                height: 240px;
            }

            .service-details-page .service-info h1 {
                font-size: 1.8rem;
            }
        }

        @media (max-width: 480px) {
            .service-details-page .service-info h1 {
                font-size: 1.6rem;
            }

            .service-details-page .service-info .price {
                font-size: 1.3rem;
            }

            .service-details-page .service-image img {
                height: 200px;
            }

            .service-details-page .action-buttons a,
            .service-details-page .action-buttons form button {
                width: 40px;
                height: 40px;
                font-size: 1rem;
            }

            .service-details-page .back-button {
                padding: 8px 16px;
                font-size: 0.85rem;
            }
        }
    </style>
</head>

<body>
    <div class="service-details-page">
        <div class="container">
            <div class="service-detail">
                <div class="service-image">
                    <img src="<?php echo $imageSrc; ?>" alt="<?php echo htmlspecialchars($service['service_name']); ?>">
                </div>
                <div class="service-info">
                    <h1><?php echo htmlspecialchars($service['service_name']); ?></h1>
                    <span class="category"><?php echo htmlspecialchars($service['service_category']); ?></span>
                    <p class="price"><?php echo number_format($service['service_price'], 0, '.', ','); ?> MMK</p>
                    <p class="duration"><i class="fas fa-clock"></i> <?php echo htmlspecialchars($service['duration']); ?></p>
                    <p class="description"><?php echo nl2br(htmlspecialchars($service['service_description'])); ?></p>
                    <div class="action-buttons">
                        <a href="#" class="view-details" title="View More">
                            <i class="fas fa-eye"></i>
                        </a>
                        <form action="Add_to_cart.php" method="post">
                            <input type="hidden" name="service_id" value="<?php echo $service['service_id']; ?>">
                            <button type="submit" class="add-to-cart" title="Add to Cart">
                                <i class="fas fa-cart-plus"></i>
                            </button>
                        </form>
                        <a href="Add_to_wishlist.php?id=<?php echo $service['service_id']; ?>" class="add-to-wishlist"
                            title="Add to Wishlist">
                            <i class="fas fa-seedling"></i>
                        </a>
                    </div>
                    <a href="ServicePage.php" class="back-button"><i class="fas fa-arrow-left"></i> Back to Services</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>