<?php
$title = "User Dashboard";
require_once "./template/header.php";

// Redirect to the login page if the user is not logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

// Check if the user is trying to log out
if (isset($_POST['logout'])) {
    // Unset all session variables
    $_SESSION = array();

    // Destroy the session
    session_destroy();

    // Redirect to the login page after logout
    header("Location: login.php");
    exit();
}
// Retrieve user ID from the session
$userID = $_SESSION['user_id'];

if ($userID) {
    // User found, retrieve additional information using getUserData function
    $userData = getUserData($conn, $userID);

    // Display user information
    $username = $userData['username'];
    $email = $userData['email'];

    ?>

    <div class="container">
        <h2>Welcome, <?php echo $username; ?>!</h2>

        <h3>User Settings</h3>
        <p>Email: <?php echo $email; ?></p>

        <h3>Order History</h3>
        <!-- Add logic to retrieve and display order history -->

        <!-- Add more sections as needed based on your requirements -->

        <!-- Sign-out button -->
    <form method="post" action="userdash.php">
        <input type="submit" name="logout" class="btn btn-danger" value="Sign Out">
    </form>
    </div>

    <?php
} else {
    // User not found, redirect to login page or display an error message
    header("Location: login.php");
    exit();
}

require_once "./template/footer.php";
?>
