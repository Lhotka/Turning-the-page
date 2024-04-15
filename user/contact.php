<?php
$title = "Kontakt";
require_once "../header.php";

?>

<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-6 text-center">
        <form class="form-horizontal" action="contactprocess.php" method="post">
            <fieldset>
                <legend>Kontaktirajte nas</legend>
                <div class="form-group">
                    <label for="inputEmail" class="col-lg-2 control-label">E-pošta</label>
                    <div class="col-lg-10">
                        <input type="email" class="form-control" id="inputEmail" name="inputEmail" placeholder="E-pošta" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="textArea" class="col-lg-2 control-label">Sporočilo</label>
                    <div class="col-lg-10">
                        <textarea class="form-control" rows="3" id="textArea" name="textArea" required></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-lg-10 col-lg-offset-2">
                        <button type="button" class="btn btn-default" onclick="history.back()">Prekliči</button>
                        <button type="submit" class="btn btn-primary">Pošlji</button>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
    <div class="col-md-3"></div>
</div>

<?php
// Preveri sporočilo o uspehu
if (isset($_SESSION['success_message'])) {
    echo '<div class="alert alert-success" role="alert">' . $_SESSION['success_message'] . '</div>';
    unset($_SESSION['success_message']); // Počisti sporočilo, da se ne prikaže ponovno
}

// Preveri sporočilo o napaki
if (isset($_SESSION['error_message'])) {
    echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error_message'] . '</div>';
    unset($_SESSION['error_message']); // Počisti sporočilo, da se ne prikaže ponovno
}

require_once "../footer.php";
?>

