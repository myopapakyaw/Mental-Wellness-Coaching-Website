<?php
session_start();
// Start the session to ensure Nav.php can access $_SESSION

// Initialize user preferences if not set
if (!isset($_SESSION['user_preferences'])) {
    $_SESSION['user_preferences'] = [
        'content_type' => 'general',
        'interaction_style' => 'visual',
        'wellness_goals' => 'stress_reduction',
        'theme_color' => '#4e73df' // Added theme color preference
    ];
}

// Apply theme color from session
echo '<style>:root { --primary-color: ' . ($_SESSION['user_preferences']['theme_color'] ?? '#4e73df') . '; }</style>';

// Personalization content
$heroText = [
    'general' => "Find peace in your daily practice",
    'trauma' => "A safe space for your healing journey",
    'anxiety' => "Calm your mind, one breath at a time",
    'depression' => "Lighting the path to brighter days"
];
$currentHero = $heroText[$_SESSION['user_preferences']['content_type']] ?? $heroText['general'];

include 'Header.php';
include 'Nav.php';
?>



<!-- Personalization Modal -->
<div id="personalization-modal" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow-lg"
            style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);">
            <div class="modal-header border-0">
                <h3 class="modal-title fw-bold text-center w-100">
                    <i class="fas fa-magic me-2" style="color: var(--primary-color);"></i> Personalize Your Experience
                </h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <p class="lead">Let's tailor Velora to your wellness journey</p>
                    <div class="avatar-pulse mb-3">
                        <div class="avatar-circle">
                            <i class="fas fa-user-astronaut fa-2x"></i>
                        </div>
                    </div>
                </div>

                <div class="wellness-goals mb-4">
                    <h5 class="text-center mb-3"><i class="fas fa-bullseye me-2"></i>Primary Focus Area</h5>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <button class="goal-btn" data-goal="general">
                            <i class="fas fa-seedling me-2"></i> General Wellness
                        </button>
                        <button class="goal-btn" data-goal="anxiety">
                            <i class="fas fa-cloud-meatball me-2"></i> Anxiety Relief
                        </button>
                        <button class="goal-btn" data-goal="depression">
                            <i class="fas fa-cloud-sun me-2"></i> Mood Elevation
                        </button>
                        <button class="goal-btn" data-goal="trauma">
                            <i class="fas fa-heartbeat me-2"></i> Trauma Healing
                        </button>
                    </div>
                </div>

                <div class="preference-slider mb-4">
                    <h5 class="text-center mb-3"><i class="fas fa-sliders-h me-2"></i>Content Preference</h5>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span><i class="fas fa-book text-muted"></i> Reading</span>
                        <input type="range" class="form-range" min="0" max="100" id="contentPreference"
                            value="<?php echo $_SESSION['user_preferences']['interaction_style'] === 'audio' ? '75' : '25'; ?>">
                        <span><i class="fas fa-headphones text-muted"></i> Audio</span>
                    </div>
                </div>

                <div class="color-theme mb-4">
                    <h5 class="text-center mb-3"><i class="fas fa-palette me-2"></i>Theme Color</h5>
                    <div class="d-flex justify-content-center gap-2">
                        <div class="color-option" data-color="#4e73df" style="background-color:#4e73df"></div>
                        <div class="color-option" data-color="#1cc88a" style="background-color:#1cc88a"></div>
                        <div class="color-option" data-color="#36b9cc" style="background-color:#36b9cc"></div>
                        <div class="color-option" data-color="#f6c23e" style="background-color:#f6c23e"></div>
                        <div class="color-option" data-color="#e74a3b" style="background-color:#e74a3b"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button id="save-preferences" class="btn btn-primary btn-lg px-4 rounded-pill"
                    style="background-color: var(--primary-color);">
                    <i class="fas fa-save me-2"></i> Save Preferences
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Voice Navigation Component -->
<div class="voice-navigation position-fixed end-0 me-4 z-index-1000">
    <button id="voice-command-btn" class="btn btn-circle btn-primary shadow-lg" title="Voice Commands"
        style="background-color:rgb(31, 215, 129); border-color: #006400;">
        <i class="fas fa-microphone"></i>
    </button>
    <div id="voice-feedback" class="alert alert-info d-none position-absolute end-0 mb-2"
        style="width: 300px; bottom: 100%;"></div>
