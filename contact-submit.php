<?php
require_once __DIR__ . '/config/app.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: contact.html?error=1');
    exit;
}

$name = trim((string) ($_POST['name'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$message = trim((string) ($_POST['message'] ?? ''));

if ($name === '' || $email === '' || $message === '') {
    header('Location: contact.html?error=1');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: contact.html?error=1');
    exit;
}

$to = (string) env('CONTACT_EMAIL', '22nysam@gmail.com');
$from = (string) env('MAIL_FROM', 'no-reply@yourdomain.com');
$fromName = (string) env('MAIL_FROM_NAME', 'Alpha Moussa Sow Portfolio');

$subject = 'Nouveau message depuis le portfolio';
$body = "Nom: {$name}\n";
$body .= "Email: {$email}\n\n";
$body .= "Message:\n{$message}\n";

$headers = [
    'From: ' . $fromName . ' <' . $from . '>',
    'Reply-To: ' . $email,
    'X-Mailer: PHP/' . phpversion(),
    'Content-Type: text/plain; charset=UTF-8',
];

$sent = mail($to, $subject, $body, implode("\r\n", $headers));

if (!$sent) {
    header('Location: contact.html?error=1');
    exit;
}

header('Location: contact.html?success=1');
exit;
