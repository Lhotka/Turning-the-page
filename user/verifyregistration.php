<?php
$title = "Preverjanje registracije";
require_once "../header.php";

$conn = dbConnect();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["register"])) {
    // Retrieve user input
    $newEmail = filter_input(INPUT_POST, 'new_email', FILTER_SANITIZE_EMAIL);
    $newUsername = filter_input(INPUT_POST, 'new_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $newPassword = $_POST['new_pass'];

    // Validate and sanitize the input (add additional validation if needed)

    // Check if the email already exists
    if (!isEmailExists($conn, $newEmail)) {

        // Add the user to the database
        if (addUser($conn, $newEmail, $newUsername, $newPassword)) {
            // Registration successful
            // Get the user ID
            $userID = mysqli_insert_id($conn);

            session_start();

            // Set session variables to indicate user is logged in
            $_SESSION['user_email'] = $newEmail;
            $_SESSION['user_id'] = $userID;
            header("Location: userdash.php"); // Redirect to the user page
            exit();
        } else {
            echo "Registracija ni uspela. Poskusite znova.";
        }
    } else {
        echo "E-pošta že obstaja. Prosimo, izberite drug e-poštni naslov.";
    }
} else {
    // Redirect to the registration page if accessed without proper form submission
    header("Location: login.php");
    exit();
}

require_once "../footer.php";
