<?php
session_start(); // Start the session

require_once("Connect.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Check if the therapist exists
    $stmt = $pdo->prepare("SELECT * FROM therapists WHERE therapist_id = ?");
    $stmt->execute([$id]);
    $therapist = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$therapist) {
        $_SESSION['error_message'] = "Therapist not found!";
        header("Location: TherapistTable.php");
        exit();
    }

    // Delete the therapist
    try {
        $stmt = $pdo->prepare("DELETE FROM therapists WHERE therapist_id = ?");
        $stmt->execute([$id]);
        $_SESSION['success_message'] = "Therapist deleted successfully!";
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error deleting therapist: " . $e->getMessage();
    }
} else {
    $_SESSION['error_message'] = "Invalid request!";
}

header("Location: TherapistTable.php");
exit();
?>