<?php
session_start();
include 'Connect.php';

try {
    if (!isset($_SESSION['user_id'])) {
        header("Location: Login.php?message=Please login to add items to wishlist");
        exit();
    }
    
    $user_id = $_SESSION['user_id'];
    
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $service_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
        
        $check_service = $pdo->prepare("SELECT COUNT(*) FROM services WHERE service_id = ?");
        $check_service->execute([$service_id]);
        $service_exists = $check_service->fetchColumn();
        
        if ($service_exists == 0) {
            header("Location: ServicePage.php?error=Invalid service");
            exit();
        }
        
        $check_query = "SELECT COUNT(*) FROM wishlist WHERE user_id = ? AND service_id = ?";
        $check_stmt = $pdo->prepare($check_query);
        $check_stmt->execute([$user_id, $service_id]);
        $in_wishlist = $check_stmt->fetchColumn();
        
        if ($in_wishlist == 0) {
            $insert_query = "INSERT INTO wishlist (user_id, service_id) VALUES (?, ?)";
            $insert_stmt = $pdo->prepare($insert_query);
            if ($insert_stmt->execute([$user_id, $service_id])) {
                // Debug: Confirm insertion
                echo "Added to wishlist: user_id=$user_id, service_id=$service_id<br>";
                $count = $pdo->query("SELECT COUNT(*) FROM wishlist WHERE user_id = $user_id AND service_id = $service_id")->fetchColumn();
                echo "Database count after insert: $count<br>";
                header("Location: ServicePage.php?success=Service added to wishlist");
            } else {
                echo "Insert failed: " . print_r($pdo->errorInfo(), true);
            }
        } else {
            $delete_query = "DELETE FROM wishlist WHERE user_id = ? AND service_id = ?";
            $delete_stmt = $pdo->prepare($delete_query);
            if ($delete_stmt->execute([$user_id, $service_id])) {
                header("Location: wishlist.php?success=Service removed from wishlist");
            } else {
                echo "Delete failed: " . print_r($pdo->errorInfo(), true);
            }
        }
        exit(); 
        
    } else {
        header("Location: ServicePage.php?error=Invalid service ID");
        exit();
    }
    
} catch (Exception $e) {
    header("Location: ServicePage.php?error=Database error: " . $e->getMessage());
    exit();
}
?>