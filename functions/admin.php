<?php
function isAdmin()
{
    if (isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
        return true;
    }
    return false;
}
function checkAdmin()
{
    if (!isAdmin()) {
        header("Location: ../user/login.php");
        exit();
    }
}
