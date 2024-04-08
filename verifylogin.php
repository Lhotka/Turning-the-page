<?php
$title = "Login verify";
require_once "./template/header.php";

$conn=db_connect();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and retrieve user input
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['pass'];

    // Check if email and password are provided
    if (!empty($email) && !empty($password)) {
        // Perform SQL query to retrieve hashed password based on the provided email
        $sql = "SELECT id, email, password, user_type FROM user WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        // Bind result variables
        $stmt->bind_result($userID, $email, $hashedPassword, $userType);

        // Fetch the result
        $stmt->fetch();

        if ($stmt->num_rows > 0 && password_verify($password, $hashedPassword)) {
            // Password is correct, user is authenticated
            // Set session variables
            $_SESSION['user_id'] = $userID;
            $_SESSION['user_email'] = $email; // Add this line to set user_email

            // Redirect to the appropriate page based on user type
            if ($userType === 'admin') {
                $_SESSION['admin'] = true;
                header("Location: admin/admindash.php");
                exit();
            } else {
                header("Location: userdash.php");
                exit();
            }
        } else {
            // User not found or incorrect password
            echo "User not found or incorrect password";
        }

        $stmt->close();
    } else {
        // Email or password not provided
        echo "Please provide both email and password";
    }
}
require_once "./template/footer.php";
