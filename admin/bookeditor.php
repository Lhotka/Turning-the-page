<?php
    $title="Book editor";
    require_once "../template/header.php";
    checkAdmin();

    // If save change is not set, something is wrong
    if (!isset($_POST['save_change'])) {
        echo "Something is wrong!";
        exit;
    }

    // Get the ISBN from the form
    $isbn = trim($_POST['isbn']);

    // Get the new book data from the form
    $title = mysqli_real_escape_string($conn, trim($_POST['title']));
    
    // New Author and Publisher IDs
    $newAuthorId = $_POST['new_author'];
    $newPublisherId = $_POST['new_publisher'];

    $descr = mysqli_real_escape_string($conn, trim($_POST['descr']));
    $price = floatval(trim($_POST['price']));

    // Check if a new image is provided
    if (isset($_FILES['image']) && $_FILES['image']['name'] != "") {
        $image = $_FILES['image']['name'];

        // File upload logic
        $directory_self = str_replace(basename($_SERVER['PHP_SELF']), '', $_SERVER['PHP_SELF']);
        $uploadDirectory = $_SERVER['DOCUMENT_ROOT'] . $directory_self . "bootstrap/img/";
        $uploadDirectory .= $image;

        // Move the uploaded file
        move_uploaded_file($_FILES['image']['tmp_name'], $uploadDirectory);
    }

    // Update the book in the database
    $query = "UPDATE book SET  
        book_title = '$title', 
        author_id = '$newAuthorId', 
        publisher_id = '$newPublisherId', 
        book_descr = '$descr', 
        book_price = '$price'";

    // Add the image update to the query if a new image is provided
    if (isset($image)) {
        $query .= ", book_image='$image'";
    }

    $query .= " WHERE book_isbn = '$isbn'";

    // Debugging: Print the SQL query
    echo "SQL query: $query";

    // Execute the query
    $result = mysqli_query($conn, $query);

    if (!$result) {
        echo "Can't update data " . mysqli_error($conn);
        exit;
    } else {
        // Redirect to the specified location
        header("Location: bookedit.php?bookisbn=$isbn");
    }

    require "../template/footer.php";
?>
