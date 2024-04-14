<?php
$title = "Urejanje založnika";
require_once "../template/header.php";
checkAdmin();
$conn=dbConnectAdmin();

if (isset($_GET['publisher_id'])) {
    $publisherid = $_GET['publisher_id'];
} else {
    echo "Prazen poizvedek! GET parametri: " . print_r($_GET, true);
    exit;
}

// get publisher data
$query = "SELECT * FROM publisher WHERE publisher_id = '$publisherid'";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    echo "Založnik ni najden!";
    exit;
}

$publisher = mysqli_fetch_assoc($result);

// Get books linked to the publisher
$bookQuery = "SELECT b.* FROM book b WHERE b.publisher_id = '$publisherid'";
$bookResult = mysqli_query($conn, $bookQuery);
if (!$bookResult) {
    echo "Ne morem pridobiti knjig " . mysqli_error($conn);
    exit;
}
?>

<h2><?php echo $publisher['publisher_name']; ?></h2>

<!-- Display books by the publisher -->
<h3>Knjige:</h3>
<?php if (mysqli_num_rows($bookResult) > 0) { ?>
    <ul>
        <?php while ($bookRow = mysqli_fetch_assoc($bookResult)) { ?>
            <li><a href="bookedit.php?bookisbn=<?php echo $bookRow['book_isbn']; ?>"><?php echo $bookRow['book_title']; ?></a></li>
        <?php } ?>
    </ul>
<?php } else { ?>
    <p>Ni najdenih knjig za tega založnika.</p>
<?php } ?>

<!-- Form to edit publisher details -->
<form method="post" action="">
    <input type="hidden" name="publisher_id" value="<?php echo $publisher['publisher_id']; ?>">
    <label for="publisher_name">Ime založnika:</label>
    <input type="text" name="publisher_name" value="<?php echo $publisher['publisher_name']; ?>" required>
    <input type="submit" name="save_change" value="Shrani spremembe" class="btn btn-success">
    <input type="reset" value="Ponastavi" class="btn btn-danger">
    <a href="publisher.php" class="btn btn-default">Nazaj</a>
</form>

<?php
require_once "../template/footer.php";
?>
