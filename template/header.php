<?php
session_start();

// Dynamically determine the base path
$basePath = $_SERVER['DOCUMENT_ROOT'] . '/FINAL';

// Use the base path to include other files
require_once $basePath . '/functions/database_functions.php';
require_once $basePath . '/functions/admin.php';

// Determine the base URL dynamically
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$baseUrl = $protocol . '://' . $_SERVER['HTTP_HOST'] . '/FINAL';
$faviconPath = $baseUrl . '/bootstrap/img/favicon.ico';

// Error reporting
ini_set('display_errors', 1); // Disable displaying errors on production - 0
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); // Disable error reporting on production - 0

// Set session error flag
$_SESSION['err'] = 1;

// Set session timeout
$sessionTimeout = 1800; // 30 minutes in seconds
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $sessionTimeout)) {
    // Expire session if user is inactive for too long
    session_unset();     // unset $_SESSION variable for this page
    session_destroy();   // destroy session data
}
$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?php echo $title; ?></title>

    <!-- Favicon -->

    <link rel="icon" href="<?php echo $faviconPath; ?>" type="image/x-icon">
    <link rel="shortcut icon" href="<?php echo $faviconPath; ?>" type="image/x-icon">

    <!-- Dynamic paths for CSS -->
    <link href="<?php echo $baseUrl; ?>/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo $baseUrl; ?>/bootstrap/css/bootstrap-theme.min.css" rel="stylesheet">

    <!--  <link href="/FINAL/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/FINAL/bootstrap/css/bootstrap-theme.min.css" rel="stylesheet">-->

    <!-- <link href="./bootstrap/css/bootstrap.min.css" rel="stylesheet"> -->
    <!-- <link href="./bootstrap/css/bootstrap-theme.min.css" rel="stylesheet"> -->

    <!-- credit card icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body>
    <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <!-- Navigation for screen readers -->
                    <span class="sr-only">Toggle navigation</span>
                    <!-- Burger navigation on zoomed screens -->
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="<?php echo $baseUrl; ?>/index.php">Turning the page</a>
            </div>
            <!-- Admin dashboard button -->
            <div id="navbar" class="navbar-collapse collapse">
                <ul class="nav navbar-nav navbar-left">
                    <?php if (isAdmin()) : ?>
                        <li><a href="<?php echo $baseUrl; ?>/admin/admindash.php"><span class="glyphicon glyphicon-dashboard"></span>&nbsp; Admin Dashboard</a></li>
                    <?php endif; ?>
                </ul>
                <!-- Search bar -->
                <form class="navbar-form navbar-left" action="<?php echo $baseUrl; ?>/books.php" method="get">
                    <div class="form-group">
                        <input type="text" class="form-control" name="q" placeholder="Search" value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
                    </div>
                    <button type="submit" class="btn btn-default">Search</button>
                </form>
                <!-- All other buttons -->
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="<?php echo $baseUrl; ?>/books.php"><span class="glyphicon glyphicon-book"></span>&nbsp; All Books</a></li>
                    <li><a href="<?php echo $baseUrl; ?>/contact.php"><span class="glyphicon glyphicon-phone-alt"></span>&nbsp; Contact</a></li>
                    <li><a href="<?php echo $baseUrl; ?>/cart.php"><span class="glyphicon glyphicon-shopping-cart"></span>&nbsp; Cart</a></li>
                    <li><a href="<?php echo $baseUrl; ?>/userdash.php"><span class="glyphicon glyphicon-user"></span>&nbsp; User</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container" id="main" style="padding-top: 70px;">