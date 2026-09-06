<?php
$pageTitle = "Feedback | Velora Mental Wellness";
$currentPage = 'feedback';
session_start();

// Start session only if not already active
// if (session_status() === PHP_SESSION_NONE) {
//     session_start();
// }

include("Header.php");
include("Nav.php");

// Database connection
include("Connect.php");

// Get user_id and username from session
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'User';

// if (!$user_id) {
//     header("Location: Login.php");
//     exit();
// }

// Fetch services for dropdown
$sql_services = "SELECT service_id, service_name FROM services";
$stmt_services = $pdo->prepare($sql_services);
$stmt_services->execute();
$services = $stmt_services->fetchAll(PDO::FETCH_ASSOC);

// Fetch existing feedback with usernames and service names
$sql_feedback = "
    SELECT f.rating, f.comment, f.service_id, s.service_name, u.username
    FROM feedback f
    JOIN services s ON f.service_id = s.service_id
    JOIN users u ON f.user_id = u.user_id
    ORDER BY f.feedback_id DESC
    LIMIT 10"; // Limit to 10 recent feedbacks for performance
$stmt_feedback = $pdo->prepare($sql_feedback);
$stmt_feedback->execute();
$feedbacks = $stmt_feedback->fetchAll(PDO::FETCH_ASSOC);

$thank_you_message = '';
$error_message = '';

if (isset($_POST['submit'])) {
    try {
        $service_id = filter_input(INPUT_POST, 'service_id', FILTER_VALIDATE_INT);
        $rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT);
        $comment = trim($_POST['comment']);

        // Server-side validation
        if (!$service_id || !array_key_exists($service_id - 1, $services)) {
            throw new Exception("Please select a valid service.");
        }
        if (!$rating || $rating < 1 || $rating > 5) {
            throw new Exception("Please select a valid rating between 1 and 5.");
        }
        if (empty($comment)) {
            throw new Exception("Feedback comment cannot be empty.");
        }

        // Insert into database
        $sql = "INSERT INTO feedback (user_id, service_id, rating, comment) VALUES (:user_id, :service_id, :rating, :comment)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $user_id,
            ':service_id' => $service_id,
            ':rating' => $rating,
            ':comment' => $comment
        ]);

        $thank_you_message = "Thank you, $username, for your feedback!";
        // Refresh feedback list after submission
        $stmt_feedback->execute();
        $feedbacks = $stmt_feedback->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(to bottom, #f0f8f5, #e0f2e9);
            margin: 0;
            padding: 0;
            line-height: 1.6;
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        main {
            flex: 1;
            padding: 40px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .form-container {
            width: 100%;
            max-width: 480px;
            background: linear-gradient(135deg, #3b9d4a, #00796b);
            border: 2px solid #00cc66;
            padding: 32px;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
            color: white;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 40px;
        }

        .form-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.2);
        }

        .form-container::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1), transparent);
            transform: rotate(30deg);
            pointer-events: none;
        }

        .form-container h2 {
            font-size: 1.75rem;
            font-weight: 600;
            text-align: center;
            margin-bottom: 10px;
            color: #fff;
            position: relative;
            z-index: 1;
        }

        .form-container .subtitle {
            font-size: 14px;
            text-align: center;
            margin-bottom: 24px;
            color: rgba(255, 255, 255, 0.9);
            position: relative;
            z-index: 1;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            font-size: 15px;
            margin-bottom: 8px;
            color: #fff;
        }

        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #00cc66;
            background-color: #1e4d2b;
            color: #fff;
            font-size: 14px;
            box-sizing: border-box;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #33ff99;
            box-shadow: 0 0 5px rgba(51, 255, 153, 0.5);
        }

        .form-group select {
            appearance: none;
            background: #1e4d2b url('data:image/svg+xml;utf8,<svg fill="white" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/></svg>') no-repeat right 12px center;
        }

        .form-submit-btn {
            width: 100%;
            padding: 14px;
            background-color: #007a33;
            border: 1px solid #00cc66;
            border-radius: 8px;
            color: #fff;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            z-index: 1;
        }

        .form-submit-btn:hover {
            background-color: #33ff99;
            color: #212121;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(51, 255, 153, 0.3);
        }

        .message {
            text-align: center;
            font-size: 16px;
            font-weight: 600;
            margin-top: 20px;
            padding: 10px;
            border-radius: 8px;
            position: relative;
            z-index: 1;
            animation: fadeIn 0.5s ease forwards;
        }

        .thank-you-message {
            color: #33ff99;
            background: rgba(255, 255, 255, 0.1);
        }

        .error-message {
            color: #ff6666;
            background: rgba(255, 0, 0, 0.1);
        }

        /* Feedback Display Styles */
        .feedback-container {
            width: 100%;
            max-width: 600px;
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            margin-bottom: 40px;
        }

        .feedback-container h3 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #00796b;
            margin-bottom: 20px;
            text-align: center;
        }

        .feedback-item {
            border-bottom: 1px solid #e0f2e9;
            padding: 15px 0;
        }

        .feedback-item:last-child {
            border-bottom: none;
        }

        .feedback-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .feedback-username {
            font-weight: 600;
            color: #3b9d4a;
        }

        .feedback-rating {
            font-size: 14px;
            color: #00796b;
        }

        .feedback-service {
            font-size: 13px;
            color: #555;
            font-style: italic;
            margin-bottom: 8px;
        }

        .feedback-comment {
            font-size: 14px;
            color: #333;
            line-height: 1.5;
        }

        .no-feedback {
            text-align: center;
            color: #777;
            font-style: italic;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 768px) {
            .form-container, .feedback-container {
                padding: 24px;
                max-width: 90%;
            }
            .form-container h2, .feedback-container h3 {
                font-size: 1.5rem;
            }
            .form-container .subtitle {
                font-size: 13px;
            }
        }
    </style>
