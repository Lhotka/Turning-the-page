<?php
$title = "Prijava";
require_once "../header.php";

// Preveri, ali obstaja sporočilo o napaki iz preusmeritve
if (isset($_SESSION['redirect_message']) && $_SESSION['redirect_message'] === 'cart') {
    // Prikaži sporočilo
    echo '<div class="alert alert-danger" role="alert">Za uporabo košarice se morate prijaviti.</div>';
    // Počisti sporočilo, da se ne prikaže znova
    unset($_SESSION['redirect_message']);
}

// Preveri, ali je skrbnik že prijavljen
if (isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
    // Preusmeri na nadzorno ploščo skrbnika
    header("Location: ../admin/admindash.php");
    exit();
}

// Preveri, ali je uporabnik že prijavljen
if (isset($_SESSION['user_id'])) {
    // Preusmeri na nadzorno ploščo uporabnika
    header("Location: userdash.php");
    exit();
}
?>

<div class="container">
    <div class="row">
        <!-- Sekcija za prijavo -->
        <div class="col-md-6">
            <legend>Prijava</legend>
            <form class="form-horizontal" method="post" action="verifylogin.php">
                <div class="form-group">
                    <label for="email" class="control-label col-md-2">E-pošta</label>
                    <div class="col-md-8">
                        <input type="text" id="email" name="email" class="form-control" required autocomplete="email">
                    </div>
                </div>
                <div class="form-group">
                    <label for="pass" class="control-label col-md-2">Geslo</label>
                    <div class="col-md-8">
                        <input type="password" id="pass" name="pass" class="form-control" required autocomplete="current-password">
                    </div>
                </div>
            <input type="submit" name="submit" class="btn btn-primary" value="Potrdi">
            </form>
        </div>

        <!-- Sekcija za registracijo -->
        <div class="col-md-6">
            <legend>Registracija</legend>
            <form class="form-horizontal" method="post" action="verifyregistration.php">
                <div class="form-group">
                    <label for="new_email" class="control-label col-md-2">E-pošta</label>
                    <div class="col-md-8">
                        <input type="text" id="new_email" name="new_email" class="form-control" required autocomplete="new-email">
                    </div>
                </div>
                <div class="form-group">
                    <label for="new_name" class="control-label col-md-2">Uporabniško ime</label>
                    <div class="col-md-8">
                        <input type="text" id="new_name" name="new_name" class="form-control" required autocomplete="new-username">
                    </div>
                </div>
                <div class="form-group">
                    <label for="new_pass" class="control-label col-md-2">Geslo</label>
                    <div class="col-md-8">
                        <input type="password" id="new_pass" name="new_pass" class="form-control" required autocomplete="new-password">
                    </div>
                </div>
                <input type="submit" name="register" class="btn btn-primary" value="Potrdi">
            </form>
        </div>
    </div>
</div>

<?php
require_once "../footer.php";
?>