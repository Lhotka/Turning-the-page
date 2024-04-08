<?php
require_once "./functions/database_functions.php";
$book_isbn = $_GET['bookisbn'];

$conn = db_connect();

// Fetch book details
$query = "SELECT b.*, GROUP_CONCAT(a.author_name SEPARATOR ', ') AS avtorji
          FROM book b
          LEFT JOIN book_author ba ON b.book_isbn = ba.book_isbn
          LEFT JOIN author a ON ba.author_id = a.author_id
          WHERE b.book_isbn = '$book_isbn'
          GROUP BY b.book_isbn";

$result = mysqli_query($conn, $query);
if (!$result) {
    echo "Podatkov ni mogoče pridobiti " . mysqli_error($conn);
    exit;
}

$row = mysqli_fetch_assoc($result);
if (!$row) {
    echo "Prazna knjiga";
    exit;
}

$title = $row['book_title'];
require_once "./template/header.php";
?>

<p class="lead" style="margin: 25px 0"><a href="books.php">Vse knjige</a> > <?php echo $row['book_title']; ?></p>
<div class="row">
    <div class="col-md-3 text-center">
        <img class="img-responsive img-thumbnail" src="./bootstrap/img/<?php echo $row['book_image']; ?>">
    </div>
    <div class="col-md-6">

        <h4>Podrobnosti knjige</h4>

        <table class="table">
            <tr>
                <td>ISBN</td>
                <td><?php echo $row['book_isbn']; ?></td>
            </tr>
            <tr>
                <td>Naslov</td>
                <td><?php echo $row['book_title']; ?></td>
            </tr>
            <tr>
                <td>Avtor</td>
                <td><?php echo $row['avtorji']; ?></td>
            </tr>
            <tr>
                <td>Založnik</td>
                <td><?php echo getPubName($conn, $row['publisher_id']); ?></td>
            </tr>
            <tr>
                <td>Datum izdaje</td>
                <td><?php echo $row['book_pub_date']; ?></td>
            </tr>
            <tr>
                <td>Cena</td>
                <td><?php echo $row['book_price'] . " €"; ?></td>
            </tr>

            <tr>
                <td>Na voljo</td>
                <td>
                    <?php
                    $stock = $row['book_quantity'];
                    if ($stock > 0) {
                        echo "Na zalogi";
                    } else {
                        echo "Ni na zalogi";
                    }
                    ?>
                </td>
            </tr>
        </table>
        <?php

        // Check if the user is logged in
        if (isLoggedIn() == true) {
            // If logged in, call the addToCart function
        ?>
            <form method="post" action="cart.php">
                <input type="hidden" name="bookisbn" value="<?php echo $book_isbn; ?>">
                <input type="submit" value="Dodaj v košarico" name="cart" class="btn btn-primary">
            </form>
        <?php ;
        } else {
        ?>

            <div class="alert alert-danger" role="alert" style="display: inline-block; margin-right: 10px;">Morate biti prijavljeni, da uporabljate košarico</div>

        <?php ;
        } ?>
    </div>
</div>
<br />
<div class="row ">
    <h4>Opis knjige</h4>
    <p class="justified-text"><?php echo $row['book_descr']; ?></p>
</div>
<style>
    .justified-text {
        text-align: justify;
    }
</style>

<?php
require "./template/footer.php";
?>