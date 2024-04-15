<?php
require_once "../functions/database_functions.php";
$book_isbn = $_GET['bookisbn'];

$conn = dbConnect();

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
require_once "../header.php";
?>

<p class="lead" style="margin: 25px 0"><a href="books.php">Vse knjige</a> > <?php echo $row['book_title']; ?></p>
<div class="row">
    <div class="col-md-4 text-center">
        <img style="width: 300px; height: auto;" class="img-responsive img-thumbnail" src="../bootstrap/img/<?php echo $row['book_image']; ?>">
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
        </table>
        <?php
        $stock = $row['book_quantity'];

        if (isLoggedIn() && $row['book_quantity'] > 0) : ?>
            <form method="post" action="cart.php" class="d-flex align-items-center">
                <input type="hidden" name="bookisbn" value="<?php echo $book_isbn; ?>">
                <input type="submit" value="Dodaj v košarico" name="cart" class="btn btn-primary mr-2">
                <p style="color:green;display: inline-block; margin: 10px;">Na zalogi</p>
            </form>
        <?php elseif (!isLoggedIn()) : ?>
            <div class="alert alert-danger" role="alert" style="display: inline-block; margin-right: 10px;">Morate biti prijavljeni, da uporabljate košarico</div>
        <?php else : ?>
            <form method="post" action="cart.php" class="d-flex align-items-center">
                <input type="hidden" name="bookisbn" value="<?php echo $book_isbn; ?>">
                <button type="button" class="btn btn-secondary mr-2" disabled>Dodaj v košarico</button>
                <p style="color:red;display: inline-block; margin: 10px;">Ni na zalogi</p>
            </form>
        <?php endif; ?>
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
require "../footer.php";
?>