<?php
// AdminProfile.php
session_start(); 
include 'Connect.php'; 
// Fetch the user ID from the session
$user_id = $_SESSION['user_id'] ?? 1; 

try {
    // Fetch the admin's profile picture and other details from the database
    $stmt = $pdo->prepare("SELECT profile_picture, name, email, bio FROM admins WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Set the profile picture path and other details
    $profile_picture = ($result && $result['profile_picture']) ? 'data:image/jpeg;base64,' . base64_encode($result['profile_picture']) : "default_profile.jpg";
    $name = $result['name'] ?? '';
    $email = $result['email'] ?? '';
    $bio = $result['bio'] ?? '';
} catch (PDOException $e) {
    // Handle database error
    $profile_picture = "default_profile.jpg"; // Default image path if an error occurs
    $name = '';
    $email = '';
    $bio = '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile - Velora Cosmetics</title>
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

        .sidebar-item.active {
            background-color: #4A5568;
        }

        /* Custom styles for the profile page */
        .profile-picture {
            width: 160px;
            height: 160px;
            border: 4px solid #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .file-upload-label {
            background-color: #4F46E5;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .file-upload-label:hover {
            background-color: #4338CA;
        }

        .file-upload-input {
            display: none;
        }

        /* Enhanced form styling */
        .form-input {
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .form-input:focus {
            border-color: #4F46E5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
        }

        .form-textarea {
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .form-textarea:focus {
            border-color: #4F46E5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
        }
    </style>
</head>
<body class="bg-gray-100">
    <?php include 'AdminNavbar.php'; ?>
    <?php include 'AdminSidebar.php'; ?>
    <div class="content">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-800 mb-8">Admin Profile</h1>

            <!-- Success and Error Messages -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <div class="bg-white p-8 rounded-lg shadow-lg">
                <!-- Profile Picture Section -->
                <div class="text-center mb-8">
                    <div class="inline-block relative">
                        <img src="<?php echo $profile_picture; ?>" alt="Profile Picture" class="profile-picture rounded-full object-cover mx-auto">
                        <label for="profile_picture" class="file-upload-label absolute bottom-0 right-0">
                            <i class="fas fa-camera mr-2"></i>Change
                        </label>
                        <input type="file" name="profile_picture" id="profile_picture" accept="image/*" class="file-upload-input">
                    </div>
                    <p class="text-gray-500 text-sm mt-2">JPG, PNG, GIF (Max 2MB)</p>
                </div>

                <!-- Profile Update Form -->
                <form action="Admin_update_profile.php" method="post" enctype="multipart/form-data" class="space-y-6">
                    <!-- Name Field -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                        <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($name); ?>" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm form-input" placeholder="Enter your full name">
                    </div>

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                        <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm form-input" placeholder="Enter your email">
                    </div>

                    <!-- Bio Field -->
                    <div>
                        <label for="bio" class="block text-sm font-medium text-gray-700">Bio</label>
                        <textarea name="bio" id="bio" rows="4" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm form-textarea" placeholder="Tell us about yourself"><?php echo htmlspecialchars($bio); ?></textarea>
                    </div>

                    <!-- Submit Button -->
                    <div class="text-right">
                        <button type="submit" class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all">
                            <i class="fas fa-save mr-2"></i>Update Profile
                        </button>
                    </div>
                </form>

                <!-- Password Update Section -->
                <div class="mt-12">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Change Password</h2>
                    <form action="Admin_update_password.php" method="post" class="space-y-6">
                        <!-- Current Password Field -->
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                            <input type="password" name="current_password" id="current_password" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm form-input" placeholder="Enter your current password">
                        </div>

                        <!-- New Password Field -->
                        <div>
                            <label for="new_password" class="block text-sm font-medium text-gray-700">New Password</label>
                            <input type="password" name="new_password" id="new_password" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm form-input" placeholder="Enter your new password">
                        </div>

                        <!-- Confirm New Password Field -->
                        <div>
                            <label for="confirm_new_password" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                            <input type="password" name="confirm_new_password" id="confirm_new_password" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm form-input" placeholder="Confirm your new password">
                        </div>

                        <!-- Submit Button -->
                        <div class="text-right">
                            <button type="submit" class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all">
                                <i class="fas fa-key mr-2"></i>Change Password
                            </button>
                        </div>
                    </form>
                </div>
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

        // Preview uploaded profile picture
        const profilePictureInput = document.getElementById('profile_picture');
        const profilePictureImg = document.querySelector('.profile-picture');

        profilePictureInput.addEventListener('change', (event) => {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    profilePictureImg.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>