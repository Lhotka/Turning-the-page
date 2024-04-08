<form action="cart.php" method="post">
    <table class="table">
        <tr>
            <th>Postavka</th>
            <th>Cena</th>
            <th>Količina</th>
            <th>Skupaj</th>
        </tr>
        <?php
            foreach($_SESSION['cart'] as $isbn => $qty){
                $conn = db_connect();
                $book = getBookByIsbn($conn, $isbn);
        ?>
        <tr>
            <td><?php echo $book['book_title'] . " - " . $book['authors']; ?></td>
            <td><?php echo $book['book_price'] . " €"; ?></td>
            <td><input type="text" value="<?php echo $qty; ?>" size="2" name="<?php echo $isbn; ?>"></td>
            <td><?php echo ($qty * $book['book_price']) . " €"; ?></td>
        </tr>
        <?php } ?>
        <tr>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th><?php echo $_SESSION['total_items']; ?></th>
            <th><?php echo $_SESSION['total_price'] . " €"; ?></th>
        </tr>
        <tr>
            <th>Delež DDV (5%):</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <td><?php echo ($_SESSION['total_price'] * 0.05) . " €"; ?></td>
        </tr>
    </table>
    <input type="submit" class="btn btn-default" name="save_change" value="Posodobi košarico">
</form>
<br/><br/>
<!-- Gumbi v isti vrstici -->
<div class="form-group">
    <a href="checkout.php" class="btn btn-primary">Nakup</a>
    <a href="books.php" class="btn btn-default">Nadaljuj z nakupovanjem</a>
</div>