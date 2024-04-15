<?php
require_once "../functions/database_functions.php";

$conn = dbConnect();

// Preveri, ali je ime avtorja podano v URL parametru
if (isset($_GET['name'])) {
    // Pridobi ime avtorja iz URL parametra
    $imeAvtorja = $_GET['name'];
    $title = $imeAvtorja;
    require_once "../header.php";

    // Poizvedba v bazi podatkov za pridobitev informacij o avtorju na podlagi imena avtorja
    $poizvedba = "SELECT * FROM author WHERE author_name = '$imeAvtorja'";
    $rezultat = mysqli_query($conn, $poizvedba);

    // Preveri, ali je poizvedba uspešna in ali avtor obstaja
    if ($rezultat && mysqli_num_rows($rezultat) > 0) {
        // Pridobi informacije o avtorju iz rezultata poizvedbe
        $avtor = mysqli_fetch_assoc($rezultat);

        // Pridobi knjige, povezane z avtorjem
        $poizvedbaKnjige = "SELECT b.* FROM book b JOIN book_author ba ON b.book_isbn = ba.book_isbn WHERE ba.author_id = '{$avtor['author_id']}'";
        $rezultatKnjige = mysqli_query($conn, $poizvedbaKnjige);
?>
        <h1><?php echo $avtor['author_name']; ?></h1>
        <br>
        <p><?php echo $avtor['author_description']; ?></p>

        <h3>Knjige:</h3>
        <div class="row">
            <?php while ($vrsticaKnjige = mysqli_fetch_assoc($rezultatKnjige)) { ?>
                <div class="col-md-3 text-center">
                    <a href="book.php?bookisbn=<?php echo $vrsticaKnjige['book_isbn']; ?>">
                        <img class="img-responsive img-thumbnail" style="margin: 10px;" src="../bootstrap/img/<?php echo $vrsticaKnjige['book_image']; ?>">
                        <p><strong><?php echo $vrsticaKnjige['book_title']; ?></strong></p>
                    </a>
                </div>
            <?php } ?>
        </div>


<?php
    } else {
        echo "<p>Avtor ni bil najden</p>";
    }
} else {
    // Ime avtorja ni podano v URL parametru
    echo "<p>Ni podanega avtorja</p>";
}
require_once "../footer.php";
?>