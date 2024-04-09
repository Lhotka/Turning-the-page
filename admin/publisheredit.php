<?php
$title = "Edit Publisher";
require_once "../template/header.php";
checkAdmin();
$conn=dbConnectAdmin();

if (isset($_GET['publisher_id'])) {
    $publisherid = $_GET['publisher_id'];
} else {
    echo "Empty query! GET parameters: " . print_r($_GET, true);
    exit;
}

// get publisher data
$query = "SELECT * FROM publisher WHERE publisher_id = '$publisherid'";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    echo "Publisher not found!";
    exit;
}

$publisher = mysqli_fetch_assoc($result);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_change'])) {
    $newPublisherName = mysqli_real_escape_string($conn, $_POST['publisher_name']);

    // Update the publisher's name in the database
    $updateQuery = "UPDATE publisher SET publisher_name = '$newPublisherName' WHERE publisher_id = '$publisherid'";
    $updateResult = mysqli_query($conn, $updateQuery);

    if ($updateResult) {
        echo "Publisher name updated successfully!";
        // You might want to redirect to another page after successful update
    } else {
        echo "Error updating publisher name: " . mysqli_error($conn);
    }
}
?>

<h2>Edit Publisher</h2>

<form method="post" action="">
    <input type="hidden" name="publisher_id" value="<?php echo $publisher['publisher_id']; ?>">
    <label for="publisher_name">Publisher Name:</label>
    <input type="text" name="publisher_name" value="<?php echo $publisher['publisher_name']; ?>" required>
    <input type="submit" name="save_change" value="Save changes" class="btn btn-success">
</form>

<?php
require_once "../template/footer.php";
?>