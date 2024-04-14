<?php
$title = "Upravljanje knjig";
require_once "../template/header.php";
checkAdmin();
$conn = dbConnectAdmin();

// Get the total count of books
$totalBooks = getBookCount($conn);

// Pagination variables
$booksPerPage = 8; // Število knjig na stran
$totalPages = ceil($totalBooks / $booksPerPage); // Izračun skupnega števila strani

// Get the current page number from the query string
$page = isset($_GET['page']) ? $_GET['page'] : 1;

// Calculate the offset for the SQL query
$offset = ($page - 1) * $booksPerPage;

// Get the sorting option from the query string
$sortingOption = isset($_GET['sort']) ? $_GET['sort'] : 'latest';

// Get books for the current page with the selected sorting option
$result = getAllBooks($conn, $sortingOption, $booksPerPage, $offset);
?>
<h2>Upravljanje knjig</h2>

<p class="lead"><a href="bookadd.php">Dodaj novo knjigo</a></p>

<!-- Sorting dropdown -->
<form method="get">
    <label for="sort">Razvrsti po:</label>
    <select id="sort" name="sort" onchange="this.form.submit()">
        <option value="latest" <?php echo ($sortingOption === 'latest') ? 'selected' : ''; ?>>Najnovejše</option>
        <option value="popular" <?php echo ($sortingOption === 'popular') ? 'selected' : ''; ?>>Priljubljene</option>
        <option value="alphabetical_asc" <?php echo ($sortingOption === 'alphabetical_asc') ? 'selected' : ''; ?>>Naslov (A-Ž)</option>
        <option value="alphabetical_desc" <?php echo ($sortingOption === 'alphabetical_desc') ? 'selected' : ''; ?>>Naslov (Ž-A)</option>
        <option value="price_asc" <?php echo ($sortingOption === 'price_asc') ? 'selected' : ''; ?>>Cena (Naraščajoče)</option>
        <option value="price_desc" <?php echo ($sortingOption === 'price_desc') ? 'selected' : ''; ?>>Cena (Padajoče)</option>
        <option value="rating" <?php echo ($sortingOption === 'rating') ? 'selected' : ''; ?>>Ocena</option>
    </select>
</form>

<table class="table" style="margin-top: 20px;">
    <tr>
        <th>ISBN</th>
        <th>Naslov</th>
        <th>Avtor</th>
        <th>Založnik</th>
        <th>Opis</th>
        <th>Cena</th>
        <th>Zaloga</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
    </tr>
    <?php
    while ($row = mysqli_fetch_assoc($result)) {
        $authors = getAuthorsByISBN($conn, $row['book_isbn']);
    ?>
        <tr>
            <td style="vertical-align: middle;"><?php echo $row['book_isbn']; ?></td>
            <td style="vertical-align: middle;"><?php echo $row['book_title']; ?></td>
            <td style="vertical-align: middle;"><?php echo implode(', ', $authors); ?></td>
            <td style="vertical-align: middle;"><?php echo getPubName($conn, $row['publisher_id']); ?></td>
            <td style="vertical-align: middle;"><?php echo empty($row['book_descr']) ? 'PRAZNO' : countWords($row['book_descr']) . " besed"; ?></td>
            <td style="vertical-align: middle;"><?php echo $row['book_price'] . "€"; ?></td>
            <td style="vertical-align: middle;"><?php echo $row['book_quantity']; ?></td>
            <td style="vertical-align: middle;">
                <a class="btn btn-warning" style="display: inline-block;margin-bottom:5px" href="bookedit.php?bookisbn=<?php echo $row['book_isbn']; ?>">Uredi</a>
                <a class="btn btn-danger" style="display: inline-block" href="bookdelete.php?bookisbn=<?php echo $row['book_isbn']; ?>">Izbriši</a>
            </td>
        </tr>
    <?php } ?>
</table>

<!-- Pagination -->
<div class="pagination">
    <ul class="pagination justify-content-center">
        <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
            <li class="page-item <?php if ($i == $page) echo 'active'; ?>"><a class="page-link" href="?page=<?php echo $i . '&sort=' . $sortingOption; ?>"><?php echo $i; ?></a></li>
        <?php endfor; ?>
    </ul>
</div>

<?php
require_once "../template/footer.php";
?>