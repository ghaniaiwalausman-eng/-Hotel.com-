// ajax/toggle-favorite.php
<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$hotel_id = $_POST['hotel_id'] ?? 0;

if (!$hotel_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid hotel']);
    exit();
}

$result = toggleFavorite($user_id, $hotel_id);

if ($result) {
    echo json_encode(['success' => true, 'action' => $result]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
