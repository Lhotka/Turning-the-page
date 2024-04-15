<?php
$title = "Nakup";
require_once "../header.php";
require_once "../functions/cart_functions.php";

// Check if the user is logged in
checkLoggedIn();

$conn = dbConnect();

$_SESSION['err'] = 1;
foreach ($_POST as $key => $value) {
    if (trim($value) == '') {
        $_SESSION['err'] = 0;
    }
    break;
}

if ($_SESSION['err'] == 0) {
    header("Location: checkout.php");
} else {
    unset($_SESSION['err']);
}


$_SESSION['ship'] = array();
foreach ($_POST as $key => $value) {
    if ($key != "submit") {
        $_SESSION['ship'][$key] = $value;
    }
}

if (isset($_SESSION['cart']) && (array_count_values($_SESSION['cart']))) {
    require_once './cartlayoutnoedit.php';
?>
    <script src="https://kit.fontawesome.com/14c1c695aa.js" crossorigin="anonymous"></script>

    <form method="post" action="purchaseprocess.php" class="form-horizontal">
        <?php if (isset($_SESSION['err']) && $_SESSION['err'] == 1) { ?>
            <p class="text-danger">Vsa polja morajo biti izpolnjena</p>
        <?php } ?>
        <div class="form-group">
            <label for="card_type" class="col-md-4 control-label">Način plačila</label>
            <div class="col-md-4">
                <select id="card_type" name="card_type" class="form-control">
                    <option value="Kartica">Kartica</option>
                    <!-- Dodaj Paypal, po povzetju-->
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="card_owner" class="col-md-4 control-label">Ime na kartici</label>
            <div class="col-md-4">
                <input type="text" id="card_owner" name="card_owner" class="form-control" data-placeholder-en="Card name">
            </div>
        </div>
        <div class="form-group">
            <label for="card_number" class="col-md-4 control-label">Številka kartice</label>
            <div class="col-md-4">
                <!-- Use PHP function call with isset() -->
                <input type="text" id="card_number" name="card_number" class="form-control" data-placeholder-en="Card Number" value="<?php echo isset($_POST['card_number']) ? $_POST['card_number'] : ''; ?>">
            </div>
            <!-- Container to display the credit card icon -->
            <div id="card_icon" class="col-md-4" style="line-height: 38px; font-size: 24px;">
                <?php echo isset($_POST['card_number']) ? detectCreditCardType($_POST['card_number']) : ''; ?>
            </div>
        </div>
        <div class="form-group">
            <label for="card_CVV" class="col-md-4 control-label">CVV koda</label>
            <div class="col-md-4">
                <input type="text" id="card_CVV" name="card_CVV" class="form-control" data-placeholder-en="CVV Code">
            </div>
        </div>
        <div class="form-group">
            <label for="card_expire" class="col-md-4 control-label">Datum poteka</label>
            <div class="col-md-4">
                <input type="month" id="card_expire" name="card_expire" class="form-control" min="<?php echo date('Y-m'); ?>" data-placeholder-en="Expiration Date">
            </div>
        </div>
        <div class="form-group">
            <div class="col-lg-10 col-lg-offset-4">
                <button type="submit" class="btn btn-primary">Nakup</button>
                <a href="checkout.php" class="btn btn-default">Nazaj</a>
            </div>
        </div>
    </form>
    <script>
        // Script to dynamically detect and display credit card type icon
        document.getElementById('card_number').addEventListener('input', function() {
            var cardNumber = this.value;
            var cardIcon = document.getElementById('card_icon');
            cardIcon.innerHTML = detectCreditCardType(cardNumber);
        });

        function detectCreditCardType(accountNumber) {
            var type = getCreditCardType(accountNumber);
            var iconHTML = ''; // Initialize an empty string to hold the HTML for the icon

            switch (type) {
                case "mastercard":
                    iconHTML = '<i class="fab fa-cc-mastercard"></i>';
                    break;

                case "visa":
                    iconHTML = '<i class="fab fa-cc-visa"></i>';
                    break;

                case "amex":
                    iconHTML = '<i class="fab fa-cc-amex"></i>';
                    break;

                default:
                    iconHTML = ''; // No icon for unknown card type
                    break;
            }

            return iconHTML; // Return the HTML for the icon
        }

        // Function to detect credit card type based on the card number
        function getCreditCardType(accountNumber) {
            // Regular expressions for different card types
            var cardTypes = {
                mastercard: /^5[1-5]/,
                visa: /^4/,
                amex: /^3[47]/
            };

            // Loop through card types and return the type if the card number matches
            for (var type in cardTypes) {
                if (cardTypes[type].test(accountNumber)) {
                    return type;
                }
            }

            // Return unknown if no match is found
            return "unknown";
        }
    </script>
<?php
} else {
    echo "<p class=\"text-warning\">Vaša košarica je prazna!</p>";
}
require_once "../footer.php";
?>