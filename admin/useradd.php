<!-- useradd.php -->
<?php
$title = "Add New User";
require_once "../template/header.php";
checkAdmin();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // User addition code here...
}
?>

<div class="container">
    <h2>Add New User</h2>

    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-success">Add User</button>
        </div>
        <input type="hidden" name="action" value="add">
    </form>
</div>

<?php
require_once "../template/footer.php";
?>