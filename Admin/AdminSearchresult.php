<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'Connect.php';


$search = isset($_GET['search_query']) ? trim($_GET['search_query']) : '';
$min_price = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? (int)$_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? (int)$_GET['max_price'] : PHP_INT_MAX;
$duration = isset($_GET['duration']) ? trim($_GET['duration']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : '';

// Prepare SQL query with filters
$query = "SELECT * FROM services WHERE (service_name LIKE ? OR service_category LIKE ? OR service_description LIKE ?) AND service_price BETWEEN ? AND ?";
$params = ["%$search%", "%$search%", "%$search%", $min_price, $max_price];

if (!empty($duration)) {
    $query .= " AND duration = ?";
    $params[] = $duration;
}

// Add sorting
switch ($sort) {
    case 'price_asc':
        $query .= " ORDER BY service_price ASC";
        break;
    case 'price_desc':
        $query .= " ORDER BY service_price DESC";
        break;
    case 'name_asc':
        $query .= " ORDER BY service_name ASC";
        break;
    case 'popularity':
        $query .= " ORDER BY service_id DESC"; 
        break;
    default:
        $query .= " ORDER BY service_id ASC"; 
}

$stmt_services = $pdo->prepare($query);
$stmt_services->execute($params);
$services = $stmt_services->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - Velora Mindful Healing</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
       
        .service-card {
            background: linear-gradient(135deg, #ffffff, #f9fafb);
            border-radius: 1rem;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.1);
        }
        .service-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-bottom: 2px solid #e5e7eb;
        }
        .search-form {
            background: #ffffff;
            border-radius: 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
        }
        .no-results {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            padding: 1rem;
            border-radius: 0.5rem;
            text-align: center;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased">
    <div class="container mx-auto px-4 py-10 max-w-7xl">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800">Search Services</h2>
            <a href="AdminDashboard.php" class="text-blue-600 hover:underline">Back to Dashboard</a>
        </div>

        <!-- Search Form -->
        <form method="GET" action="" class="search-form mb-8 grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Keyword</label>
                <input type="text" name="search_query" placeholder="e.g., Meditation" value="<?php echo htmlspecialchars($search); ?>" 
                       class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Min Price (MMK)</label>
                <input type="number" name="min_price" placeholder="0" value="<?php echo $min_price ?: ''; ?>" min="0" 
                       class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Max Price (MMK)</label>
                <input type="number" name="max_price" placeholder="Max" value="<?php echo $max_price === PHP_INT_MAX ? '' : $max_price; ?>" min="0" 
                       class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Duration</label>
                <select name="duration" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="" <?php echo empty($duration) ? 'selected' : ''; ?>>Any</option>
                    <option value="20 Minutes" <?php echo $duration === '20 Minutes' ? 'selected' : ''; ?>>20 Minutes</option>
                    <option value="30 Minutes" <?php echo $duration === '30 Minutes' ? 'selected' : ''; ?>>30 Minutes</option>
                    <!-- Add more durations as needed -->
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 text-white p-3 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-search mr-2"></i> Search
                </button>
            </div>
            <div class="md:col-span-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                <select name="sort" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="" <?php echo $sort === '' ? 'selected' : ''; ?>>Default</option>
                    <option value="price_asc" <?php echo $sort === 'price_asc' ? 'selected' : ''; ?>>Price: Low to High</option>
                    <option value="price_desc" <?php echo $sort === 'price_desc' ? 'selected' : ''; ?>>Price: High to Low</option>
                    <option value="name_asc" <?php echo $sort === 'name_asc' ? 'selected' : ''; ?>>Name: A to Z</option>
                    <option value="popularity" <?php echo $sort === 'popularity' ? 'selected' : ''; ?>>Popularity</option>
                </select>
            </div>
        </form>

        <!-- Search Results -->
        <?php if (!empty($services)): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php foreach ($services as $service): ?>
                    <div class="service-card">
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($service['service_image']); ?>" 
                             alt="<?php echo htmlspecialchars($service['service_name']); ?>">
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-800 truncate"><?php echo htmlspecialchars($service['service_name']); ?></h3>
                            <p class="text-sm text-gray-600"><?php echo htmlspecialchars($service['service_category']); ?></p>
                            <p class="text-gray-700 font-medium mt-2">
                                <?php echo number_format($service['service_price'], 0, '.', ','); ?> MMK • 
                                <span class="text-gray-500"><?php echo htmlspecialchars($service['duration']); ?></span>
                            </p>
                            <p class="text-sm text-gray-500 mt-1 line-clamp-2"><?php echo htmlspecialchars($service['service_description']); ?></p>
                            <a href="../Customer/Service_details.php?id=<?php echo $service['service_id']; ?>" 
                               class="mt-3 inline-block bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors">
                                View Details
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="no-results">No services found matching your criteria. Try adjusting your search.</p>
        <?php endif; ?>
    </div>
</body>
</html>