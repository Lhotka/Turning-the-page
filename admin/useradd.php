<?php
$title = "Add New User";
require_once "../template/header.php";
checkAdmin();
$conn = dbConnectAdmin();

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $email = trim($_POST['email']);
    $userType = $_POST['user_type']; // Added user type input

    // Validate input
    if (empty($username)) {
        $errors[] = "Username is required.";
    }
    if (empty($password)) {
        $errors[] = "Password is required.";
    }
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if (empty($userType)) {
        $errors[] = "User type is required.";
    }

    if (empty($errors)) {
        // Check if the email already exists in the database
        $checkQuery = "SELECT * FROM user WHERE email = '$email'";
        $checkResult = mysqli_query($conn, $checkQuery);
        if (mysqli_num_rows($checkResult) > 0) {
            $errors[] = "Email already exists. Please choose a different email.";
        } else {
            // Hash the password before storing it in the database
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert the new user into the database
            $insertQuery = "INSERT INTO user (username, password, email, user_type) VALUES ('$username', '$hashedPassword', '$email', '$userType')";
            $insertResult = mysqli_query($conn, $insertQuery);

            if ($insertResult) {
                // User addition successful
                echo "User added successfully!";
                header("Location: user.php");
                exit;
            } else {
                echo "Error adding user: " . mysqli_error($conn);
            }
        }
    }
}
?>

<div class="container">
    <h2>Add New User</h2>

    <?php if (!empty($errors)) : ?>
        <div class="alert alert-danger" role="alert">
            <ul>
                <?php foreach ($errors as $error) : ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="user_type">User Type:</label>
            <select name="user_type" class="form-control" required>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-success">Add User</button>
            <a href="user.php" class="btn btn-default">Nazaj</a>
        </div>
    </form>
</div>

<?php
require_once "../template/footer.php";
?>
