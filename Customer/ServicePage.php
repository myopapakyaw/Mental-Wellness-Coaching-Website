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

// Pagination settings
$itemsPerPage = 6;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $itemsPerPage;

$totalStmt = $pdo->query("SELECT COUNT(*) FROM services");
$totalServices = $totalStmt->fetchColumn();
$totalPages = ceil($totalServices / $itemsPerPage);

$stmt = $pdo->prepare("SELECT * FROM services LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);

$categories = array_unique(array_column($services, 'service_category'));
$durations = array_unique(array_column($services, 'duration'));
$prices = array_column($services, 'service_price');
$minPrice = min($prices);
$maxPrice = max($prices);

$priceBrackets = [
    ['label' => 'Under 10,000 MMK', 'min' => 0, 'max' => 10000],
    ['label' => '10,000 - 20,000 MMK', 'min' => 10000, 'max' => 20000],
    ['label' => '20,000 - 50,000 MMK', 'min' => 20000, 'max' => 50000],
    ['label' => '50,000 - 100,000 MMK', 'min' => 50000, 'max' => 100000],
    ['label' => 'Over 100,000 MMK', 'min' => 100000, 'max' => PHP_FLOAT_MAX],
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mindful Services | Mental Wellness Solutions</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --primary: #0c821aff;
            --primary-light: #78d3a7ff;
            --secondary: #07b329ff;
            --accent: #d9dcd6;
            --light: #f8f9fa;
            --dark: #165b4cff;
            --success: #66bf69ff;
            --warning: #ff9800;
            --danger: #f44336;
            --text: #333333;
            --text-light: #6c757d;
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        /* * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fa;
            color: var(--text);
            line-height: 1.6;
        } */

        .services-hero {
            background: linear-gradient(135deg, rgba(27, 136, 78, 0.9), rgba(47, 102, 144, 0.9)),
                url('https://images.unsplash.com/photo-1593814681464-eef5af2b0628?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 80px 20px;
            text-align: center;
            margin-bottom: 40px;
        }

        .services-hero h1 {
            font-family: serif;
            font-size: 3.5rem;
            font-weight: 600;
            margin-bottom: 20px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .services-hero p {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto 30px;
            opacity: 0.9;
        }

        /* .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        } */

        /* Filter Section */
        .filter-section {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: var(--shadow);
            margin-bottom: 40px;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            align-items: flex-end;
        }

        .filter-group {
            flex: 1;
            min-width: 220px;
        }

        .filter-group label {
            display: block;
            font-weight: 500;
            color: var(--secondary);
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .filter-group select,
        .filter-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 0.95rem;
            color: var(--text);
            background-color: white;
            transition: var(--transition);
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 15px;
        }

        .filter-group select:focus,
        .filter-group input:focus {
            outline: none;
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(58, 124, 165, 0.2);
        }

        .filter-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--secondary);
            transform: translateY(-2px);
        }

        .btn-outline {
            background-color: transparent;
            border: 1px solid var(--primary);
            color: var(--primary);
        }

        .btn-outline:hover {
            background-color: rgba(58, 124, 165, 0.1);
        }

        /* Service Grid */
        .service-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
        }

        .service-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
            position: relative;
        }

        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.12);
        }

        .service-card-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background-color: var(--primary);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            z-index: 1;
        }

        .service-card-img {
            width: 100%;
            height: 220px;
            object-fit: cover;
        }

        .service-card-body {
            padding: 25px;
        }

        .service-card-category {
            display: inline-block;
            color: var(--primary);
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .service-card-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.6rem;
            font-weight: 600;
            margin-bottom: 12px;
            color: var(--dark);
        }

        .service-card-description {
            color: var(--text-light);
            margin-bottom: 20px;
            font-size: 0.95rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .service-card-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-top: 15px;
            border-top: 1px solid #f0f0f0;
        }

        .service-card-price {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--primary);
        }

        .service-card-price small {
            font-size: 0.9rem;
            font-weight: 400;
            color: var(--text-light);
        }

        .service-card-duration {
            display: flex;
            align-items: center;
            gap: 5px;
            color: var(--text-light);
            font-size: 0.9rem;
        }

        .service-card-actions {
            display: flex;
            gap: 10px;
        }

        .action-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            cursor: pointer;
            transition: var(--transition);
            border: none;
            font-size: 1rem;
        }

        .action-btn:hover {
            transform: scale(1.1);
        }

        .action-btn-view {
            background-color: var(--primary);
        }

        .action-btn-view:hover {
            background-color: var(--secondary);
        }

        .action-btn-cart {
            background-color: var(--success);
        }

        .action-btn-cart:hover {
            background-color: #3d8b40;
        }

        .action-btn-wishlist {
            background-color: var(--warning);
        }

        .action-btn-wishlist:hover {
            background-color: #e68a00;
        }

        .action-btn-wishlist.active {
            background-color: var(--danger);
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin: 40px 0;
        }

        .pagination a,
        .pagination span {
            width: 25px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            text-decoration: none;
            color: var(--text);
            font-weight: 500;
            transition: var(--transition);
        }

        .pagination a {
            background-color: white;
            border: 1px solid #e0e0e0;
        }

        .pagination a:hover {
            background-color: var(--primary-light);
            color: white;
            border-color: var(--primary-light);
        }

        .pagination .current {
            background-color: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .pagination .disabled {
            opacity: 0.5;
            pointer-events: none;
        }

        /* No Results */
        .no-results {
            text-align: center;
            padding: 30px 20px;
            grid-column: 1 / -1;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 12px;
            box-shadow: var(--shadow);
            margin: 20px 0;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        .no-results-icon {
            font-size: 2rem;
            color: var(--text-light);
            margin-bottom: 15px;
        }

        .no-results h3 {
            font-size: 1.25rem;
            color: var(--text);
            margin-bottom: 12px;
            font-weight: 500;
        }

        .no-results p {
            color: var(--text-light);
            font-size: 0.95rem;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .no-results .btn {
            padding: 10px 20px;
            font-size: 0.9rem;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .service-grid {
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .services-hero h1 {
                font-size: 2.5rem;
            }

            .filter-section {
                flex-direction: column;
            }

            .filter-group {
                min-width: 100%;
            }

            .filter-actions {
                width: 100%;
                justify-content: flex-end;
            }
        }

        @media (max-width: 576px) {
            .services-hero {
                padding: 60px 20px;
            }

            .services-hero h1 {
                font-size: 2rem;
            }

            .service-grid {
                grid-template-columns: 1fr;
            }

            .pagination a,
            .pagination span {
                width: 35px;
                height: 35px;
                font-size: 0.9rem;
            }
        }

        /* Floating Action Button */
        .fab {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            cursor: pointer;
            transition: var(--transition);
            z-index: 100;
            border: none;
        }

        .fab:hover {
            background-color: var(--secondary);
            transform: translateY(-3px) scale(1.05);
        }

        /* Loading Animation */
        .loading-spinner {
            display: none;
            text-align: center;
            padding: 30px;
            grid-column: 1 / -1;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid rgba(58, 124, 165, 0.2);
            border-radius: 50%;
            border-top-color: var(--primary);
            animation: spin 1s ease-in-out infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <!-- Hero Section -->
    <section class="services-hero">
        <h1>Find Your Path to Wellness</h1>
        <p>Discover our carefully curated mental health services designed to support your journey to emotional
            well-being</p>
    </section>

    <div class="container">
        <!-- Filter Section -->
        <div class="filter-section">
            <div class="filter-group">
                <label for="category"><i class="fas fa-filter"></i> Category</label>
                <select id="category">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo htmlspecialchars($category); ?>">
                            <?php echo htmlspecialchars($category); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-group">
                <label for="price"><i class="fas fa-tag"></i> Price Range</label>
                <select id="price">
                    <option value="">All Prices</option>
                    <?php foreach ($priceBrackets as $bracket): ?>
                        <option value="<?php echo htmlspecialchars($bracket['min'] . '-' . $bracket['max']); ?>">
                            <?php echo htmlspecialchars($bracket['label']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-group">
                <label for="duration"><i class="far fa-clock"></i> Duration</label>
                <select id="duration">
                    <option value="">All Durations</option>
                    <?php foreach ($durations as $duration): ?>
                        <option value="<?php echo htmlspecialchars($duration); ?>">
                            <?php echo htmlspecialchars($duration); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-actions">
                <button class="btn btn-outline" id="reset-filters">
                    <i class="fas fa-sync-alt"></i> Reset
                </button>
            </div>
        </div>

        <!-- Service Grid -->
        <div class="service-grid" id="service-grid">
            <?php foreach ($services as $service): ?>
                <div class="service-card" data-category="<?php echo htmlspecialchars($service['service_category']); ?>"
                    data-price="<?php echo $service['service_price']; ?>"
                    data-duration="<?php echo htmlspecialchars($service['duration']); ?>">

                    <?php if ($service['service_price'] > 50000): ?>
                        <span class="service-card-badge">Premium</span>
                    <?php endif; ?>

                    <?php
                    $imageData = $service['service_image'];
                    $base64Image = base64_encode($imageData);
                    $imageSrc = "data:image/jpeg;base64," . $base64Image;
                    ?>
                    <img src="<?php echo $imageSrc; ?>" alt="<?php echo htmlspecialchars($service['service_name']); ?>"
                        class="service-card-img">

                    <div class="service-card-body">
                        <span
                            class="service-card-category"><?php echo htmlspecialchars($service['service_category']); ?></span>
                        <h3 class="service-card-title"><?php echo htmlspecialchars($service['service_name']); ?></h3>
                        <p class="service-card-description">
                            <?php echo nl2br(htmlspecialchars($service['service_description'])); ?></p>

                        <div class="service-card-meta">
                            <div class="service-card-price">
                                <?php echo number_format($service['service_price'], 0, '.', ','); ?> <small>MMK</small>
                            </div>
                            <div class="service-card-duration">
                                <i class="far fa-clock"></i> <?php echo htmlspecialchars($service['duration']); ?>
                            </div>
                        </div>

                        <div class="service-card-actions">
                            <a href="Service_details.php?id=<?php echo $service['service_id']; ?>"
                                class="action-btn action-btn-view" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>

                            <form action="Add_to_cart.php" method="post" style="display: inline;">
                                <input type="hidden" name="service_id" value="<?php echo $service['service_id']; ?>">
                                <button type="submit" class="action-btn action-btn-cart" title="Add to Cart">
                                    <i class="fas fa-cart-plus"></i>
                                </button>
                            </form>

                            <?php
                            $in_wishlist = false;
                            if (isset($_SESSION['user_id'])) {
                                $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM wishlist WHERE user_id = ? AND service_id = ?");
                                $check_stmt->execute([$_SESSION['user_id'], $service['service_id']]);
                                $in_wishlist = $check_stmt->fetchColumn() > 0;
                            }
                            ?>
                            <a href="Add_to_wishlist.php?id=<?php echo $service['service_id']; ?>"
                                class="action-btn action-btn-wishlist <?php echo $in_wishlist ? 'active' : ''; ?>"
                                title="<?php echo $in_wishlist ? 'Remove from Wishlist' : 'Add to Wishlist'; ?>">
                                <i class="fas fa-heart"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>" class="prev"><i class="fas fa-chevron-left"></i></a>
            <?php else: ?>
                <span class="disabled"><i class="fas fa-chevron-left"></i></span>
            <?php endif; ?>

            <?php
            $maxVisible = 5;
            $halfRange = floor($maxVisible / 2);
            $start = max(1, $page - $halfRange);
            $end = min($totalPages, $start + $maxVisible - 1);

            if ($end - $start + 1 < $maxVisible) {
                $start = max(1, $end - $maxVisible + 1);
            }

            if ($start > 1): ?>
                <a href="?page=1">1</a>
                <?php if ($start > 2): ?>
                    <span>...</span>
                <?php endif; ?>
            <?php endif; ?>

            <?php for ($i = $start; $i <= $end; $i++): ?>
                <a href="?page=<?php echo $i; ?>" class="<?php echo $i === $page ? 'current' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <?php if ($end < $totalPages): ?>
                <?php if ($end < $totalPages - 1): ?>
                    <span>...</span>
                <?php endif; ?>
                <a href="?page=<?php echo $totalPages; ?>"><?php echo $totalPages; ?></a>
            <?php endif; ?>

            <?php if ($page < $totalPages): ?>
                <a href="?page=<?php echo $page + 1; ?>" class="next"><i class="fas fa-chevron-right"></i></a>
            <?php else: ?>
                <span class="disabled"><i class="fas fa-chevron-right"></i></span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Floating Action Button -->
    <!-- <button class="fab" id="back-to-top" title="Back to top">
        <i class="fas fa-arrow-up"></i>
    </button> -->

    <script>
        // DOM Elements
        const categoryFilter = document.getElementById('category');
        const priceFilter = document.getElementById('price');
        const durationFilter = document.getElementById('duration');
        const resetBtn = document.getElementById('reset-filters');
        const serviceGrid = document.getElementById('service-grid');
        // const backToTopBtn = document.getElementById('back-to-top');

        // Filter Services
        function filterServices() {
            const selectedCategory = categoryFilter.value;
            const selectedPrice = priceFilter.value;
            const selectedDuration = durationFilter.value;
            let visibleCount = 0;

            document.querySelectorAll('.service-card').forEach(card => {
                const category = card.dataset.category;
                const price = parseFloat(card.dataset.price);
                const duration = card.dataset.duration;

                const categoryMatch = !selectedCategory || category === selectedCategory;
                const durationMatch = !selectedDuration || duration === selectedDuration;
                let priceMatch = true;

                if (selectedPrice) {
                    const [minPrice, maxPrice] = selectedPrice.split('-').map(Number);
                    priceMatch = price >= minPrice && price <= maxPrice;
                }

                if (categoryMatch && priceMatch && durationMatch) {
                    card.style.display = 'block';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Show no results message if needed
            const noResults = document.getElementById('no-results');
            if (visibleCount === 0) {
                if (!noResults) {
                    const noResultsHTML = `
                        <div class="no-results" id="no-results">
                            <div class="no-results-icon">
                                <i class="far fa-frown"></i>
                            </div>
                            <h3>No services found</h3>
                            <p>Try adjusting your filters to find what you're looking for.</p>
                            <button class="btn btn-primary" id="reset-filters-inline">Reset Filters</button>
                        </div>
                    `;
                    serviceGrid.insertAdjacentHTML('beforeend', noResultsHTML);
                    document.getElementById('reset-filters-inline').addEventListener('click', resetFilters);
                }
            } else if (noResults) {
                noResults.remove();
            }
        }

        // Reset Filters
        function resetFilters() {
            categoryFilter.value = '';
            priceFilter.value = '';
            durationFilter.value = '';
            filterServices();
        }

        // Event Listeners
        categoryFilter.addEventListener('change', filterServices);
        priceFilter.addEventListener('change', filterServices);
        durationFilter.addEventListener('change', filterServices);
        resetBtn.addEventListener('click', resetFilters);

        // Back to Top Button
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                backToTopBtn.style.display = 'flex';
            } else {
                backToTopBtn.style.display = 'none';
            }
        });

        backToTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            backToTopBtn.style.display = 'none';
        });

         document.addEventListener('DOMContentLoaded', function() {
        const footer = document.querySelector('footer.site-footer');
        // const backToTopBtn = document.getElementById('back-to-top');
        if (footer && backToTopBtn) {
            footer.appendChild(backToTopBtn);
        }
    });
    </script>

    <?php include("Footer.php"); ?>
</body>

</html>