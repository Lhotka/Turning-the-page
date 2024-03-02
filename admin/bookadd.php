<?php
$title = "Add new book";
require "../template/header.php";
checkAdmin();

if (isset($_POST['add'])) {
    $isbn = trim($_POST['isbn']);
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $descr = trim($_POST['descr']);
    $price = floatval(trim($_POST['price']));
    $publisher = trim($_POST['publisher']);

    // Validate input
    if (empty($isbn) || empty($title) || empty($author) || empty($descr) || empty($price) || empty($publisher)) {
        echo "All fields are required or format is wrong?";
        exit;
    }

    // Check if ISBN already exists
    $checkISBNQuery = "SELECT * FROM books WHERE book_isbn = '$isbn'";
    $checkISBNResult = mysqli_query($conn, $checkISBNQuery);
    if (mysqli_num_rows($checkISBNResult) > 0) {
        echo "ISBN already exists. Please use a unique ISBN.";
        exit;
    }

    // Validate and sanitize publisher name
    $publisher = mysqli_real_escape_string($conn, $publisher);

    // Check if the publisher already exists
    $checkPublisherQuery = "SELECT * FROM publisher WHERE publisher_name = '$publisher'";
    $checkPublisherResult = mysqli_query($conn, $checkPublisherQuery);
    if (!$checkPublisherResult) {
        echo "Error checking publisher: " . mysqli_error($conn);
        exit;
    }

    // If the publisher doesn't exist, add it to the publisher table
    if (mysqli_num_rows($checkPublisherResult) == 0) {
        $insertPublisherQuery = "INSERT INTO publisher (publisher_name) VALUES ('$publisher')";
        $insertPublisherResult = mysqli_query($conn, $insertPublisherQuery);
        if (!$insertPublisherResult) {
            echo "Error adding new publisher: " . mysqli_error($conn);
            exit;
        }
    }

    // Get the publisher ID
    $publisherIDQuery = "SELECT publisherid FROM publisher WHERE publisher_name = '$publisher'";
    $publisherIDResult = mysqli_query($conn, $publisherIDQuery);
    if (!$publisherIDResult) {
        echo "Error getting publisher ID: " . mysqli_error($conn);
        exit;
    }

    $row = mysqli_fetch_assoc($publisherIDResult);
    $publisherid = $row['publisherid'];

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

    // Insert the book into the database
    $query = "INSERT INTO books (book_isbn, book_title, book_author, book_image, book_descr, book_price, publisherid, date_added) VALUES ('$isbn', '$title', '$author', '$image', '$descr', '$price', '$publisherid', NOW())";
    $result = mysqli_query($conn, $query);
    if (!$result) {
        echo "Can't add new data " . mysqli_error($conn);
        exit;
    } else {
        header("Location: bookadd.php");
    }
}

?>

	<form method="post" action="bookadd.php" enctype="multipart/form-data">
		<table class="table">
			<tr>
				<th>ISBN</th>
				<td><input type="text" name="isbn"></td>
			</tr>
			<tr>
				<th>Title</th>
				<td><input type="text" name="title" required></td>
			</tr>
			<tr>
				<th>Author</th>
				<td><input type="text" name="author" required></td>
			</tr>
            <tr>
				<th>Publisher</th>
				<td><input type="text" name="publisher" required></td>
			</tr>
			<tr>
				<th>Image</th>
				<td><input type="file" name="image"></td>
			</tr>
			<tr>
				<th>Description</th>
                <td><textarea id="descriptionTextarea" name="descr" cols="60" rows="5"></textarea></td>
			</tr>
			<tr>
				<th>Price</th>
				<td><input type="text" name="price" required></td>
			</tr>

		</table>
		<input type="submit" name="add" value="Add book" class="btn btn-primary">
		<input type="reset" value="Reset" class="btn btn-danger">
        <a href="book.php" class="btn btn-default">Go back</a>

	</form>
	<br/>
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