<?php
$title = "Purchase Process";
require_once "./template/header.php";

// Check if the user is logged in
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
    exit(); // Add exit to prevent further execution
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

// Retrieve shipping details from $_SESSION['ship'] array
$ship_name = isset($_SESSION['ship']['ship_name']) ? $_SESSION['ship']['ship_name'] : "";
$ship_address = isset($_SESSION['ship']['ship_address']) ? $_SESSION['ship']['ship_address'] : "";
$ship_city = isset($_SESSION['ship']['ship_city']) ? $_SESSION['ship']['ship_city'] : "";
$ship_zip_code = isset($_SESSION['ship']['ship_zip_code']) ? $_SESSION['ship']['ship_zip_code'] : "";
$ship_country = isset($_SESSION['ship']['ship_country']) ? $_SESSION['ship']['ship_country'] : "";

// Retrieve customerid from the user session
if (isset($_SESSION['user_id'])) {
    $customerid = $_SESSION['user_id'];
}

// Insert into orders table and store the result
$insertOrderResult = insertIntoOrder($conn, $customerid, $_SESSION['total_price'], $date, $ship_name, $ship_address, $ship_city, $ship_zip_code, $ship_country);

// Check if the insertion was successful
if ($insertOrderResult) {
    // Retrieve the order ID
    $orderid = getOrderId($conn, $customerid);

    // Insert order items into the database
    foreach ($_SESSION['cart'] as $isbn => $qty) {
        $bookprice = getBookPrice($conn, $isbn);

        // Use prepared statements to prevent SQL injection
        $query = "INSERT INTO order_items (orderid, book_isbn, book_price, quantity) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);

        mysqli_stmt_bind_param($stmt, "isdi", $orderid, $isbn, $bookprice, $qty);
        $result = mysqli_stmt_execute($stmt);

        if (!$result) {
            echo "Insert value false!" . mysqli_error($conn);
            exit;
        }
    }

    // Display success message
    echo '<div class="alert alert-success" role="alert" style="text-align:center";>Your order has been processed successfully.<br/>Please check your email to get your order confirmation and shipping details!<br/>Your cart is now empty.</div>';

} else {
    // Display error message
    echo '<div class="alert alert-danger" role="alert" style="text-align:center";>There was an error processing your order. Please try again later or contact support.</div>';
}

require_once "./template/footer.php";
