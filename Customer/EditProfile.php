<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit();
}

include 'Connect.php';

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("Connection error");
}

$stmt = $pdo->prepare("SELECT username, email, phone FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);

    $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, phone = ? WHERE user_id = ?");
    $stmt->execute([$username, $email, $phone, $_SESSION['user_id']]);
    header("Location: Profile.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f0fff4 0%, #c6f6d5 50%, #9ae6b4 100%);
            min-height: 100vh;
        }
        .input-field {
            border-bottom: 1px solid #c6f6d5;
            transition: all 0.3s ease;
        }
        .input-field:focus {
            border-color: #38a169;
            outline: none;
        }
        .card {
            backdrop-filter: blur(8px);
            background-color: rgba(255, 255, 255, 0.85);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="max-w-md mx-auto py-12 px-4">
        <div class="text-center mb-10">
            <h1 class="text-2xl font-light text-gray-800 tracking-tight">Edit Profile</h1>
            <div class="mt-2 h-px w-12 bg-emerald-300 mx-auto"></div>
        </div>

        <div class="card rounded-xl px-8 py-10">
            <form method="POST" class="space-y-6">
                <div>
                    <label class="block text-xs font-medium text-gray-600 uppercase tracking-wider mb-1">Username</label>
                    <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" 
                           class="input-field w-full py-2 px-0 bg-transparent">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 uppercase tracking-wider mb-1">Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" 
                           class="input-field w-full py-2 px-0 bg-transparent">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 uppercase tracking-wider mb-1">Phone</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" 
                           class="input-field w-full py-2 px-0 bg-transparent">
                </div>

                <div class="pt-2">
                    <button type="submit" 
                            class="w-full py-3 bg-emerald-00 hover:bg-emerald-700 text-white rounded-md transition-colors">
                        Update Profile
                    </button>
                </div>
            </form>
        </div>

        <div class="text-center mt-8">
            <a href="Profile.php" class="text-sm text-emerald-700 hover:text-emerald-800 transition-colors">
                ← Return to profile
            </a>
        </div>
    </div>
</body>
</html>