<?php
    $title = "Login";
    require_once "./template/header.php";

    // Check if the admin is already logged in
    if (isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
        // Redirect to the admin dashboard
        header("Location: admin/admindash.php");
        exit();
    }

    // Check if the user is already logged in
    if (isset($_SESSION['user_id'])) {
        // Redirect to the user dashboard
        header("Location: userdash.php");
        exit();
    }
?>

<div class="container">
    <div class="row">
        <!-- Login Section -->
        <div class="col-md-6">
            <legend>Login</legend>
            <form class="form-horizontal" method="post" action="verifylogin.php">
                <div class="form-group">
                    <label for="email" class="control-label col-md-2">Email</label>
                    <div class="col-md-8">
                        <input type="text" id="email" name="email" class="form-control" required autocomplete="email">
                    </div>
                </div>
                <div class="form-group">
                    <label for="pass" class="control-label col-md-2">Password</label>
                    <div class="col-md-8">
                        <input type="password" id="pass" name="pass" class="form-control" required autocomplete="current-password">
                    </div>
                </div>
                <input type="submit" name="submit" class="btn btn-primary">
            </form>
        </div>

        <!-- Registration Section -->
        <div class="col-md-6">
            <legend>Register</legend>
            <form class="form-horizontal" method="post" action="verifyregistration.php">
                <div class="form-group">
                    <label for="new_email" class="control-label col-md-2">Email</label>
                    <div class="col-md-8">
                        <input type="text" id="new_email" name="new_email" class="form-control" required autocomplete="new-email">
                    </div>
                </div>
                <div class="form-group">
                    <label for="new_name" class="control-label col-md-2">Username</label>
                    <div class="col-md-8">
                        <input type="text" id="new_name" name="new_name" class="form-control" required autocomplete="new-username">
                    </div>
                </div>
                <div class="form-group">
                    <label for="new_pass" class="control-label col-md-2">Password</label>
                    <div class="col-md-8">
                        <input type="password" id="new_pass" name="new_pass" class="form-control" required autocomplete="new-password">
                    </div>
                </div>
                <input type="submit" name="register" class="btn btn-primary">
            </form>
        </div>
    </div>
</div>

<?php
    require_once "./template/footer.php";
?>
