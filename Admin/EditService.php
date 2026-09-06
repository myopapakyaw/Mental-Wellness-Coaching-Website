<?php
include("Connect.php");

if (!isset($_GET['id'])) {
    die("Service ID is missing.");
}

$service_id = $_GET['id'];

try {
    // Fetch the service details from the database
    $sql = "SELECT * FROM services WHERE service_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $service_id]);
    $service = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$service) {
        die("Service not found.");
    }
} catch (Exception $e) {
    die("Failed to fetch service: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get form data
        $service_name = $_POST['sname'];
        $service_category = $_POST['scategory'];
        $service_price = $_POST['price'];
        $service_description = $_POST['description'];
        $duration = $_POST['duration'];

        // Handle image upload
        if (isset($_FILES['service_image']) && $_FILES['service_image']['error'] === UPLOAD_ERR_OK) {
            $targetDir = "img/";
            $targetFile = $targetDir . basename($_FILES['service_image']['name']);

            // Validate file type and size
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $maxFileSize = 2 * 1024 * 1024; // 2MB
            $fileType = $_FILES['service_image']['type'];
            $fileSize = $_FILES['service_image']['size'];

            if (!in_array($fileType, $allowedTypes)) {
                throw new Exception("Only JPG, PNG, and GIF images are allowed.");
            }

            if ($fileSize > $maxFileSize) {
                throw new Exception("Image size must be less than 2MB.");
            }

            // Move the uploaded file
            if (!move_uploaded_file($_FILES['service_image']['tmp_name'], $targetFile)) {
                throw new Exception("Failed to upload image.");
            }

            // Read the new image file as binary data
            $service_image = file_get_contents($targetFile);
        } else {
            // Keep the existing image if no new image is uploaded
            $service_image = $service['service_image'];
        }

        // Update the service in the database
        $sql = "UPDATE services 
                SET service_name = :service_name, 
                    service_category = :service_category, 
                    service_price = :service_price, 
                    service_description = :service_description, 
                    duration = :duration, 
                    service_image = :service_image 
                WHERE service_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'service_name' => $service_name,
            'service_category' => $service_category,
            'service_price' => $service_price,
            'service_description' => $service_description,
            'duration' => $duration,
            'service_image' => $service_image,
            'id' => $service_id
        ]);

        // Redirect to the service table page
        header("Location: ServiceTable.php");
        exit();
    } catch (Exception $e) {
        die("Failed to update service: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Service</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">Edit Service</h2>
            <a href="ServiceTable.php" class="inline-block mb-6 text-blue-600 hover:text-blue-800">&larr; Back to Service Table</a>
            <form method="post" action="" enctype="multipart/form-data" class="space-y-6">
                <div>
                    <label for="service_id" class="block text-sm font-medium text-gray-700">Service ID</label>
                    <input type="text" name="service_id" id="service_id" value="<?php echo htmlspecialchars($service['service_id']); ?>" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-100">
                </div>
                <div>
                    <label for="service_name" class="block text-sm font-medium text-gray-700">Service Name</label>
                    <input type="text" name="sname" id="service_name" value="<?php echo htmlspecialchars($service['service_name']); ?>" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                    <select name="scategory" id="category" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="Guided Meditation" <?php echo $service['service_category'] === 'Guided Meditation' ? 'selected' : ''; ?>>Guided Meditation</option>
                        <option value="Stress Management" <?php echo $service['service_category'] === 'Stress Management' ? 'selected' : ''; ?>>Stress Management</option>
                        <option value="Mindfulness Training" <?php echo $service['service_category'] === 'Mindfulness Training' ? 'selected' : ''; ?>>Mindfulness Training</option>
                        <option value="Work-Life Balance" <?php echo $service['service_category'] === 'Work-Life Balance' ? 'selected' : ''; ?>>Work-Life Balance</option>
                        <option value="Relaxation Techniques" <?php echo $service['service_category'] === 'Relaxation Techniques' ? 'selected' : ''; ?>>Relaxation Techniques</option>
                    </select>
                </div>
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
                    <input type="text" name="price" id="price" value="<?php echo htmlspecialchars($service['service_price']); ?>" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="duration" class="block text-sm font-medium text-gray-700">Duration</label>
                    <input type="text" name="duration" id="duration" value="<?php echo htmlspecialchars($service['duration']); ?>" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="3" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"><?php echo htmlspecialchars($service['service_description']); ?></textarea>
                </div>
                <div>
                    <label for="service_image" class="block text-sm font-medium text-gray-700">Service Image</label>
                    <div class="mt-1 flex items-center">
                        <input type="file" name="service_image" id="service_image" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                    <p class="mt-2 text-sm text-gray-500">Upload a new image (JPG, PNG, or GIF). Leave blank to keep the existing image.</p>
                    <?php if (!empty($service['service_image'])): ?>
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700">Current Image:</label>
                            <img src="getImage.php?service_id=<?php echo $service['service_id']; ?>" alt="Current Service Image" class="w-32 h-32 object-cover rounded">
                        </div>
                    <?php endif; ?>
                </div>
                <div class="text-right">
                    <button type="submit" name="submit" class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Update Service</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>