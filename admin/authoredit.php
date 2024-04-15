<?php
$title = "Uredi avtorja";
require_once "../header.php";
checkAdmin();
$conn = dbConnectAdmin();

// Check if the form is submitted for saving changes
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_change'])) {
    // Get the author ID
    $author_id = $_GET['author_id'];

    // Get the updated author details from the form
    $author_name = $_POST['author_name'];
    $author_description = $_POST['author_description'];

    // Update the author details in the database
    $update_query = "UPDATE author SET author_name = '$author_name', author_description = '$author_description' WHERE author_id = '$author_id'";
    $update_result = mysqli_query($conn, $update_query);

    if (!$update_result) {
        echo "Napaka pri shranjevanju sprememb: " . mysqli_error($conn);
    } else {
        echo "Spremembe so bile uspešno shranjene!";
    }
}

// Get the author ID
if (isset($_GET['author_id'])) {
    $author_id = $_GET['author_id'];
} else {
    echo "Prazno id avtorja!";
    exit;
}

// Get author data
$query = "SELECT * FROM author WHERE author_id = '$author_id'";
$result = mysqli_query($conn, $query);
if (!$result) {
    echo "Ne morem pridobiti podatkov " . mysqli_error($conn);
    exit;
}

$row = mysqli_fetch_assoc($result);

// Get books linked to the author
$bookQuery = "SELECT b.* FROM book b JOIN book_author ba ON b.book_isbn = ba.book_isbn WHERE ba.author_id = '$author_id'";
$bookResult = mysqli_query($conn, $bookQuery);
if (!$bookResult) {
    echo "Ne morem pridobiti knjig " . mysqli_error($conn);
    exit;
}
?>
<h2><?php echo $row['author_name']; ?></h2>

<!-- Display books by the author -->
<h3>Knjige:</h3>
<?php if (mysqli_num_rows($bookResult) > 0) { ?>
    <ul>
        <?php while ($bookRow = mysqli_fetch_assoc($bookResult)) { ?>
            <li><a href="bookedit.php?bookisbn=<?php echo $bookRow['book_isbn']; ?>"><?php echo $bookRow['book_title']; ?></a></li>
        <?php } ?>
    </ul>
<?php } else { ?>
    <p>Ni najdenih knjig za tega avtorja.</p>
<?php } ?>

<!-- Form to edit author details -->
<form method="post" action="authoredit.php?author_id=<?php echo $author_id; ?>">
    <table class="table">
        <tr>
            <th style="vertical-align: middle;">ID avtorja</th>
            <td><input type="text" name="author_id" value="<?php echo $row['author_id']; ?>" readOnly="true"></td>
        </tr>
        <tr>
            <th style="vertical-align: middle;">Ime avtorja</th>
            <td><input type="text" name="author_name" value="<?php echo $row['author_name']; ?>" required></td>
        </tr>
        <tr>
            <th style="vertical-align: middle;">Opis</th>
            <td><textarea id="author_description" name="author_description" cols="60" rows="5"><?php echo $row['author_description']; ?></textarea></td>
        </tr>
        <tr>
            <td colspan="2">
                <input type="submit" name="save_change" value="Shrani spremembe" class="btn btn-success">
                <input type="reset" value="Ponastavi" class="btn btn-danger">
                <a href="author.php" type="button" class="btn btn-default">Nazaj</a>
            </td>
        </tr>
    </table>
</form>
<script>
    // Function to auto-resize textarea and adjust input sizes
    function autoResizeInputs() {
        // Resize text input fields
        const textInputs = document.querySelectorAll('input[type="text"]');
        textInputs.forEach(input => {
            input.style.width = 'auto';
            input.style.width = input.scrollWidth + 'px';
        });

        // Resize textarea
        const textarea = document.getElementById('author_description');
        if (textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = textarea.scrollHeight + 'px';
        }
    }

    // Attach the function to text inputs and textarea's input event
    document.querySelectorAll('input[type="text"]').forEach(input => {
        input.addEventListener('input', autoResizeInputs);
    });

    const authorDescriptionTextarea = document.getElementById('author_description');
    if (authorDescriptionTextarea) {
        authorDescriptionTextarea.addEventListener('input', autoResizeInputs);
    }

    // Trigger the function on page load to adjust sizes if there's initial content
    window.addEventListener('load', autoResizeInputs);
</script>

<?php
require_once "../footer.php";
?>