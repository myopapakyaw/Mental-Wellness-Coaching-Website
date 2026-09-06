<?php
ob_start();
session_start();
date_default_timezone_set('Asia/Yangon');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include PHPMailer using Composer autoloader
require '../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "myopapakyaw_mental_wellness_service";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("Failed to connect: " . htmlspecialchars($e->getMessage()));
}

// Initialize variables
$cart_items = [];
$total = 0;
$error_message = '';
$first_name = '';
$last_name = '';
$email = '';
$phone = '';
$message = '';
$payment_method = '';
$booking_confirmed = false;

// Fetch cart items for logged-in users
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("
        SELECT a.cart_id, a.service_id, s.service_name, s.service_category, s.service_price, s.duration, a.quantity
        FROM add_to_cart a
        JOIN services s ON a.service_id = s.service_id
        WHERE a.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($cart_items as &$item) {
        $stmt = $pdo->prepare("
            SELECT t.therapist_id, t.therapist_name
            FROM therapists t
            JOIN service_therapists st ON t.therapist_id = st.therapist_id
            WHERE st.service_id = ?
        ");
        $stmt->execute([$item['service_id']]);
        $item['therapists'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total += $item['service_price'] * $item['quantity'];
    }
    unset($item);
} elseif (isset($_SESSION['cart'])) {
    // Fetch cart items for guest users
    foreach ($_SESSION['cart'] as $service_id => $item) {
        $stmt = $pdo->prepare("
            SELECT s.service_name, s.service_category, s.service_price, s.duration
            FROM services s
            WHERE s.service_id = ?
        ");
        $stmt->execute([$service_id]);
        $service = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($service) {
            $stmt = $pdo->prepare("
                SELECT t.therapist_id, t.therapist_name
                FROM therapists t
                JOIN service_therapists st ON t.therapist_id = st.therapist_id
                WHERE st.service_id = ?
            ");
            $stmt->execute([$service_id]);
            $therapists = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $cart_items[] = [
                'cart_id' => null,
                'service_id' => $service_id,
                'service_name' => $service['service_name'],
                'service_category' => $service['service_category'],
                'service_price' => $service['service_price'],
                'duration' => $service['duration'],
                'quantity' => $item['qty'],
                'therapists' => $therapists ?: []
            ];
            $total += $service['service_price'] * $item['qty'];
        }
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_checkout'])) {
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $message = $_POST['message'] ?? '';
    $payment_method = $_POST['payment_method'] ?? '';
    $appointment_dates = $_POST['appointment_date'] ?? [];
    $selected_therapists = $_POST['therapist_id'] ?? [];
    $service_types = $_POST['service_type'] ?? [];

    // Validate form inputs
    if (empty($first_name) || empty($last_name) || empty($email) || empty($phone)) {
        $error_message = "Please fill in all required client details.";
    } elseif (empty($payment_method)) {
        $error_message = "Please select a payment method.";
    } elseif (empty($cart_items)) {
        $error_message = "Your cart is empty.";
    }

    // Validate appointment details for non-chat sessions
    foreach ($cart_items as $index => $item) {
        if ($item['service_category'] !== 'Chat Session') {
            if (!isset($appointment_dates[$index]) || empty($appointment_dates[$index])) {
                $error_message = "Please select appointment date for " . htmlspecialchars($item['service_name']);
                break;
            }
            if (!isset($selected_therapists[$index]) || empty($selected_therapists[$index])) {
                $error_message = "Please select therapist for " . htmlspecialchars($item['service_name']);
                break;
            }
            if (!isset($service_types[$index]) || empty($service_types[$index])) {
                $error_message = "Please select service type for " . htmlspecialchars($item['service_name']);
                break;
            }
        }
    }

    // Process booking if no errors
    if (empty($error_message)) {
        try {
            $pdo->beginTransaction();
            $booking_details = [];

            foreach ($cart_items as $index => $item) {
                $therapist_id = $item['service_category'] === 'Chat Session' ? null : $selected_therapists[$index];
                $appointment_date = $item['service_category'] === 'Chat Session' ? null : date('Y-m-d H:i:s', strtotime($appointment_dates[$index]));
                $service_type = $item['service_category'] === 'Chat Session' ? null : $service_types[$index];

                for ($i = 0; $i < $item['quantity']; $i++) {
                    $stmt = $pdo->prepare("
                        INSERT INTO appointments (user_id, service_id, therapist_id, appointment_date, status)
                        VALUES (?, ?, ?, ?, 'pending')
                    ");
                    $stmt->execute([$_SESSION['user_id'] ?? null, $item['service_id'], $therapist_id, $appointment_date]);

                    $appointment_id = $pdo->lastInsertId();

                    $stmt = $pdo->prepare("
                        INSERT INTO payments (user_id, appointment_id, amount, payment_date, status, payment_method)
                        VALUES (?, ?, ?, NOW(), 'pending', ?)
                    ");
                    $stmt->execute([$_SESSION['user_id'] ?? null, $appointment_id, $item['service_price'], $payment_method]);
                }

                // Fetch therapist name securely
                $therapist_name = 'To be assigned';
                if ($therapist_id) {
                    $stmt = $pdo->prepare("SELECT therapist_name FROM therapists WHERE therapist_id = ?");
                    $stmt->execute([$therapist_id]);
                    $therapist_name = $stmt->fetchColumn() ?: 'To be assigned';
                }

                $booking_details[] = [
                    'service_name' => $item['service_name'],
                    'service_category' => $item['service_category'],
                    'appointment_date' => $appointment_date,
                    'therapist_name' => $therapist_name,
                    'price' => $item['service_price'],
                    'quantity' => $item['quantity'],
                    'service_type' => $service_type
                ];
            }

            // Clear cart
            if (isset($_SESSION['user_id'])) {
                $stmt = $pdo->prepare("DELETE FROM add_to_cart WHERE user_id = ?");
                $stmt->execute([$_SESSION['user_id']]);
            }
            unset($_SESSION['cart']);

            $pdo->commit();
            $booking_confirmed = true;
            $_SESSION['booking_details'] = [
                'first_name' => $first_name,
                'email' => $email,
                'total' => $total,
                'payment_method' => $payment_method,
                'items' => $booking_details
            ];

            $mail = new PHPMailer(true);
            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'myopapakyaw218@gmail.com'; 
                $mail->Password = 'ddaj ymhb jqrs ljvl';    
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                $mail->SMTPDebug = 0; 

                // Recipients
                $mail->setFrom('myopapakyaw218@gmail.com', 'Velora Mental Wellness');
                $mail->addAddress($email, "$first_name $last_name");
                $mail->addReplyTo('myopapakyaw218@gmail.com', 'Velora Support');

                $mail->isHTML(true);
                $mail->Subject = "Your Booking Confirmation - Velora Mental Wellness";
                
                $email_body = '
<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: \'Inter\', Arial, sans-serif;
            background-color: #f7fafc;
            color: #4a5568;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        .logo {
            max-width: 150px;
            height: auto;
        }
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            padding: 25px;
            margin: 20px 0;
        }
        .card-header {
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        .card-title {
            font-size: 18px;
            font-weight: 600;
            color: #2d3748;
            margin: 0;
        }
        .card-body {
            padding: 10px 0;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: 500;
            color: #4a5568;
        }
        .info-value {
            color: #2d3748;
            font-weight: 400;
        }
        .service-card {
            background: #f8fafc;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 3px solid #4299e1;
        }
        .service-title {
            font-weight: 600;
            color: #2b6cb0;
            margin: 0 0 10px 0;
        }
        .service-detail {
            margin-bottom: 5px;
        }
        .footer {
            text-align: center;
            padding: 20px 0;
            color: #718096;
            font-size: 14px;
        }
        .thank-you {
            font-size: 20px;
            color: #2d3748;
            margin-bottom: 20px;
        }
        .total-card {
            background: #ebf8ff;
            border-radius: 6px;
            padding: 15px;
            text-align: right;
            font-weight: 600;
            font-size: 18px;
            margin-top: 20px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4299e1;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 500;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        
        <div class="card">
            <h1 class="thank-you">Thank You for Your Booking!</h1>
            <p>Dear '.htmlspecialchars($first_name.' '.$last_name).',</p>
            <p>Your booking has been confirmed. Below are your appointment details:</p>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Booking Summary</h2>
            </div>
            <div class="card-body">
                <div class="info-row">
                    <span class="info-label">Booking Date:</span>
                    <span class="info-value">'.date('F j, Y').'</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Payment Method:</span>
                    <span class="info-value">'.htmlspecialchars($payment_method).'</span>
                </div>
            </div>
        </div>';
        
foreach ($booking_details as $item) {
    $email_body .= '
        <div class="service-card">
            <h3 class="service-title">'.htmlspecialchars($item['service_name']).'</h3>
            <div class="service-detail">
                <strong>Category:</strong> '.htmlspecialchars($item['service_category']).'
            </div>';
            
    if ($item['appointment_date']) {
        $email_body .= '
            <div class="service-detail">
                <strong>Appointment Date:</strong> '.date('F j, Y, g:i a', strtotime($item['appointment_date'])).'
            </div>';
    } else {
        $email_body .= '
            <div class="service-detail">
                <strong>Access:</strong> Available within 24 hours
            </div>';
    }
    
    $email_body .= '
            <div class="service-detail">
                <strong>Therapist:</strong> '.htmlspecialchars($item['therapist_name']).'
            </div>
            <div class="service-detail">
                <strong>Service Type:</strong> '.htmlspecialchars($item['service_type'] ?? 'Not Specified').'
            </div>
            <div class="service-detail">
                <strong>Price:</strong> MMK'.number_format($item['price'], 2).' × '.$item['quantity'].'
            </div>
        </div>';
}

$email_body .= '
        <div class="total-card">
            Total: MMK'.number_format($total, 2).'
        </div>
        
        <div class="card">
            <p>We look forward to serving you! If you have any questions about your booking, please reply to this email.</p>
            <p>Best regards,<br>The Velora Team</p>
        </div>
        
        <div class="footer">
            <p>&copy; '.date('Y').' Velora Mental Wellness. All rights reserved.</p>
        </div>
    </div>
</body>
</html>';

                $mail->Body = $email_body;
                $mail->AltBody = strip_tags($email_body); 

                // Send the email
                $mail->send();
                error_log("Email sent successfully to $email");
            } catch (Exception $e) {
                $error_message = "Email sending failed: " . $mail->ErrorInfo;
                error_log("Email sending failed: " . $mail->ErrorInfo);
                echo "<div style='color: red; padding: 10px; border: 1px solid red; margin: 10px;'>$error_message</div>";
            }

        } catch (Exception $e) {
            $pdo->rollBack();
            $error_message = "Checkout failed. Please try again.";
            error_log("Checkout error: " . $e->getMessage());
        }
    }
}

include("Header.php");
include("Nav.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Velora</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f5f5f5;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .checkout-wrapper {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .checkout-header h1 {
            font-size: 2rem;
            font-weight: 700;
            color: #333;
            margin: 0 0 30px;
        }

        .checkout-container {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
        }

        .client-details,
        .booking-details {
            flex: 1;
            min-width: 300px;
        }

        .client-details h2,
        .booking-details h2 {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 500;
            color: #333;
            margin-bottom: 4px;
        }

        .form-group label span.required {
            color: #ff0000;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 0.85rem;
            color: #333;
            background: #fff;
            box-sizing: border-box;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #666;
            outline: none;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        .service-card {
            background: #fafafa;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 8px;
            margin-bottom: 10px;
            transition: box-shadow 0.3s ease;
            max-width: 300px;
        }

        .service-card:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .service-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .service-header h3 {
            font-size: 0.9rem;
            font-weight: 600;
            color: #333;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .service-header .service-price {
            font-size: 0.8rem;
            font-weight: 500;
            color: #666;
        }

        .payment-method {
            margin-top: 20px;
            padding: 15px;
            background: #fafafa;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        .payment-method h3 {
            font-size: 1rem;
            font-weight: 600;
            color: #333;
            margin: 0 0 15px;
        }

        .payment-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
        }

        .payment-option input[type="radio"] {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .payment-option label {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 15px;
            background: #fff;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .payment-option label:hover {
            border-color: #007bff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .payment-option input[type="radio"]:checked+label {
            border-color: #007bff;
            background: #f0f7ff;
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.2);
            transform: translateY(-2px);
        }

        .payment-icon {
            font-size: 1.5rem;
            color: #007bff;
            transition: transform 0.3s ease;
        }

        .payment-option input[type="radio"]:checked+label .payment-icon {
            transform: scale(1.1);
        }

        .payment-label {
            font-size: 1rem;
            font-weight: 500;
            color: #333;
        }

        .total-section {
            margin-top: 20px;
            padding: 15px;
            background: #f0f0f0;
            border-radius: 6px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .total-section span {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
        }

        .confirm-checkout {
            width: 100%;
            padding: 12px;
            margin-top: 20px;
            background: #000;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .confirm-checkout:hover {
            background: #333;
        }

        .error-message {
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            background: #ffcccc;
            color: #cc0000;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.9rem;
            border-left: 4px solid #cc0000;
        }

        .confirmation-card {
            max-width: 600px;
            margin: 40px auto;
            padding: 30px;
            background: linear-gradient(135deg, #e6f3ff 0%, #f0f7ff 100%);
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            animation: fadeIn 0.5s ease-in-out;
        }

        .confirmation-card .icon {
            font-size: 3rem;
            color: #28a745;
            margin-bottom: 20px;
        }

        .confirmation-card h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #333;
            margin: 0 0 15px;
        }

        .confirmation-card p {
            font-size: 1rem;
            color: #666;
            margin: 0 0 20px;
        }

        .confirmation-details {
            text-align: left;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .confirmation-details h3 {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin: 0 0 15px;
        }

        .confirmation-details p {
            font-size: 0.95rem;
            color: #555;
            margin: 5px 0;
        }

        .confirmation-card .btn-home {
            display: inline-block;
            padding: 12px 25px;
            background: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 500;
            transition: background 0.3s ease, transform 0.3s ease;
        }

        .confirmation-card .btn-home:hover {
            background: #0056b3;
            transform: translateY(-2px);
        }

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

        @media (max-width: 768px) {
            .checkout-container {
                flex-direction: column;
            }

            .payment-options {
                grid-template-columns: 1fr;
            }

            .confirmation-card {
                margin: 20px;
                padding: 20px;
            }
        }

        .back-to-services {
            display: inline-flex;
            align-items: center;
            padding: 10px 20px;
            margin-bottom: 20px;
            background: #f5f5f5;
            color: #333;
            text-decoration: none;
            border-radius: 5px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: background 0.3s ease, transform 0.3s ease;
        }

        .back-to-services i {
            margin-right: 8px;
            font-size: 1rem;
        }

        .back-to-services:hover {
            background: #e0e0e0;
            transform: translateX(-2px);
        }

        .confirmation-card .back-to-services {
            background: #fff;
            border: 1px solid #ddd;
            margin-left: 10px;
        }

        .confirmation-card .back-to-services:hover {
            background: #f5f5f5;
        }
    </style>
</head>

<body>
    <?php if ($booking_confirmed && isset($_SESSION['booking_details'])): ?>
        <div class="confirmation-card">
            <i class="fas fa-check-circle icon"></i>
            <h2>Booking Confirmed!</h2>
            <p>Thank you, <?php echo htmlspecialchars($_SESSION['booking_details']['first_name']); ?>. Your booking has been successfully placed. A confirmation email has been sent to <?php echo htmlspecialchars($_SESSION['booking_details']['email']); ?>.</p>

            <div class="confirmation-details">
                <h3>Booking Details</h3>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['booking_details']['email']); ?></p>
                <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($_SESSION['booking_details']['payment_method']); ?></p>
                <p><strong>Total:</strong> MMK<?php echo number_format($_SESSION['booking_details']['total'], 2); ?></p><br>

                <h3>Appointments</h3>
                <?php foreach ($_SESSION['booking_details']['items'] as $item): ?>
                    <p>
                        <strong>Service:</strong> <?php echo htmlspecialchars($item['service_name']); ?> (<?php echo htmlspecialchars($item['service_category']); ?>)<br>
                        <?php if ($item['appointment_date']): ?>
                            <strong>Date:</strong> <?php echo date('F j, Y, g:i a', strtotime($item['appointment_date'])); ?><br>
                        <?php else: ?>
                            <strong>Access:</strong> Available within 24 hours<br>
                        <?php endif; ?>
                        <strong>Therapist:</strong> <?php echo htmlspecialchars($item['therapist_name']); ?><br>
                        <strong>Service Type:</strong> <?php echo htmlspecialchars($item['service_type'] ?? 'Not Specified'); ?><br>
                        <strong>Price:</strong> MMK<?php echo number_format($item['price'], 2); ?> x <?php echo $item['quantity']; ?>
                    </p>
                <?php endforeach; ?>
            </div>

            <a href="index.php" class="btn-home">Return to Home</a>
            <a href="ServicePage.php" class="back-to-services"><i class="fas fa-arrow-left"></i> Back to Services</a>
        </div>
    <?php else: ?>
        <div class="checkout-wrapper">
            <a href="ServicePage.php" class="back-to-services"><i class="fas fa-arrow-left"></i> Back to Services</a>

            <?php if (!empty($error_message)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <p><?php echo htmlspecialchars($error_message); ?></p>
                </div>
            <?php endif; ?>

            <div class="checkout-header">
                <h1>Checkout Form</h1>
            </div>

            <form id="checkout-form" method="post">
                <div class="checkout-container">
                    <div class="client-details">
                        <h2>Client Details</h2>
                        <div class="form-group">
                            <label>First name <span class="required">*</span></label>
                            <input type="text" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Last name <span class="required">*</span></label>
                            <input type="text" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Email <span class="required">*</span></label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Phone <span class="required">*</span></label>
                            <input type="tel" name="phone" value="<?php echo htmlspecialchars($phone); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Message</label>
                            <textarea name="message"><?php echo htmlspecialchars($message); ?></textarea>
                        </div>
                    </div>

                    <div class="booking-details">
                        <h2>Booking Details</h2>
                        <?php if (empty($cart_items)): ?>
                            <p>Your cart is empty.</p>
                        <?php else: ?>
                            <?php foreach ($cart_items as $index => $item): ?>
                                <div class="service-card">
                                    <div class="service-header">
                                        <h3><?php echo htmlspecialchars($item['service_name']); ?> (<?php echo htmlspecialchars($item['service_category']); ?>)</h3>
                                        <span class="service-price">MMK<?php echo number_format($item['service_price'], 2); ?></span>
                                    </div>
                                    <?php if ($item['service_category'] !== 'Chat Session'): ?>
                                        <div class="form-group">
                                            <label>Appointment Date <span class="required">*</span></label>
                                            <input type="datetime-local" name="appointment_date[<?php echo $index; ?>]" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Therapist <span class="required">*</span></label>
                                            <select name="therapist_id[<?php echo $index; ?>]" required>
                                                <option value="">Select Therapist</option>
                                                <?php foreach ($item['therapists'] as $therapist): ?>
                                                    <option value="<?php echo $therapist['therapist_id']; ?>">
                                                        <?php echo htmlspecialchars($therapist['therapist_name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Service Type <span class="required">*</span></label>
                                            <select name="service_type[<?php echo $index; ?>]" required>
                                                <option value="">Select Service Type</option>
                                                <option value="Video Call">Video Call</option>
                                                <option value="Audio Call">Audio Call</option>
                                                <option value="Chat">Chat</option>
                                            </select>
                                        </div>
                                    <?php else: ?>
                                        <p><em>Chat support available within 24 hours. A therapist will be assigned after booking.</em></p>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>

                            <div class="payment-method">
                                <h3>Payment Method <span class="required">*</span></h3>
                                <div class="payment-options">
                                    <div class="payment-option">
                                        <input type="radio" id="kpay" name="payment_method" value="Kpay" <?php echo ($payment_method === 'Kpay') ? 'checked' : ''; ?>>
                                        <label for="kpay">
                                            <i class="fas fa-wallet payment-icon"></i>
                                            <span class="payment-label">Kpay</span>
                                        </label>
                                    </div>
                                    <div class="payment-option">
                                        <input type="radio" id="aya-pay" name="payment_method" value="AYA Pay" <?php echo ($payment_method === 'AYA Pay') ? 'checked' : ''; ?>>
                                        <label for="aya-pay">
                                            <i class="fas fa-mobile-alt payment-icon"></i>
                                            <span class="payment-label">AYA Pay</span>
                                        </label>
                                    </div>
                                    <div class="payment-option">
                                        <input type="radio" id="credit-card" name="payment_method" value="Credit Card" <?php echo ($payment_method === 'Credit Card') ? 'checked' : ''; ?>>
                                        <label for="credit-card">
                                            <i class="fas fa-credit-card payment-icon"></i>
                                            <span class="payment-label">Credit Card</span>
                                        </label>
                                    </div>
                                    <div class="payment-option">
                                        <input type="radio" id="cb-pay" name="payment_method" value="CB Pay" <?php echo ($payment_method === 'CB Pay') ? 'checked' : ''; ?>>
                                        <label for="cb-pay">
                                            <i class="fas fa-money-bill-wave payment-icon"></i>
                                            <span class="payment-label">CB Pay</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="total-section">
                                <span>Total:</span>
                                <span>MMK<?php echo number_format($total, 2); ?></span>
                            </div>

                            <button type="submit" name="confirm_checkout" class="confirm-checkout">Complete Booking</button>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            flatpickr("input[type='datetime-local']", {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                minDate: "today"
            });

            <?php if (!$booking_confirmed): ?>
                document.getElementById('checkout-form').addEventListener('submit', function (e) {
                    let valid = true;

                    if (!document.querySelector('input[name="payment_method"]:checked')) {
                        alert('Please select a payment method');
                        valid = false;
                    }

                    document.querySelectorAll('input[name^="appointment_date"]').forEach(input => {
                        if (input.required && !input.value) {
                            alert('Please select all required appointment dates');
                            valid = false;
                        }
                    });

                    document.querySelectorAll('select[name^="therapist_id"]').forEach(select => {
                        if (select.required && !select.value) {
                            alert('Please select therapists for all required services');
                            valid = false;
                        }
                    });

                    document.querySelectorAll('select[name^="service_type"]').forEach(select => {
                        if (select.required && !select.value) {
                            alert('Please select a service type for all required services');
                            valid = false;
                        }
                    });

                    if (!valid) {
                        e.preventDefault();
                    }
                });
            <?php endif; ?>
        });
    </script>
</body>

</html>
<?php ob_end_flush(); ?>