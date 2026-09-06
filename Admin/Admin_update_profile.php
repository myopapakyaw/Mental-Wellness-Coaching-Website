<?php
session_start();
include 'Connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $bio = trim($_POST['bio']);
    $profile_picture = null;

    // Handle profile picture upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = mime_content_type($_FILES['profile_picture']['tmp_name']);
        $file_size = $_FILES['profile_picture']['size'];

        // Validate file type and size (e.g., max 2MB)
        if (!in_array($file_type, $allowed_types)) {
            $_SESSION['error'] = "Invalid file type. Only JPEG, PNG, and GIF are allowed.";
            header("Location: AdminProfile.php");
            exit();
        }
        if ($file_size > 2 * 1024 * 1024) { // 2MB limit
            $_SESSION['error'] = "File size exceeds 2MB limit.";
            header("Location: AdminProfile.php");
            exit();
        }

        // Read the file content for BLOB storage
        $profile_picture = file_get_contents($_FILES['profile_picture']['tmp_name']);
    }

    try {
        if ($profile_picture) {
            // Update with profile picture
            $stmt = $pdo->prepare("UPDATE admins SET name = ?, email = ?, bio = ?, profile_picture = ? WHERE user_id = ?");
            $stmt->execute([$name, $email, $bio, $profile_picture, $user_id]);
        } else {
            // Update without profile picture
            $stmt = $pdo->prepare("UPDATE admins SET name = ?, email = ?, bio = ? WHERE user_id = ?");
            $stmt->execute([$name, $email, $bio, $user_id]);
        }
        $_SESSION['success'] = "Profile updated successfully!";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error updating profile: " . $e->getMessage();
    }
    header("Location: AdminProfile.php");
    exit();
}
?>