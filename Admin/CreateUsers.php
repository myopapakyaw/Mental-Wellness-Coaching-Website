<?php
session_start(); // Start the session

require_once("Connect.php");

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit();
}

// Initialize error array
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];
    $phone = trim($_POST['phone']) ?: null; 

    // Basic validation
    if (empty($username) || empty($email) || empty($password) || empty($role)) {
        $errors['general'] = "All required fields must be filled.";
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
    }

    // Validate role
    $valid_roles = ['customer', 'admin'];
    if (!in_array($role, $valid_roles)) {
        $errors['role'] = "Invalid role selected.";
    }

    if ($phone && !preg_match('/^\+?[0-9]{7,15}$/', $phone)) {
        $errors['phone'] = "Invalid phone number format.";
    }

    // Handle profile picture upload (optional)
    $profile_picture = ''; // Default to empty BLOB
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
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, phone, profile_picture) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$username, $email, $password, $role, $phone, $profile_picture]);
            header("Location: UserTable.php?status=success&message=" . urlencode("User created successfully!"));
            exit();
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Integrity constraint violation (duplicate entry)
                if (strpos($e->getMessage(), 'username') !== false) {
                    $errors['username'] = "Username already exists.";
                } elseif (strpos($e->getMessage(), 'email') !== false) {
                    $errors['email'] = "Email already exists.";
                } else {
                    $errors['general'] = "Duplicate entry error.";
                }
            } else {
                $errors['general'] = "Error creating user: " . $e->getMessage();
            }
        }
    }

    if (!empty($errors)) {
        $_SESSION['form_errors'] = $errors;
        $_SESSION['form_data'] = $_POST; // Store form data to repopulate fields
        header("Location: CreateUsers.php");
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
    <title>Create User</title>
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
                <h2 class="text-2xl font-bold mb-6 text-gray-800">Create New User</h2>
                <?php if (isset($form_errors['general'])): ?>
                    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-md">
                        <?php echo htmlspecialchars($form_errors['general']); ?>
                    </div>
                <?php endif; ?>
                <form method="post" enctype="multipart/form-data" class="space-y-6">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                        <input type="text" name="username" id="username" placeholder="Enter Username"
                            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 <?php echo isset($form_errors['username']) ? 'border-red-500' : ''; ?>"
                            value="<?php echo htmlspecialchars($form_data['username'] ?? ''); ?>">
                        <?php if (isset($form_errors['username'])): ?>
                            <div class="text-red-500 text-sm mt-1"><?php echo $form_errors['username']; ?></div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" id="email" placeholder="Enter Email"
                            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 <?php echo isset($form_errors['email']) ? 'border-red-500' : ''; ?>"
                            value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>">
                        <?php if (isset($form_errors['email'])): ?>
                            <div class="text-red-500 text-sm mt-1"><?php echo $form_errors['email']; ?></div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input type="password" name="password" id="password" placeholder="Enter Password"
                            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone (Optional)</label>
                        <input type="text" name="phone" id="phone" placeholder="Enter Phone Number"
                            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 <?php echo isset($form_errors['phone']) ? 'border-red-500' : ''; ?>"
                            value="<?php echo htmlspecialchars($form_data['phone'] ?? ''); ?>">
                        <?php if (isset($form_errors['phone'])): ?>
                            <div class="text-red-500 text-sm mt-1"><?php echo $form_errors['phone']; ?></div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                        <select name="role" id="role"
                            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 <?php echo isset($form_errors['role']) ? 'border-red-500' : ''; ?>">
                            <option value="customer" <?php echo ($form_data['role'] ?? '') == 'customer' ? 'selected' : ''; ?>>Customer</option>
                            <option value="admin" <?php echo ($form_data['role'] ?? '') == 'admin' ? 'selected' : ''; ?>>Admin</option>
                            <option value="admin" <?php echo ($form_data['role'] ?? '') == 'therapist' ? 'selected' : ''; ?>>Therapist</option>
                        </select>
                        <?php if (isset($form_errors['role'])): ?>
                            <div class="text-red-500 text-sm mt-1"><?php echo $form_errors['role']; ?></div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label for="profile_picture" class="block text-sm font-medium text-gray-700">Profile Picture (Optional)</label>
                        <input type="file" name="profile_picture" id="profile_picture"
                            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 <?php echo isset($form_errors['profile_picture']) ? 'border-red-500' : ''; ?>">
                        <?php if (isset($form_errors['profile_picture'])): ?>
                            <div class="text-red-500 text-sm mt-1"><?php echo $form_errors['profile_picture']; ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="text-right">
                        <button type="submit"
                            class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <span id="btn-text">Create User</span>
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
        const sidebar = document.querySelector('.sidebar');
        const navbar = document.querySelector('.navbar');
        const content = document.querySelector('.content');
        const sidebarToggler = document.querySelector('.sidebar-toggler');

        sidebarToggler.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            navbar.classList.toggle('collapsed');
            content.classList.toggle('collapsed');
        });

        const submitBtn = document.querySelector('button[type="submit"]');
        const btnText = document.getElementById('btn-text');
        const btnLoading = document.getElementById('btn-loading');

        submitBtn.addEventListener('click', function () {
            btnText.textContent = 'Creating...';
            btnLoading.classList.remove('hidden');
        });
    </script>
</body>

</html>