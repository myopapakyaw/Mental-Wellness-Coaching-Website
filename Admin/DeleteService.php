<?php
include("Connect.php"); 

if (!isset($_GET['id'])) {
    die("Service ID is missing.");
}

$service_id = $_GET['id'];

try {
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

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Delete the service from the database
        $sql = "DELETE FROM services WHERE service_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $service_id]);

        // Redirect to the service table page
        header("Location: ServiceTable.php");
        exit();
    } catch (Exception $e) {
        die("Failed to delete service: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Service</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-lg p-8">
            <!-- Page Title -->
            <h2 class="text-2xl font-bold mb-6 text-gray-800">Delete Service</h2>

            <!-- Confirmation Message -->
            <p class="mb-6 text-gray-700">
                Are you sure you want to delete the service <strong><?php echo htmlspecialchars($service['service_name']); ?></strong>?
            </p>

            <!-- Back Button -->
            <a href="ServiceTable.php" class="inline-block mb-6 text-blue-600 hover:text-blue-800">
                &larr; Cancel and Go Back
            </a>

            <!-- Delete Form -->
            <form method="post" action="" class="space-y-6">
                <div class="text-right">
                    <button type="submit" name="submit"
                        class="inline-flex items-center px-6 py-3 bg-red-600 border border-transparent rounded-md font-semibold text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                        Delete Service
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>