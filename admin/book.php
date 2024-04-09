<?php
$title = "Book management";
require_once "../template/header.php";
checkAdmin();
$conn = dbConnectAdmin();

// Get the total count of books
$totalBooks = getBookCount($conn);

// Pagination variables
$booksPerPage = 8; // Number of books per page
$totalPages = ceil($totalBooks / $booksPerPage); // Calculate total pages

// Get the current page number from the query string
$page = isset($_GET['page']) ? $_GET['page'] : 1;

// Calculate the offset for the SQL query
$offset = ($page - 1) * $booksPerPage;

// Get the sorting option from the query string
$sortingOption = isset($_GET['sort']) ? $_GET['sort'] : 'latest';

// Get books for the current page with the selected sorting option
$result = getAllBooks($conn, $sortingOption, $booksPerPage, $offset);
?>
<h2>Book Management</h2>

<p class="lead"><a href="bookadd.php">Add new book</a></p>

<!-- Sorting dropdown -->
<form method="get">
    <label for="sort">Sort by:</label>
    <select id="sort" name="sort" onchange="this.form.submit()">
        <option value="latest" <?php echo ($sortingOption === 'latest') ? 'selected' : ''; ?>>Latest</option>
        <option value="popular" <?php echo ($sortingOption === 'popular') ? 'selected' : ''; ?>>Popular</option>
        <option value="alphabetical_asc" <?php echo ($sortingOption === 'alphabetical_asc') ? 'selected' : ''; ?>>Title (A-Z)</option>
        <option value="alphabetical_desc" <?php echo ($sortingOption === 'alphabetical_desc') ? 'selected' : ''; ?>>Title (Z-A)</option>
        <option value="price_asc" <?php echo ($sortingOption === 'price_asc') ? 'selected' : ''; ?>>Price (Low to High)</option>
        <option value="price_desc" <?php echo ($sortingOption === 'price_desc') ? 'selected' : ''; ?>>Price (High to Low)</option>
        <option value="rating" <?php echo ($sortingOption === 'rating') ? 'selected' : ''; ?>>Rating</option>
    </select>
</form>

<table class="table" style="margin-top: 20px">
    <tr>
        <th>ISBN</th>
        <th>Title</th>
        <th>Author</th>
        <th>Publisher</th>
        <th>Image</th>
        <th>Description</th>
        <th>Price</th>
        <th>Stock</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
    </tr>
    <?php
    while ($row = mysqli_fetch_assoc($result)) {
        $authors = getAuthorsByISBN($conn, $row['book_isbn']);
    ?>
        <tr>
            <td><?php echo $row['book_isbn']; ?></td>
            <td><?php echo $row['book_title']; ?></td>
            <td><?php echo implode(', ', $authors); ?></td>
            <td><?php echo getPubName($conn, $row['publisher_id']); ?></td>
            <td><?php echo $row['book_image']; ?></td>
            <td><?php echo empty($row['book_descr']) ? 'EMPTY' : countWords($row['book_descr']) . " words"; ?></td>
            <td><?php echo $row['book_price'] . "€"; ?></td>
            <td><?php echo $row['book_quantity']; ?></td>
            <td><a href="bookedit.php?bookisbn=<?php echo $row['book_isbn']; ?>">Edit</a></td>
            <td><a style="color:red" href="bookdelete.php?bookisbn=<?php echo $row['book_isbn']; ?>">Delete</a></td>
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
