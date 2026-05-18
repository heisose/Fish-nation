<?php
/**
 * actions/newsletter-submit.php
 * Handles AJAX newsletter signup from index.php footer
 */
include('../config/db.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

$email = trim($_POST['email'] ?? '');

if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}

// Check if already subscribed
$check = $conn->prepare("SELECT id FROM newsletter_subscribers WHERE email = ?");
$check->bind_param('s', $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo json_encode(['success' => true, 'message' => 'You are already subscribed!']);
    $check->close();
    exit;
}
$check->close();

// Insert new subscriber
$stmt = $conn->prepare(
    "INSERT INTO newsletter_subscribers (email, subscribed_at) VALUES (?, NOW())"
);

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error. Please try again.']);
    exit;
}

$stmt->bind_param('s', $email);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Subscribed successfully!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Could not subscribe. Please try again.']);
}

$stmt->close();
$conn->close();
