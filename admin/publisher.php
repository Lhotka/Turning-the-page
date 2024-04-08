<?php
$title = "Publisher Management";
require_once "../template/header.php";
checkAdmin();
$conn=db_connect();

// Handle form submission for adding a new publisher
if (isset($_POST['add_publisher'])) {
    $newPublisherName = trim($_POST['new_publisher_name']);
    // Perform any additional validation if needed

    // Insert the new publisher into the database
    $query = "INSERT INTO publisher (publisher_name) VALUES ('$newPublisherName')";
    $insertResult = mysqli_query($conn, $query);

    if (!$insertResult) {
        echo "Error adding new publisher: " . mysqli_error($conn);
    } else {
        echo "New publisher added successfully!";
    }
}

// Handle form submission for deleting a publisher
if (isset($_POST['delete_publisher'])) {
    $deletePublisherID = $_POST['delete_publisher_id'];

    // Delete the selected publisher from the database
    $deleteQuery = "DELETE FROM publisher WHERE publisher_id = '$deletePublisherID'";
    $deleteResult = mysqli_query($conn, $deleteQuery);

    if (!$deleteResult) {
        echo "Error deleting publisher: " . mysqli_error($conn);
    }
}

$result = getAllPublishers($conn);
?>
<h2>Publisher Management</h2>

<!-- <button type="button" onclick="window.location.href='admindash.php';" class="btn btn-default">Go to Dashboard</button> -->

<!-- Form for adding a new publisher -->
<form method="post" action="">
    <label for="new_publisher_name">New Publisher Name:</label>
    <input type="text" id="new_publisher_name" name="new_publisher_name" required>
    <button type="submit" name="add_publisher" class="btn btn-success">Add Publisher</button>
</form>

<!-- Display the existing publishers in a table or other format -->
<table class="table" style="margin-top: 20px">
    <tr>
        <th>Publisher ID</th>
        <th>Publisher Name</th>
        <th>Action</th>
    </tr>
    <?php
    while ($row = mysqli_fetch_assoc($result)) {
    ?>
        <tr>
            <td><?php echo $row['publisher_id']; ?></td>
            <td><?php echo $row['publisher_name']; ?></td>
            <td>
                <!-- Edit Button -->
                <a href="publisheredit.php?publisher_id=<?php echo $row['publisher_id']; ?>" class="btn btn-warning">Edit</a>
                <!-- Delete Button -->
                <form method="post" action="" style="display: inline-block">
                    <input type="hidden" name="delete_publisher_id" value="<?php echo $row['publisher_id']; ?>">
                    <button type="submit" name="delete_publisher" class="btn btn-danger">Delete</button>
                </form>
            </td>
        </tr>
    <?php } ?>
</table>

<?php
require_once "../template/footer.php";
?>