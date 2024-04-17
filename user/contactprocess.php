<?php
$title = "Procesiranje kontaktnega obrazca";
require_once "../header.php";
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["inputEmail"]) && isset($_POST["textArea"])) {
    $userEmail = $_POST["inputEmail"];
    $messageText = $_POST["textArea"];

    $subject = "Nova vloga preko kontaktnega obrazca";
    $messageBody = "E-pošta: $userEmail\nSporočilo: $messageText";

    // Preveri vnose
    if (empty($userEmail) || empty($messageText)) {
        header("Location: " . $_SERVER['HTTP_REFERER'] . "?error=empty_fields");
        exit();
    }

    $dotenvFilePath = 'C:\xampp\htdocs\FINAL\.env';

    if (!file_exists($dotenvFilePath) || !is_readable($dotenvFilePath)) {
        die('Napaka: Ni mogoče prebrati datoteke .env.');
    }

    $dotenvFilePath = 'C:\xampp\htdocs\FINAL'; // Usmeri neposredno v mapo, ne v datoteko

    try {
        $dotenv = Dotenv\Dotenv::createImmutable($dotenvFilePath);
        $dotenv->load();
    } catch (\Dotenv\Exception\InvalidPathException $e) {
        die('Napaka: Ni mogoče naložiti datoteke .env. ' . $e->getMessage());
    }

    $mail = new PHPMailer(true);

    // Nastavitve strežnika
    try {
        $mail->SMTPDebug = 0; //2 za razhroščevanje
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['GMAIL_USERNAME'];
        $mail->Password   = trim($_ENV['GMAIL_PASSWORD']);
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Prejemniki
        $mail->setFrom($userEmail, $userEmail);
        $mail->addAddress($_ENV['GMAIL_USERNAME']);

        // Vsebina
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $messageBody;

        // Pošlji e-pošto
        $mail->send();

        // Nastavi sporočilo o uspehu in preusmeri
        $_SESSION['success_message'] = 'E-pošta uspešno poslana';
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    } catch (Exception $e) {
        // Zabeleži sporočilo o napaki za razhroščevanje
        $errorLog = fopen('error_log.txt', 'a');
        fwrite($errorLog, date('Y-m-d H:i:s') . ' Napaka: ' . $e->getMessage() . PHP_EOL);
        fclose($errorLog);

        // Nastavi sporočilo o napaki
        $_SESSION['error_message'] = 'Pošiljanje e-pošte ni uspelo';
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }
} else {
    // Če obrazec ni poslan preko POST ali če ključi niso nastavljeni, preusmeri na stran za stik
    header("Location: contact.php");
    exit();
}
?>
