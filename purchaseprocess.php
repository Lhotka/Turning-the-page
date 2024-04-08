<?php
$title = "Purchase Process";
require_once "./template/header.php";

// Check if the user is logged in
checkLoggedIn();

print_r($_SESSION);
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

// Insert order into the database
echo "Inserting order...<br>"; // Debugging
echo "$ship_name, $ship_address, $ship_city, $ship_zip_code, $ship_country";
insertIntoOrder($conn, $customerid, $_SESSION['total_price'], $date, $ship_name, $ship_address, $ship_city, $ship_zip_code, $ship_country);

// Retrieve the order ID
$orderid = getOrderId($conn, $customerid);

// Insert order items into the database
foreach ($_SESSION['cart'] as $isbn => $qty) {
    $bookprice = getbookprice($conn, $isbn);

    // Use prepared statements to prevent SQL injection
    $query = "INSERT INTO order_items (orderid, book_isbn, book_price, quantity) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);

    // Debugging the data types and values
    echo "Data types and values: ";
    var_dump($orderid, $isbn, $bookprice, $qty);

    // Adjusted data types: "isdi" -> "isdii"
    mysqli_stmt_bind_param($stmt, "isdi", $orderid, $isbn, $bookprice, $qty);
    $result = mysqli_stmt_execute($stmt);

    if (!$result) {
        echo "Insert value false!" . mysqli_error($conn);
        exit;
    }
}

//session_unset();

?>

<p class="lead text-success">Your order has been processed successfully. Please check your email to get your order confirmation and shipping details! Your cart is now empty.</p>

<?php
require_once "./template/footer.php";
?>