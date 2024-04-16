<?php
$title = "Upravljanje založnikov";
require_once "../header.php";
checkAdmin();
$conn = dbConnectAdmin();

// Handle form submission for adding a new publisher
if (isset($_POST['add_publisher'])) {
    $newPublisherName = trim($_POST['new_publisher_name']);
    // Perform any additional validation if needed

    // Insert the new publisher into the database
    $query = "INSERT INTO publisher (publisher_name) VALUES ('$newPublisherName')";
    $insertResult = mysqli_query($conn, $query);

    if (!$insertResult) {
        echo "Napaka pri dodajanju novega založnika: " . mysqli_error($conn);
    } else {
        echo "Nov založnik uspešno dodan!";
    }
}

// Handle form submission for deleting a publisher
if (isset($_POST['delete_publisher'])) {
    $deletePublisherID = $_POST['delete_publisher_id'];

    // Delete the selected publisher from the database
    $deleteQuery = "DELETE FROM publisher WHERE publisher_id = '$deletePublisherID'";
    $deleteResult = mysqli_query($conn, $deleteQuery);

    if (!$deleteResult) {
        echo "Napaka pri brisanju založnika: " . mysqli_error($conn);
    }
}

//Pagination variables
$totalPublishers = getPublishersCount($conn); // Get the total number of publishers
$publishersPerPage = 10; // Set the number of publishers per page
$totalPages = ceil($totalPublishers / $publishersPerPage); // Calculate total number of pages
$page = isset($_GET['page']) ? $_GET['page'] : 1; // Get the current page number
$offset = ($page - 1) * $publishersPerPage; // Calculate the offset for the SQL query

$result = getAllPublishersPaginated($conn, $publishersPerPage, $offset); // Get publishers for the current page
?>
<div class="container">
    <h2>Upravljanje založnikov</h2>

    <!-- Obrazec za dodajanje novega založnika -->
    <form method="post" action="">
        <label for="new_publisher_name">Ime novega založnika:</label>
        <input type="text" id="new_publisher_name" name="new_publisher_name" required>
        <button type="submit" name="add_publisher" class="btn btn-success">Dodaj založnika</button>
    </form>

    <!-- Prikaz obstoječih založnikov v tabeli ali drugi obliki -->
    <table class="table" style="margin-top: 20px">
        <tr>
            <th>ID</th>
            <th>Ime založnika</th>
            <th>Število knjig</th>
            <th></th>
        </tr>
        <?php
        while ($row = mysqli_fetch_assoc($result)) {
            $publisherId = $row['publisher_id'];
            $bookCount = getPublisherBookCount($conn, $publisherId);
        ?>
            <tr>
                <td style="vertical-align: middle;"><?php echo $row['publisher_id']; ?></td>
                <td style="vertical-align: middle;"><?php echo $row['publisher_name']; ?></td>
                <td style="vertical-align: middle;"><?php echo $bookCount; ?></td>
                <td style="vertical-align: middle;">
                    <!-- Gumb za urejanje -->
                    <a href="publisheredit.php?publisher_id=<?php echo $row['publisher_id']; ?>" class="btn btn-warning">Uredi</a>
                    <!-- Gumb za brisanje -->
                    <form method="post" action="" style="display: inline-block">
                        <input type="hidden" name="delete_publisher_id" value="<?php echo $row['publisher_id']; ?>">
                        <button type="submit" name="delete_publisher" class="btn btn-danger">Izbriši</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>
    <!-- Pagination -->

    <ul class="pagination justify-content-center">
        <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
            <li class="page-item <?php if ($i == $page) echo 'active'; ?>"><a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
        <?php endfor; ?>
    </ul>
</div>

<?php
require_once "../footer.php";
?>