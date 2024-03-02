<?php
	$book_isbn = $_GET['bookisbn'];
    $title="Book deletion";
    require "../template/header.php";
    checkAdmin();

	$query = "DELETE FROM books WHERE book_isbn = '$book_isbn'";
	$result = mysqli_query($conn, $query);
	if(!$result){
		echo "delete data unsuccessfully " . mysqli_error($conn);
		exit;
	}
	header("Location: book.php");

    require_once "../template/footer.php";
?>