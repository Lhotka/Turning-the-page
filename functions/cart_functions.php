<?php
/*
		loop through array of $_SESSION['cart'][book_isbn] => number
		get isbn => take from database => take book price
		price * number (quantity)
		return sum of price
	*/

// Modify the total_price() function to accept the $conn variable as an argument
function total_price($conn, $cart)
{
    $price = 0.0;
    if (is_array($cart)) {
        foreach ($cart as $isbn => $qty) {
            // Pass $conn as the first argument to getBookPrice()
            $bookprice = getBookPrice($conn, $isbn);
            if ($bookprice) {
                $price += $bookprice * $qty;
            }
        }
    }
    return $price;
}

/*
		loop through array of $_SESSION['cart'][book_isbn] => number
		$_SESSION['cart'] is associative array which is [book_isbn] => number of books for each book_isbn
		calculate sum of books 
	*/
function total_items($cart)
{
    $items = 0;
    if (is_array($cart)) {
        foreach ($cart as $isbn => $qty) {
            $items += $qty;
        }
    }
    return $items;
}

function getCreditCardType($accountNumber)
{
    // Start without knowing the credit card type
    $result = "unknown";

    // First check for MasterCard
    if (preg_match('/^5[1-5]/', $accountNumber)) {
        $result = "mastercard";
    }

    // Then check for Visa
    else if (preg_match('/^4/', $accountNumber)) {
        $result = "visa";
    }

    // Then check for AmEx
    else if (preg_match('/^3[47]/', $accountNumber)) {
        $result = "amex";
    }

    return $result;
}
function detectCreditCardType($accountNumber)
{
    $type = getCreditCardType($accountNumber);
    $iconHTML = ''; // Initialize an empty string to hold the HTML for the icon

    switch ($type) {
        case "mastercard":
            $iconHTML = '<i class="fab fa-cc-mastercard"></i>';
            break;

        case "visa":
            $iconHTML = '<i class="fab fa-cc-visa"></i>';
            break;

        case "amex":
            $iconHTML = '<i class="fab fa-cc-amex"></i>';
            break;

        default:
            $iconHTML = ''; // No icon for unknown card type
            break;
    }

    return $iconHTML; // Return the HTML for the icon
}