</div>

<style>
    .voice-navigation {
        position: fixed !important;
        bottom: 6rem !important;
        right: 31px !important;
        z-index: 1000 !important;
        margin-right: 0 !important;
    }

    #voice-command-btn {
        margin-right: 0 !important;
    }

    /* Personalization Modal Styles */
    .modal-content {
        border: none;
        overflow: hidden;
    }

    .avatar-pulse {
        display: flex;
        justify-content: center;
        position: relative;
    }

    .avatar-circle {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(45deg, var(--primary-color) 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.05);
        }

        100% {
            transform: scale(1);
        }
    }

    .goal-btn {
        border: 2px solid #e0e0e0;
        background: white;
        padding: 10px 15px;
        border-radius: 50px;
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .goal-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .goal-btn.active {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }

    .color-option {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        cursor: pointer;
        transition: transform 0.2s;
        border: 2px solid transparent;
    }

    .color-option:hover {
        transform: scale(1.2);
    }

    .color-option.selected {
        border: 2px solid #333;
        transform: scale(1.2);
    }

    #contentPreference {
        width: 60%;
        height: 8px;
        cursor: pointer;
    }
</style>

<!-- Hero Section -->
<section class="hero-gradient text-white py-5 position-relative">
    <!-- Video Background Element -->
    <div class="video-background">
        <video autoplay muted loop playsinline>
            <source src="../Img/Greenery.mp4" type="video/mp4">
            <!-- Fallback image if video doesn't load -->
            <!-- <img src="../Img/DaisyHero.jpg" alt="Fallback background"> -->
        </video>
    </div>
    
    <div class="container py-5 position-relative">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h1 class="display-4 fw-bold mb-4"><?php echo $currentHero; ?></h1>
                <p class="lead mb-4">Your journey to mental wellness starts here. Join millions on their healing journey
                    with guided meditations, therapy sessions, and sleep tools.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="Therapists.php" class="btn btn-outline-light btn-lg px-4">Explore Therapists</a>
                    <button id="personalize-btn" class="btn btn-light btn-lg px-4 fw-bold">Personalize My
                        Experience</button>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="position-relative">
                    <img src="https://images.unsplash.com/photo-1545205597-3d9d02c29597"
                        class="img-fluid rounded-3 shadow-lg" alt="Woman meditating">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Bar -->
<section class="bg-white py-4 shadow-sm">
    <div class="container">
        <div class="row g-0 border rounded overflow-hidden shadow-sm">
            <div class="col-md-4 p-4 text-center border-end">
                <h3 class="fw-bold mb-1"><i class="fas fa-user-shield text-primary me-2"></i>1:1</h3>
                <p class="text-muted mb-0">Personalized Care Ratio</p>
            </div>
            <div class="col-md-4 p-4 text-center border-end">
                <h3 class="fw-bold mb-1"><i class="fas fa-calendar-check text-success me-2"></i>98%</h3>
                <p class="text-muted mb-0">Session Completion Rate</p>
            </div>
            <div class="col-md-4 p-4 text-center">
                <h3 class="fw-bold mb-1"><i class="fas fa-clock text-warning me-2"></i>30min</h3>
                <p class="text-muted mb-0">Average Response Time</p>
            </div>
        </div>
    </div>
</section>

