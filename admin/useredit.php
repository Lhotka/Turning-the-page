<?php
$title = "Edit User";
require_once "../template/header.php";
checkAdmin();
$conn=db_connect();

// Ensure a userid is provided in the query string
if (!isset($_GET['userid'])) {
    // Redirect or handle the error as needed
    header("Location: user.php");
    exit();
}

// Get the user ID from the query string
$userid = $_GET['userid'];

// Retrieve user details
$user = getUserData($conn, $userid);

// Check if the user exists
if (!$user) {
    // Redirect or handle the error as needed
    header("Location: user.php");
    exit();
}

// Check if the form is submitted for editing
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'edit') {
    // Retrieve and sanitize input data
    $newUsername = mysqli_real_escape_string($conn, $_POST['newUsername']);
    $newUserType = mysqli_real_escape_string($conn, $_POST['newUserType']);

    // Call function to update user in the database
    updateUser($conn, $userid, $newUsername, $newUserType);

    // Redirect to user management page or display a success message
    header("Location: user.php");
    exit();
}
?>

<div class="container">
    <h2>Edit User</h2>

    <!-- Edit User Form -->
    <form method="post" action="<?php echo $_SERVER['PHP_SELF'] . '?userid=' . $userid; ?>" enctype="multipart/form-data">
        <table class="table">
            <tr>
                <th>Username</th>
                <td><input type="text" name="newUsername" class="form-control" value="<?php echo $user['username']; ?>" required></td>
            </tr>
            <tr>
                <th>User Type</th>
                <td>
                    <select name="newUserType" class="form-control">
                        <option value="user" <?php echo ($user['user_type'] == 'user') ? 'selected' : ''; ?>>User</option>
                        <option value="admin" <?php echo ($user['user_type'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <button type="submit" class="btn btn-warning">Update User</button>
                    <button type="reset" class="btn btn-danger">Reset</button>
                </td>
            </tr>
        </table>
        <input type="hidden" name="action" value="edit">
    </form>
</div>


<?php
require_once "../template/footer.php";
?>