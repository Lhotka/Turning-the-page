<?php
$title = "Nadzorna plošča";
require_once "../header.php";
checkAdmin();
?>

<div style="text-align: center;">
    <div style="margin: 10px;">
        <a href="user.php" class="btn btn-info btn-lg" style="margin: 10px; width: 200px; text-align: center;">Uporabniki</a>
    </div>
    <div style="margin: 10px;">
        <a href="book.php" class="btn btn-warning btn-lg" style="margin: 10px; width: 200px; text-align: center;">Knjige</a>
        <a href="author.php" class="btn btn-warning btn-lg" style="margin: 10px; width: 200px; text-align: center;">Avtorji</a>
        <a href="publisher.php" class="btn btn-warning btn-lg" style="margin: 10px; width: 200px; text-align: center;">Založniki</a>
    </div>
    <div style="margin: 10px;">
        <a href="order.php" class="btn btn-success btn-lg" style="margin: 10px; width: 200px; text-align: center;">Naročila</a>
        <a href="orderold.php" class="btn btn-primary btn-lg" style="margin: 10px; width: 200px; text-align: center;">Pretekla naročila</a>
    </div>
</div>

<?php
require_once "../footer.php";
?>