<?php
$title = "Upravljanje Avtorjev";
require_once "../header.php";
$conn = dbConnectAdmin();
checkAdmin();

// Handle form submission for adding a new author
if (isset($_POST['add_author'])) {
    $newAuthorName = trim($_POST['new_author_name']);
    // Perform any additional validation if needed

    // Insert the new author into the database
    $query = "INSERT INTO author (author_name) VALUES ('$newAuthorName')";
    $insertResult = mysqli_query($conn, $query);
}

// Handle form submission for deleting an author
if (isset($_POST['delete_author'])) {
    $deleteAuthorID = $_POST['delete_author_id'];

    // Check if the author has associated books
    $checkQuery = "SELECT COUNT(*) AS book_count FROM book_author WHERE author_id = '$deleteAuthorID'";
    $checkResult = mysqli_query($conn, $checkQuery);
    $bookCount = mysqli_fetch_assoc($checkResult)['book_count'];

    if ($bookCount > 0) {
        echo "Napaka: Tega avtorja ni mogoče izbrisati, ker je povezan z $bookCount knjigami.";
    } else {
        // Delete the selected author from the database
        $deleteQuery = "DELETE FROM author WHERE author_id = '$deleteAuthorID'";
        $deleteResult = mysqli_query($conn, $deleteQuery);

        if (!$deleteResult) {
            echo "Napaka pri brisanju avtorja: " . mysqli_error($conn);
        }
    }
}

// Fetch all authors with associated book count from the database
$authors = getAllAuthorsWithBookCount($conn);
?>

<h2>Upravljanje Avtorjev</h2>

<!-- Form for adding a new author -->
<form method="post" action="">
    <label for="new_author_name">Nov Avtor:</label>
    <input type="text" id="new_author_name" name="new_author_name" required>
    <button type="submit" name="add_author" class="btn btn-success">Dodaj Avtorja</button>
    <?php
    if (isset($_POST['add_author'])) {
        // Check if the author was successfully added
        if (isset($insertResult) && $insertResult) {
            echo "<p class='text-success'>Nov avtor je bil uspešno dodan!</p>";
        } else {
            echo "<p class='text-danger'>Napaka pri dodajanju novega avtorja: " . mysqli_error($conn) . "</p>";
        }
    }
    ?>
</form>


<!-- Display the existing authors in a table -->
<table class="table" style="margin-top: 20px">
    <tr>
        <th>ID</th>
        <th>Ime avtorja</th>
        <th>Število knjig</th>
        <th>Opis</th>
        <th></th>
    </tr>
    <?php foreach ($authors as $author) { ?>
        <tr>
            <td style="vertical-align: middle;"><?php echo $author['author_id']; ?></td>
            <td style="vertical-align: middle;"><?php echo $author['author_name']; ?></td>
            <td style="vertical-align: middle;"><?php echo $author['book_count']; ?></td>
            <td style="vertical-align: middle;"><?php echo empty($author['author_description']) ? 'PRAZNO' : (countWords($author['author_description']) . " besed"); ?></td>
            <td style="vertical-align: middle;">
                <!-- Edit and delete Button -->
                <a href="authoredit.php?author_id=<?php echo $author['author_id']; ?>" class="btn btn-warning">Uredi</a>
                <!-- Delete Button -->
                <form method="post" action="" style="display: inline-block" onsubmit="return confirm('Ste prepričani, da želite izbrisati tega avtorja? Tega dejanja ni mogoče razveljaviti.');">
                    <input type="hidden" name="delete_author_id" value="<?php echo $author['author_id']; ?>">
                    <button type="submit" name="delete_author" class="btn btn-danger">Izbriši</button>
                </form>
            </td>
        </tr>
    <?php } ?>
</table>

<?php require_once "../footer.php"; ?>