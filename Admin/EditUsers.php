<?php
session_start(); // Start the session

require_once("Connect.php");

// Initialize error array
$errors = [];

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $_SESSION['error_message'] = "User not found!";
        header("Location: UserTable.php");
        exit();
    }
} else {
    header("Location: UserTable.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id']; // Get the ID from the form
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $update_password = isset($_POST['update_password']);
    $password = $_POST['password']; // Will be empty if not updating
    $password_confirm = $_POST['password_confirm'];
    $role = $_POST['role'];
    $phone = trim($_POST['phone']) ?: null; // Set to null if empty

    // Server-side validation
    // Removed username validation for length and characters
    $stmt = $pdo->prepare("SELECT user_id FROM users WHERE username = ? AND user_id != ?");
    $stmt->execute([$username, $id]);
    if ($stmt->fetch()) {
        $errors['username'] = "Username already exists.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
    }

    // Check for duplicate email (excluding the current user)
    $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
    $stmt->execute([$email, $id]);
    if ($stmt->fetch()) {
        $errors['email'] = "Email already exists.";
    }

    // Validate phone (optional, but if provided, should be a valid format)
    if ($phone && !preg_match('/^\+?[0-9]{7,15}$/', $phone)) {
        $errors['phone'] = "Invalid phone number format.";
    }

    if ($update_password) {
        if (strlen($password) < 6) {
            $errors['password'] = "Password must be at least 6 characters.";
        }

        if ($password !== $password_confirm) {
            $errors['password_confirm'] = "Passwords do not match.";
        }
    }

    // Handle profile picture upload (optional)
    $profile_picture = $user['profile_picture']; // Keep existing by default
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = mime_content_type($_FILES['profile_picture']['tmp_name']);
        $file_size = $_FILES['profile_picture']['size'];

        // Validate file type and size (e.g., max 2MB)
        if (!in_array($file_type, $allowed_types)) {
            $errors['profile_picture'] = "Invalid file type. Only JPEG, PNG, and GIF are allowed.";
        }
        if ($file_size > 2 * 1024 * 1024) { // 2MB limit
            $errors['profile_picture'] = "File size exceeds 2MB limit.";
        }

        if (empty($errors)) {
            // Read the file content for BLOB storage
            $profile_picture = file_get_contents($_FILES['profile_picture']['tmp_name']);
        }
    }

    if (empty($errors)) {
        try {
            if ($update_password) {
                // Store password as plain text (no hashing)
                $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, password = ?, role = ?, phone = ?, profile_picture = ? WHERE user_id = ?");
                $stmt->execute([$username, $email, $password, $role, $phone, $profile_picture, $id]);
            } else {
                $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, role = ?, phone = ?, profile_picture = ? WHERE user_id = ?");
                $stmt->execute([$username, $email, $role, $phone, $profile_picture, $id]);
            }
            $_SESSION['success_message'] = "User updated successfully!";
            header("Location: UserTable.php");
            exit();
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                if (strpos($e->getMessage(), 'username') !== false) {
                    $errors['username'] = "Username already exists.";
                } elseif (strpos($e->getMessage(), 'email') !== false) {
                    $errors['email'] = "Email already exists.";
                } else {
                    $errors['database'] = "Duplicate entry error.";
                }
            } else {
                $errors['database'] = "Error updating user: " . $e->getMessage();
            }
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_data'] = $_POST;
            header("Location: EditUsers.php?id=" . $id);
            exit();
        }
    } else {
        // Store errors in session
        $_SESSION['form_errors'] = $errors;
        $_SESSION['form_data'] = $_POST; // Store form data to repopulate fields
        header("Location: EditUsers.php?id=" . $id); // Redirect back to form
        exit();
    }
}

$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']); // Clear form data after use

$form_errors = $_SESSION['form_errors'] ?? [];
unset($_SESSION['form_errors']); // Clear errors after use
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            overflow-y: auto;
            transition: all 0.3s;
        }

        .sidebar.collapsed {
            width: 0;
            overflow: hidden;
        }

        .navbar {
            width: calc(100% - 250px);
            margin-left: 250px;
            position: fixed;
            top: 0;
            z-index: 1000;
            transition: all 0.3s;
        }

        .navbar.collapsed {
            margin-left: 0;
            width: 100%;
        }

        .content {
            margin-left: 250px;
            margin-top: 70px;
            padding: 2rem;
            transition: all 0.3s;
        }

        .content.collapsed {
            margin-left: 0;
        }

        .sidebar-toggler {
            cursor: pointer;
        }

        .profile-pic {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
        }
    </style>
</head>

