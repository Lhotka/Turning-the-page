<?php
	$title = "Checkout";
	require "./template/header.php";
	// the shopping cart needs sessions, to start one
	/*
		Array of session(
			cart => array (
				book_isbn (get from $_GET['book_isbn']) => number of books
			),
			items => 0,
			total_price => '0.00'
		)
	*/

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
	</table>
	<!-- Form for user details and purchase button -->
	<form method="post" action="purchase.php" class="form-horizontal">
        <?php if(isset($_SESSION['err']) && $_SESSION['err'] == 1){ ?>
            <p class="text-danger">All fields have to be filled</p>
        <?php } ?>
        <!-- User details input fields -->
        <div class="form-group">
            <label for="name" class="control-label col-md-4">Name</label>
            <div class="col-md-4">
                <input type="text" id="name" name="name" class="form-control" autocomplete="name">
            </div>
        </div>
        <div class="form-group">
            <label for="address" class="control-label col-md-4">Address</label>
            <div class="col-md-4">
                <input type="text" id="address" name="address" class="form-control" autocomplete="address">
            </div>
        </div>
        <div class="form-group">
            <label for="city" class="control-label col-md-4">City</label>
            <div class="col-md-4">
                <input type="text" id="city" name="city" class="form-control" autocomplete="address-level2">
            </div>
        </div>
        <div class="form-group">
            <label for="zip_code" class="control-label col-md-4">Zip Code</label>
            <div class="col-md-4">
                <input type="text" id="zip_code" name="zip_code" class="form-control" autocomplete="postal-code">
            </div>
        </div>
        <div class="form-group">
            <label for="country" class="control-label col-md-4">Country</label>
            <div class="col-md-4">
                <input type="text" id="country" name="country" class="form-control" autocomplete="country">
            </div>
        </div>

        <!-- Button Group in the Same Row -->

        <div class="form-group">
            <div class="col-lg-10 col-lg-offset-4">
                <button type="submit" name="submit" class="btn btn-primary" value="Continue">Continue</button>
                <a href="cart.php" class="btn btn-default">Back to Cart</a>
            </div>
        </div>
    </form>

<?php
	} else {
		echo "<p class=\"text-warning\">Your cart is empty!</p>";
	}
	require_once "./template/footer.php";
?>
