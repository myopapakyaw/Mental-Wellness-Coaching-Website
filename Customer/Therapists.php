<?php
session_start();
include("Header.php");
include("Nav.php");

// PDO Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "myopapakyaw_mental_wellness_service";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("Fail to connect: " . $e->getMessage());
}

// Fetch therapists with biography
$stmt = $pdo->prepare("SELECT therapist_id, therapist_name, specialization, profile_picture, biography FROM therapists");
$stmt->execute();
$therapists = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Therapists - Mental Wellness Coaching</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #2a5d54;
            --primary-light: #3a7d6e;
            --secondary: #f8f9fa;
            --accent: #ff7e5f;
            --text: #2d3748;
            --text-light: #4a5568;
            --white: #ffffff;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        /* * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        } */

        body {
            /* font-family: 'Poppins', sans-serif; */
            color: var(--text);
            line-height: 1.6;
            background-color: #f5f7fa;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Added space below navigation */
        .therapists-page {
            flex: 1;
            padding: 80px 0 40px;
            /* Increased top padding to 80px */
            position: relative;
            overflow-x: hidden;
            margin-top: 20px;
            /* Additional margin for safety */
        }

        .page-header {
            text-align: center;
            margin-bottom: 40px;
            position: relative;
            padding: 0 20px;
        }

        .page-header::after {
            content: '';
            display: block;
            width: 80px;
            height: 4px;
            background: var(--accent);
            margin: 15px auto 0;
            border-radius: 2px;
        }

        .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 10px;
            position: relative;
        }

        .page-subtitle {
            font-size: 1.1rem;
            color: var(--text-light);
            max-width: 700px;
            margin: 0 auto;
        }

        .therapists-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .therapists-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }

        .therapist-card {
            background: var(--white);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
            position: relative;
            padding-top: 20px;
        }

        .therapist-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .therapist-image-container {
            height: 200px;
            width: 200px;
            /* Makes it square */
            margin-top: 10px;
            margin: 0 auto;
            /* Centers it */
            border-radius: 20%;
            /* Makes it circular */
            overflow: hidden;
            position: relative;
            /* border: 4px solid var(--primary); */
            /* Optional border */
        }

        .therapist-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }

        .therapist-card:hover .therapist-image {
            transform: scale(1.05);
        }



        .therapist-info {
            padding: 25px;
        }

        .therapist-name {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 5px;
        }

        .therapist-specialization {
            display: inline-block;
            font-size: 0.9rem;
            color: var(--white);
            background: var(--primary);
            padding: 3px 12px;
            border-radius: 20px;
            margin-bottom: 15px;
        }

        .therapist-bio {
            font-size: 0.95rem;
            color: var(--text-light);
            margin-bottom: 20px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .therapist-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 30px;
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
            cursor: pointer;
            border: none;
            font-size: 0.9rem;
        }

        .btn-primary {
            background: var(--primary);
            color: var(--white);
        }

        .btn-primary:hover {
            background: var(--primary-light);
            transform: translateY(-2px);
        }

        .btn-outline {
            background: transparent;
            color: var(--primary);
            border: 2px solid var(--primary);
        }

        .btn-outline:hover {
            background: var(--primary);
            color: var(--white);
        }

        .social-links {
            display: flex;
            gap: 10px;
        }

        .social-link {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: #f0f2f5;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            transition: var(--transition);
        }

        .social-link:hover {
            background: var(--primary);
            color: var(--white);
            transform: translateY(-3px);
        }

        .no-therapists {
            text-align: center;
            padding: 50px;
            background: var(--white);
            border-radius: 12px;
            box-shadow: var(--shadow);
            max-width: 800px;
            margin: 0 auto;
        }

        .no-therapists i {
            font-size: 3rem;
            color: var(--primary);
            margin-bottom: 20px;
        }

        .no-therapists h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: var(--primary);
        }

        .no-therapists p {
            margin-bottom: 25px;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 80px;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .modal.active {
            display: flex;
            opacity: 1;
        }

        .modal-content {
            background: var(--white);
            border-radius: 12px;
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            position: relative;
            transform: translateY(20px);
            transition: transform 0.3s ease;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .modal.active .modal-content {
            transform: translateY(0);
        }

        .close-modal {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 1.5rem;
            color: var(--text-light);
            cursor: pointer;
            transition: var(--transition);
        }

        .close-modal:hover {
            color: var(--primary);
            transform: rotate(90deg);
        }

        .modal-header {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .modal-image {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 20px;
            border: 4px solid var(--secondary);
        }

        .modal-title {
            font-size: 1.5rem;
            color: var(--primary);
            margin-bottom: 5px;
        }

        .modal-specialization {
            font-size: 0.9rem;
            color: var(--text-light);
            margin-bottom: 10px;
        }

        .modal-bio {
            line-height: 1.7;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }

        .modal-bio h4 {
            font-size: 1.1rem;
            margin: 15px 0 10px;
            color: var(--primary);
        }

        .modal-bio ul {
            padding-left: 20px;
            margin: 10px 0;
        }

        .modal-bio li {
            margin-bottom: 8px;
        }


        /* Responsive Styles */
        @media (max-width: 992px) {
            .therapists-grid {
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .page-title {
                font-size: 2rem;
            }

            .page-subtitle {
                font-size: 1rem;
            }

            .modal-header {
                flex-direction: column;
                text-align: center;
            }

            .modal-image {
                margin-right: 0;
                margin-bottom: 15px;
            }

            .modal-content {
                padding: 20px;
            }

            /* Adjusted spacing for mobile */
            .therapists-page {
                padding: 60px 0 30px;
            }
        }

        @media (max-width: 576px) {
            .therapists-grid {
                grid-template-columns: 1fr;
            }

            .page-title {
                font-size: 1.8rem;
            }

            .therapist-actions {
                flex-direction: column;
                gap: 15px;
            }

            .btn {
                width: 100%;
                text-align: center;
            }

            .modal-image {
                width: 80px;
                height: 80px;
            }

            .modal-title {
                font-size: 1.3rem;
            }

            /* Further adjusted spacing for small mobile */
            .therapists-page {
                padding: 50px 0 25px;
                margin-top: 15px;
            }
        }

        /* Animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .therapist-card {
            animation: fadeIn 0.5s ease forwards;
            opacity: 0;
        }

        .therapist-card:nth-child(1) {
            animation-delay: 0.1s;
        }

        .therapist-card:nth-child(2) {
            animation-delay: 0.2s;
        }

        .therapist-card:nth-child(3) {
            animation-delay: 0.3s;
        }

        .therapist-card:nth-child(4) {
            animation-delay: 0.4s;
        }

        .therapist-card:nth-child(5) {
            animation-delay: 0.5s;
        }

        .therapist-card:nth-child(6) {
            animation-delay: 0.6s;
        }
    </style>
</head>

<body>
    <div class="therapists-page">
        <div class="page-header">
            <h1 class="page-title">Our Expert Therapists</h1>
            <p class="page-subtitle">Meet our team of licensed professionals dedicated to your mental wellness journey
            </p>
        </div>

        <div class="therapists-container">
            <?php if (count($therapists) > 0): ?>
                <div class="therapists-grid">
                    <?php foreach ($therapists as $therapist):
                        $imageData = base64_encode($therapist['profile_picture']);
                        $imageSrc = "data:image/jpeg;base64," . $imageData;
                        $bio = htmlspecialchars($therapist['biography'] ?: 'Our therapist is currently updating their biography. Check back soon for more information.');
                        $modalId = "modal-" . $therapist['therapist_id'];
                        ?>
                        <div class="therapist-card">
                            <div class="therapist-image-container">
                                <img src="<?php echo $imageSrc; ?>"
                                    alt="<?php echo htmlspecialchars($therapist['therapist_name']); ?>" class="therapist-image">

                            </div>
                            <div class="therapist-info">
                                <h3 class="therapist-name"><?php echo htmlspecialchars($therapist['therapist_name']); ?></h3>
                                <span
                                    class="therapist-specialization"><?php echo htmlspecialchars($therapist['specialization'] ?: 'Mental Health Specialist'); ?></span>
                                <p class="therapist-bio"><?php echo $bio; ?></p>
                                <div class="therapist-actions">
                                    <button class="btn btn-outline" onclick="openModal('<?php echo $modalId; ?>')">View
                                        Profile</button>
                                </div>
                            </div>
                        </div>

                        <!-- Modal -->
                        <div id="<?php echo $modalId; ?>" class="modal">
                            <div class="modal-content">
                                <span class="close-modal" onclick="closeModal('<?php echo $modalId; ?>')">&times;</span>
                                <div class="modal-header">
                                    <img src="<?php echo $imageSrc; ?>"
                                        alt="<?php echo htmlspecialchars($therapist['therapist_name']); ?>" class="modal-image">
                                    <div>
                                        <h3 class="modal-title"><?php echo htmlspecialchars($therapist['therapist_name']); ?>
                                        </h3>
                                        <p class="modal-specialization">
                                            <?php echo htmlspecialchars($therapist['specialization'] ?: 'Mental Health Specialist'); ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="modal-bio">
                                    <h4>About Me</h4>
                                    <p><?php echo nl2br($bio); ?></p>

                                    <h4>Approach to Therapy</h4>
                                    <p>I believe in a client-centered approach that focuses on your unique needs and goals. My
                                        therapeutic style combines evidence-based practices with genuine compassion to help you
                                        navigate life's challenges.</p>

                                    <h4>Education & Credentials</h4>
                                    <ul>
                                        <li>PhD in Clinical Psychology, University of Wellness</li>
                                        <li>Licensed Professional Counselor (LPC)</li>
                                        <li>10+ years of clinical experience</li>
                                        <li>Specialized training in CBT and Mindfulness</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-therapists">
                    <i class="fas fa-user-md"></i>
                    <h3>Currently Updating Our Team</h3>
                    <p>We're in the process of adding our talented therapists to this page. Please check back soon or
                        contact us for more information about our services.</p>
                    <a href="contact.php" class="btn btn-primary">Contact Us</a>
                </div>
            <?php endif; ?>
        </div>

        <?php include("ChatBot.php"); ?>
    </div>

    <?php include("Footer.php"); ?>

    <script>
        // Modal functionality
        function openModal(modalId) {
            document.getElementById(modalId).classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside content
        window.addEventListener('click', function (event) {
            document.querySelectorAll('.modal').forEach(modal => {
                if (event.target === modal) {
                    modal.classList.remove('active');
                    document.body.style.overflow = 'auto';
                }
            });
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>

</html>