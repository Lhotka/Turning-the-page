<?php
    $title = "All books";
    require_once "./template/header.php";

    // Fetch sorting option from query parameter (default to 'latest' if not provided)
    $sortingOption = isset($_GET['sort']) ? $_GET['sort'] : 'latest';

    // Fetch all books based on the sorting option
    $result = getAllBooks($conn, $sortingOption);
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

    <!-- Use a dropdown menu for sorting options -->
    <form method="get">
        <label for="sort">Sort by:</label>
        <select id="sort" name="sort" onchange="this.form.submit()">
            <option value="latest" <?php echo ($sortingOption === 'latest') ? 'selected' : ''; ?>>Latest</option>
            <option value="popular" <?php echo ($sortingOption === 'popular') ? 'selected' : ''; ?>>Most Popular</option>
            <option value="alphabetical_asc" <?php echo ($sortingOption === 'alphabetical_asc') ? 'selected' : ''; ?>>Alphabetical (A-Z)</option>
            <option value="alphabetical_desc" <?php echo ($sortingOption === 'alphabetical_desc') ? 'selected' : ''; ?>>Alphabetical (Z-A)</option>
            <option value="price_asc" <?php echo ($sortingOption === 'price_asc') ? 'selected' : ''; ?>>Price (↑)</option>
            <option value="price_desc" <?php echo ($sortingOption === 'price_desc') ? 'selected' : ''; ?>>Price (↓)</option>
            <option value="rating" <?php echo ($sortingOption === 'rating') ? 'selected' : ''; ?>>Rating</option>
        </select>
    </form>
    
    <?php
    $count = 0;

    while ($query_row = mysqli_fetch_assoc($result)) {
        $authors = getAuthorsByISBN($conn, $query_row['book_isbn']);

        if ($count % 4 == 0) {
            echo '<div class="row">';
        }
        ?>
        <div class="col-md-3">
            <a href="book.php?bookisbn=<?php echo $query_row['book_isbn']; ?>">
                <img class="img-responsive img-thumbnail" src="./bootstrap/img/<?php echo $query_row['book_image']; ?>">
            </a>
            <p class="text-center"><strong><?php echo $query_row['book_title']; ?></strong></p>
            <p class="text-center"><?php echo implode(', ', $authors); ?></p>
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
