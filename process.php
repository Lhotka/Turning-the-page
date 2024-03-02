<?php
$title = "Purchase Process";
require_once "./template/header.php";

foreach ($_POST as $key => $value) {
    if (trim($value) == '') {
        $_SESSION['err'] = 0;
        break;
    }
}

if ($_SESSION['err'] == 0) {
    header("Location: checkout.php");
} else {
    unset($_SESSION['err']);
}

extract($_SESSION['ship']);

// validate post section
$card_type = isset($_POST['card_type']) ? $_POST['card_type'] : "";
$card_owner = isset($_POST['card_owner']) ? $_POST['card_owner'] : "";
$card_number = isset($_POST['card_number']) ? $_POST['card_number'] : "";
$card_CVV = isset($_POST['card_CVV']) ? $_POST['card_CVV'] : "";
$card_expire = isset($_POST['card_expire']) ? strtotime($_POST['card_expire']) : "";

$date = date("Y-m-d H:i:s");
insertIntoOrder($conn, null, $_SESSION['total_price'], $date, $ship_name, $ship_address, $ship_city, $ship_zip_code, $ship_country);

// take orderid from the order to insert order items
$orderid = getOrderId($conn, null);

foreach ($_SESSION['cart'] as $isbn => $qty) {
    $bookprice = getbookprice($isbn);

    // Use prepared statements to prevent SQL injection
    $query = "INSERT INTO order_items (orderid, book_isbn, book_price, quantity) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);

    mysqli_stmt_bind_param($stmt, "isii", $orderid, $isbn, $bookprice, $qty);
    $result = mysqli_stmt_execute($stmt);

    if (!$result) {
        echo "Insert value false!" . mysqli_error($conn);
        exit;
    }
}

session_unset();

?>
<p class="lead text-success">Your order has been processed successfully. Please check your email to get your order confirmation and shipping details! Your cart has been empty.</p>

<?php

require_once "./template/footer.php";
?>
