<?php
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$dotenvFilePath = 'C:\xampp\htdocs\FINAL\.env';

    if (!file_exists($dotenvFilePath) || !is_readable($dotenvFilePath)) {
        die('Error: Unable to read the .env file.');
    }
    
    $dotenvFilePath = 'C:\xampp\htdocs\FINAL'; // Point directly to the directory, not the file

try {
    $dotenv = Dotenv\Dotenv::createImmutable($dotenvFilePath);
    $dotenv->load();
} catch (\Dotenv\Exception\InvalidPathException $e) {
    die('Error: Unable to load the .env file. ' . $e->getMessage());
}

// Create a new PHPMailer instance
$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->SMTPDebug = 2;   // Enable verbose debug output
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['GMAIL_USERNAME'];
        $mail->Password   = trim($_ENV['GMAIL_PASSWORD']);  
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    // Recipients
    $mail->setFrom('filip.lhotka@gmail.com', 'Filip');  // Your name and email
    $mail->addAddress('filip.lhotka@gmail.com');    // Recipient's email address

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Test Email';
    $mail->Body    = 'This is a test email sent using PHPMailer.';

    // Send email
    $mail->send();
    
    echo 'Email sent successfully!';
} catch (Exception $e) {
    echo 'Error: ' . $mail->ErrorInfo;
}
?>
