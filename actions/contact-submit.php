<?php
/**
 * actions/contact-submit.php
 * Handles AJAX contact form submissions from index.php
 */
include('../config/db.php');

header('Content-Type: application/json');

// Only accept AJAX POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

$name    = trim($_POST['name']    ?? '');
$email   = trim($_POST['email']   ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

// Validate
if (!$name || !$email || !$subject || !$message) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}

if (strlen($message) < 10) {
    echo json_encode(['success' => false, 'message' => 'Message is too short.']);
    exit;
}

// Prepared statement — safe from SQL injection
$stmt = $conn->prepare(
    "INSERT INTO contact_messages (name, email, subject, message, created_at)
     VALUES (?, ?, ?, ?, NOW())"
);

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error. Please try again.']);
    exit;
}

$stmt->bind_param('ssss', $name, $email, $subject, $message);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Message sent successfully!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Could not save your message. Please try again.']);
}

$stmt->close();
$conn->close();
