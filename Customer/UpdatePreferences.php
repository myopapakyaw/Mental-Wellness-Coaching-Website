<?php
session_start(); // Start the session

header('Content-Type: application/json'); 

$input = file_get_contents('php://input');
$data = json_decode($input, true);

$validGoals = ['general', 'trauma', 'anxiety', 'depression'];

if (isset($data['goal']) && in_array($data['goal'], $validGoals)) {
    // Update session preferences
    $_SESSION['user_preferences']['content_type'] = $data['goal'];
    echo json_encode(['success' => true, 'message' => 'Preferences updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid or missing goal. Valid options: general, trauma, anxiety, depression']);
}

exit;
?>