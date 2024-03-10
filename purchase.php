<?php
	$_SESSION['err'] = 1;
	foreach($_POST as $key => $value){
		if(trim($value) == ''){
			$_SESSION['err'] = 0;
		}
		break;
	}

	if($_SESSION['err'] == 0){
		header("Location: checkout.php");
	} else {
		unset($_SESSION['err']);
	}


	$_SESSION['ship'] = array();
	foreach($_POST as $key => $value){
		if($key != "submit"){
			$_SESSION['ship'][$key] = $value;
		}
	}

	$title = "Purchase";
	require "./template/header.php";
	// connect database
	if(isset($_SESSION['cart']) && (array_count_values($_SESSION['cart']))){
?>
	<table class="table">
		<tr>
			<th>Item</th>
			<th>Price</th>
	    	<th>Quantity</th>
	    	<th>Total</th>
	    </tr>
	    	<?php
			    foreach($_SESSION['cart'] as $isbn => $qty){
					$conn = db_connect();
					$book = mysqli_fetch_assoc(getBookByIsbn($conn, $isbn));
			?>
		<tr>
			<td><?php echo $book['book_title'] . " by " . $book['book_author']; ?></td>
			<td><?php echo "€" . $book['book_price']; ?></td>
			<td><?php echo $qty; ?></td>
			<td><?php echo "€" . $qty * $book['book_price']; ?></td>
		</tr>
		<?php } ?>
		<tr>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
			<th><?php echo $_SESSION['total_items']; ?></th>
			<th><?php echo "€" . $_SESSION['total_price']; ?></th>
		</tr>
		<tr>
			<td>Shipping</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>4.99</td>
		</tr>
		<tr>
			<th>Total (Including Shipping)</th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
			<th><?php echo "€" . ($_SESSION['total_price'] + 4.99); ?></th>
		</tr>
	</table>
    <form method="post" action="process.php" class="form-horizontal">
        <?php if(isset($_SESSION['err']) && $_SESSION['err'] == 1){ ?>
            <p class="text-danger">All fields have to be filled</p>
        <?php } ?>
        <div class="form-group">
            <label for="card_type" class="col-md-4 control-label">Type</label>
            <div class="col-md-4">
                <select id="card_type" name="card_type" class="form-control">
                    <option value="VISA">VISA</option>
                    <option value="MasterCard">MasterCard</option>
                    <!-- Add Paypal and other options -->
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="card_owner" class="col-md-4 control-label">Name On Card</label>
            <div class="col-md-4">
                <input type="text" id="card_owner" name="card_owner" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label for="card_number" class="col-md-4 control-label">Card Number</label>
            <div class="col-md-4">
                <input type="text" id="card_number" name="card_number" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label for="card_CVV" class="col-md-4 control-label">CVV Code</label>
            <div class="col-md-4">
                <input type="text" id="card_CVV" name="card_CVV" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label for="card_expire" class="col-md-4 control-label">Expiry Date</label>
            <div class="col-md-4">
                <input type="month" id="card_expire" name="card_expire" class="form-control"
                min="<?php echo date('Y-m');//only present and future months ?>">
            </div>
        </div>
        <div class="form-group">
            <div class="col-lg-10 col-lg-offset-4">
                <button type="submit" class="btn btn-primary">Purchase</button>
                <a href="checkout.php" class="btn btn-default">Go back</a>
            </div>
        </div>
    </form>

<?php
	} else {
		echo "<p class=\"text-warning\">Your cart is empty!</p>";
	}
	require_once "./template/footer.php";
?>