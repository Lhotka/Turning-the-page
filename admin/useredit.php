<?php
$title = "Edit User";
require_once "../template/header.php";
checkAdmin();
$conn = dbConnectAdmin();

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
    $newEmail = mysqli_real_escape_string($conn, $_POST['newEmail']);
    $newUserType = mysqli_real_escape_string($conn, $_POST['newUserType']);

    // Validate input
    if (empty($newUsername)) {
        $errors[] = "Username is required.";
    }
    if (empty($newEmail)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if (empty($newUserType)) {
        $errors[] = "User type is required.";
    }

    // If no errors, proceed with updating user
    if (empty($errors)) {
        // Call function to update user in the database
        updateUser($conn, $userid, $newUsername, $newEmail, $newUserType);

        // Redirect to user management page or display a success message
        header("Location: user.php");
        exit();
    }
}
?>

<div class="container">
    <h2>Edit User</h2>

    <!-- Edit User Form -->
    <form method="post" action="<?php echo $_SERVER['PHP_SELF'] . '?userid=' . $userid; ?>" enctype="multipart/form-data">
        <?php if (!empty($errors)) : ?>
            <div class="alert alert-danger" role="alert">
                <ul>
                    <?php foreach ($errors as $error) : ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="form-group">
            <label for="newUsername">Username:</label>
            <input type="text" name="newUsername" class="form-control" value="<?php echo $user['username']; ?>" required>
        </div>
        <div class="form-group">
            <label for="newEmail">Email:</label>
            <input type="email" name="newEmail" class="form-control" value="<?php echo $user['email']; ?>" required>
        </div>
        <div class="form-group">
            <label for="newUserType">User Type:</label>
            <select name="newUserType" class="form-control">
                <option value="user" <?php echo ($user['user_type'] == 'user') ? 'selected' : ''; ?>>User</option>
                <option value="admin" <?php echo ($user['user_type'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
            </select>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-success">Save changes</button>
            <button type="reset" class="btn btn-danger">Reset</button>
            <a href="user.php" class="btn btn-default">Nazaj</a>
        </div>
        <input type="hidden" name="action" value="edit">
    </form>
</div>

<?php
require_once "../template/footer.php";
?>
