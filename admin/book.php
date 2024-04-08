<?php
$title = "Book management";
require_once "../template/header.php";
checkAdmin();

$result = getAllBooks($conn, 'latest');
?>
<h2>Book Management</h2>

<p class="lead"><a href="bookadd.php">Add new book</a></p>
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

<?php
require_once "../template/footer.php";
?>