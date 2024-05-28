<?php
$title = "Vse knjige";
require_once "../header.php";

$conn = dbConnect();

// Define the number of books per page
$booksPerPage = 8;

// Determine the current page number
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

// Calculate the SQL OFFSET for pagination
$offset = ($page - 1) * $booksPerPage;

// Pridobi možnost sortiranja iz parametra poizvedbe (privzeto na 'najnovejše', če ni podano)
$sortingOption = isset($_GET['sort']) ? $_GET['sort'] : 'latest';

// Check if search query is present
if (isset($_GET['q']) && !empty($_GET['q'])) {
    $searchQuery = mysqli_real_escape_string($conn, $_GET['q']);
    $result = searchBooks($conn, $searchQuery, $booksPerPage, $offset);
    $totalBooks = countSearchBooks($conn, $searchQuery);
} else {
    // Pridobi vse knjige glede na možnost sortiranja in dodaj omejitev za strani
    $result = getAllBooks($conn, $sortingOption, $booksPerPage, $offset);
    // Število vseh knjig
    $totalBooks = getBookCount($conn);
}

// Število strani
$totalPages = ceil($totalBooks / $booksPerPage);

?>

<p class="lead text-center text-muted">Vse knjige</p>

<!-- Spustni meni za možnosti sortiranja -->
<form method="get" style="text-align:right;margin-bottom:20px">
    <label for="sort">Razvrščanje:</label>
    <select id="sort" name="sort" onchange="this.form.submit()">
        <option value="latest" <?php echo ($sortingOption === 'latest') ? 'selected' : ''; ?>>Najnovejše</option>
        <option value="popular" <?php echo ($sortingOption === 'popular') ? 'selected' : ''; ?>>Najbolj priljubljene</option>
        <option value="alphabetical_asc" <?php echo ($sortingOption === 'alphabetical_asc') ? 'selected' : ''; ?>>Abecedno (A-Z)</option>
        <option value="alphabetical_desc" <?php echo ($sortingOption === 'alphabetical_desc') ? 'selected' : ''; ?>>Abecedno (Z-A)</option>
        <option value="price_asc" <?php echo ($sortingOption === 'price_asc') ? 'selected' : ''; ?>>Cena (↑)</option>
        <option value="price_desc" <?php echo ($sortingOption === 'price_desc') ? 'selected' : ''; ?>>Cena (↓)</option>
        <option value="rating" <?php echo ($sortingOption === 'rating') ? 'selected' : ''; ?>>Ocena</option>
    </select>
</form>

<div class="container">
    <div class="row">
        <?php
        $count = 0;

        while ($query_row = mysqli_fetch_assoc($result)) {
            $avtorji = getAuthorsByISBN($conn, $query_row['book_isbn']);

            if ($count % 4 == 0) {
                echo '<div class="row">';
            }
        ?>
            <div class="col-md-3 text-center book-container">
                <a href="book.php?bookisbn=<?php echo $query_row['book_isbn']; ?>" class="book-link">
                    <div class="book-image-wrapper" >
                        <img class="img-responsive img-thumbnail book-cover" src="../bootstrap/img/<?php echo $query_row['book_image']; ?>" alt="<?php echo $query_row['book_title']; ?>">
                    </div>
                    <p style="margin:10px;color: black;"><strong><?php echo $query_row['book_title']; ?></strong></p>
                    <?php
                    foreach ($avtorji as $avtor) {
                        echo '<a style="color: gray;" href="author.php?name=' . urlencode($avtor) . '">' . $avtor . '</a>';
                        if (next($avtorji)) {
                            echo ', ';
                        }
                    } ?>
                </a>
            </div>
        <?php
            $count++;
            if ($count % 4 == 0) {
                echo '</div><br/><br/>';
            }
        }

        // Close the last row if the total number of books is not a multiple of 4
        if ($count % 4 != 0) {
            echo '</div><br/>';
        }
        ?>
    </div>
</div>

<!-- Pagination -->
<div class="container">
    <ul class="pagination justify-content-center">
        <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
            <li class="page-item <?php if ($i === $page) echo 'active'; ?>">
                <a class="page-link" href="?page=<?php echo $i . '&sort=' . $sortingOption; ?><?php if (isset($searchQuery)) echo '&q=' . urlencode($searchQuery); ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</div>

<style>
    .book-container {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
        /* Ensure the container occupies full height of its parent */
    }

    .book-image-wrapper {
        flex: 1;
        /* Allow the image wrapper to expand to fill remaining space */
        display: flex;
        justify-content: center;
        align-items: flex-end;
        /* Align the image to the bottom of its container */
    }

    .book-cover {
        width: 90%;
        /* Ensure the image takes up 100% width of its container */
        margin: auto;
        /* Center */
        height: auto;
        /* Automatically adjust height to maintain aspect ratio */
    }

    .book-details {
        text-align: center;
    }
</style>

<?php
require_once "../footer.php";
?>