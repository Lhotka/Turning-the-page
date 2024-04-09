<?php
$title = "Vse knjige";
require_once "./template/header.php";

$conn = dbConnect();

// Define the number of books per page
$booksPerPage = 8;

// Determine the current page number
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

// Calculate the SQL OFFSET for pagination
$offset = ($page - 1) * $booksPerPage;

// Pridobi možnost sortiranja iz parametra poizvedbe (privzeto na 'najnovejše', če ni podano)
$sortingOption = isset($_GET['sort']) ? $_GET['sort'] : 'latest';

// Pridobi vse knjige glede na možnost sortiranja in dodaj omejitev za strani
$result = getAllBooks($conn, $sortingOption, $booksPerPage, $offset);

// Število vseh knjig
$totalBooks = getBookCount($conn);

// Število strani
$totalPages = ceil($totalBooks / $booksPerPage);

?>

<!DOCTYPE html>
<html lang="sl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vse knjige</title>
</head>

<body>
    <p class="lead text-center text-muted">Vse knjige</p>

    <!-- Uporabi spustni meni za možnosti sortiranja -->
    <form method="get">
        <label for="sort">Razvrsti po:</label>
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
                <div class="col-md-3 text-center">
                    <a href="book.php?bookisbn=<?php echo $query_row['book_isbn']; ?>">
                        <img class="img-responsive img-thumbnail" src="./bootstrap/img/<?php echo $query_row['book_image']; ?>">
                        <p><strong><?php echo $query_row['book_title']; ?></strong></p>
                        <?php
                        foreach ($avtorji as $avtor) {
                            echo '<a href="author.php?name=' . urlencode($avtor) . '">' . $avtor . '</a>';
                            if (next($avtorji)) {
                                echo ', ';
                            }
                        } ?>
                </div>
            <?php
                $count++;
                if ($count % 4 == 0) {
                    echo '</div><br>';
                }
            }

            // Zapri zadnjo vrstico, če skupno število knjig ni večkratnik števila 4
            if ($count % 4 != 0) {
                echo '</div><br>';
            }
            ?>
        </div>
    </div>

    <!-- Pagination -->
    <div class="container">
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                <li class="page-item <?php if ($i === $page) echo 'active'; ?>"><a class="page-link" href="?page=<?php echo $i . '&sort=' . $sortingOption; ?>"><?php echo $i; ?></a></li>
            <?php endfor; ?>
        </ul>
    </div>

    <?php
    require_once "./template/footer.php";
    ?>
</body>

</html>
