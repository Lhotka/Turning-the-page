<?php
  $title = "Turning the page";
  require_once "./template/header.php";

  // Retrieve the latest books
  $row = selectLatestBooks($conn);
  require_once "./template/latest_books.php";

  require_once "./template/footer.php";
