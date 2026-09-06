<?php
session_start();
if (!isset($_SESSION['appointments']) || !isset($_SESSION['user_id'])) {
    header("Location: Checkout.php");
    exit;
}

// Debug session data
echo "Session data: ";
var_dump($_SESSION);

// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

// Database connection
include "Connect.php";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Fetch customer's email
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT email FROM users WHERE user_id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

$to = $user['email'];
echo "Sending to: " . htmlspecialchars($to) . "<br>";

// PHPMailer setup
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'infovelora@gmail.com';
    $mail->Password = 'physqgimsgwxtzak'; // App Password without spaces
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->SMTPDebug = 2;

    $mail->setFrom('infovelora@gmail.com', 'Velora Wellness');
    $mail->addAddress($to);

    $mail->isHTML(true);
    $mail->Subject = "Confirmation - Your Wellness Services Have Been Booked Successfully!";
    
    $email_body = "<html><body>";
    $email_body .= "<h2>Your Wellness Services Have Been Booked Successfully!</h2>";
    $email_body .= "<p>Thank you for booking with Velora. Below are your appointment details:</p>";
    $email_body .= "<h3>Appointment Details</h3>";
    $email_body .= "<table border='1' cellpadding='5'>";
    $email_body .= "<tr><th>Service</th><th>Appointment Date</th><th>Appointment ID</th></tr>";

    foreach ($_SESSION['appointments'] as $appointment) {
        $email_body .= "<tr>";
        $email_body .= "<td>" . htmlspecialchars($appointment['service_name']) . "</td>";
        $email_body .= "<td>" . htmlspecialchars($appointment['appointment_date']) . "</td>";
        $email_body .= "<td>" . htmlspecialchars($appointment['appointment_id']) . "</td>";
        $email_body .= "</tr>";
    }

    $email_body .= "</table>";

    if (isset($_SESSION['payments']) && isset($_SESSION['total'])) {
        $email_body .= "<h3>Payment Details</h3>";
        $email_body .= "<table border='1' cellpadding='5'>";
        $email_body .= "<tr><th>Appointment ID</th><th>Amount</th><th>Status</th></tr>";
        foreach ($_SESSION['payments'] as $payment) {
            $email_body .= "<tr>";
            $email_body .= "<td>" . htmlspecialchars($payment['appointment_id']) . "</td>";
            $email_body .= "<td>" . number_format($payment['amount'], 0, '.', ',') . " MMK</td>";
            $email_body .= "<td>" . htmlspecialchars($payment['status']) . "</td>";
            $email_body .= "</tr>";
        }
        $email_body .= "</table>";
        $email_body .= "<p><strong>Total Paid:</strong> " . number_format($_SESSION['total'], 0, '.', ',') . " MMK</p>";
    }

    $email_body .= "<p>We look forward to serving you!</p>";
    $email_body .= "</body></html>";

    $mail->Body = $email_body;
    $mail->AltBody = "Thank you for booking with Velora. Your appointment details are below...";

    $mail->send();
    $mail_success = true;
} catch (Exception $e) {
    $mail_success = false;
    $mail_error = "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
    echo $mail_error;
}

include("Header.php");
include("Nav.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Velora</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="confirmation-message">
        <i class="fas fa-check-circle"></i>
        <p>Your wellness services have been booked successfully!</p>
        <?php if ($mail_success): ?>
            <p>A confirmation email has been sent to <?php echo htmlspecialchars($to); ?>.</p>
        <?php else: ?>
            <p>Failed to send confirmation email. Please contact support. <?php echo isset($mail_error) ? htmlspecialchars($mail_error) : ''; ?></p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
unset($_SESSION['appointments']);
unset($_SESSION['payments']);
unset($_SESSION['total']);
?>