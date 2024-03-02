<?php
$title = "Contact";
require_once "./template/header.php";
?>

<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-6 text-center">
        <?php
        // Check if there's an error parameter in the URL
        if (isset($_GET['error'])) {
            $error = $_GET['error'];
            echo '<div class="alert alert-danger" role="alert">Error: ' . $error . '</div>';
        }
        ?>

        <form class="form-horizontal" action="process_contact.php" method="post">
            <fieldset>
                <legend>Contact</legend>
                <div class="form-group">
                    <label for="inputEmail" class="col-lg-2 control-label">Email</label>
                    <div class="col-lg-10">
                        <input type="email" class="form-control" id="inputEmail" name="inputEmail" placeholder="Email" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="textArea" class="col-lg-2 control-label">Message</label>
                    <div class="col-lg-10">
                        <textarea class="form-control" rows="3" id="textArea" name="textArea" required></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-lg-10 col-lg-offset-2">
                        <button type="button" class="btn btn-default" onclick="history.back()">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
    <div class="col-md-3"></div>
</div>

<?php
require_once "./template/footer.php";
?>
