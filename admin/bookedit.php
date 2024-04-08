<?php
$title = "Edit book";
require_once "../template/header.php";
checkAdmin();

//Get the ISBN
if (isset($_GET['bookisbn'])) {
    $book_isbn = $_GET['bookisbn'];
} else {
    echo "Empty ISBN query!";
    exit;
}

// Get book data
$query = "SELECT *, DATE_FORMAT(book_pub_date, '%Y-%m-%d') AS book_pub_date_formatted FROM book WHERE book_isbn = '$book_isbn'";
$result = mysqli_query($conn, $query);
if (!$result) {
    echo "Can't retrieve data " . mysqli_error($conn);
    exit;
}

$row = mysqli_fetch_assoc($result);

//Book editing if Save change is chosen
if (isset($_POST['save_change'])) {

    // Get the book data from the form
    $isbn = mysqli_real_escape_string($conn, trim($_POST['isbn']));
    $title = mysqli_real_escape_string($conn, trim($_POST['title']));
    $authorId = mysqli_real_escape_string($conn, trim($_POST['author']));
    $newAuthorName = mysqli_real_escape_string($conn, trim($_POST['new_author']));
    $publisher = mysqli_real_escape_string($conn, trim($_POST['publisher']));
    $newPublisherName = mysqli_real_escape_string($conn, trim($_POST['new_publisher']));
    $pubDate = mysqli_real_escape_string($conn, $_POST['pub_date']);
    $descr = mysqli_real_escape_string($conn, trim($_POST['descr']));
    $price = floatval(trim($_POST['price']));
    $quantity = intval(trim($_POST['quantity']));

    // Validate input
    if (empty($title) || ($authorId === 'new_author' && empty($newAuthorName)) || empty($publisher) || empty($descr) || empty($price)) {
        echo "All fields are required";
        exit;
    }   
    // Publisher handling
    $checkPublisherQuery = "SELECT * FROM publisher WHERE publisher_name = '$publisher'";
    $checkPublisherResult = mysqli_query($conn, $checkPublisherQuery);
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

    // Author handling

    // Check if the selected author is an existing author or a new author
    if ($authorId === 'new_author') {

        // Remove existing associations with authors for the given book ISBN
        $deleteBookAuthorQuery = "DELETE FROM book_author WHERE book_isbn = '$isbn'";
        $deleteBookAuthorResult = mysqli_query($conn, $deleteBookAuthorQuery);

        if (!$deleteBookAuthorResult) {
            echo "Error deleting existing book authors: " . mysqli_error($conn);
            exit;
        }

        // Insert the new author into the database
        $insertAuthorQuery = "INSERT INTO author (author_name) VALUES ('$newAuthorName')";
        $insertAuthorResult = mysqli_query($conn, $insertAuthorQuery);

        if (!$insertAuthorResult) {
            echo "Error adding new author: " . mysqli_error($conn);
            exit;
        }

        // Get the ID of the newly inserted author
        $authorId = mysqli_insert_id($conn);


        // Update the book_author table
        $insertBookAuthorQuery = "INSERT INTO book_author (book_isbn, author_id) VALUES ('$isbn', '$authorId')";
        $insertBookAuthorResult = mysqli_query($conn, $insertBookAuthorQuery);
    
        if (!$insertBookAuthorResult) {
            echo "Error updating book_author table: " . mysqli_error($conn);
            exit;
        }

    } elseif (!empty($authorId)) {
        // Existing author selected
        // Update the author_id in the book table directly
        $query = "UPDATE book SET author_id = '$authorId' WHERE book_isbn = '$isbn'";
        $result = mysqli_query($conn, $query);
        
        if (!$result) {
            echo "Error updating book author: " . mysqli_error($conn);
            exit;
        }
    
        // Update the book_author table
        $updateBookAuthorQuery = "UPDATE book_author SET author_id = '$authorId' WHERE book_isbn = '$isbn'";
        $updateBookAuthorResult = mysqli_query($conn, $updateBookAuthorQuery);
    
        if (!$updateBookAuthorResult) {
            echo "Error updating book_author table: " . mysqli_error($conn);
            exit;
        }
    }

    // Image handling
    $image = $row['book_image']; // Keep the existing image by default

    if (isset($_FILES['image']) && $_FILES['image']['name'] != "") {
        // Process the new image only if it's uploaded
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

        // Update the book_image field only when a new image is uploaded
        $query = "UPDATE book SET  
            book_image='$image'
            WHERE book_isbn = '$isbn'
        ";

        // Execute the query to update the book image in the database
        $result = mysqli_query($conn, $query);
        if (!$result) {
            echo "Can't update data " . mysqli_error($conn);
            exit;
        }
    }

    // Query for book editing (without the book_image field)
    $query = "UPDATE book SET  
        book_title = '$title', 
        book_descr = '$descr', 
        book_price = '$price',
        book_quantity = '$quantity',
        author_id = '$authorId',
        publisher_id = '$publisherId',
        book_pub_date = '$pubDate'
        WHERE book_isbn = '$isbn'
    ";

    // Execute the query to update the book in the database
    $result = mysqli_query($conn, $query);
    if (!$result) {
        echo "Can't update data " . mysqli_error($conn);
        exit;
    }

    // Redirect to the specified location
    header("Location: bookedit.php?bookisbn=$isbn");
}
?>

