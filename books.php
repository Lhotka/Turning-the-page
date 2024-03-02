<?php
    $title = "All books";
    require_once "./template/header.php";

    // Fetch all books
    $result = getAll($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Books</title>
    <!-- Include your CSS stylesheets or external libraries here -->
</head>

<body>
<p class="lead text-center text-muted">All books</p>

<?php
$count = 0;

while ($query_row = mysqli_fetch_assoc($result)) {
    if ($count % 4 == 0) {
        echo '<div class="row">';
    }
    ?>
    <div class="col-md-3">
        <a href="book.php?bookisbn=<?php echo $query_row['book_isbn']; ?>">
            <img class="img-responsive img-thumbnail" src="./bootstrap/img/<?php echo $query_row['book_image']; ?>">
        </a>
    </div>
    <?php
    $count++;
    if ($count % 4 == 0) {
        echo '</div>';
    }
}

// Close the last row if the total number of books is not a multiple of 4
if ($count % 4 != 0) {
    echo '</div>';
}

require_once "./template/footer.php";
?>
