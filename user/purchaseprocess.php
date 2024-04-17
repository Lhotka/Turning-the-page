<?php
$title = "Postopek nakupa";
require_once "../header.php";

// Preveri, ali je uporabnik prijavljen
checkLoggedIn();

$conn = dbConnect();

foreach ($_POST as $key => $value) {
    if (trim($value) == '') {
        $_SESSION['err'] = 0;
        break;
    }
}

if ($_SESSION['err'] == 0) {
    header("Location: checkout.php");
    exit(); // Dodajte izhod, da preprečite nadaljnje izvajanje
} else {
    unset($_SESSION['err']);
}

extract($_SESSION['ship']);

// Preverjanje oddelka posta
$card_type = isset($_POST['card_type']) ? $_POST['card_type'] : "";
$card_owner = isset($_POST['card_owner']) ? $_POST['card_owner'] : "";
$card_number = isset($_POST['card_number']) ? $_POST['card_number'] : "";
$card_CVV = isset($_POST['card_CVV']) ? $_POST['card_CVV'] : "";
$card_expire = isset($_POST['card_expire']) ? strtotime($_POST['card_expire']) : "";

$date = date("Y-m-d H:i:s");

// Pridobivanje podrobnosti o dostavi iz tabele $_SESSION['ship']
$ship_name = isset($_SESSION['ship']['ship_name']) ? $_SESSION['ship']['ship_name'] : "";
$ship_address = isset($_SESSION['ship']['ship_address']) ? $_SESSION['ship']['ship_address'] : "";
$ship_city = isset($_SESSION['ship']['ship_city']) ? $_SESSION['ship']['ship_city'] : "";
$ship_zip_code = isset($_SESSION['ship']['ship_zip_code']) ? $_SESSION['ship']['ship_zip_code'] : "";
$ship_country = isset($_SESSION['ship']['ship_country']) ? $_SESSION['ship']['ship_country'] : "";

// Pridobite customerid iz seje uporabnika
if (isset($_SESSION['user_id'])) {
    $customerid = $_SESSION['user_id'];
}

// Vstavljanje v tabelo naročil in shranjevanje rezultata
$insertOrderResult = insertIntoOrder($conn, $customerid, $_SESSION['total_price'], $date, $ship_name, $ship_address, $ship_city, $ship_zip_code, $ship_country);

// Preveri, ali je vstavljanje uspešno
if ($insertOrderResult) {
    // Pridobite ID naročila
    $orderid = getOrderId($conn, $customerid);

    // Vstavi predmete naročila v bazo podatkov
    foreach ($_SESSION['cart'] as $isbn => $qty) {
        $bookprice = getBookPrice($conn, $isbn);

        // Uporaba pripravljenih izjav, da se prepreči SQL injekcija
        $query = "INSERT INTO order_items (orderid, book_isbn, book_price, quantity) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);

        mysqli_stmt_bind_param($stmt, "isdi", $orderid, $isbn, $bookprice, $qty);
        $result = mysqli_stmt_execute($stmt);

        if (!$result) {
            echo "Vstavljanje vrednosti ni uspelo!" . mysqli_error($conn);
            exit;
        }
    }
    // Prikaži sporočilo o uspehu
    $success_message = 'Vaše naročilo je bilo uspešno obdelano.<br/>Prosimo, preverite svoj e-poštni nabiralnik za potrditev naročila in podrobnosti o dostavi!<br/>Vaša košarica je sedaj prazna.';
    // Prazni košarico po uspešnem naročilu
    unset($_SESSION['cart']);
    // Redirect user to userdash.php after successful order
    header("Location: userdash.php?success_message=" . urlencode($success_message));
    exit();
} else {
    // Prikaži sporočilo o napaki
    $error_message = 'Prišlo je do napake pri obdelavi vašega naročila.<br/>Prosimo, poskusite znova ali stopite v stik s podporo.';
    echo '<div class="alert alert-danger" role="alert" style="text-align:center;">' . $error_message . '</div>';
    header("Location: userdash.php?error_message=" . urlencode($error_message));
    exit();
}
