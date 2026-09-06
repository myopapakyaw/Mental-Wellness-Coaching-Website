<?php
session_start();
include 'Connect.php';

// Handle login submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $_SESSION['error_message'] = "Please fill in all fields.";
        header("Location: Login.php");
        exit();
    }

    try {
        $stmt = $pdo->prepare("SELECT user_id, username, password, role FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $password === $user['password']) { // Plain text comparison
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'admin') {
                header("Location: AdminDashboard.php");
            } else {
                header("Location: ../Customer/Index.php");
            }
            exit();
        } else {
            $_SESSION['error_message'] = "Invalid username or password.";
            header("Location: Login.php");
            exit();
        }
    } catch (PDOException $e) {
        error_log("Login error: " . $e->getMessage());
        $_SESSION['error_message'] = "An error occurred. Please try again later.";
        header("Location: Login.php");
        exit();
    }
}

// Handle forgot password submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['forgot_password'])) {
    $email = trim($_POST['email']);

    if (empty($email)) {
        $_SESSION['error_message'] = "Please enter your email.";
        header("Location: Login.php");
        exit();
    }

    try {
        $stmt = $pdo->prepare("SELECT user_id, username FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $reset_token = bin2hex(random_bytes(32));
            $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE user_id = ?");
            $stmt->execute([$reset_token, $user['user_id']]);
            $_SESSION['reset_token'] = $reset_token;
            $_SESSION['success_message'] = "Use this token to reset your password:<br><span class='break-all font-mono text-sm'>$reset_token</span><br>Enter it in the reset form below.";
        } else {
            $_SESSION['error_message'] = "No account found with that email.";
        }
    } catch (PDOException $e) {
        error_log("Forgot password error: " . $e->getMessage());
        $_SESSION['error_message'] = "An error occurred. Please try again later.";
    }
    header("Location: Login.php");
    exit();
}

// Handle password reset submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reset_password'])) {
    $token = trim($_POST['reset_token']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($token) || empty($new_password) || empty($confirm_password)) {
        $_SESSION['error_message'] = "Please fill in all fields.";
        header("Location: Login.php");
        exit();
    }

    if ($new_password !== $confirm_password) {
        $_SESSION['error_message'] = "Passwords do not match.";
        header("Location: Login.php");
        exit();
    }

    try {
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE reset_token = ? AND reset_expires > NOW()");
        $stmt->execute([$token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE user_id = ?");
            $stmt->execute([$new_password, $user['user_id']]);
            $_SESSION['success_message'] = "Password successfully reset. Please login.";
            unset($_SESSION['reset_token']);
        } else {
            $_SESSION['error_message'] = "Invalid or expired reset token.";
        }
    } catch (PDOException $e) {
        error_log("Reset password error: " . $e->getMessage());
        $_SESSION['error_message'] = "An error occurred. Please try again later.";
    }
    header("Location: Login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .fade-in { animation: fadeIn 0.5s ease-in; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
        .input-focus { transition: all 0.3s ease; }
        .input-focus:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3); }
        .gradient-bg { background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); }
        .break-all { word-break: break-all; }
    </style>
</head>
<body class="gradient-bg flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-xl shadow-2xl w-full max-w-lg fade-in"> <!-- Changed max-w-md to max-w-lg -->
        <div class="text-center mb-8">
            <h2 class="text-3xl font-extrabold text-gray-900">Welcome Back</h2>
            <p class="mt-2 text-sm text-gray-600">Please sign in to continue</p>
        </div>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-lg animate-pulse">
                <?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-lg">
                <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form method="post" class="space-y-6">
            <input type="hidden" name="login" value="1">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                <div class="mt-1 relative">
                    <input type="text" name="username" id="username" required class="input-focus block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none" placeholder="Enter your username">
                    <span class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </span>
                </div>
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <div class="mt-1 relative">
                    <input type="password" name="password" id="password" required class="input-focus block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none" placeholder="Enter your password">
                    <span class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0-1.1-.9-2-2-2s-2 .9-2 2m4 0c0-1.1-.9-2-2-2s-2 .9-2 2m4 0c0 1.1-.9 2-2 2s-2-.9-2-2m-6 7h12m-6-7v7" />
                        </svg>
                    </span>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <!-- <div class="flex items-center">
                    <input id="remember" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="remember" class="ml-2 block text-sm text-gray-700">Remember me</label>
                </div> -->
                <button type="button" onclick="document.getElementById('forgot-password-modal').classList.remove('hidden')" class="text-sm text-blue-600 hover:underline">Forgot password?</button>
            </div>
            <button type="submit" class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-300 ease-in-out transform hover:-translate-y-1">Sign In</button>
        </form>

        <!-- Forgot Password Modal -->
        <div id="forgot-password-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center">
            <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-sm">
                <h3 class="text-lg font-bold mb-4">Reset Password</h3>
                <form method="post" class="space-y-4">
                    <input type="hidden" name="forgot_password" value="1">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" id="email" required class="input-focus block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm">
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="document.getElementById('forgot-password-modal').classList.add('hidden')" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Generate Reset Token</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Reset Password Form (shown after token generation) -->
        <?php if (isset($_SESSION['reset_token'])): ?>
            <div class="mt-6 p-6 bg-gray-100 rounded-lg">
                <h3 class="text-lg font-bold mb-4">Enter Reset Token</h3>
                <form method="post" class="space-y-4">
                    <input type="hidden" name="reset_password" value="1">
                    <div>
                        <label for="reset_token" class="block text-sm font-medium text-gray-700">Reset Token</label>
                        <textarea name="reset_token" id="reset_token" required class="input-focus block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm resize-none h-20" placeholder="Paste the token here"></textarea>
                    </div>
                    <div>
                        <label for="new_password" class="block text-sm font-medium text-gray-700">New Password</label>
                        <input type="password" name="new_password" id="new_password" required class="input-focus block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm">
                    </div>
                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                        <input type="password" name="confirm_password" id="confirm_password" required class="input-focus block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm">
                    </div>
                    <button type="submit" class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Reset Password</button>
                </form>
            </div>
        <?php endif; ?>

        <p class="mt-2 text-center text-sm text-gray-600">
            <!-- Don't have an account? <a href="#" class="font-medium text-blue-600 hover:underline">Sign up</a> -->
            Don't have an account? <a href="../Customer/Signup.php" class="font-medium text-blue-600 hover:underline">Sign up</a>

        </p>
    </div>
</body>
</html>