<?php
    $title = "Edit book";
    require_once "../template/header.php";
    checkAdmin();

    if (isset($_GET['bookisbn'])) {
        $book_isbn = $_GET['bookisbn'];
    } else {
        echo "Empty query!";
        exit;
    }

    if (!isset($book_isbn)) {
        echo "Empty isbn! check again!";
        exit;
    }

    // get book data
    $query = "SELECT * FROM book WHERE book_isbn = '$book_isbn'";
    $result = mysqli_query($conn, $query);
    if (!$result) {
        echo "Can't retrieve data " . mysqli_error($conn);
        exit;
    }
    $row = mysqli_fetch_assoc($result);
?>
<form method="post" action="bookeditor.php" enctype="multipart/form-data">
    <table class="table">
        <tr>
            <th>ISBN</th>
            <td><input type="text" name="isbn" value="<?php echo $row['book_isbn']; ?>" readOnly="true"></td>
        </tr>
        <tr>
            <th>Title</th>
            <td><input type="text" name="title" value="<?php echo $row['book_title']; ?>" required></td>
        </tr>
        <tr>
            <th>Author</th>
            <td>
                <select name="new_author" required>
                    <?php
                    // Fetch all authors from the database
                    $allAuthors = getAllAuthors($conn);

                    // Loop through authors and populate the dropdown
                    foreach ($allAuthors as $author) {
                        $authorId = $author['author_id'];
                        $authorName = $author['author_name'];

                        // Check if the author is the current author of the book
                        $isSelected = ($authorId == $row['author_id']) ? 'selected' : '';

                        echo "<option value='$authorId' $isSelected>$authorName</option>";
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <th>Publisher</th>
            <td>
                <select name="new_publisher" required>
                    <?php
                    // Fetch all publishers from the database
                    $allPublishers = getAllPublishers($conn);

                    // Loop through publishers and populate the dropdown
                    foreach ($allPublishers as $publisher) {
                        $publisherId = $publisher['publisher_id'];
                        $publisherName = $publisher['publisher_name'];

                        // Check if the publisher is the current publisher of the book
                        $isSelected = ($publisherId == $row['publisher_id']) ? 'selected' : '';

                        echo "<option value='$publisherId' $isSelected>$publisherName</option>";
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <th>Image</th>
            <td style="display: flex; align-items: center;">
                <?php
                $existingImage = $row['book_image'];
                if (!empty($existingImage)) {
                    $imagePath = 'bootstrap/img/' . $existingImage;
                    echo '<img src="../' . $imagePath . '" alt="Existing Image" style="max-width: 100px; max-height: 100px; margin-right: 10px;">';
                    echo '<span>' . $existingImage . '</span>';
                }
                ?>
                <input type="file" name="image">
            </td>
        </tr>
            <th>Description</th>
            <td><textarea id="descriptionTextarea" name="descr" cols="60" rows="5"><?php echo $row['book_descr']; ?></textarea></td>
        </tr>
        <tr>
            <th>Price</th>
            <td><input type="text" name="price" value="<?php echo $row['book_price']; ?>" required></td>
        </tr>
        <tr>
            <td colspan="2">
                <input type="submit" name="save_change" value="Change" class="btn btn-success">
                <input type="reset" value="Reset" class="btn btn-danger">
                <a href="book.php" class="btn btn-default">Go back</a>
            </td>
        </tr>
    </table>
</form>
<br />
<script>
    // Function to auto-resize textarea and adjust input sizes
    function autoResizeInputs() {
        const inputs = document.querySelectorAll('input[type="text"]');
        inputs.forEach(input => {
            input.style.width = 'auto';
            input.style.width = input.scrollWidth + 'px';
        });

        const textarea = document.getElementById('descriptionTextarea');
        textarea.style.height = 'auto';
        textarea.style.height = textarea.scrollHeight + 'px';
    }

    // Attach the function to the inputs and textarea's input event
    document.querySelectorAll('input[type="text"]').forEach(input => {
        input.addEventListener('input', autoResizeInputs);
    });
    document.getElementById('descriptionTextarea').addEventListener('input', autoResizeInputs);

    // Trigger the function on page load to adjust sizes if there's initial content
    window.addEventListener('load', autoResizeInputs);
</script>

<?php
require "../template/footer.php"
?>