<!-- About us -->
<section class="about-section" id="about-us">
    <div class="container">
        <div class="row align-items-center g-5">
            <!-- Image Column -->
            <div class="col-lg-6">
                <div class="about-image-wrapper position-relative">
                    <video class="img-fluid rounded-4 shadow-lg" controls autoplay loop muted>
                        <source src="../Img/Therapist.mp4" type="video/mp4">
                    </video>
                </div>
            </div>

            <!-- Content Column -->
            <div class="col-lg-6">
                <div class="about-content ps-lg-4">
                    <span class="badge bg-velora-primary text-white mb-3 px-3 py-2 rounded-pill">Our Philosophy</span>
                    <h2 class="display-4 fw-bold mb-4">
                        Healing Minds, <span class="text-velora-primary">Nurturing Souls</span>
                    </h2>

                    <div class="lead-text mb-4">
                        <p class="lead fw-medium">At Velora Mental Wellness, we believe mental health is the <span
                                class="highlight-text">cornerstone of a fulfilling life</span>.</p>
                        <p>Our mission is to provide a safe, compassionate, and empowering space where individuals can
                            embark on their journey toward <span class="highlight-text">emotional well-being</span> and
                            personal growth through evidence-based therapies and mindfulness practices.</p>
                    </div>

                    <div class="features-list mb-5">
                        <div class="d-flex align-items-start mb-3">
                            <div
                                class="feature-icon bg-velora-primary bg-opacity-10 text-velora-primary rounded-circle p-2 me-3">
                                <i class="fas fa-check"></i>
                            </div>
                            <div>
                                <h5 class="mb-1">Trauma-Informed Specialists</h5>
                                <p class="text-muted small mb-0">Certified professionals with specialized training</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-start mb-3">
                            <div
                                class="feature-icon bg-velora-primary bg-opacity-10 text-velora-primary rounded-circle p-2 me-3">
                                <i class="fas fa-check"></i>
                            </div>
                            <div>
                                <h5 class="mb-1">Personalized Treatment Plans</h5>
                                <p class="text-muted small mb-0">Tailored to your unique needs and goals</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-start">
                            <div
                                class="feature-icon bg-velora-primary bg-opacity-10 text-velora-primary rounded-circle p-2 me-3">
                                <i class="fas fa-check"></i>
                            </div>
                            <div>
                                <h5 class="mb-1">Holistic Mind-Body Approach</h5>
                                <p class="text-muted small mb-0">Integrating multiple therapeutic modalities</p>
                            </div>
                        </div>
                    </div>

                    <div class="action-buttons d-flex flex-wrap gap-3">
                        <a href="AboutUs.php" class="btn btn-primary btn-lg px-4 py-3 rounded-pill shadow-sm">
                            <i class="fas fa-book-open me-2"></i> Our Story
                        </a>
                        <a href="Therapists.php" class="btn btn-outline-primary btn-lg px-4 py-3 rounded-pill">
                            <i class="fas fa-users me-2"></i> Meet Our Team
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Testimonial Quote -->
        <div class="row justify-content-center mt-7">
            <div class="col-lg-10 col-xl-8">
                <div class="testimonial-card p-5 rounded-4 position-relative shadow-sm"
                    style="background: linear-gradient(135deg, #5EDB81, #2E8B57);">
                    <div class="quote-mark text-velora-primary opacity-10">
                        <i class="fas fa-quote-right"></i>
                    </div>
                    <blockquote class="mb-4 fs-2 fw-light text-center">
                        "A single act of kindness can cause ripples of healing"
                    </blockquote>
                    <div class="text-center">
                        <a href="ServicePage.php" class="btn btn-velora-primary rounded-pill px-4 py-2">
                            Start Your Healing Journey <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<?php
include 'ChatBot.php';
?>

