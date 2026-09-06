<?php
// AdminSidebar.php
include 'Connect.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit();
}

// $user_id = $_SESSION['user_id']; 
// Get the user ID from the session

try {
    $stmt = $pdo->prepare("SELECT profile_picture FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result && !empty($result['profile_picture'])) {

        $profile_picture = "data:image/jpeg;base64," . base64_encode($result['profile_picture']);
    } else {
        $profile_picture = "default_profile.jpg"; // Default image path
    }
} catch (PDOException $e) {
    // Handle database error (log or display a message)
    error_log("Database error in AdminSidebar.php: " . $e->getMessage());
    $profile_picture = "default_profile.jpg"; // Default image path if error occurs
}
?>

<div class="sidebar text-white" style="background: linear-gradient(90deg, rgb(0, 4, 16) 0%, rgb(3, 41, 101) 100%);">
    <nav class="p-6">
        <a href="../Customer/Index.php" class="block mb-6">
            <h3 class="text-2xl font-bold text-darkgreen-500 inline-block align-middle">Velora Mental Wellness</h3>
        </a>
        <div class="flex items-center mb-6">
            <img class="rounded-full w-10 h-10 mr-3" src="<?php echo $profile_picture; ?>" alt="User">
            <div>
                <?php
                if (isset($_SESSION['username'])) {
                    echo htmlspecialchars($_SESSION['username']); // Display the logged-in username
                } else {
                    echo "Guest";
                }
                ?><br>
                <span class="text-sm text-gray-300">Welcome!</span>
            </div>
        </div>
        <div class="space-y-2">
            <a href="AdminDashboard.php" class="block p-2 rounded hover:bg-gray-900"><i
                    class="fas fa-home mr-2"></i>Dashboard</a>
            <a href="CreateServices.php" class="block p-2 rounded hover:bg-gray-900"><i
                    class="fas fa-plus mr-2"></i>Create Service</a>
            <a href="ServiceTable.php" class="block p-2 rounded hover:bg-gray-900"><i
                    class="fas fa-table mr-2"></i>Services</a>
            <a href="CreateUsers.php" class="block p-2 rounded hover:bg-gray-900"><i class="fas fa-plus mr-2"></i>Create
                User</a>
            <a href="UserTable.php" class="block p-2 rounded hover:bg-gray-900"><i
                    class="fas fa-users mr-2"></i>Users</a>
            <a href="CreateTherapists.php" class="block p-2 rounded hover:bg-gray-900"><i
                    class="fas fa-plus mr-2"></i>Create Therapist</a>
            <a href="TherapistTable.php" class="block p-2 rounded hover:bg-gray-900"><i
                    class="fas fa-users mr-2"></i>Therapists</a>
            <a href="AdminProfile.php" class="block p-2 rounded hover:bg-gray-900"><i
                    class="fas fa-user mr-2"></i>Profile</a>
            <a href="Login.php" class="block p-2 rounded hover:bg-gray-900"><i
                    class="fas fa-sign-out-alt mr-2"></i>Logout</a>
        </div>
    </nav>
</div>