<?php
// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    // session_start();
}

// Database connection
include 'Connect.php';

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("Fail to connect: " . $e->getMessage());
}

// Calculate the total quantity of items in the cart
$qty = 0;
if (isset($_SESSION['user_id'])) {
    // Logged-in user: Fetch quantity from add_to_cart table
    $stmt = $pdo->prepare("
        SELECT SUM(quantity) as total
        FROM add_to_cart
        WHERE user_id = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $qty = $result['total'] ?? 0;

    // Fetch user profile picture and username if available
    $stmt = $pdo->prepare("SELECT profile_picture, username FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $profile_picture = $user['profile_picture'] ?? null;
    $username = $user['username'] ?? 'User';
} elseif (isset($_SESSION['cart'])) {
    // Guest user: Calculate from session cart
    foreach ($_SESSION['cart'] as $item) {
        $qty += $item['qty'];
    }
}
?>

<nav class="navbar navbar-expand-lg navbar-light bg-white py-3 sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="Index.php" style="color: var(--velora-primary);">
            <div class="logo-container d-flex align-items-center">
                <img src="../Img/11.png" alt="Logo" width="40" height="30" class="me-2">
                Velora - Mindful Healing
            </div>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="Index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="ServicePage.php">Treatments</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="Therapists.php">Therapists</a>
                </li>
            </ul>

            <div class="d-flex align-items-center">
                <div class="d-none d-lg-block me-3">
                    <form action="Search.php" method="GET" class="search-form">
                        <div class="input-group">
                            <input type="text" name="query" placeholder="Search..." class="form-control">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <a href="Cart.php" class="cart-link position-relative me-3" style="color:rgb(16, 170, 70);">
                    <i class="fa-solid fa-cart-shopping fa-lg"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        <?= $qty ?>
                    </span>
                </a>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="dropdown">
                        <a href="#" class="profile-link dropdown-toggle" id="profileDropdown" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <?php if ($profile_picture): ?>
                                <img src="data:image/jpeg;base64,<?= base64_encode($profile_picture) ?>" alt="Profile Picture"
                                    class="rounded-circle border border-2"
                                    style="width: 36px; height: 36px; object-fit: cover; border-color: rgb(16, 170, 70);">
                            <?php else: ?>
                                <i class="fas fa-user-circle fa-2x" style="color:rgb(16, 170, 70);"></i>
                            <?php endif; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end profile-dropdown" aria-labelledby="profileDropdown">
                            <li class="dropdown-header text-center py-3 bg-light border-bottom">
                                <div class="d-flex flex-column align-items-center">
                                    <?php if ($profile_picture): ?>
                                        <img src="data:image/jpeg;base64,<?= base64_encode($profile_picture) ?>"
                                            alt="Profile Picture" class="rounded-circle mb-2"
                                            style="width: 60px; height: 60px; object-fit: cover; border: 3px solid rgb(16, 170, 70);">
                                    <?php else: ?>
                                        <i class="fas fa-user-circle fa-3x mb-2" style="color: rgb(16, 170, 70);"></i>
                                    <?php endif; ?>
                                    <span class="fw-bold text-dark"><?= htmlspecialchars($username) ?></span>
                                </div>
                            </li>
                            <li><a class="dropdown-item" href="Profile.php"><i class="fas fa-user me-2"></i> My Profile</a>
                            </li>
                            <li><a class="dropdown-item" href="Wishlist.php"><i class="fas fa-seedling me-2"></i>
                                    Wishlist</a></li>
                            <li>
                                <div class="dropdown-item">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="dark-mode-toggle">
                                        <label class="form-check-label" for="dark-mode-toggle"><i
                                                class="fas fa-moon me-2"></i> Dark Mode</label>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item text-danger" href="Logout.php"><i
                                        class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="Login.php" class="btn btn-outline-primary me-2">Log In</a>
                    <a href="Register.php" class="btn btn-primary">Sign Up Free</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<style>
    .profile-dropdown {
        min-width: 220px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        border: none;
        overflow: hidden;
        animation: dropdownFadeIn 0.2s ease-out;
    }

    .profile-dropdown .dropdown-header {
        background: #f8f9fa;
        padding: 15px;
    }

    .profile-dropdown .dropdown-item {
        padding: 10px 15px;
        font-size: 0.95rem;
        color: #333;
        transition: all 0.3s ease;
    }

    .profile-dropdown .dropdown-item:hover {
        background-color: rgb(16, 170, 70);
        color: #fff;
    }

    .profile-dropdown .dropdown-item i {
        width: 20px;
        text-align: center;
    }

    .profile-dropdown .dropdown-divider {
        margin: 5px 0;
        border-color: #e0e0e0;
    }

    .profile-dropdown .text-danger {
        color: #dc3545 !important;
    }

    .profile-dropdown .text-danger:hover {
        background-color: #dc3545 !important;
        color: #fff !important;
    }

    @keyframes dropdownFadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .profile-link.dropdown-toggle::after {
        display: none;
    }

    .profile-link:hover {
        opacity: 0.8;
    }

    body.dark-mode {
        background-color: #121212;
        color: #f8f8f2;
    }

    body.dark-mode .navbar {
        background-color: #1e1e1e !important;
        color: #f8f8f2 !important;
    }

    body.dark-mode .navbar-brand {
        color: #f8f8f2 !important;
    }

    body.dark-mode .nav-link {
        color: #f8f8f2 !important;
    }

    body.dark-mode .btn-outline-primary {
        color: #bb86fc;
        border-color: #bb86fc;
    }

    body.dark-mode .btn-outline-primary:hover {
        background-color: #bb86fc;
        color: #121212;
    }

    body.dark-mode .btn-primary {
        background-color: #bb86fc;
        border-color: #bb86fc;
        color: #121212;
    }

    body.dark-mode .btn-primary:hover {
        background-color: #9c66da;
        border-color: #9c66da;
    }

    body.dark-mode .dropdown-menu {
        background-color: #1e1e1e;
        border-color: #333;
    }

    body.dark-mode .dropdown-item {
        color: #f8f8f2;
    }

    body.dark-mode .dropdown-item:hover {
        background-color: #333;
        color: #f8f8f2;
    }

    body.dark-mode .dropdown-header {
        background-color: #333;
        color: #f8f8f2;
        border-bottom-color: #555;
    }

    body.dark-mode .profile-dropdown {
        box-shadow: 0 4px 12px rgba(255, 255, 255, 0.15);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const darkModeToggle = document.getElementById('dark-mode-toggle');
        const body = document.body;

        // Load saved theme from localStorage
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark') {
            body.classList.add('dark-mode');
            if (darkModeToggle) {
                darkModeToggle.checked = true;
            }
        }

        // Event listener for dark mode toggle
        if (darkModeToggle) {
            darkModeToggle.addEventListener('change', function () {
                body.classList.toggle('dark-mode');
                const theme = body.classList.contains('dark-mode') ? 'dark' : 'light';
                localStorage.setItem('theme', theme);
            });
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>