<!-- Services Section -->
<section class="py-5 bg-white" id="services">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="fw-bold mb-3">Our Treatments</h2>
            <p class="lead text-muted">Comprehensive care for your mental wellbeing</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="service-item h-100">
                    <div class="icon-wrapper">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h3>Therapy Sessions</h3>
                    <p class="text-muted">Personalized one-on-one therapy to address your unique needs with our licensed
                        professionals.</p>
                    <a href="ServicePage.php" class="btn btn-link text-primary ps-0">Learn More <i
                            class="fas fa-arrow-right ms-2"></i></a>
                </div>
            </div>

            <div class="col-md-4">
                <div class="service-item h-100">
                    <div class="icon-wrapper">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Support Groups</h3>
                    <p class="text-muted">Join our supportive community to share experiences and heal together in a safe
                        space.</p>
                    <a href="ServicePage.php" class="btn btn-link text-primary ps-0">Learn More <i
                            class="fas fa-arrow-right ms-2"></i></a>
                </div>
            </div>

            <div class="col-md-4">
                <div class="service-item h-100">
                    <div class="icon-wrapper">
                        <i class="fas fa-brain"></i>
                    </div>
                    <h3>Mindfulness Programs</h3>
                    <p class="text-muted">Learn evidence-based mindfulness techniques to reduce stress and improve
                        focus.</p>
                    <a href="ServicePage.php" class="btn btn-link text-primary ps-0">Learn More <i
                            class="fas fa-arrow-right ms-2"></i></a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Meditations -->
<section class="py-5 bg-light">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <h2 class="fw-bold">Featured Meditations</h2>
            <a href="ServicePage.php" class="text-primary fw-bold">Explore More</a>
        </div>

        <div class="row">
            <?php
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "myopapakyaw_mental_wellness_service";

            try {
                $pdo = new PDO("mysql:host=$servername;dbname=$dbname;", $username, $password);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Updated query for MariaDB 10.4 compatibility
                $sql = "SELECT s.service_name, s.service_category, s.service_description, s.duration, s.service_image,
                               t.therapist_name, t.profile_picture
                        FROM services s
                        LEFT JOIN (
                            SELECT service_id, MIN(therapist_id) AS therapist_id
                            FROM service_therapists
                            GROUP BY service_id
                        ) st ON s.service_id = st.service_id
                        LEFT JOIN therapists t ON st.therapist_id = t.therapist_id
                        INNER JOIN (
                            SELECT MIN(service_id) AS service_id
                            FROM services
                            GROUP BY service_category
                        ) cat ON s.service_id = cat.service_id
                        LIMIT 3";
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($results) > 0) {
                    foreach ($results as $row) {
                        $serviceImageSrc = "https://via.placeholder.com/300x200";
                        if (!empty($row['service_image'])) {
                            $serviceImageData = base64_encode($row['service_image']);
                            $serviceImageSrc = "data:image/jpeg;base64," . $serviceImageData;
                        }

                        $therapistImageSrc = "https://randomuser.me/api/portraits/women/32.jpg";
                        if (!empty($row['profile_picture'])) {
                            $therapistImageData = base64_encode($row['profile_picture']);
                            $therapistImageSrc = "data:image/jpeg;base64," . $therapistImageData;
                        }
                        ?>
                        <div class="col-md-4 mb-4">
                            <div class="card feature-card h-100 border-0 shadow-sm">
                                <img src="<?php echo $serviceImageSrc; ?>" class="card-img-top"
                                    alt="<?php echo htmlspecialchars($row['service_name']); ?>">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="badge bg-primary bg-opacity-10 text-primary">
                                            <?php echo htmlspecialchars($row['service_category']); ?>
                                        </span>
                                        <span class="text-muted">
                                            <?php echo htmlspecialchars($row['duration'] ?? 'N/A'); ?>
                                        </span>
                                    </div>
                                    <h5 class="card-title">
                                        <?php echo htmlspecialchars($row['service_name']); ?>
                                    </h5>
                                    <p class="card-text text-muted">
                                        <?php echo htmlspecialchars(substr($row['service_description'] ?? '', 0, 50)) . '...'; ?>
                                    </p>
                                </div>
                                <div class="card-footer bg-transparent border-0">
                                    <div class="d-flex align-items-center">
                                        <img src="<?php echo $therapistImageSrc; ?>" class="rounded-circle me-2" width="30"
                                            alt="<?php echo htmlspecialchars($row['therapist_name'] ?? 'Unknown Therapist'); ?>">
                                        <small
                                            class="text-muted"><?php echo htmlspecialchars($row['therapist_name'] ?? 'Unknown Therapist'); ?></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo "<p>No featured meditations available at this time.</p>";
                }
            } catch (Exception $e) {
                echo "<p>Fail to connect or query failed: " . htmlspecialchars($e->getMessage()) . "</p>";
            }
            ?>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="py-5" id="testimonials"
    style="background: linear-gradient(135deg, rgba(5, 238, 94, 0.08) 0%, rgba(47, 239, 95, 0.05) 50%, rgba(168,230,207,0.08) 100%);">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="fw-bold mb-3">What Our Clients Say</h2>
            <p class="lead text-muted">Real stories from our community</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="testimonial-item h-100 p-4 rounded-4 shadow-sm"
                    style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(8px);">
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3">
                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center"
                                style="width: 50px; height: 50px;">
                                <i class="fas fa-user fa-lg"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="mb-0">Thinzar Phyo Wai</h5>
                            <div class="text-warning small">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <p class="mb-0">"Velora has been a lifesaver for me. The therapists are incredibly supportive and
                        understanding, creating a safe space for healing."</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="testimonial-item h-100 p-4 rounded-4 shadow-sm"
                    style="background: rgba(255,255,255,0.9); backdrop-filter: blur(8px);">
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3">
                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center"
                                style="width: 50px; height: 50px;">
                                <i class="fas fa-user fa-lg"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="mb-0">Hein Wai Lin Myint</h5>
                            <div class="text-warning small">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <p class="mb-0">"The support groups are amazing. I've found a community that truly cares and uplifts
                        each other through difficult times."</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="testimonial-item h-100 p-4 rounded-4 shadow-sm"
                    style="background: rgba(255,255,255,0.9); backdrop-filter: blur(8px);">
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3">
                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center"
                                style="width: 50px; height: 50px;">
                                <i class="fas fa-user fa-lg"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="mb-0">Thuya Htun</h5>
                            <div class="text-warning small">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                        </div>
                    </div>
                    <p class="mb-0">"Velora became my sanctuary during a really tough time. The mindfulness programs
                        gave me tools I use every day."</p>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- Daily Wellness Tip -->
