<?php
$book_isbn = $_GET['bookisbn'];
$title = "Book deletion";
require "../template/header.php";
checkAdmin();
$conn=db_connect();

// Delete related rows in book_author first
$deleteBookAuthorQuery = "DELETE FROM book_author WHERE book_isbn = '$book_isbn'";
$deleteBookAuthorResult = mysqli_query($conn, $deleteBookAuthorQuery);

if (!$deleteBookAuthorResult) {
    echo "Error deleting related book_author rows: " . mysqli_error($conn);
    exit;
}

// Now, delete the row in the book table
$deleteBookQuery = "DELETE FROM book WHERE book_isbn = '$book_isbn'";
$deleteBookResult = mysqli_query($conn, $deleteBookQuery);

if (!$deleteBookResult) {
    echo "Error deleting book row: " . mysqli_error($conn);
    exit;
}

header("Location: book.php");


require_once "../template/footer.php";
