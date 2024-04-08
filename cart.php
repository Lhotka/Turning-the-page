<?php
$title = "Košarica";
require "./template/header.php";
require_once "./functions/cart_functions.php";

$conn=db_connect();

// Check if the user is logged in
checkLoggedIn();

// Pridobi ISBN knjige iz metode POST obrazca
if (isset($_POST['bookisbn'])) {
    $book_isbn = $_POST['bookisbn'];
}

// Če je ISBN knjige določen, obdelaj posodobitve v košarici
if (isset($book_isbn)) {
    // Ustvari košarico, če ni določena
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
        $_SESSION['total_items'] = 0;
        $_SESSION['total_price'] = '0.00';
    }

    // Če knjiga ni v košarici, jo dodaj s količino 1
    if (!isset($_SESSION['cart'][$book_isbn])) {
        $_SESSION['cart'][$book_isbn] = 1;
    } elseif (isset($_POST['cart'])) { // Če je kliknjen gumb 'Dodaj v košarico', povečaj količino
        $_SESSION['cart'][$book_isbn]++;
        unset($_POST);
    }
}

// Če je kliknjen gumb 'Shrani spremembo', posodobi količine v košarici
if (isset($_POST['save_change'])) {
    foreach ($_SESSION['cart'] as $isbn => $qty) {
        if ($_POST[$isbn] == '0') {
            unset($_SESSION['cart']["$isbn"]);
        } else {
            $_SESSION['cart']["$isbn"] = $_POST["$isbn"];
        }
    }
}

// Če košarica ni prazna, prikaži vsebino košarice
if (isset($_SESSION['cart']) && (array_count_values($_SESSION['cart']))) {
    // Pass $conn as an argument to total_price()
    $_SESSION['total_price'] = total_price($conn, $_SESSION['cart']);
    $_SESSION['total_items'] = total_items($_SESSION['cart']);

    require_once 'template/cart.php';
} else {
    echo "<p class=\"text-warning\">Vaša košarica je prazna!</p>";
}
require_once "./template/footer.php";
