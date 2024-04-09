<?php
$title = "Admin Dashboard";
require_once "../template/header.php";
checkAdmin();
?>

<div class="lead"   style="text-align: center;">
    <a href="user.php" class="btn btn-info" style="display: inline-block; margin-right: 10px;">User Management</a>
    <a href="book.php" class="btn btn-warning" style="display: inline-block; margin-right: 10px;">Book Management</a>
    <a href="author.php" class="btn btn-warning" style="display: inline-block; margin-right: 10px;">Author Management</a>
    <a href="publisher.php" class="btn btn-warning" style="display: inline-block; margin-right: 10px;">Publisher Management</a>
</div>

<?php
require_once "../template/footer.php";
?>