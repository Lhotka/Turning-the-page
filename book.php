<?php
require_once "./functions/database_functions.php";
$book_isbn = $_GET['bookisbn'];

$conn = db_connect();

$query = "SELECT b.*, GROUP_CONCAT(a.author_name SEPARATOR ', ') AS authors
          FROM book b
          LEFT JOIN book_author ba ON b.book_isbn = ba.book_isbn
          LEFT JOIN author a ON ba.author_id = a.author_id
          WHERE b.book_isbn = '$book_isbn'
          GROUP BY b.book_isbn";

$result = mysqli_query($conn, $query);
if (!$result) {
    echo "Can't retrieve data " . mysqli_error($conn);
    exit;
}

$row = mysqli_fetch_assoc($result);
if (!$row) {
    echo "Empty book";
    exit;
}

$title = $row['book_title'];
require_once "./template/header.php";
?>

<p class="lead" style="margin: 25px 0"><a href="books.php">All Books</a> > <?php echo $row['book_title']; ?></p>
<div class="row">
    <div class="col-md-3 text-center">
        <img class="img-responsive img-thumbnail" src="./bootstrap/img/<?php echo $row['book_image']; ?>">
    </div>
    <div class="col-md-6">

        <h4>Book Details</h4>

        <table class="table">
            <tr>
                <td>ISBN</td>
                <td><?php echo $row['book_isbn']; ?></td>
            </tr>
            <tr>
                <td>Title</td>
                <td><?php echo $row['book_title']; ?></td>
            </tr>
            <tr>
                <td>Author</td>
                <td><?php echo $row['authors']; ?></td>
            </tr>
            <tr>
                <td>Price</td>
                <td><?php echo $row['book_price']; ?></td>
            </tr>
            <tr>
                <td>Publisher</td>
                <td><?php echo getPubName($conn, $row['publisherid']); ?></td>
            </tr>
        </table>

        <form method="post" action="cart.php">
            <input type="hidden" name="bookisbn" value="<?php echo $book_isbn; ?>">
            <input type="submit" value="Add to cart" name="cart" class="btn btn-primary">
        </form>
    </div>
</div>
<br/>
<div class="row ">
    <h4>Book Description</h4>
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
