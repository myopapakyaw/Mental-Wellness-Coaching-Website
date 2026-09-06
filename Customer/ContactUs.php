<?php

// Start session if needed
session_start();

// Database connection using PDO
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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $message = $_POST['message'];
        
        $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, message) VALUES (:name, :email, :message)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':message', $message);
        
        $stmt->execute();
        $success = "Message sent successfully!";
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<?php include 'Header.php'; ?>

<body>
    <?php include 'Nav.php'; ?>
    
    <div class="C-container">
        <div class="header-section">
            <h1>Contact Us</h1>
            <p>We're here to assist you on your wellness journey.</p>
        </div>
        
        <?php 
        if (isset($success)) {
            echo "<div class='success-message animate-pop'>$success</div>";
        }
        if (isset($error)) {
            echo "<div class='error-message animate-pop'>$error</div>";
        }
        ?>
        
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="contact-form">
            <div class="form-group animate-slide-up" style="animation-delay: 0.1s">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
            </div>
            
            <div class="form-group animate-slide-up" style="animation-delay: 0.2s">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group animate-slide-up" style="animation-delay: 0.3s">
                <label for="message">Message:</label>
                <textarea id="message" name="message" rows="3" required></textarea>
            </div>
            
            <button type="submit" class="submit-btn animate-slide-up" style="animation-delay: 0.4s">
                <span>Send</span>
            </button>
        </form>
    </div>

    <?php include 'Footer.php'; ?>
</body>
</html>

<style>
:root {
    --primary-color: #6B7280;
    --accent-color: #10B981;
    --bg-color: #F3F4F6;
    --text-color: #374151;
}

.C-container {
    max-width: 600px; 
    margin: 40px auto; 
    padding: 20px; 
    background: linear-gradient(135deg, #ffffff 0%, var(--bg-color) 100%);
    border-radius: 10px; 
    box-shadow: 0 8px 20px rgba(0,0,0,0.05);
}

.header-section {
    text-align: center;
    margin-bottom: 25px; 
}

h1 {
    color: var(--primary-color);
    font-size: 2em;
    margin-bottom: 10px; 
    font-weight: 700;
    letter-spacing: -0.5px;
}

.C-container p {
    color: var(--text-color);
    font-size: 1em; 
    line-height: 1.5;
    opacity: 0.9;
}

.contact-form {
    background: white;
    padding: 25px; 
    border-radius: 8px; 
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

.form-group {
    margin-bottom: 15px;
    position: relative;
}

label {
    display: block;
    margin-bottom: 5px; 
    color: var(--primary-color);
    font-weight: 500;
    font-size: 0.9em; 
    transition: all 0.3s ease;
}

input, textarea {
    width: 100%;
    padding: 8px 12px; 
    border: 2px solid #E5E7EB;
    border-radius: 6px; 
    box-sizing: border-box;
    font-size: 0.95em; 
    transition: all 0.3s ease;
}

input:focus, textarea:focus {
    border-color: var(--accent-color);
    outline: none;
    box-shadow: 0 0 0 3px rgba(16,185,129,0.1);
}

textarea {
    resize: vertical;
    min-height: 80px; 
}

.submit-btn {
    background: var(--accent-color);
    color: white;
    padding: 10px 20px; 
    border: none;
    border-radius: 6px; 
    cursor: pointer;
    width: 100%;
    font-size: 1em; 
    font-weight: 600;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
}

.submit-btn span {
    position: relative;
    z-index: 1;
}

.submit-btn::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
    transform: translate(-50%, -50%);
    transition: width 0.6s ease, height 0.6s ease;
}

.submit-btn:hover::before {
    width: 200px; 
    height: 200px;
}

.submit-btn:hover {
    transform: translateY(-2px);
}

.success-message, .error-message {
    padding: 12px 15px; 
    margin-bottom: 20px; 
    border-radius: 6px; 
    font-weight: 500;
    text-align: center;
    font-size: 0.9em; 
}

.success-message {
    background: rgba(16,185,129,0.1);
    color: var(--accent-color);
    border: 1px solid rgba(16,185,129,0.3);
}

.error-message {
    background: rgba(239,68,68,0.1);
    color: #EF4444;
    border: 1px solid rgba(239,68,68,0.3);
}

/* Animations */
@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(15px); 
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes pop {
    0% {
        transform: scale(0.95);
        opacity: 0;
    }
    50% {
        transform: scale(1.03); 
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

.animate-slide-up {
    animation: slideUp 0.5s ease-out forwards; 
}

.animate-pop {
    animation: pop 0.3s ease-out forwards;
}
</style>