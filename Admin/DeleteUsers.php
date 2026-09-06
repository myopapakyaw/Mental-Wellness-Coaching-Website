<?php
session_start(); 

require_once("Connect.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Check if the therapist exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$id]);
    $therapist = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$therapist) {
        $_SESSION['error_message'] = "User not found!";
        header("Location: UserTable.php");
        exit();
    }

    // Delete the therapist
    try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->execute([$id]);
        $_SESSION['success_message'] = "User deleted successfully!";
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error deleting user: " . $e->getMessage();
    }
} else {
    $_SESSION['error_message'] = "Invalid request!";
}

header("Location: UserTable.php");
exit();
?>