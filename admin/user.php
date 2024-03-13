<?php
    $title = "User Management";
    require_once "../template/header.php";
    checkAdmin();

    // Check if form submitted for user actions (add, edit, delete)
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['action'])) {
            $action = $_POST['action'];

            switch ($action) {
                case 'add':
                    // Handle user addition
                    // Retrieve and sanitize input data
                    $username = mysqli_real_escape_string($conn, $_POST['username']);
                    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password for security

                    // Call function to insert user into the database
                    insertUser($conn, $username, $password);
                    break;

                case 'edit':
                    // Handle user editing
                    // Retrieve and sanitize input data
                    $userid = $_POST['userid'];
                    $newUsername = mysqli_real_escape_string($conn, $_POST['newUsername']);
                    $newUserType = mysqli_real_escape_string($conn, $_POST['newUserType']);
                    // Call function to update user in the database
                    updateUser($conn, $userid, $newUsername, $newUserType);
                    break;

                case 'delete':
                    // Handle user deletion
                    // Retrieve user ID from POST data
                    $userid = $_POST['userid'];

                    // Call function to delete user from the database
                    deleteUser($conn, $userid);
                    break;

                default:
                    // Invalid action
                    break;
            }
        }
    }

    // Get all users from the database
    $users = getAllUsers($conn);
?>

<div class="container">
    <h2>User Management</h2>

    <!-- Add User -->
    <p class="lead"><a href="useradd.php">Add New User</a></p>


    <!-- List of Users -->
    <table class="table">
        <thead>
            <tr>
                <th>User ID</th>
                <th>Username</th>
                <th>User type</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo $user['username']; ?></td>
                    <td><?php echo $user['user_type']; ?></td>
                    <td>
                        <!-- Edit Button -->
                        <a href="useredit.php?userid=<?php echo $user['id']; ?>" class="btn btn-warning">Edit</a>
                        
                        <!-- Delete Button -->
                        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display: inline-block">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="userid" value="<?php echo $user['id']; ?>">
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
    require_once "../template/footer.php";
?>
