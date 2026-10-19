<?php
header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if (empty($name) || empty($email) || empty($subject) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Please complete all required fields.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please provide a valid email address.']);
    exit;
}

$saved = saveContactMessage($name, $email, $subject, $message);

if ($saved) {
    // Notify Admin (Malik Raheem) via email
    $adminEmail = 'malik2341971@gmail.com';
    $mailSubject = "Portfolio Inquiry from {$name}: {$subject}";
    $mailBody = "Assalam-o-Alaikum Malik Raheem,\n\n"
        . "You have received a new inquiry from your live portfolio:\n\n"
        . "--------------------------------------------------\n"
        . "Sender Name:   " . $name . "\n"
        . "Sender Email:  " . $email . "\n"
        . "Subject:       " . $subject . "\n"
        . "Date & Time:   " . date('Y-m-d H:i:s') . "\n"
        . "--------------------------------------------------\n\n"
        . "Message:\n" . $message . "\n\n"
        . "--------------------------------------------------\n"
        . "You can reply to this message directly from your Admin Dashboard:\n"
        . "https://iamraheem.com/admin/messages.php\n";

    $headers = "From: portfolio@iamraheem.com\r\n"
        . "Reply-To: {$email}\r\n"
        . "X-Mailer: PHP/" . phpversion();

    @mail($adminEmail, $mailSubject, $mailBody, $headers);

    echo json_encode([
        'success' => true,
        'message' => 'Thank you, ' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '! Your message regarding "' . htmlspecialchars($subject, ENT_QUOTES, 'UTF-8') . '" has been sent successfully. Malik Raheem will get back to you shortly.'
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save message. Please try again.']);
}
