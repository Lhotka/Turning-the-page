<?php
$title = "Uporabniška nadzorna plošča";
require_once "../header.php";

// Check if a message is passed in the URL
if (isset($_GET['success_message'])) {
    $message = $_GET['success_message'];
    // Display the success message
    echo '<div class="alert alert-success" role="alert" style="text-align:center;">' . $message . '</div>';
} elseif (isset($_GET['error_message'])) {
    $message = $_GET['error_message'];
    // Display the error message
    echo '<div class="alert alert-danger" role="alert" style="text-align:center;">' . $message . '</div>';
}

// Preusmeri na prijavno stran, če uporabnik ni prijavljen
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

// Preveri, ali uporabnik poskuša izpisati
if (isset($_POST['logout'])) {
    // Odstrani vse sejne spremenljivke
    $_SESSION = array();

    // Uniči sejo
    session_destroy();

    // Preusmeri na prijavno stran po odjavi
    header("Location: login.php");
    exit();
}
// Pridobi ID uporabnika iz seje
$userID = $_SESSION['user_id'];

if ($userID) {
    // Uporabnik najden, pridobi dodatne informacije z uporabo funkcije getUserData
    $conn = dbConnect();
    $userData = getUserData($conn, $userID);

    // Prikaži informacije o uporabniku
    $username = $userData['username'];
    $email = $userData['email'];

?>

    <div class="container">
        <h2>Dobrodošli <?php echo $username; ?>!</h2>
        <p>E-pošta: <?php echo $email; ?></p>

        <a href="userorders.php" class="btn btn-info" style="display: inline-block; margin: 10px;">Naročila</a>
        <!-- Dodaj logiko -->


        <!-- Dodaj več odsekov -->


        <!-- Gumb za odjavo -->
        <form method="post" action="userdash.php" style="display: inline-block;">
            <input type="submit" name="logout" style="margin: 10px;" class="btn btn-danger" value="Odjava">
        </form>
    </div>

<?php
} else {
    // Uporabnik ni najden, preusmeri na prijavno stran ali prikaži sporočilo o napaki
    header("Location: login.php");
    exit();
}

require_once "../footer.php";
?>
