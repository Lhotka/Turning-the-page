<?php
    $title = "Book management";
    require_once "../template/header.php";
    checkAdmin();

    // Assuming $conn is already established
    $result = getAll($conn);
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
        <th>&nbsp;</th>
        <th>&nbsp;</th>
    </tr>
    <?php
    while ($row = mysqli_fetch_assoc($result)) {
    ?>
        <tr>
            <td><?php echo $row['book_isbn']; ?></td>
            <td><?php echo $row['book_title']; ?></td>
            <td><?php echo $row['book_author']; ?></td>
            <td><?php echo getPubName($conn, $row['publisherid']); ?></td>
            <td><?php echo $row['book_image']; ?></td>
            <!-- limit description's character output -->
            <td><?php echo substr($row['book_descr'], 0, 50) . (strlen($row['book_descr']) > 200 ? '...' : ''); ?></td>
            <td><?php echo $row['book_price']; ?></td>
            <td><a href="bookedit.php?bookisbn=<?php echo $row['book_isbn']; ?>">Edit</a></td>
            <td><a style="color:red" href="bookdelete.php?bookisbn=<?php echo $row['book_isbn']; ?>">Delete</a></td>
        </tr>
    <?php } ?>
</table>

<?php
    require_once "../template/footer.php";
?>
