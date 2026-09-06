<?php
include 'Header.php';
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dark Mode Toggle</title>

</head>

<body class="light-mode">

    <!-- Dark Mode Toggle -->


    <!-- <label class="switch">
        <input type="checkbox" id="theme-toggle">
        <span class="slider"></span>
    </label> -->

    <label class="switch">
        <input type="checkbox" id="theme-toggle">
        <span class="slider"></span>
    </label>

    <script>
        const themeToggle = document.getElementById('theme-toggle');

        // Load saved theme from localStorage and apply on page load
        if (localStorage.getItem('theme') === 'dark') {
            document.body.classList.add('dark-mode');
            themeToggle.checked = true;
        }

        themeToggle.addEventListener('change', () => {
            document.body.classList.toggle('dark-mode');
            const theme = document.body.classList.contains('dark-mode') ? 'dark' : 'light';
            localStorage.setItem('theme', theme);
        });

        const logo = document.getElementById('logo');

        toggleButton.addEventListener('click', () => {
            body.classList.toggle('dark-mode');
            const isDarkMode = body.classList.contains('dark-mode');

            // Change logo based on mode
            logo.src = isDarkMode ? 'dark-logo.png' : 'light-logo.png';

            // Save the user's preference in localStorage
            localStorage.setItem('dark-mode', isDarkMode);
        });

        // Check for saved user preference on page load
        window.addEventListener('load', () => {
            const savedDarkMode = localStorage.getItem('dark-mode') === 'true';
            if (savedDarkMode) {
                body.classList.add('dark-mode');
                logo.src = 'dark-logo.png';
            }
        });
    </script>

</body>

</html>