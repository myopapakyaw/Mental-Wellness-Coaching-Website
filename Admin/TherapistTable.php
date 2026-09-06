<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit();
}

include("Connect.php");

// Search parameter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';


try {
    $sql = "SELECT therapist_id, therapist_name, email, specialization, profile_picture, biography FROM therapists WHERE 1=1";
    $params = [];
    if (!empty($search)) {
        $sql .= " AND (therapist_name LIKE :search OR email LIKE :search OR specialization LIKE :search OR biography LIKE :search)";
        $params[':search'] = "%$search%";
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $therapists = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $therapists = [];
    $error_message = "Error fetching therapists: " . $e->getMessage();
}

// Status messages
$status = isset($_GET['status']) ? $_GET['status'] : '';
$message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Therapist Management</title>
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
            transition: width 0.3s ease;
        }
        .sidebar.collapsed { width: 0; overflow: hidden; }
        .navbar {
            width: calc(100% - 250px);
            margin-left: 250px;
            position: fixed;
            top: 0;
            z-index: 1000;
            transition: all 0.3s ease;
        }
        .navbar.collapsed { margin-left: 0; width: 100%; }
        .content {
            margin-left: 250px;
            margin-top: 70px;
            padding: 2rem;
            transition: margin-left 0.3s ease;
        }
        .content.collapsed { margin-left: 0; }

        /* Table Styling */
        .table-container {
            background: #ffffff;
            border-radius: 1rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        th, td {
            padding: 1.25rem 1rem;
            text-align: left;
            vertical-align: middle;
            border-bottom: 1px solid #e5e7eb;
        }
        th {
            background: #f9fafb;
            color: #4b5563;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        td { color: #1f2937; font-size: 0.875rem; }
        tr:hover { background-color: #f3f4f6; transition: background-color 0.2s ease; }
        .profile-pic {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 0.5rem;
        }
        td.actions a {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }
        td.actions a.edit { color: #2563eb; }
        td.actions a.edit:hover { background: #dbeafe; color: #1e40af; }
        td.actions a.delete { color: #dc2626; }
        td.actions a.delete:hover { background: #fee2e2; color: #b91c1c; }

        /* Biography Styling */
        .bio-text {
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: pointer;
        }
        .bio-text.expanded {
            white-space: normal;
            overflow: visible;
            text-overflow: clip;
        }

        /* Enhanced Search Bar */
        .search-bar {
            position: relative;
            display: flex;
            align-items: center;
            width: 300px;
            background: linear-gradient(135deg, #ffffff, #f9fafb);
            border-radius: 2rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 0.5rem;
            transition: all 0.3s ease;
        }
        .search-bar:hover, .search-bar:focus-within {
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            width: 350px;
        }
        .search-bar input {
            flex: 1;
            padding: 0.75rem 1rem;
            border: none;
            background: transparent;
            outline: none;
            font-size: 0.875rem;
            color: #1f2937;
            width: 100%;
        }
        .search-bar input::placeholder {
            color: #9ca3af;
            font-style: italic;
        }
        .search-bar button {
            padding: 0.75rem;
            background: #3b82f6;
            color: white;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s ease;
        }
        .search-bar button:hover {
            background: #2563eb;
        }
        .search-bar .clear-btn {
            position: absolute;
            right: 3.5rem;
            top: 50%;
            transform: translateY(-50%);
            background: #e5e7eb;
            color: #6b7280;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: <?php echo empty($search) ? 'none' : 'flex'; ?>;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.2s ease;
        }
        .search-bar .clear-btn:hover {
            background: #d1d5db;
            color: #374151;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .search-bar {
                width: 100%;
                max-width: 250px;
            }
            .search-bar:hover, .search-bar:focus-within {
                width: 100%;
                max-width: 300px;
            }
            .table-container { overflow-x: auto; }
            th, td { padding: 0.75rem; font-size: 0.75rem; }
            .profile-pic { width: 40px; height: 40px; }
            td.actions a { padding: 0.25rem 0.5rem; }
            .bio-text { max-width: 150px; }
        }
        
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased">
    <?php include 'AdminNavbar.php'; ?>
    <?php include 'AdminSidebar.php'; ?>

    <div class="content">
        <div class="container mx-auto px-4 py-8 max-w-7xl">
            <!-- Header and Search -->
            <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                <h1 class="text-3xl font-bold text-gray-800">Therapist List</h1>
                <div class="flex items-center gap-4">
                    <form method="GET" class="search-bar">
                        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                               placeholder="Search therapists..." id="search-input">
                        <span class="clear-btn" onclick="clearSearch()">
                            <i class="fas fa-times"></i>
                        </span>
                        <button type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                    <a href="CreateTherapists.php" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                        <i class="fas fa-plus mr-2"></i> Add Therapist
                    </a>
                </div>
            </div>

            <!-- Table -->
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Profile Picture</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Specialization</th>
                            <th>Biography</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($therapists)): ?>
                            <tr>
                                <td colspan="7" class="py-6 text-center text-gray-500">No therapists found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($therapists as $therapist): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($therapist['therapist_id']); ?></td>
                                    <td>
                                        <?php if (!empty($therapist['profile_picture'])): ?>
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($therapist['profile_picture']); ?>" alt="Profile Picture" class="profile-pic">
                                        <?php else: ?>
                                            <span class="text-gray-400 text-sm">No Image</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($therapist['therapist_name']); ?></td>
                                    <td><?php echo htmlspecialchars($therapist['email']); ?></td>
                                    <td><?php echo htmlspecialchars($therapist['specialization']); ?></td>
                                    <td class="biography">
                                        <span class="bio-text" title="<?php echo htmlspecialchars($therapist['biography'] ?: 'No biography available'); ?>">
                                            <?php echo htmlspecialchars(substr($therapist['biography'] ?? 'No biography available', 0, 50)) . (strlen($therapist['biography'] ?? '') > 50 ? '...' : ''); ?>
                                        </span>
                                    </td>
                                    <td class="actions">
                                        <a href="EditTherapist.php?id=<?php echo $therapist['therapist_id']; ?>" class="edit">Edit</a>
                                        <a href="DeleteTherapist.php?id=<?php echo $therapist['therapist_id']; ?>" class="delete ml-2" 
                                           onclick="return confirm('Are you sure you want to delete this therapist?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($status && $message): ?>
                <div class="mt-6 p-4 rounded-md <?php echo $status === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>" role="alert">
                    <span><?php echo $message; ?></span>
                    <span role="button" class="float-right cursor-pointer">×</span>
                </div>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
                <div class="mt-6 p-4 rounded-md bg-red-100 text-red-700" role="alert">
                    <span><?php echo $error_message; ?></span>
                    <span role="button" class="float-right cursor-pointer">×</span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        const sidebar = document.querySelector('.sidebar');
        const navbar = document.querySelector('.navbar');
        const content = document.querySelector('.content');
        const sidebarToggler = document.querySelector('.sidebar-toggler');

        sidebarToggler?.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            navbar.classList.toggle('collapsed');
            content.classList.toggle('collapsed');
        });

        function clearSearch() {
            const input = document.getElementById('search-input');
            input.value = '';
            input.form.submit();
        }

        document.querySelectorAll('[role="alert"]').forEach(alert => {
            let closeBtn = alert.querySelector('span[role="button"]');
            if (closeBtn) {
                closeBtn.addEventListener('click', () => {
                    alert.style.display = 'none';
                });
            }
        });

        // Toggle biography expansion
        document.querySelectorAll('.bio-text').forEach(bio => {
            bio.addEventListener('click', () => {
                bio.classList.toggle('expanded');
            });
        });
    </script>
</body>
</html>