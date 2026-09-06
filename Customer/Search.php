<?php
session_start();
include("Header.php");
include("Nav.php");
include('Loader.php');

// Database connection
include 'Connect.php';

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("Failed to connect to the database: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        /* Scoped styles for search page */
        .search-page {
            font-family: 'Poppins', sans-serif;
            background: radial-gradient(circle at center, #e8f5e9 0%, #c8e6c9 100%);
            background-image: url('data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"%3E%3Cg fill="%23ffffff" fill-opacity="0.05"%3E%3Ccircle cx="50" cy="50" r="50"/%3E%3C/g%3E%3C/svg%3E');
            background-size: 100px 100px;
            margin: 0;
            padding: 0;
            color: #2c3e50;
            line-height: 1.6;
        }

        .search-page .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
        }

        h2 {
            text-align: center;
            color: #2d6a4f;
            margin-bottom: 30px;
            font-size: 2rem;
            font-weight: 600;
        }

        .service-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            padding: 20px;
        }

        .service-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            padding: 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .service-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #6ab04c, #badc58);
            transition: height 0.3s ease;
        }

        .service-card:hover::before {
            height: 8px;
        }

        .service-card:hover {
            transform: translateY(-5px);
        }

        .service-card img {
            max-width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 15px;
            transition: transform 0.3s ease;
        }

        .service-card:hover img {
            transform: scale(1.05);
        }

        .service-card h3 {
            margin-bottom: 10px;
            font-size: 1.5rem;
            color: #2d6a4f;
            font-weight: 600;
        }

        .service-card p {
            margin-bottom: 10px;
            color: #34495e;
            font-size: 0.95rem;
            line-height: 1.7;
        }

        .service-card .price {
            font-weight: 600;
            color: #2d6a4f;
            font-size: 1.2rem;
            margin-bottom: 10px;
        }

        .service-card .duration {
            color: #7f8c8d;
            font-weight: 500;
            margin-bottom: 15px;
        }

        .service-card .duration i {
            margin-right: 6px;
            color: #6ab04c;
        }

        .button-group {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-top: 20px;
            flex-wrap: nowrap;
        }

        .button-group a,
        .button-group form button {
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
        }

        .button-group form {
            display: inline-flex;
            margin: 0;
            padding: 0;
        }

        .view-details {
            background: #6ab04c;
        }

        .view-details:hover {
            background: #5d9b42;
            transform: scale(1.08);
        }

        .add-to-cart {
            background: #3498db;
        }

        .add-to-cart:hover {
            background: #2980b9;
            transform: scale(1.08);
        }

        .add-to-wishlist {
            background: rgb(11, 164, 32);
        }

        .add-to-wishlist:hover {
            background: #c0392b;
            transform: scale(1.08);
        }

        .no-results {
            text-align: center;
            font-size: 1.2rem;
            color: #34495e;
            margin-top: 20px;
        }

        .back-to-top {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: linear-gradient(135deg, #badc58, #6ab04c);
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .back-to-top:hover {
            background: linear-gradient(135deg, #6ab04c, #5d9b42);
            transform: scale(1.1);
        }

        .fallback-image {
            width: 100%;
            height: 200px;
            background-color: #ddd;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: #666;
        }

        @media (max-width: 768px) {
            .service-grid {
                grid-template-columns: 1fr;
            }

            .service-card img {
                height: 180px;
            }
        }

        @media (max-width: 480px) {
            h2 {
                font-size: 1.6rem;
            }

            .service-card img {
                height: 160px;
            }

            .button-group a,
            .button-group form button {
                width: 40px;
                height: 40px;
                font-size: 1rem;
            }
        }
    </style>
</head>

<body>
    <div class="search-page">
        <div class="container">
            <?php
            if (isset($_GET['query']) && !empty($_GET['query'])) {
                $search_query = "%" . $_GET['query'] . "%";
                $sql = "SELECT * FROM services 
                        WHERE service_name LIKE :search_query COLLATE utf8mb4_general_ci
                        OR service_description LIKE :search_query COLLATE utf8mb4_general_ci";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['search_query' => $search_query]);
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($results) {
                    echo "<h2>Search Results for \"" . htmlspecialchars($_GET['query']) . "\"</h2>";
                    echo "<div class='service-grid'>";
                    foreach ($results as $row) {
                        $image_data = $row['service_image'];
                        $image_src = 'data:image/jpeg;base64,' . base64_encode($image_data);

                        echo "<div class='service-card'>
                                <img src='" . $image_src . "' alt='" . htmlspecialchars($row['service_name']) . "' onerror='this.src=\"img/fallback.jpg\"; this.onerror=null;'>
                                <h3>" . htmlspecialchars($row['service_name']) . "</h3>
                                <p>" . htmlspecialchars($row['service_description']) . "</p>
                                <p class='price'>" . number_format($row['service_price'], 0, '.', ',') . " MMK</p>
                                <p class='duration'><i class='fas fa-clock'></i> " . htmlspecialchars($row['duration']) . "</p>
                                <div class='button-group'>
                                    <a href='Service_details.php?id=" . $row['service_id'] . "' class='view-details' title='View More'>
                                        <i class='fas fa-eye'></i>
                                    </a>
                                    <form action='Add_to_cart.php' method='post'>
                                        <input type='hidden' name='service_id' value='" . $row['service_id'] . "'>
                                        <button type='submit' class='add-to-cart' title='Add to Cart'>
                                            <i class='fas fa-cart-plus'></i>
                                        </button>
                                    </form>
                                    <a href='Add_to_wishlist.php?id=" . $row['service_id'] . "' class='add-to-wishlist' title='Add to Wishlist'>
                                        <i class='fas fa-seedling'></i>
                                    </a>
                                </div>
                            </div>";
                    }
                    echo "</div>";
                } else {
                    echo "<div class='no-results'>No services found matching your search.</div>";
                }
            } else {
                $sql = "SELECT s.service_name, s.service_category, s.service_description, s.duration, s.service_image, s.service_price, s.service_id
                        FROM services s
                        INNER JOIN (
                            SELECT MIN(service_id) AS service_id
                            FROM services
                            GROUP BY service_category
                        ) cat ON s.service_id = cat.service_id
                        LIMIT 3";
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($results) {
                    echo "<h2>Featured Services</h2>";
                    echo "<div class='service-grid'>";
                    foreach ($results as $row) {
                        $image_data = $row['service_image'];
                        $image_src = 'data:image/jpeg;base64,' . base64_encode($image_data);

                        echo "<div class='service-card'>
                                <img src='" . $image_src . "' alt='" . htmlspecialchars($row['service_name']) . "' onerror='this.src=\"img/fallback.jpg\"; this.onerror=null;'>
                                <h3>" . htmlspecialchars($row['service_name']) . "</h3>
                                <p>" . htmlspecialchars($row['service_description']) . "</p>
                                <p class='price'>" . number_format($row['service_price'], 0, '.', ',') . " MMK</p>
                                <p class='duration'><i class='fas fa-clock'></i> " . htmlspecialchars($row['duration']) . "</p>
                                <div class='button-group'>
                                    <a href='Service_details.php?id=" . $row['service_id'] . "' class='view-details' title='View More'>
                                        <i class='fas fa-eye'></i>
                                    </a>
                                    <form action='Add_to_cart.php' method='post'>
                                        <input type='hidden' name='service_id' value='" . $row['service_id'] . "'>
                                        <button type='submit' class='add-to-cart' title='Add to Cart'>
                                            <i class='fas fa-cart-plus'></i>
                                        </button>
                                    </form>
                                    <a href='Add_to_wishlist.php?id=" . $row['service_id'] . "' class='add-to-wishlist' title='Add to Wishlist'>
                                        <i class='fas fa-seedling'></i>
                                    </a>
                                </div>
                            </div>";
                    }
                    echo "</div>";
                } else {
                    echo "<div class='no-results'>No featured services available.</div>";
                }
            }
            ?>
        </div>
    </div>
    <button class="back-to-top" onclick="window.scrollTo({top: 0, behavior: 'smooth'});"><i
            class="fas fa-arrow-up"></i></button>

    <script>
        window.addEventListener('load', function () {
            setTimeout(function () {
                const loader = document.getElementById('loader');
                if (loader) {
                    loader.style.display = 'none';
                }
            }, 1000);
        });
    </script>

    <?php include("Footer.php"); ?>
</body>

</html>