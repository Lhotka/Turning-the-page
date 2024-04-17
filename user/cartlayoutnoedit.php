<table class="table">
    <tr>
        <th>Postavka</th>
        <th>Cena</th>
        <th>Količina</th>
        <th>Skupaj</th>
    </tr>
    <?php
    foreach ($_SESSION['cart'] as $isbn => $qty) {
        $book = getBookByIsbn($conn, $isbn);
    ?>
        <tr>
            <td><?php echo $book['book_title'] . " - " . $book['authors']; ?></td>
            <td><?php echo $book['book_price'] . " €"; ?></td>
            <td><?php echo $qty; ?></td>
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
        <td><?php echo number_format(($_SESSION['total_price'] * 0.05), 2) . " €"; ?></td>
    </tr>
    <tr>
        <th>Dostava</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <td>3.99€</td>
    </tr>
    <tr>
        <th colspan="3">Skupaj (vključno z dostavo)</th>
        <th STYLE="text-decoration:underline"><?php echo ($_SESSION['total_price'] + 3.99) . " €"; ?></th>
    </tr>
</table>