<?php
session_start();
include 'Connect.php';

// Check if the user is an admin (assuming role is in users table)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: Login.php");
    exit();
}

// Handle approval action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve_appointment'])) {
    $appointment_id = $_POST['appointment_id'];
    try {
        $stmt = $pdo->prepare("UPDATE appointments SET status = 'confirmed' WHERE appointment_id = ?");
        $rowsAffected = $stmt->execute([$appointment_id]);
        if ($rowsAffected) {
            $_SESSION['success_message'] = "Appointment #$appointment_id has been approved.";
        } else {
            $_SESSION['error_message'] = "No rows updated for appointment #$appointment_id.";
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error approving appointment: " . $e->getMessage();
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

try {
    $stmt = $pdo->prepare("
        SELECT a.appointment_id, a.appointment_date, a.status, a.notes, s.service_name, t.therapist_name AS therapist_name, u.username AS user_name
        FROM appointments a
        LEFT JOIN services s ON a.service_id = s.service_id
        LEFT JOIN therapists t ON a.therapist_id = t.therapist_id
        LEFT JOIN users u ON a.user_id = u.user_id
        ORDER BY a.appointment_date DESC
    ");
    $stmt->execute();
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching appointments: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - View Appointments</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .fade-in { animation: fadeIn 0.5s ease-in; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
        .gradient-bg { background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); }
        .sidebar { width: 250px; height: 100vh; position: fixed; top: 0; left: 0; overflow-y: auto; transition: all 0.3s; }
        .sidebar.collapsed { width: 0; overflow: hidden; }
        .navbar { width: calc(100% - 250px); margin-left: 250px; position: fixed; top: 0; z-index: 1000; transition: all 0.3s; }
        .navbar.collapsed { margin-left: 0; width: 100%; }
        .content { margin-left: 250px; margin-top: 70px; padding: 2rem; transition: all 0.3s; }
        .content.collapsed { margin-left: 0; }
        .sidebar-toggler { cursor: pointer; }
    </style>
</head>
<body class="gradient-bg">
    <?php include 'AdminNavbar.php'; ?>
    <?php include 'AdminSidebar.php'; ?>

    <div class="content">
        <div class="bg-white p-8 rounded-xl shadow-2xl w-full max-w-6xl fade-in">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-extrabold text-gray-900">All Appointments</h2>
                <p class="mt-2 text-sm text-gray-600">Here are all the appointments in the system.</p>
            </div>

            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-lg">
                    <?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-lg animate-pulse">
                    <?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
                </div>
            <?php endif; ?>

            <p class="text-gray-500">Found <?php echo count($appointments); ?> appointments.</p>

            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Therapist</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (empty($appointments)): ?>
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">No appointments found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($appointments as $appointment): ?>
                                <tr class="hover:bg-gray-50 transition duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($appointment['user_name'] ?? 'Unknown'); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars(date('M j, Y h:i A', strtotime($appointment['appointment_date']))); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($appointment['service_name'] ?? 'Unknown'); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($appointment['therapist_name'] ?? 'Not Assigned'); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-sm font-semibold rounded-full 
                                            <?php echo $appointment['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                                  ($appointment['status'] === 'confirmed' ? 'bg-green-100 text-green-800' : 
                                                  'bg-red-100 text-red-800'); ?>">
                                            <?php echo htmlspecialchars(ucfirst($appointment['status'])); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-normal"><?php echo htmlspecialchars($appointment['notes'] ?? 'No notes'); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if ($appointment['status'] === 'pending'): ?>
                                            <form method="POST" action="">
                                                <input type="hidden" name="appointment_id" value="<?php echo $appointment['appointment_id']; ?>">
                                                <button type="submit" name="approve_appointment" class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 transition duration-200">
                                                    Approve
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-gray-500">No actions available</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.querySelector('.sidebar');
            const navbar = document.querySelector('.navbar');
            const content = document.querySelector('.content');
            const toggler = document.querySelector('.sidebar-toggler');
            if (toggler) {
                toggler.addEventListener('click', function () {
                    sidebar.classList.toggle('collapsed');
                    navbar.classList.toggle('collapsed');
                    content.classList.toggle('collapsed');
                });
            }
        });
    </script>
</body>
</html>