<?php
session_start();
include 'Connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];

    // Validate new password and confirmation
    if ($new_password !== $confirm_new_password) {
        $_SESSION['error'] = "New password and confirmation do not match.";
        header("Location: AdminProfile.php");
        exit();
    }

    try {
        // Fetch current password from the 'admins' table
        $stmt = $pdo->prepare("SELECT password FROM admins WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (password_verify($current_password, $user['password'])) {
            // Update password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE user_id = ?");
            $stmt->execute([$hashed_password, $user_id]);
            $_SESSION['success'] = "Password updated successfully!";
        } else {
            $_SESSION['error'] = "Current password is incorrect.";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error updating password: " . $e->getMessage();
    }
    header("Location: AdminProfile.php");
    exit();
}
?>