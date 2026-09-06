<?php
include("Connect.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Service</title>
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

        .image-preview img {
            max-width: 100%;
            max-height: 200px;
            margin-top: 10px;
            border-radius: 0.5rem;
        }
    </style>
</head>

<body class="bg-gray-100">
    <?php include 'AdminNavbar.php'; ?>
    <?php include 'AdminSidebar.php'; ?>
    <div class="content">
        <div class="container mx-auto px-4 py-8">
            <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-lg p-8">
                <a href="AdminDashboard.php" class="inline-block mb-6 text-blue-600 hover:text-blue-800">
                    &larr; Back to Dashboard
                </a>
                <h2 class="text-2xl font-bold mb-6 text-gray-800">Create New Service</h2>
                <form method="post" action="InsertServices.php" enctype="multipart/form-data" class="space-y-6">
                    <div>
                        <label for="service_id" class="block text-sm font-medium text-gray-700">Service ID</label>
                        <input type="number" name="sid" id="service_id" placeholder="Enter Service ID"
                            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="service_name" class="block text-sm font-medium text-gray-700">Service Name</label>
                        <input type="text" name="sname" id="service_name" placeholder="Enter Service Name"
                            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                        <select name="scategory" id="category"
                            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="Guided Meditation">Guided Meditation</option>
                            <option value="Stress Management">Stress Management</option>
                            <option value="Mindfulness Training">Mindfulness Training</option>
                            <option value="Work-Life Balance">Work-Life Balance</option>
                            <option value="Relaxation Techniques">Relaxation Techniques</option>
                        </select>
                    </div>

                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
                        <input type="text" name="price" id="price" placeholder="Enter Price"
                            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="duration" class="block text-sm font-medium text-gray-700">Duration</label>
                        <input type="text" name="duration" id="duration" placeholder="Enter Duration"
                            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="description" rows="3" placeholder="Enter description"
                            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>

                    <div>
                        <label for="service_image" class="block text-sm font-medium text-gray-700">Service Image</label>
                        <div class="mt-1 flex items-center">
                            <input type="file" name="service_image" id="service_image" accept="image/*"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>
                        <div id="image-preview" class="image-preview">
                            <img id="preview" src="#" alt="Image Preview" class="mt-2 hidden">
                        </div>
                        <p class="mt-2 text-sm text-gray-500">Upload a high-quality image of the service (JPG, PNG, or
                            GIF).</p>
                    </div>

                    <div class="text-right">
                        <button type="submit" name="submit" id="submit-btn"
                            class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <span id="btn-text">Create Service</span>
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

        // Image Preview Script
        const serviceImageInput = document.getElementById('service_image');
        const imagePreview = document.getElementById('preview');

        serviceImageInput.addEventListener('change', function (event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    imagePreview.src = e.target.result;
                    imagePreview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                imagePreview.src = "#";
                imagePreview.classList.add('hidden');
            }
        });
    </script>
</body>

</html>