<form method="post" action="bookedit.php?bookisbn=<?php echo $book_isbn; ?>" enctype="multipart/form-data">
    <table class="table">
        <tr>
            <th style="vertical-align: middle;">ISBN</th>
            <td><input type="text" name="isbn" value="<?php echo $row['book_isbn']; ?>" readOnly="true"></td>
        </tr>
        <tr>
            <th style="vertical-align: middle;">Title</th>
            <td><input type="text" name="title" value="<?php echo $row['book_title']; ?>" required></td>
        </tr>
        <tr>
            <th style="vertical-align: middle;">Author</th>
            <td>
                <select name="author" required>
                    <option value="new_author" selected>ADD NEW AUTHOR</option>
                    <?php
                        // Fetch all authors from the database
                        $allAuthors = getAllAuthors($conn);
                        $originalAuthorId = $row['author_id'];
                        // Loop through authors and populate the dropdown
                        foreach ($allAuthors as $author) {
                            $authorId = $author['author_id'];
                            $authorName = $author['author_name'];

                            // Check if the current author matches the original author
                            if ($authorId == $originalAuthorId) {
                                echo "<option value='$authorId' selected>$authorName</option>";
                            } else {
                                echo "<option value='$authorId'>$authorName</option>";
                            }
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
                    $originalPublisherName = getPubName($conn, $row['publisher_id']);

                    // Loop through publishers and populate the dropdown
                    foreach ($allPublishers as $publisher) {
                        $publisherName = $publisher['publisher_name'];
                        // Check if the current publisher matches the original publisher
                        if ($publisherName == $originalPublisherName) {
                            echo "<option value='$publisherName' selected>$publisherName</option>";
                        } else {
                            echo "<option value='$publisherName'>$publisherName</option>";
                        }
                    }
                    ?>
                </select>
                <input type="text" name="new_publisher" placeholder="Enter publisher name">
            </td>
        </tr>
        <tr>
            <th style="vertical-align: middle;">Publication Date</th>
            <td><input type="date" name="pub_date" value="<?php echo $row['book_pub_date_formatted']; ?>" required></td>
        </tr>
        <tr>
            <th style="vertical-align: middle;">Image</th>
            <td style="display: flex; align-items: center;">
                <?php
                $existingImage = $row['book_image'];
                if (!empty($existingImage)) {
                    $imagePath = 'bootstrap/img/' . $existingImage;
                    echo '<img src="../' . $imagePath . '" alt="Existing Image" style="max-width: 100px; max-height: 100px; margin-right: 10px;">';
                    echo '<span>' . $existingImage . '</span>&nbsp&nbsp';
                }
                ?>
                <input type="file" name="image" accept="image/*">
            </td>
        </tr>
        <tr>
            <th style="vertical-align: middle;">Description</th>
            <td><textarea id="descriptionTextarea" name="descr" cols="60" rows="5"><?php echo $row['book_descr']; ?></textarea></td>
        </tr>
        <tr>
            <th style="vertical-align: middle;">Price</th>
            <td><input type="text" name="price" value="<?php echo $row['book_price']; ?>" required></td>
        </tr>
        <tr>
            <th style="vertical-align: middle;">Quantity</th>
            <td><input type="number" name="quantity" value="<?php echo $row['book_quantity']; ?>" required></td>
        </tr>
        <tr>
            <td colspan="2">
                <input type="submit" name="save_change" value="Save changes" class="btn btn-success">
                <input type="reset" value="Reset" class="btn btn-danger">
                <a href="book.php" class="btn btn-default">Go back</a>
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
    const authorInput = document.querySelector('input[name="new_author"]');
    const publisherInput = document.querySelector('input[name="new_publisher"]');

    authorDropdown.addEventListener('change', () => toggleNewInput(authorDropdown, authorInput));
    publisherDropdown.addEventListener('change', () => toggleNewInput(publisherDropdown, publisherInput));

    // Trigger the function on page load to set the initial position
    toggleNewInput(authorDropdown, authorInput);
    toggleNewInput(publisherDropdown, publisherInput);

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
require "../template/footer.php";
?>