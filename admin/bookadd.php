<?php
$title = "Add new book";
require "../template/header.php";
checkAdmin();

if (isset($_POST['add'])) {
    // Get the book data from the form
    $isbn = mysqli_real_escape_string($conn, trim($_POST['isbn']));
    $title = mysqli_real_escape_string($conn, trim($_POST['title']));
    $authorId = mysqli_real_escape_string($conn, trim($_POST['author']));
    $newAuthorName = mysqli_real_escape_string($conn, trim($_POST['new_author']));
    $publisher = mysqli_real_escape_string($conn, trim($_POST['publisher']));
    $newPublisherName = mysqli_real_escape_string($conn, trim($_POST['new_publisher']));
    $descr = mysqli_real_escape_string($conn, trim($_POST['descr']));
    $price = floatval(trim($_POST['price']));

    // Validate input
    if (empty($isbn) || empty($title) || empty($authorId) || empty($publisher) || empty($descr) || empty($price)) {
        echo "All fields are required";
        exit;
    }

    // Check if ISBN already exists
    $checkISBNQuery = "SELECT * FROM book WHERE book_isbn = '$isbn'";
    $checkISBNResult = mysqli_query($conn, $checkISBNQuery);

    if (mysqli_num_rows($checkISBNResult) > 0) {
        echo "ISBN already exists. Please use a unique ISBN.";
        exit;
    }


    // Check if the publisher already exists
    $checkPublisherQuery = "SELECT * FROM publisher WHERE publisher_name = '$publisher'";
    $checkPublisherResult = mysqli_query($conn, $checkPublisherQuery);

    if (!$checkPublisherResult) {
        echo "Error checking publisher: " . mysqli_error($conn);
        exit;
    }

    // If the publisher doesn't exist, add it to the publisher table
    if (mysqli_num_rows($checkPublisherResult) == 0 && $publisher !== 'new_publisher') {
        echo "Please choose 'ADD NEW PUBLISHER' or select an existing one.";
        exit;
    } elseif ($publisher === 'new_publisher' && !empty($newPublisherName)) {
        $insertPublisherQuery = "INSERT INTO publisher (publisher_name) VALUES ('$newPublisherName')";
        $insertPublisherResult = mysqli_query($conn, $insertPublisherQuery);

        if (!$insertPublisherResult) {
            echo "Error adding new publisher: " . mysqli_error($conn);
            exit;
        }

        // Get the newly inserted publisher ID
        $publisherId = mysqli_insert_id($conn);
    } else {
        // Get the publisher ID if it already exists
        $row = mysqli_fetch_assoc($checkPublisherResult);
        $publisherId = $row['publisher_id'];
    }


    // Initialize $image
    $image = "";

    if (isset($_FILES['image']) && $_FILES['image']['name'] != "") {
        $imageFile = $_FILES['image'];
        $imageInfo = getimagesize($imageFile['tmp_name']);

        // Check if the file is a valid image
        if ($imageInfo === false) {
            echo "Invalid image file";
            exit;
        }

        $imageType = $imageInfo[2];

        // Check if the image type is supported (1 = GIF, 2 = JPG, 3 = PNG)
        if (!in_array($imageType, [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG])) {
            echo "Unsupported image type";
            exit;
        }

        // Set the file extension based on the image type
        $allowedExtensions = [
            IMAGETYPE_GIF => "gif",
            IMAGETYPE_JPEG => "jpeg",
            IMAGETYPE_PNG => "png"
        ];

        $fileExtension = $allowedExtensions[$imageType];

        // Generate a new file name
        $originalName = pathinfo($imageFile['name'], PATHINFO_FILENAME);
        $image = $originalName . 'Crop' . '.' . $fileExtension;

        // Move the original image
        $directorySelf = str_replace(basename($_SERVER['PHP_SELF']), '', $_SERVER['PHP_SELF']);
        $uploadDirectory = "../bootstrap/img/";
        $originalImagePath = $uploadDirectory . $image;
        move_uploaded_file($imageFile['tmp_name'], $originalImagePath);

        // Check and resize the image if needed
        checkAndResizeImage($originalImagePath, 200);
    }

    // Check if the selected author is 'new_author'
    if ($authorId == 'new_author' && !empty($newAuthorName)) {
        // Insert the new author
        $insertAuthorQuery = "INSERT INTO author (author_name) VALUES ('$newAuthorName')";
        $insertAuthorResult = mysqli_query($conn, $insertAuthorQuery);

        if (!$insertAuthorResult) {
            echo "Error adding new author: " . mysqli_error($conn);
            exit;
        }

        // Get the newly inserted author ID
        $authorId = mysqli_insert_id($conn);
    }

    // Insert the book into the database
    $query = "INSERT INTO book (book_isbn, book_title, author_id, book_image, book_descr, book_price, publisher_id, date_added) VALUES ('$isbn', '$title', '$authorId', '$image', '$descr', '$price', '$publisherId', NOW())";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        echo "Can't add new data " . mysqli_error($conn);
        exit;
    } else {
        // Fetch the correct book_isbn associated with the inserted ISBN
        $getBookIsbnQuery = "SELECT book_isbn FROM book WHERE book_isbn = '$isbn'";
        $getBookIsbnResult = mysqli_query($conn, $getBookIsbnQuery);

        if (!$getBookIsbnResult) {
            echo "Error getting book ISBN: " . mysqli_error($conn);
            exit;
        }

        $row = mysqli_fetch_assoc($getBookIsbnResult);
        $bookIsbn = $row['book_isbn'];

        // Check if the entry already exists in book_author
        $checkBookAuthorQuery = "SELECT * FROM book_author WHERE book_isbn = '$bookIsbn' AND author_id = '$authorId'";
        $checkBookAuthorResult = mysqli_query($conn, $checkBookAuthorQuery);

        if (mysqli_num_rows($checkBookAuthorResult) == 0) {
            // Insert into book_author
            $insertBookAuthorQuery = "INSERT INTO book_author (book_isbn, author_id) VALUES ('$bookIsbn', '$authorId')";
            $insertBookAuthorResult = mysqli_query($conn, $insertBookAuthorQuery);

            if (!$insertBookAuthorResult) {
                echo "Error updating book_author table: " . mysqli_error($conn);
                exit;
            }
        }

        header("Location: bookadd.php");
    }
}
?>

