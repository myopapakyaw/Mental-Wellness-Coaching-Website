<?php
session_start();

include_once 'Connect.php';

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("Fail to connect: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password']; 
    $phone = $_POST['phone'] ?? null; 
    $profile_picture = null;

    // Handle profile picture upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $profile_picture = file_get_contents($_FILES['profile_picture']['tmp_name']);
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, phone, profile_picture, role, created_at) VALUES (:username, :email, :password, :phone, :profile_picture, 'customer', NOW())");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password); // Plain text password
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':profile_picture', $profile_picture, PDO::PARAM_LOB);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Account created successfully!";
        } else {
            $_SESSION['error_message'] = "Failed to create account.";
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Database error: " . $e->getMessage();
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .fade-in { animation: fadeIn 0.5s ease-in; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
        .input-focus { transition: all 0.3s ease; }
        .input-focus:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3); }
        .gradient-bg { background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); }
    </style>
</head>
<body class="gradient-bg flex items-center justify-center min-h-screen">
    <div class="bg-white p-6 rounded-xl shadow-2xl w-full max-w-md fade-in">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Create an Account</h2>
            <p class="mt-1 text-sm text-gray-600">Join us today!</p>
        </div>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="mb-4 p-3 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-lg animate-pulse">
                <?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="mb-4 p-3 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-lg">
                <?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data" class="space-y-4">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <input type="text" name="username" id="username" required class="input-focus block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none" placeholder="Enter your username">
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" id="email" required class="input-focus block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none" placeholder="Enter your email">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" id="password" required class="input-focus block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none" placeholder="Enter your password">
            </div>
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone (Optional)</label>
                <input type="text" name="phone" id="phone" class="input-focus block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none" placeholder="Enter your phone number">
            </div>
            <div>
                <label for="profile_picture" class="block text-sm font-medium text-gray-700 mb-1">Profile Picture (Optional)</label>
                <input type="file" name="profile_picture" id="profile_picture" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>
            <button type="submit" class="w-full px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-300 ease-in-out transform hover:-translate-y-1">Sign Up</button>
        </form>

        <p class="mt-2 text-center text-sm text-gray-600">
            Already have an account? <a href="Login.php" class="font-medium text-blue-600 hover:underline">Sign in</a>
        </p>
    </div>
</body>
</html>