<section class="py-5 bg-white">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0">
                <div class="tip-card p-4 p-lg-5 rounded-3 shadow-sm border-0 text-center h-100"
                    style="background: linear-gradient(135deg, #f5f7fa 0%,rgb(38, 224, 72) 100%);">
                    <div class="tip-icon mb-4 mx-auto rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 80px; height: 80px; background-color: var(--primary-color);">
                        <i class="fas fa-lightbulb fa-2x text-white"></i>
                    </div>
                    <h3 class="fw-bold mb-3">Today's Wellness Tip</h3>
                    <p class="lead mb-4" id="daily-tip-text">Take 5 deep breaths whenever you feel stressed. Inhale for
                        4 seconds, hold for 4, exhale for 6.</p>
                    <div class="d-flex justify-content-center gap-3">
                        <button id="new-tip-btn" class="btn btn-outline-success">
                            <i class="fas fa-sync-alt me-2"></i>New Tip
                        </button>
                        <!-- <button id="save-tip-btn" class="btn btn-primary">
                            <i class="fas fa-bookmark me-2"></i>Save
                        </button> -->
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <h2 class="fw-bold mb-4">Small Steps, Big Changes</h2>
                <p class="lead mb-4">Daily wellness tips to help you build healthy habits for your mental health.</p>

                <div class="d-flex align-items-start mb-4">
                    <div class="me-3 text-primary">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="mb-2">Science-Backed</h5>
                        <p class="text-muted mb-0">All tips are based on psychological research and proven techniques.
                        </p>
                    </div>
                </div>

                <div class="d-flex align-items-start mb-4">
                    <div class="me-3 text-primary">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="mb-2">Quick & Easy</h5>
                        <p class="text-muted mb-0">Simple practices that take less than 5 minutes to complete.</p>
                    </div>
                </div>

                <div class="d-flex align-items-start">
                    <div class="me-3 text-primary">
                        <i class="fas fa-calendar-alt fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="mb-2">Daily Updates</h5>
                        <p class="text-muted mb-0">Fresh content every day to keep you motivated.</p>
                    </div>
                </div>

                <div class="mt-5">
                    <!-- <a href="signup.php" class="btn btn-dark btn-lg px-4 me-2">
                        <i class="fas fa-envelope me-2"></i>Get Daily Tips
                    </a> -->
                    <a href="Wellness-tips.php" class="btn btn-outline-dark btn-lg px-4">
                        Browse All Tips
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    // Voice Command Functionality
    const voiceBtn = document.getElementById('voice-command-btn');
    const feedback = document.getElementById('voice-feedback');

    if ('webkitSpeechRecognition' in window) {
        const recognition = new webkitSpeechRecognition();
        recognition.continuous = false;
        recognition.interimResults = false;

        voiceBtn.addEventListener('click', () => {
            recognition.start();
            feedback.classList.remove('d-none');
            feedback.textContent = "Listening... Say something like 'therapist' or 'meditation'";
        });

        recognition.onresult = (event) => {
            const command = event.results[0][0].transcript.toLowerCase();
            feedback.textContent = `You said: "${command}"`;

            // Process commands
            if (command.includes('therapist')) {
                setTimeout(() => window.location.href = 'Therapists.php', 1000);
            } else if (command.includes('meditation')) {
                setTimeout(() => window.location.href = 'ServicePage.php#meditation', 1000);
            } else if (command.includes('contact')) {
                setTimeout(() => window.location.href = '#contact', 1000);
            } else if (command.includes('home')) {
                setTimeout(() => window.location.href = 'Index.php', 1000);
            }
        };

        recognition.onerror = (event) => {
            feedback.textContent = "Error occurred in recognition: " + event.error;
        };
    } else {
        voiceBtn.style.display = 'none';
    }

    // Enhanced Personalization Modal
    document.getElementById('personalize-btn').addEventListener('click', function () {
        // Initialize modal
        const modal = new bootstrap.Modal(document.getElementById('personalization-modal'));

        // Show modal with animation
        modal.show();

        // Initialize interactive elements
        const goalButtons = document.querySelectorAll('.goal-btn');
        const colorOptions = document.querySelectorAll('.color-option');
        let selectedGoal = '<?php echo $_SESSION['user_preferences']['wellness_goals'] ?? 'general'; ?>';
        let selectedColor = '<?php echo $_SESSION['user_preferences']['theme_color'] ?? '#4e73df'; ?>';

        // Set initial active goal button
        goalButtons.forEach(btn => {
            if (btn.dataset.goal === selectedGoal) {
                btn.classList.add('active');
            }

            btn.addEventListener('click', function () {
                goalButtons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                selectedGoal = this.dataset.goal;

                // Play subtle sound effect
                const audio = new Audio('https://assets.mixkit.co/sfx/preview/mixkit-arcade-game-jump-coin-216.mp3');
                audio.volume = 0.3;
                audio.play();
            });
        });

        // Color selection
        colorOptions.forEach(option => {
            if (option.dataset.color === selectedColor) {
                option.classList.add('selected');
            }

            option.addEventListener('click', function () {
                colorOptions.forEach(opt => opt.classList.remove('selected'));
                this.classList.add('selected');
                selectedColor = this.dataset.color;

                // Animate selection
                this.style.transform = 'scale(1.3)';
                setTimeout(() => {
                    this.style.transform = 'scale(1.2)';
                }, 200);
            });
        });

        // Save preferences
        document.getElementById('save-preferences').addEventListener('click', function () {
            const contentPreference = document.getElementById('contentPreference').value;
            const interactionStyle = contentPreference > 50 ? 'audio' : 'visual';

            // Show loading state
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Saving...';
            this.disabled = true;

            // Send data to server
            fetch('UpdatePreferences.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    goal: selectedGoal,
                    interaction_style: interactionStyle,
                    theme_color: selectedColor
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success animation
                        this.innerHTML = '<i class="fas fa-check me-2"></i> Saved!';

                        // Apply theme color immediately
                        document.documentElement.style.setProperty('--primary-color', selectedColor);

                        // Close modal after delay
                        setTimeout(() => {
                            modal.hide();
                            location.reload();
                        }, 1000);
                    } else {
                        this.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i> Try Again';
                        this.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i> Error';
                    setTimeout(() => {
                        this.innerHTML = '<i class="fas fa-save me-2"></i> Save Preferences';
                        this.disabled = false;
                    }, 2000);
                });
        });

        // Add enter key support for accessibility
        document.addEventListener('keypress', function (e) {
            if (e.key === 'Enter' && document.getElementById('personalization-modal').style.display === 'block') {
                document.getElementById('save-preferences').click();
            }
        });
    });

    // Smooth Scroll Transitions
    window.addEventListener('scroll', () => {
        const sections = document.querySelectorAll('.fade-in-section');
        const voiceNav = document.querySelector('.voice-navigation');
        const scrollPosition = window.scrollY;

        // Smoothly adjust voice navigation position
        if (scrollPosition > 50) {
            voiceNav.style.bottom = '5.5rem';
            voiceNav.style.opacity = '0.8';
        } else {
            voiceNav.style.bottom = '6rem';
            voiceNav.style.opacity = '1';
        }

        // Smoothly fade in sections as they enter the viewport
        sections.forEach(section => {
            const sectionTop = section.getBoundingClientRect().top;
            const windowHeight = window.innerHeight;
            if (sectionTop < windowHeight - 100) {
                section.classList.add('visible');
            } else {
                section.classList.remove('visible');
            }
        });
    });

    // Array of wellness tips
    const wellnessTips = [
        "Practice gratitude by listing 3 things you're thankful for today.",
        "Take a 5-minute walk outside to refresh your mind.",
        "Try the 5-4-3-2-1 grounding technique: Name 5 things you see, 4 you feel, 3 you hear, 2 you smell, 1 you taste.",
        "Set a timer for 2 minutes and focus only on your breathing.",
        "Write down one positive thing about your day before bed.",
        "Stretch your body for 3 minutes to release physical tension.",
        "Put your phone away 30 minutes before bedtime for better sleep.",
        "Compliment someone today - it boosts both your moods.",
        "Drink a glass of water when you feel tired or unfocused.",
        "Do one thing today that brings you joy, no matter how small."
    ];

    // Get random tip
    function getRandomTip() {
        const randomIndex = Math.floor(Math.random() * wellnessTips.length);
        document.getElementById('daily-tip-text').textContent = wellnessTips[randomIndex];
    }

    // New tip button
    document.getElementById('new-tip-btn').addEventListener('click', getRandomTip);

    // Save tip button
    document.getElementById('save-tip-btn').addEventListener('click', function () {
        const currentTip = document.getElementById('daily-tip-text').textContent;
        alert(`Tip saved: "${currentTip}"\n(Note: Would be saved to user account in full implementation)`);
    });

    // Initialize with random tip
    getRandomTip();

    // Trigger initial scroll check on page load
    window.dispatchEvent(new Event('scroll'));
</script>


<?php
include 'Footer.php';
?>