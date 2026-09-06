<?php
include("Connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $service_id = $_POST['sid'];
        $service_name = $_POST['sname'];
        $service_category = $_POST['scategory'];
        $service_price = $_POST['price'];
        $service_description = $_POST['description'];
        $duration = $_POST['duration'];

        // Handle image upload
        if (isset($_FILES['service_image']) && $_FILES['service_image']['error'] === UPLOAD_ERR_OK) {
            $targetDir = "img/";
            $targetFile = $targetDir . basename($_FILES['service_image']['name']);
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
            if (!move_uploaded_file($_FILES['service_image']['tmp_name'], $targetFile)) {
                throw new Exception("Failed to upload image.");
            }
            $service_image = file_get_contents($targetFile);
        } else {
            $service_image = null; // Or handle default image
        }

        $sql = "INSERT INTO services (service_id, service_name, service_category, service_description, service_price, duration, service_image) 
                VALUES (:sid, :sname, :scategory, :sdescription, :sprice, :duration, :simage)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'sid' => $service_id,
            'sname' => $service_name,
            'scategory' => $service_category,
            'sdescription' => $service_description,
            'sprice' => $service_price,
            'duration' => $duration,
            'simage' => $service_image
        ]);

        header("Location: ServiceTable.php");
        exit();
    } catch (Exception $e) {
        die("Failed to insert service: " . $e->getMessage());
    }
}
?>