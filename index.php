<?php
$title = "Turning the page";
require_once "./header.php";

$conn = dbConnect();

// Define the number of books per page
$booksPerPage = 4;

// Pridobi možnost sortiranja iz parametra poizvedbe (privzeto na 'najnovejše', če ni podano)
$sortingOption = 'latest'; // Assuming default sorting is 'latest'

// Pridobi vse knjige glede na možnost sortiranja in dodaj omejitev za strani
$result = getAllBooks($conn, $sortingOption, $booksPerPage, 0); // Assuming offset is 0 for the first page
// Število vseh knjig
$totalBooks = getBookCount($conn);

// Število strani
$totalPages = ceil($totalBooks / $booksPerPage);

?>

<p class="lead text-center text-muted">Najnovejše knjige</p>

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
            <a href="user/book.php?bookisbn=<?php echo $query_row['book_isbn']; ?>" class="book-link">
                <img class="img-responsive img-thumbnail book-cover" src="./bootstrap/img/<?php echo $query_row['book_image']; ?>" alt="<?php echo $query_row['book_title']; ?>">
                <p style="margin:10px"><strong><?php echo $query_row['book_title']; ?></strong></p>
                <?php
                foreach ($avtorji as $avtor) {
                    echo '<a href="user/author.php?name=' . urlencode($avtor) . '">' . $avtor . '</a>';
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

<style>
    .book-cover {
        height: auto;
        /* Automatically adjust height to maintain aspect ratio */
        max-height: 300px;
        /* Optional: Set a maximum height to prevent excessive stretching */
    }
</style>

<?php
require_once "./footer.php";
?>