<?php
// AdminNavbar.php
// session_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
 // Start the session

include 'Connect.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit();
}

$user_id = $_SESSION['user_id']; 
// Get the user ID from the session

try {
    $stmt = $pdo->prepare("SELECT username, profile_picture FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $username = $result['username'];
        if (!empty($result['profile_picture'])) {
            $profile_picture = "data:image/jpeg;base64," . base64_encode($result['profile_picture']);
        } else {
            $profile_picture = "default_profile.jpg";
        }
    } else {
        $username = "Guest";
        $profile_picture = "default_profile.jpg";
    }
} catch (PDOException $e) {
    error_log("Database error in AdminNavbar.php: " . $e->getMessage());
    $username = "Guest";
    $profile_picture = "default_profile.jpg";
}

// Determine the page title dynamically based on the current page
$current_page = basename($_SERVER['PHP_SELF']);
$page_titles = [
    'UserTable.php' => 'User Management',
    'EditUsers.php' => 'Edit User',
    'CreateUsers.php' => 'Create User',
    'AdminDashboard.php' => 'Admin Dashboard',
    'CreateProducts.php' => 'Create Product',
    'ProductTable.php' => 'Product Management',
    'AdminProfile.php' => 'Admin Profile',
];
$page_title = $page_titles[$current_page] ?? 'Admin Dashboard';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .navbar {
            background: linear-gradient(90deg,rgb(0, 4, 16) 0%,rgb(3, 41, 101) 100%);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 1rem 2rem;
            position: fixed;
            top: 0;
            width: calc(100% - 250px);
            margin-left: 250px;
            z-index: 1000;
            transition: all 0.3s;
        }

        .navbar.collapsed {
            margin-left: 0;
            width: 100%;
        }

        .navbar-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .navbar-left {
            display: flex;
            align-items: center;
        }

        .sidebar-toggler {
            font-size: 1.5rem;
            color: #ffffff;
            cursor: pointer;
            transition: transform 0.3s ease, color 0.3s ease;
        }

        .sidebar-toggler:hover {
            transform: rotate(90deg);
            color: #dbeafe;
        }

        .navbar-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #ffffff;
            margin-left: 1rem;
        }

        .navbar-right {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .search-container {
            position: relative;
            width: 300px;
        }

        .search-input {
            width: 100%;
            padding: 0.5rem 2.5rem 0.5rem 1rem;
            background-color: #ffffff;
            border: none;
            border-radius: 9999px;
            font-size: 0.9rem;
            color: #1e3a8a;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
        }

        .search-icon {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #3b82f6;
        }

        .profile-container {
            position: relative;
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .profile-img {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            border: 2px solid #ffffff;
            margin-right: 0.5rem;
        }

        .profile-name {
            font-size: 1rem;
            font-weight: 500;
            color: #ffffff;
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background-color: #ffffff;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            min-width: 150px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: all 0.3s ease;
        }

        .profile-container:hover .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-item {
            display: block;
            padding: 0.75rem 1rem;
            color: #1e3a8a;
            font-size: 0.9rem;
            transition: background-color 0.3s ease;
        }

        .dropdown-item:hover {
            background-color: #f3f4f6;
        }

        @media (max-width: 768px) {
            .navbar {
                width: 100%;
                margin-left: 0;
                padding: 1rem;
            }

            .navbar-title {
                font-size: 1.25rem;
            }

            .search-container {
                width: 200px;
            }

            .profile-name {
                display: none;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="navbar-container">
            <div class="navbar-left">
                <span class="sidebar-toggler">
                    <i class="fas fa-bars"></i>
                </span>
                <h1 class="navbar-title"><?php echo htmlspecialchars($page_title); ?></h1>
            </div>
            <div class="navbar-right">
                <!-- <div class="search-container">
                    <form action="AdminSearchresult.php" method="GET">
                        <input type="text" name="query" class="search-input" placeholder="Search services and clients...">
                        <span class="search-icon">
                            <i class="fas fa-search"></i>
                        </span>
                    </form>
                </div> -->
                <div class="profile-container">
                    <img class="profile-img" src="<?php echo $profile_picture; ?>" alt="User">
                    <span class="profile-name"><?php echo htmlspecialchars($username); ?></span>
                    <div class="dropdown-menu">
                        <a href="AdminProfile.php" class="dropdown-item">Profile</a>
                        <a href="Login.php" class="dropdown-item">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</body>
</html>