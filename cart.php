<?php
    $title = "Shopping cart";
    require "./template/header.php";

    // Include necessary functions
    require_once "./functions/cart_functions.php";


    // Get book ISBN from the form post method
    if(isset($_POST['bookisbn'])){
        $book_isbn = $_POST['bookisbn'];
    }

    // If book ISBN is set, handle cart updates
    if(isset($book_isbn)){
        // Initialize cart if not set
        if(!isset($_SESSION['cart'])){
            $_SESSION['cart'] = array();
            $_SESSION['total_items'] = 0;
            $_SESSION['total_price'] = '0.00';
        }

        // If book is not in cart, add it with quantity 1
        if(!isset($_SESSION['cart'][$book_isbn])){
            $_SESSION['cart'][$book_isbn] = 1;
        } elseif(isset($_POST['cart'])){ // If 'Add to cart' button is clicked, increment quantity
            $_SESSION['cart'][$book_isbn]++;
            unset($_POST);
        }
    }

    // If 'save change' button is clicked, update quantities in the cart
    if(isset($_POST['save_change'])){
        foreach($_SESSION['cart'] as $isbn =>$qty){
            if($_POST[$isbn] == '0'){
                unset($_SESSION['cart']["$isbn"]);
            } else {
                $_SESSION['cart']["$isbn"] = $_POST["$isbn"];
            }
        }
    }

    // If cart is not empty, display cart contents
    if(isset($_SESSION['cart']) && (array_count_values($_SESSION['cart']))){
        $_SESSION['total_price'] = total_price($_SESSION['cart']);
        $_SESSION['total_items'] = total_items($_SESSION['cart']);
?>
    <form action="cart.php" method="post">
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
                $book = getBookByIsbn($conn, $isbn);
            ?>
            <tr>
                <td><?php echo $book['book_title'] . " by " . $book['authors']; ?></td>
                <td><?php echo "€" . $book['book_price']; ?></td>
                <td><input type="text" value="<?php echo $qty; ?>" size="2" name="<?php echo $isbn; ?>"></td>
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
        <input type="submit" class="btn btn-default" name="save_change" value="Update cart">
    </form>
    <br/><br/>
    <!-- Button Group in the Same Row -->
    <div class="form-group">
        <a href="checkout.php" class="btn btn-primary">Checkout</a>
        <a href="books.php" class="btn btn-default">Continue Shopping</a>
    </div>

<?php
    } else {
        echo "<p class=\"text-warning\">Your cart is empty!</p>";
    }
    require_once "./template/footer.php";
?>
