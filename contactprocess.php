<?php
$title = "Process Contact";
require_once __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["inputEmail"]) && isset($_POST["textArea"])) {
    $userEmail = $_POST["inputEmail"];
    $messageText = $_POST["textArea"];

    $subject = "New Contact Form Submission";
    $messageBody = "Email: $userEmail\nMessage: $messageText";

    // Verify inputs
    if (empty($userEmail) || empty($messageText)) {
        header("Location: " . $_SERVER['HTTP_REFERER'] . "?error=empty_fields");
        exit();
    }
    
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

    $mail = new PHPMailer(true);

    // Server settings
    try {
        $mail->SMTPDebug = 3;
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['GMAIL_USERNAME'];
        $mail->Password   = trim($_ENV['GMAIL_PASSWORD']);        
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom($userEmail, $userEmail);
        $mail->addAddress('filip.lhotka@gmail.com'); // Admin's email address

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $messageBody;

        // Send email
        $mail->send();

        // Set success message and redirect
        $_SESSION['success_message'] = 'Email sent successfully';
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    } catch (Exception $e) {
        // Set error message
        $_SESSION['error_message'] = 'Failed to send email';
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }    
} else {
    // If the form is not submitted via POST or keys are not set, redirect to the contact page
    header("Location: contact.php");
    exit();
}