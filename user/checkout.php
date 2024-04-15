<?php
$title = "Blagajna";
require "../header.php";

// Check if the user is logged in
checkLoggedIn();

$conn = dbConnect();

// košarica potrebuje seje, da se začne ena
/*
		Seznam sej(
			košarica => array (
				book_isbn (pridobi iz $_GET['book_isbn']) => število knjig
			),
			predmeti => 0,
			skupna_cena => '0.00'
		)
	*/

if (isset($_SESSION['cart']) && (array_count_values($_SESSION['cart']))) {

    require_once './cartlayoutnoedit.php';
?>
    <!-- Obrazec za uporabnikove podrobnosti in gumb za nakup -->
    <!-- Forma za zbiranje podrobnosti uporabnika -->
    <form method="post" action="purchase.php" class="form-horizontal">
        <!-- Sporočilo o napaki za nepopolna polja -->
        <?php if (isset($_SESSION['err']) && $_SESSION['err'] == 1) { ?>
            <p class="text-danger">Vsa polja morajo biti izpolnjena</p>
        <?php } ?>

        <!-- Polja za podrobnosti uporabnika -->
        <div class="form-group">
            <label for="ship_name" class="control-label col-md-4">Ime</label>
            <div class="col-md-4">
                <input type="text" id="ship_name" name="ship_name" class="form-control" autocomplete="name" data-placeholder-en="Name">
            </div>
        </div>
        <div class="form-group">
            <label for="ship_address" class="control-label col-md-4">Naslov</label>
            <div class="col-md-4">
                <input type="text" id="ship_address" name="ship_address" class="form-control" autocomplete="address" data-placeholder-en="Address">
            </div>
        </div>
        <div class="form-group">
            <label for="ship_city" class="control-label col-md-4">Mesto</label>
            <div class="col-md-4">
                <input type="text" id="ship_city" name="ship_city" class="form-control" autocomplete="address-level2" data-placeholder-en="City">
            </div>
        </div>
        <div class="form-group">
            <label for="ship_zip_code" class="control-label col-md-4">Poštna številka</label>
            <div class="col-md-4">
                <input type="text" id="ship_zip_code" name="ship_zip_code" class="form-control" autocomplete="postal-code" data-placeholder-en="Zip Code">
            </div>
        </div>

        <div class="form-group">
            <label for="ship_country" class="control-label col-md-4">Država</label>
            <div class="col-md-4">
                <input type="text" id="ship_country" name="ship_country" class="form-control" value="Slovenija" readonly>
            </div>
        </div>

        <!-- Gumbi v isti vrstici -->
        <div class="form-group">
            <div class="col-lg-10 col-lg-offset-4">
                <button type="submit" name="submit" class="btn btn-primary" value="Nadaljuj">Nadaljuj</button>
                <a href="cart.php" class="btn btn-default">Nazaj na košarico</a>
            </div>
        </div>
    </form>


<?php
} else {
    echo "<p class=\"text-warning\">Vaša košarica je prazna!</p>";
}
require_once "../footer.php";
?>