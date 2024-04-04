<?php
    $title = "Author Management";
    require_once "../template/header.php";
    checkAdmin();

    // Handle form submission for adding a new author
    if(isset($_POST['add_author'])) {
        $newAuthorName = trim($_POST['new_author_name']);
        // Perform any additional validation if needed

        // Insert the new author into the database
        $query = "INSERT INTO author (author_name) VALUES ('$newAuthorName')";
        $insertResult = mysqli_query($conn, $query);

        if(!$insertResult) {
            echo "Error adding new author: " . mysqli_error($conn);
        } else {
            echo "New author added successfully!";
        }
    }

    // Handle form submission for deleting an author
    if(isset($_POST['delete_author'])) {
        $deleteAuthorID = $_POST['delete_author_id'];
        
        // Delete the selected author from the database
        $deleteQuery = "DELETE FROM author WHERE author_id = '$deleteAuthorID'";
        $deleteResult = mysqli_query($conn, $deleteQuery);

        if(!$deleteResult) {
            echo "Error deleting author: " . mysqli_error($conn);
        }
    }

    // Fetch all authors from the database
    $authors = getAllAuthors($conn);
?>

<h2>Author Management</h2>

<!-- Form for adding a new author -->
<form method="post" action="">
    <label for="new_author_name">New Author Name:</label>
    <input type="text" id="new_author_name" name="new_author_name" required>
    <button type="submit" name="add_author" class="btn btn-success">Add Author</button>
</form>

<!-- Display the existing authors in a table -->
<table class="table" style="margin-top: 20px">
    <tr>
        <th>Author ID</th>
        <th>Author Name</th>
        <th>Description</th>
        <th>Action</th>
    </tr>
    <?php foreach ($authors as $author) { ?>
        <tr>
            <td><?php echo $author['author_id']; ?></td>
            <td><?php echo $author['author_name']; ?></td>
            <!-- Display word count or EMPTY if description is empty -->
            <td><?php echo empty($author['author_description']) ? 'EMPTY' : (countWords($author['author_description']) . " words"); ?></td>
            <td>
                <!-- Edit Button -->
                <a href="authoredit.php?author_id=<?php echo $author['author_id']; ?>" class="btn btn-warning">Edit</a>
                <!-- Delete Button -->
                <form method="post" action="" style="display: inline-block">
                    <input type="hidden" name="delete_author_id" value="<?php echo $author['author_id']; ?>">
                    <button type="submit" name="delete_author" class="btn btn-danger">Delete</button>
                </form>
            </td>
        </tr>
    <?php } ?>
</table>

<?php require_once "../template/footer.php"; ?>
