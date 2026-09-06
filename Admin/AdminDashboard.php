<?php
// AdminDashboard.php

// Start the session
session_start();

if (isset($_GET['logout']) && $_GET['logout'] === 'true') {
    session_unset();
    session_destroy();
    header("Location: Login.php");
    exit();
}

// Check if the user is logged in and has the role of 'admin'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: Login.php");
    exit();
}

// Include database connection
include 'Connect.php';

// Handle payment approval
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve_payment'])) {
    $payment_id = $_POST['payment_id'];
    try {
        $stmt = $pdo->prepare("UPDATE payments SET status = 'completed' WHERE payment_id = ?");
        $stmt->execute([$payment_id]);
        // Redirect to avoid form resubmission
        header("Location: AdminDashboard.php");
        exit();
    } catch (PDOException $e) {
        echo "Error approving payment: " . $e->getMessage();
        exit();
    }
}

// Fetch dashboard data
try {
    // Total Completed Revenue (This Year)
    $stmt = $pdo->prepare("
        SELECT SUM(amount) as completed_revenue 
        FROM payments 
        WHERE YEAR(payment_date) = YEAR(CURDATE()) 
        AND status = 'completed'
    ");
    $stmt->execute();
    $completed_revenue = $stmt->fetch(PDO::FETCH_ASSOC)['completed_revenue'] ?? 0;

    // Total Pending Revenue (This Year)
    $stmt = $pdo->prepare("
        SELECT SUM(amount) as pending_revenue 
        FROM payments 
        WHERE YEAR(payment_date) = YEAR(CURDATE()) 
        AND status = 'pending'
    ");
    $stmt->execute();
    $pending_revenue = $stmt->fetch(PDO::FETCH_ASSOC)['pending_revenue'] ?? 0;

    // Appointments (This Month)
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as appointment_count 
        FROM appointments 
        WHERE MONTH(appointment_date) = MONTH(CURDATE()) 
        AND YEAR(appointment_date) = YEAR(CURDATE())
    ");
    $stmt->execute();
    $appointment_count = $stmt->fetch(PDO::FETCH_ASSOC)['appointment_count'] ?? 0;

    // New Users (This Month)
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as new_users 
        FROM users 
        WHERE MONTH(created_at) = MONTH(CURDATE()) 
        AND YEAR(created_at) = YEAR(CURDATE())
    ");
    $stmt->execute();
    $new_users = $stmt->fetch(PDO::FETCH_ASSOC)['new_users'] ?? 0;

    // Top Booked Service (This Month)
    $stmt = $pdo->prepare("
        SELECT s.service_name, COUNT(a.service_id) as booking_count 
        FROM appointments a 
        JOIN services s ON a.service_id = s.service_id 
        WHERE MONTH(a.appointment_date) = MONTH(CURDATE()) 
        AND YEAR(a.appointment_date) = YEAR(CURDATE()) 
        GROUP BY a.service_id, s.service_name 
        ORDER BY booking_count DESC 
        LIMIT 1
    ");
    $stmt->execute();
    $top_service = $stmt->fetch(PDO::FETCH_ASSOC);
    $top_service_name = $top_service ? $top_service['service_name'] : 'N/A';

    // Service Distribution Data (for Pie Chart)
    $stmt = $pdo->prepare("
        SELECT s.service_name, COUNT(a.appointment_id) as count
        FROM appointments a
        JOIN services s ON a.service_id = s.service_id
        WHERE MONTH(a.appointment_date) = MONTH(CURDATE())
        GROUP BY s.service_name
    ");
    $stmt->execute();
    $service_distribution = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $service_labels = array_column($service_distribution, 'service_name');
    $service_data = array_column($service_distribution, 'count');
    $service_colors = [];
    foreach ($service_labels as $index => $label) {
        $service_colors[] = sprintf('hsl(%d, 70%%, 60%%)', ($index * 360 / count($service_labels)));
    }

    // Appointment Status Data (for Bar Chart)
    $stmt = $pdo->prepare("
        SELECT status, COUNT(*) as count
        FROM appointments
        WHERE MONTH(appointment_date) = MONTH(CURDATE())
        GROUP BY status
    ");
    $stmt->execute();
    $status_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $status_labels = array_column($status_data, 'status');
    $status_counts = array_column($status_data, 'count');
    $status_colors = [
        'pending' => 'rgba(255, 206, 86, 0.7)',
        'completed' => 'rgba(75, 192, 192, 0.7)',
        'cancelled' => 'rgba(255, 99, 132, 0.7)'
    ];
    $status_bg_colors = [];
    foreach ($status_labels as $status) {
        $status_bg_colors[] = $status_colors[strtolower($status)] ?? 'rgba(153, 102, 255, 0.7)';
    }

    // Fetch Services List
    $stmt = $pdo->prepare("SELECT service_name, service_price FROM services ORDER BY service_id ASC LIMIT 5");
    $stmt->execute();
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch Pending Payments
    $stmt = $pdo->prepare("
        SELECT payment_id, user_id, appointment_id, amount, payment_date 
        FROM payments 
        WHERE status = 'pending' 
        ORDER BY payment_date DESC
    ");
    $stmt->execute();
    $pending_payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch Appointment History
    $stmt = $pdo->prepare("
        SELECT 
            a.appointment_id,
            u.username,
            s.service_name,
            t.therapist_name,
            a.appointment_date,
            a.status,
            p.amount,
            p.payment_method
        FROM appointments a
        LEFT JOIN users u ON a.user_id = u.user_id
        LEFT JOIN services s ON a.service_id = s.service_id
        LEFT JOIN therapists t ON a.therapist_id = t.therapist_id
        LEFT JOIN payments p ON a.appointment_id = p.appointment_id
        ORDER BY a.appointment_date DESC
        LIMIT 10
    ");
    $stmt->execute();
    $appointment_history = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch Top Ten Customers (based on total spending)
    $stmt = $pdo->prepare("
        SELECT 
            u.user_id,
            u.username,
            u.email,
            COUNT(a.appointment_id) as appointment_count,
            COALESCE(SUM(p.amount), 0) as total_spent
        FROM users u
        LEFT JOIN appointments a ON u.user_id = a.user_id
        LEFT JOIN payments p ON a.appointment_id = p.appointment_id AND p.status = 'completed'
        GROUP BY u.user_id, u.username, u.email
        ORDER BY total_spent DESC, appointment_count DESC
        LIMIT 10
    ");
    $stmt->execute();
    $top_customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Velora - Mindful Healing</title>
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

        .chart-container {
            position: relative;
            height: 300px;
        }
        
        /* Custom scrollbar for tables */
        .table-container::-webkit-scrollbar {
            height: 8px;
        }
        
        .table-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        .table-container::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }
        
        .table-container::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-gray-100">
    <?php include 'AdminNavbar.php'; ?>
    <?php include 'AdminSidebar.php'; ?>

    <div class="content">
        <div class="max-w-7xl mx-auto">
            <h1 class="text-3xl font-bold mb-8">Admin Dashboard</h1>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="bg-white shadow-md rounded-lg p-4">
                    <h2 class="text-lg font-semibold mb-2">Total Revenue (This Year)</h2>
                    <p class="text-2xl font-bold">MMK <?= number_format($completed_revenue, 2) ?></p>
                    <p class="text-sm text-gray-500">Pending: MMK <?= number_format($pending_revenue, 2) ?></p>
                </div>

                <div class="bg-white shadow-md rounded-lg p-4">
                    <h2 class="text-lg font-semibold mb-2">Appointments (This Month)</h2>
                    <p class="text-2xl font-bold"><?= $appointment_count ?></p>
                </div>

                <div class="bg-white shadow-md rounded-lg p-4">
                    <h2 class="text-lg font-semibold mb-2">New Users (This Month)</h2>
                    <p class="text-2xl font-bold"><?= $new_users ?></p>
                </div>

                <div class="bg-white shadow-md rounded-lg p-4">
                    <h2 class="text-lg font-semibold mb-2">Top Booked Service</h2>
                    <p class="text-xl font-bold"><?= htmlspecialchars($top_service_name) ?></p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-8">
                <div class="bg-white shadow-md rounded-lg p-4">
                    <h2 class="text-lg font-semibold mb-4">Service Distribution (This Month)</h2>
                    <div class="chart-container">
                        <canvas id="serviceChart"></canvas>
                    </div>
                </div>

                <div class="bg-white shadow-md rounded-lg p-4">
                    <h2 class="text-lg font-semibold mb-4">Appointment Status (This Month)</h2>
                    <div class="chart-container">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-md rounded-lg p-4 mb-8">
                <h2 class="text-lg font-semibold mb-4">Quick Actions</h2>
                <div class="flex flex-wrap gap-4">
                    <a href="CreateServices.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Add New Service</a>
                    <a href="ViewAppointments.php" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">View Appointments</a>
                    <a href="UserTable.php" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">Manage Users</a>
                    <a href="TherapistTable.php" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">Manage Therapists</a>
                </div>
            </div>

            <div class="bg-white shadow-md rounded-lg p-4 mb-8">
                <h2 class="text-lg font-semibold mb-4">Pending Payments</h2>
                <?php if (empty($pending_payments)): ?>
                    <p class="text-gray-500">No pending payments to approve.</p>
                <?php else: ?>
                    <div class="table-container overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment ID</th>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User ID</th>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Appointment ID</th>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($pending_payments as $payment): ?>
                                    <tr>
                                        <td class="px-3 py-2 whitespace-nowrap"><?= htmlspecialchars($payment['payment_id']) ?></td>
                                        <td class="px-3 py-2 whitespace-nowrap"><?= htmlspecialchars($payment['user_id']) ?></td>
                                        <td class="px-3 py-2 whitespace-nowrap"><?= htmlspecialchars($payment['appointment_id']) ?></td>
                                        <td class="px-3 py-2 whitespace-nowrap">MMK <?= number_format($payment['amount'], 2) ?></td>
                                        <td class="px-3 py-2 whitespace-nowrap"><?= date('M j, Y', strtotime($payment['payment_date'])) ?></td>
                                        <td class="px-3 py-2 whitespace-nowrap">
                                            <form method="POST" action="">
                                                <input type="hidden" name="payment_id" value="<?= $payment['payment_id'] ?>">
                                                <button type="submit" name="approve_payment" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded text-xs">Approve</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <div class="bg-white shadow-md rounded-lg p-4 mb-8">
                <h2 class="text-lg font-semibold mb-4">Top Ten Customers</h2>
                <?php if (empty($top_customers)): ?>
                    <p class="text-gray-500">No customer data available.</p>
                <?php else: ?>
                    <div class="table-container overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Appointments</th>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Spent</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($top_customers as $index => $customer): ?>
                                    <tr>
                                        <td class="px-3 py-2 whitespace-nowrap"><?= $index + 1 ?></td>
                                        <td class="px-3 py-2 whitespace-nowrap">
                                            <div class="font-medium text-gray-900"><?= htmlspecialchars($customer['username']) ?></div>
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap">
                                            <div class="text-gray-900"><?= htmlspecialchars($customer['email']) ?></div>
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap">
                                            <div class="text-gray-900"><?= htmlspecialchars($customer['appointment_count']) ?></div>
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap">
                                            <div class="font-medium text-gray-900">MMK <?= number_format($customer['total_spent'], 2) ?></div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <div class="bg-white shadow-md rounded-lg p-4 mb-8">
                <h2 class="text-lg font-semibold mb-4">Appointment History (Recent 10)</h2>
                <?php if (empty($appointment_history)): ?>
                    <p class="text-gray-500">No appointment history available.</p>
                <?php else: ?>
                    <div class="table-container overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Therapist</th>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($appointment_history as $appointment): ?>
                                    <tr>
                                        <td class="px-3 py-2 whitespace-nowrap"><?= htmlspecialchars($appointment['appointment_id']) ?></td>
                                        <td class="px-3 py-2 whitespace-nowrap"><?= htmlspecialchars($appointment['username'] ?? 'N/A') ?></td>
                                        <td class="px-3 py-2 whitespace-nowrap"><?= htmlspecialchars($appointment['service_name'] ?? 'N/A') ?></td>
                                        <td class="px-3 py-2 whitespace-nowrap"><?= htmlspecialchars($appointment['therapist_name'] ?? 'N/A') ?></td>
                                        <td class="px-3 py-2 whitespace-nowrap"><?= date('M j, Y', strtotime($appointment['appointment_date'])) ?></td>
                                        <td class="px-3 py-2 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                <?= $appointment['status'] === 'completed' ? 'bg-green-100 text-green-800' : 
                                                   ($appointment['status'] === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') ?>">
                                                <?= htmlspecialchars($appointment['status']) ?>
                                            </span>
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap">MMK <?= number_format($appointment['amount'] ?? 0, 2) ?></td>
                                        <td class="px-3 py-2 whitespace-nowrap">
                                            <span class="text-xs font-medium"><?= htmlspecialchars($appointment['payment_method'] ?? 'N/A') ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <div class="bg-white shadow-md rounded-lg p-4">
                <h2 class="text-lg font-semibold mb-4">Services List</h2>
                <div class="table-container overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service Name</th>
                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($services as $service): ?>
                                <tr>
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        <div class="font-medium text-gray-900"><?= htmlspecialchars($service['service_name']) ?></div>
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        <div class="text-gray-900">MMK <?= number_format($service['service_price'], 2) ?></div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
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

        // Service Distribution Pie Chart
        const serviceCtx = document.getElementById('serviceChart').getContext('2d');
        const serviceChart = new Chart(serviceCtx, {
            type: 'pie',
            data: {
                labels: <?= json_encode($service_labels) ?>,
                datasets: [{
                    data: <?= json_encode($service_data) ?>,
                    backgroundColor: <?= json_encode($service_colors) ?>,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Appointment Status Bar Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        const statusChart = new Chart(statusCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($status_labels) ?>,
                datasets: [{
                    label: 'Appointments',
                    data: <?= json_encode($status_counts) ?>,
                    backgroundColor: <?= json_encode($status_bg_colors) ?>,
                    borderColor: <?= json_encode(array_map(function($c) { return str_replace('0.7', '1', $c); }, $status_bg_colors)) ?>,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    </script>
</body>

</html>