<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start(); // Start the session

require_once("Connect.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit();
}

// Initialize error array
$errors = [];

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM therapists WHERE therapist_id = ?");
    $stmt->execute([$id]);
    $therapist = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$therapist) {
        $_SESSION['error_message'] = "Therapist not found!";
        header("Location: TherapistTable.php");
        exit();
    }
} else {
    header("Location: TherapistTable.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id']; // Get the ID from the form
    $therapist_name = trim($_POST['therapist_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']) ?: null; // Set to null if empty
    $specialization = trim($_POST['specialization']) ?: null; // Set to null if empty
    $biography = trim($_POST['biography']) ?: null; // Set to null if empty

    // Server-side validation
    // Check for duplicate therapist name (excluding the current user)
    $stmt = $pdo->prepare("SELECT therapist_id FROM therapists WHERE therapist_name = ? AND therapist_id != ?");
    $stmt->execute([$therapist_name, $id]);
    if ($stmt->fetch()) {
        $errors['therapist_name'] = "Therapist name already exists.";
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
    }

    // Check for duplicate email (excluding the current user)
    $stmt = $pdo->prepare("SELECT therapist_id FROM therapists WHERE email = ? AND therapist_id != ?");
    $stmt->execute([$email, $id]);
    if ($stmt->fetch()) {
        $errors['email'] = "Email already exists.";
    }

    if ($phone && !preg_match('/^\+?[0-9]{7,15}$/', $phone)) {
        $errors['phone'] = "Invalid phone number format.";
    }

    if ($biography && strlen($biography) > 1000) {
        $errors['biography'] = "Biography must not exceed 1000 characters.";
    }

    // Handle profile picture upload (optional)
    $profile_picture = $therapist['profile_picture']; // Keep existing by default
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = mime_content_type($_FILES['profile_picture']['tmp_name']);
        $file_size = $_FILES['profile_picture']['size'];

        // Validate file type and size (max 2MB)
        if (!in_array($file_type, $allowed_types)) {
            $errors['profile_picture'] = "Invalid file type. Only JPEG, PNG, and GIF are allowed.";
        }
        if ($file_size > 2 * 1024 * 1024) { // 2MB limit
            $errors['profile_picture'] = "File size exceeds 2MB limit.";
        }

        if (empty($errors)) {
            // Read the file content for BLOB storage
            $profile_picture = file_get_contents($_FILES['profile_picture']['tmp_name']);
            if ($profile_picture === false) {
                $errors['profile_picture'] = "Failed to read uploaded file.";
            }
        }
    }

    if (empty($errors)) {
        try {
            // Update the therapist's information including biography
            $stmt = $pdo->prepare("UPDATE therapists SET therapist_name = ?, email = ?, phone = ?, specialization = ?, profile_picture = ?, biography = ? WHERE therapist_id = ?");
            $params = [$therapist_name, $email, $phone, $specialization, $profile_picture, $biography, $id];
            $success = $stmt->execute($params);

            if ($success) {
                $_SESSION['success_message'] = "Therapist updated successfully!";
                header("Location: TherapistTable.php");
                exit();
            } else {
                $errors['database'] = "Failed to update therapist: " . implode(", ", $stmt->errorInfo());
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { 
                if (strpos($e->getMessage(), 'therapist_name') !== false) {
                    $errors['therapist_name'] = "Therapist name already exists.";
                } elseif (strpos($e->getMessage(), 'email') !== false) {
                    $errors['email'] = "Email already exists.";
                } else {
                    $errors['database'] = "Duplicate entry error.";
                }
            } else {
                $errors['database'] = "Database error: " . $e->getMessage();
            }
        }
    }

    if (!empty($errors)) {
        // Store errors and form data in session
        $_SESSION['form_errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
        header("Location: EditTherapist.php?id=" . $id);
        exit();
    }
}

$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']); // Clear form data after use

// Get errors from session if available
$form_errors = $_SESSION['form_errors'] ?? [];
unset($_SESSION['form_errors']); // Clear errors after use
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Therapist</title>
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
                <a href="TherapistTable.php" class="inline-block mb-6 text-blue-600 hover:text-blue-800">
                    ← Back to Therapist Table
                </a>
                <h2 class="text-2xl font-bold mb-6 text-gray-800">Edit Therapist</h2>
                <?php if (!empty($form_errors['database'])): ?>
                    <div class="text-red-500 text-sm mb-4"><?php echo $form_errors['database']; ?></div>
                <?php endif; ?>
                <form method="post" enctype="multipart/form-data" class="space-y-6">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($therapist['therapist_id']); ?>">
                    <div>
                        <label for="therapist_name" class="block text-sm font-medium text-gray-700">Therapist Name</label>
                        <input type="text" name="therapist_name" id="therapist_name" placeholder="Enter Therapist Name"
                            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 <?php echo isset($form_errors['therapist_name']) ? 'border-red-500' : ''; ?>"
                            value="<?php echo htmlspecialchars($form_data['therapist_name'] ?? $therapist['therapist_name']); ?>">
                        <?php if (isset($form_errors['therapist_name'])): ?>
                            <div class="text-red-500 text-sm mt-1"><?php echo $form_errors['therapist_name']; ?></div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" id="email" placeholder="Enter Email"
                            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 <?php echo isset($form_errors['email']) ? 'border-red-500' : ''; ?>"
                            value="<?php echo htmlspecialchars($form_data['email'] ?? $therapist['email']); ?>">
                        <?php if (isset($form_errors['email'])): ?>
                            <div class="text-red-500 text-sm mt-1"><?php echo $form_errors['email']; ?></div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone (Optional)</label>
                        <input type="text" name="phone" id="phone" placeholder="Enter Phone Number"
                            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 <?php echo isset($form_errors['phone']) ? 'border-red-500' : ''; ?>"
                            value="<?php echo htmlspecialchars($form_data['phone'] ?? $therapist['phone'] ?? ''); ?>">
                        <?php if (isset($form_errors['phone'])): ?>
                            <div class="text-red-500 text-sm mt-1"><?php echo $form_errors['phone']; ?></div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label for="specialization" class="block text-sm font-medium text-gray-700">Specialization</label>
                        <input type="text" name="specialization" id="specialization" placeholder="Enter Specialization"
                            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 <?php echo isset($form_errors['specialization']) ? 'border-red-500' : ''; ?>"
                            value="<?php echo htmlspecialchars($form_data['specialization'] ?? $therapist['specialization'] ?? ''); ?>">
                        <?php if (isset($form_errors['specialization'])): ?>
                            <div class="text-red-500 text-sm mt-1"><?php echo $form_errors['specialization']; ?></div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label for="biography" class="block text-sm font-medium text-gray-700">Biography (Optional)</label>
                        <textarea name="biography" id="biography" placeholder="Enter therapist biography (max 1000 characters)"
                            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 <?php echo isset($form_errors['biography']) ? 'border-red-500' : ''; ?>"
                            rows="4"><?php echo htmlspecialchars($form_data['biography'] ?? $therapist['biography'] ?? ''); ?></textarea>
                        <?php if (isset($form_errors['biography'])): ?>
                            <div class="text-red-500 text-sm mt-1"><?php echo $form_errors['biography']; ?></div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Current Profile Picture</label>
                        <div class="mt-2">
                            <?php if (!empty($therapist['profile_picture'])): ?>
                                <img src="data:image/jpeg;base64,<?php echo base64_encode($therapist['profile_picture']); ?>" alt="Profile Picture" class="profile-pic">
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
                            <span id="btn-text">Update Therapist</span>
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