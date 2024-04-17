<?php
$title = "Upravljanje uporabnikov";
require_once "../header.php";
checkAdmin();
$conn = dbConnectAdmin();

// Handle user deletion if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'delete') {
    // Retrieve user ID from POST data
    $userid = $_POST['userid'];

    // Call function to delete user from the database
    deleteUser($conn, $userid);
}

// Pagination variables
$usersPerPage = 10; // Number of users per page
$totalUsers = countUsers($conn); // Get the total count of users
$totalPages = ceil($totalUsers / $usersPerPage); // Calculate the total number of pages

// Get the current page number from the query string
$page = isset($_GET['page']) ? $_GET['page'] : 1;

// Calculate the offset for the SQL query
$offset = ($page - 1) * $usersPerPage;

// Get users for the current page
$users = getUsers($conn, $usersPerPage, $offset);

?>

<div class="container">
    <h2>Upravljanje uporabnikov</h2>

    <!-- Dodaj uporabnika -->
    <p class="lead"><a href="useradd.php">Dodaj uporabnika</a></p>

    <!-- Seznam uporabnikov -->
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Uporabniško ime</th>
                <th>Tip uporabnika</th>
                <th>Akcija</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user) : ?>
                <tr>
                    <td style="vertical-align: middle;"><?php echo $user['id']; ?></td>
                    <td style="vertical-align: middle;"><?php echo $user['username']; ?></td>
                    <td style="vertical-align: middle;"><?php echo $user['user_type']; ?></td>
                    <td style="vertical-align: middle;">
                        <!-- Gumb za urejanje -->
                        <a href="useredit.php?userid=<?php echo $user['id']; ?>" class="btn btn-warning">Uredi</a>
                        <!-- Gumb za brisanje -->
                        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display: inline-block">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="userid" value="<?php echo $user['id']; ?>">
                            <button type="submit" class="btn btn-danger">Izbriši</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
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