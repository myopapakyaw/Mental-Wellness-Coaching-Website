<?php
session_start();

// Database connection
$host = 'localhost';
$dbname = 'mental_wellness';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    header("Location: login.php?error=db");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // Validate input
    if (empty($email) || empty($password)) {
        header("Location: login.php?error=empty");
        exit();
    }

    // Prepare and execute the query
    $stmt = $pdo->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Login successful
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $email;
        
        // Redirect to dashboard or home page
        header("Location: dashboard.php");
        exit();
    } else {
        // Login failed
        header("Location: login.php?error=invalid");
        exit();
    }
} else {
    // If not POST request, redirect to login
    header("Location: login.php");
    exit();
}
?> 