</head>
<body>
    <main>
        <div class="form-container">
            <h2>Share Your Feedback</h2>
            <div class="subtitle">Help us improve your Velora Mental Wellness experience, <?php echo htmlspecialchars($username); ?>!</div>
            <form method="post" action="" onsubmit="return validateForm()">
                <div class="form-group">
                    <label for="service_id">Service</label>
                    <select id="service_id" name="service_id" required>
                        <option value="" disabled selected>Select a service</option>
                        <?php foreach ($services as $service): ?>
                            <option value="<?php echo $service['service_id']; ?>">
                                <?php echo htmlspecialchars($service['service_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="rating">Rate Your Experience (1-5)</label>
                    <select id="rating" name="rating" required>
                        <option value="" disabled selected>Select a rating</option>
                        <option value="1">1 - Poor</option>
                        <option value="2">2 - Fair</option>
                        <option value="3">3 - Good</option>
                        <option value="4">4 - Very Good</option>
                        <option value="5">5 - Excellent</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="comment">Your Feedback</label>
                    <textarea id="comment" name="comment" rows="4" required placeholder="Tell us about your experience with this service..."></textarea>
                </div>
                <input type="submit" name="submit" value="Submit Feedback" class="form-submit-btn">
            </form>

            <?php if ($thank_you_message): ?>
                <div class="message thank-you-message"><?php echo $thank_you_message; ?></div>
            <?php elseif ($error_message): ?>
                <div class="message error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>
        </div>

        <!-- Feedback Display Section -->
        <div class="feedback-container">
            <h3>What Others Are Saying</h3>
            <?php if (empty($feedbacks)): ?>
                <p class="no-feedback">No feedback yet. Be the first to share!</p>
            <?php else: ?>
                <?php foreach ($feedbacks as $feedback): ?>
                    <div class="feedback-item">
                        <div class="feedback-header">
                            <span class="feedback-username"><?php echo htmlspecialchars($feedback['username']); ?></span>
                            <span class="feedback-rating">Rating: <?php echo htmlspecialchars($feedback['rating']); ?>/5</span>
                        </div>
                        <div class="feedback-service">Service: <?php echo htmlspecialchars($feedback['service_name']); ?></div>
                        <div class="feedback-comment"><?php echo htmlspecialchars($feedback['comment']); ?></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <?php include('Footer.php'); ?>
    <?php include("ChatBot.php"); ?>

    <script>
        function validateForm() {
            const service = document.getElementById('service_id').value;
            const rating = document.getElementById('rating').value;
            const comment = document.getElementById('comment').value.trim();

            if (!service) {
                alert("Please select a service.");
                return false;
            }
            if (!rating || rating < 1 || rating > 5) {
                alert("Please select a valid rating between 1 and 5.");
                return false;
            }
            if (!comment) {
                alert("Please provide your feedback in the comment section.");
                return false;
            }
            return true;
        }
    </script>
</body>
</html>