<body class="bg-gray-100">
    <?php include 'AdminNavbar.php'; ?>
    <?php include 'AdminSidebar.php'; ?>
    <div class="content">
        <div class="container mx-auto px-4 py-8">
            <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-lg p-8">
                <a href="UserTable.php" class="inline-block mb-6 text-blue-600 hover:text-blue-800">
                    ← Back to User Table
                </a>
                <h2 class="text-2xl font-bold mb-6 text-gray-800">Edit User</h2>
                <form method="post" enctype="multipart/form-data" class="space-y-6">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($user['user_id']); ?>">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                        <input type="text" name="username" id="username" placeholder="Enter Username"
                            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 <?php echo isset($form_errors['username']) ? 'border-red-500' : ''; ?>"
                            value="<?php echo htmlspecialchars($form_data['username'] ?? $user['username']); ?>">
                        <?php if (isset($form_errors['username'])): ?>
                            <div class="text-red-500 text-sm mt-1"><?php echo $form_errors['username']; ?></div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" id="email" placeholder="Enter Email"
                            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 <?php echo isset($form_errors['email']) ? 'border-red-500' : ''; ?>"
                            value="<?php echo htmlspecialchars($form_data['email'] ?? $user['email']); ?>">
                        <?php if (isset($form_errors['email'])): ?>
                            <div class="text-red-500 text-sm mt-1"><?php echo $form_errors['email']; ?></div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone (Optional)</label>
                        <input type="text" name="phone" id="phone" placeholder="Enter Phone Number"
                            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 <?php echo isset($form_errors['phone']) ? 'border-red-500' : ''; ?>"
                            value="<?php echo htmlspecialchars($form_data['phone'] ?? $user['phone'] ?? ''); ?>">
                        <?php if (isset($form_errors['phone'])): ?>
                            <div class="text-red-500 text-sm mt-1"><?php echo $form_errors['phone']; ?></div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label for="update_password" class="inline-flex items-center text-sm font-medium text-gray-700">
                            <input type="checkbox" id="update_password" name="update_password"
                                class="form-checkbox h-4 w-4 text-blue-500 focus:ring-blue-500 rounded border-gray-300">
                            <span class="ml-2">Update Password</span>
                        </label>
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                        <input type="password" name="password" id="password" placeholder="Enter New Password"
                            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 <?php echo isset($form_errors['password']) ? 'border-red-500' : ''; ?>"
                            <?php echo isset($form_data['update_password']) ? '' : 'disabled'; ?>>
                        <?php if (isset($form_errors['password'])): ?>
                            <div class="text-red-500 text-sm mt-1"><?php echo $form_errors['password']; ?></div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label for="password_confirm" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                        <input type="password" name="password_confirm" id="password_confirm"
                            placeholder="Confirm New Password"
                            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 <?php echo isset($form_errors['password_confirm']) ? 'border-red-500' : ''; ?>"
                            <?php echo isset($form_data['update_password']) ? '' : 'disabled'; ?>>
                        <?php if (isset($form_errors['password_confirm'])): ?>
                            <div class="text-red-500 text-sm mt-1"><?php echo $form_errors['password_confirm']; ?></div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                        <select name="role" id="role"
                            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="customer" <?php echo ($form_data['role'] ?? $user['role']) == 'customer' ? 'selected' : ''; ?>>Customer</option>
                            <option value="admin" <?php echo ($form_data['role'] ?? $user['role']) == 'admin' ? 'selected' : ''; ?>>Admin</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Current Profile Picture</label>
                        <div class="mt-2">
                            <?php if (!empty($user['profile_picture'])): ?>
                                <img src="data:image/jpeg;base64,<?php echo base64_encode($user['profile_picture']); ?>" alt="Profile Picture" class="profile-pic">
                            <?php else: ?>
                                <img src="default_profile.jpg" alt="Default Profile Picture" class="profile-pic">
                            <?php endif; ?>
                        </div>
                    </div>
                    <div>
                        <label for="profile_picture" class="block text-sm font-medium text-gray-700">Update Profile Picture (Optional)</label>
                        <input type="file" name="profile_picture" id="profile_picture"
                            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 <?php echo isset($form_errors['profile_picture']) ? 'border-red-500' : ''; ?>">
                        <?php if (isset($form_errors['profile_picture'])): ?>
                            <div class="text-red-500 text-sm mt-1"><?php echo $form_errors['profile_picture']; ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="text-right">
                        <button type="submit"
                            class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <span id="btn-text">Update User</span>
                            <span id="btn-loading" class="hidden ml-2">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const updatePasswordCheckbox = document.getElementById('update_password');
        const passwordInput = document.getElementById('password');
        const passwordConfirmInput = document.getElementById('password_confirm');

        updatePasswordCheckbox.addEventListener('change', function () {
            passwordInput.disabled = !this.checked;
            passwordConfirmInput.disabled = !this.checked;
        });

        // Loading State for Submit Button
        const submitBtn = document.querySelector('button[type="submit"]');
        const btnText = document.getElementById('btn-text');
        const btnLoading = document.getElementById('btn-loading');

        submitBtn.addEventListener('click', function () {
            btnText.textContent = 'Updating...';
            btnLoading.classList.remove('hidden');
        });

        // Sidebar toggle script
        document.addEventListener("DOMContentLoaded", function () {
            let sidebarToggler = document.querySelector('.sidebar-toggler');
            if (sidebarToggler) {
                sidebarToggler.addEventListener('click', () => {
                    document.querySelector('.sidebar').classList.toggle('collapsed');
                    document.querySelector('.navbar').classList.toggle('collapsed');
                    document.querySelector('.content').classList.toggle('collapsed');
                });
            }
        });
    </script>
</body>

</html>