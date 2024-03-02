<?php
require_once __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->SMTPDebug = 2; // Enable debugging for detailed information
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = getenv('GMAIL_USERNAME');
    $mail->Password   = 'xtug rjww zqxi yxhq'; // Use the actual password here
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    // Recipients
    $mail->setFrom('filip.lhotka@gmail.com', 'Your Name');
    $mail->addAddress('filip.lhotka@gmail.com', 'Recipient Name');

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Test Email';
    $mail->Body    = 'This is a test email.';

    // Send email
    $mail->send();
    echo 'Test email sent!';
} catch (Exception $e) {
    echo 'Mailer Error: ' . $mail->ErrorInfo;
}
?>