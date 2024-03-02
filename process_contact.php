<?php
$title="Process Contact";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


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

    // Initialize PHPMailer
    require_once 'vendor/autoload.php';
    
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
    

    // Debugging: Print loaded environment variables
    echo '<pre>';
    print_r($_ENV);
    echo '</pre>';

    $mail = new PHPMailer(true);

//Server settings
    try {
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = getenv('GMAIL_USERNAME');
        $mail->Password   = trim(getenv('GMAIL_PASSWORD'));
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom($userEmail);
        $mail->addAddress('filip.lhotka@gmail.com'); // Replace with admin's email address

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $messageBody;

        // Send email
        $mail->send();

        // Set success message and redirect
        header("Location: " . $_SERVER['HTTP_REFERER'] . "?thanks=1");
        exit();
    } catch (Exception $e) {
        // Set error message and redirect
        echo 'Mailer Error: ' . $mail->ErrorInfo;
        //header("Location: " . $_SERVER['HTTP_REFERER'] . "?error=mail_failed");
        exit();
    }    
} else {
    // If the form is not submitted via POST or keys are not set, redirect to the contact page
    header("Location: contact.php");
    exit();
}
?>