<form method="post" action="bookadd.php" enctype="multipart/form-data">
    <table class="table">
        <tr>
            <th style="vertical-align: middle;">ISBN</th>
            <td><input type="text" name="isbn" required></td>
        </tr>
        <tr>
            <th style="vertical-align: middle;">Title</th>
            <td><input type="text" name="title" required></td>
        </tr>
        <tr>
            <th style="vertical-align: middle;">Author</th>
            <td>
                <select name="author" required>
                    <option value="new_author" selected>ADD NEW AUTHOR</option>
                    <?php
                    // Fetch all authors from the database
                    $allAuthors = getAllAuthors($conn);

                    // Loop through authors and populate the dropdown
                    foreach ($allAuthors as $author) {
                        $authorId = $author['author_id'];
                        $authorName = $author['author_name'];
                        echo "<option value='$authorId'>$authorName</option>";
                    }
                    ?>
                </select>
                <input type="text" name="new_author" placeholder="Enter author name">
            </td>
        </tr>
        <tr>
            <th style="vertical-align: middle;">Publisher</th>
            <td>
                <select name="publisher" required>
                    <option value="new_publisher" selected>ADD NEW PUBLISHER</option>
                    <?php
                    // Fetch all publishers from the database
                    $allPublishers = getAllPublishers($conn);

                    // Loop through publishers and populate the dropdown
                    foreach ($allPublishers as $publisher) {
                        $publisherName = $publisher['publisher_name'];
                        echo "<option value='$publisherName'>$publisherName</option>";
                    }
                    ?>
                </select>
                <input type="text" name="new_publisher" placeholder="Enter publisher name">
            </td>
        </tr>
        <tr>
            <th style="vertical-align: middle;">Image</th>
            <td><input type="file" name="image" accept="image/*"></td>
        </tr>
        <tr>
            <th style="vertical-align: middle;">Description</th>
            <td><textarea id="descriptionTextarea" name="descr" cols="60" rows="5"></textarea></td>
        </tr>
        <tr>
            <th style="vertical-align: middle;">Price</th>
            <td><input type="text" name="price" required></td>
        </tr>
        <tr>
            <td colspan="2">
                <input type="submit" name="add" value="Add book" class="btn btn-success">
                <input type="reset" value="Reset" class="btn btn-danger">
                <button type="button" class="btn btn-default" onclick="goBack()">Go back</button>
            </td>
        </tr>
    </table>
</form>

<script>
    // Function to go back to the previous page
    function goBack() {
        window.history.back();
    }
</script>
<script>
    // Function to toggle visibility and position of the input for writing new author or publisher
    function toggleNewInput(selectElement, inputElement) {
        inputElement.style.display = (selectElement.value === 'new_author' || selectElement.value === 'new_publisher') ? 'inline-block' : 'none';
        inputElement.style.verticalAlign = (selectElement.value === 'new_author' || selectElement.value === 'new_publisher') ? 'top' : 'middle';
    }

    // Attach the function to the change event of author and publisher dropdowns
    const authorDropdown = document.querySelector('select[name="author"]');
    const publisherDropdown = document.querySelector('select[name="publisher"]');
    const newAuthorInput = document.querySelector('input[name="new_author"]');
    const newPublisherInput = document.querySelector('input[name="new_publisher"]');

    authorDropdown.addEventListener('change', () => toggleNewInput(authorDropdown, newAuthorInput));
    publisherDropdown.addEventListener('change', () => toggleNewInput(publisherDropdown, newPublisherInput));

    // Trigger the function on page load to set the initial position
    toggleNewInput(authorDropdown, newAuthorInput);
    toggleNewInput(publisherDropdown, newPublisherInput);

    // Function to auto-resize textarea and adjust input sizes
    function autoResizeInputs() {
        // Resize text input fields
        const textInputs = document.querySelectorAll('input[type="text"]');
        textInputs.forEach(input => {
            input.style.width = 'auto';
            input.style.width = input.scrollWidth + 'px';
        });

        // Resize textarea
        const textarea = document.getElementById('descriptionTextarea');
        textarea.style.height = 'auto';
        textarea.style.height = textarea.scrollHeight + 'px';
    }

    // Attach the function to text inputs and textarea's input event
    document.querySelectorAll('input[type="text"]').forEach(input => {
        input.addEventListener('input', autoResizeInputs);
    });
    document.getElementById('descriptionTextarea').addEventListener('input', autoResizeInputs);

    // Trigger the function on page load to adjust sizes if there's initial content
    window.addEventListener('load', autoResizeInputs);
</script>
<?php
require_once "../template/footer.php";
?>