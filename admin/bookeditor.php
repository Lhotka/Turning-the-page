<?php
    require_once "../template/header.php";
    checkAdmin();
    // if save change happens
    if (!isset($_POST['save_change'])) {
        echo "Something wrong!";
        exit;
    }

    //real escape string to allow "" and ' in the text
    $isbn = trim($_POST['isbn']);
    $title = mysqli_real_escape_string($conn, trim($_POST['title']));
    $author = mysqli_real_escape_string($conn, trim($_POST['author']));
    $descr = mysqli_real_escape_string($conn, trim($_POST['descr']));
    $price = floatval(trim($_POST['price']));
    $publisher = mysqli_real_escape_string($conn, trim($_POST['publisher']));

    // Check if a new image is provided
    if (isset($_FILES['image']) && $_FILES['image']['name'] != "") {
        $image = $_FILES['image']['name'];

        // Debugging: Print information about the uploaded file
        echo "Uploaded file details: ";
        var_dump($_FILES);

        // File upload logic
        $directory_self = str_replace(basename($_SERVER['PHP_SELF']), '', $_SERVER['PHP_SELF']);
        $uploadDirectory = $_SERVER['DOCUMENT_ROOT'] . $directory_self . "bootstrap/img/";
        $uploadDirectory .= $image;

        // Debugging: Print the destination path
        echo "Destination path: $uploadDirectory";

        // Move the uploaded file
        move_uploaded_file($_FILES['image']['tmp_name'], $uploadDirectory);
    }

    // if publisher is not in db, create new
    $findPub = "SELECT * FROM publisher WHERE publisher_name = '$publisher'";
    $findResult = mysqli_query($conn, $findPub);
    if (!$findResult) {
        // insert into publisher table and return id
        $insertPub = "INSERT INTO publisher(publisher_name) VALUES ('$publisher')";
        $insertResult = mysqli_query($conn, $insertPub);
        if (!$insertResult) {
            echo "Can't add new publisher " . mysqli_error($conn);
            exit;
        }
    }

    $query = "UPDATE books SET  
        book_title = '$title', 
        book_author = '$author', 
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
    require "../template/footer.php"

?>
