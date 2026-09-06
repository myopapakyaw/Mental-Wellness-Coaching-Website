<?php
include("Connect.php");

if (!isset($_GET['service_id'])) {
    header("HTTP/1.0 404 Not Found");
    exit();
}

$service_id = $_GET['service_id'];

try {
    $sql = "SELECT service_image FROM services WHERE service_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $service_id]);
    $image = $stmt->fetchColumn();

    if ($image) {
        header("Content-Type: image/jpeg"); 
        echo $image;
    } else {
        header("HTTP/1.0 404 Not Found");
    }
} catch (Exception $e) {
    header("HTTP/1.0 500 Internal Server Error");
    echo "Error: " . $e->getMessage();
}
exit();
?>