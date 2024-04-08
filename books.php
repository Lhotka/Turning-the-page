<?php
    $title = "Vse knjige";
    require_once "./template/header.php";

    // Pridobi možnost sortiranja iz parametra poizvedbe (privzeto na 'najnovejše', če ni podano)
    $sortingOption = isset($_GET['sort']) ? $_GET['sort'] : 'latest';

    // Pridobi vse knjige glede na možnost sortiranja
    $result = getAllBooks($conn, $sortingOption);
?>

<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vse knjige</title>
</head>

<body>
    <p class="lead text-center text-muted">Vse knjige</p>

    <!-- Uporabi spustni meni za možnosti sortiranja -->
    <form method="get">
        <label for="sort">Razvrsti po:</label>
        <select id="sort" name="sort" onchange="this.form.submit()">
            <option value="latest" <?php echo ($sortingOption === 'latest') ? 'selected' : ''; ?>>Najnovejše</option>
            <option value="popular" <?php echo ($sortingOption === 'popular') ? 'selected' : ''; ?>>Najbolj priljubljene</option>
            <option value="alphabetical_asc" <?php echo ($sortingOption === 'alphabetical_asc') ? 'selected' : ''; ?>>Abecedno (A-Z)</option>
            <option value="alphabetical_desc" <?php echo ($sortingOption === 'alphabetical_desc') ? 'selected' : ''; ?>>Abecedno (Z-A)</option>
            <option value="price_asc" <?php echo ($sortingOption === 'price_asc') ? 'selected' : ''; ?>>Cena (↑)</option>
            <option value="price_desc" <?php echo ($sortingOption === 'price_desc') ? 'selected' : ''; ?>>Cena (↓)</option>
            <option value="rating" <?php echo ($sortingOption === 'rating') ? 'selected' : ''; ?>>Ocena</option>
        </select>
    </form>
    
    <?php
    $count = 0;

    while ($query_row = mysqli_fetch_assoc($result)) {
        $avtorji = getAuthorsByISBN($conn, $query_row['book_isbn']);

        if ($count % 4 == 0) {
            echo '<div class="row">';
        }
        ?>
        <div class="col-md-3 text-center">
            <a href="book.php?bookisbn=<?php echo $query_row['book_isbn']; ?>">
                <img class="img-responsive img-thumbnail" src="./bootstrap/img/<?php echo $query_row['book_image']; ?>">
            <p><strong><?php echo $query_row['book_title']; ?></strong></p>
            <?php 
                foreach ($avtorji as $avtor) {
                    echo '<a href="author.php?name=' . urlencode($avtor) . '">' . $avtor . '</a>';
                    if (next($avtorji)) {
                        echo ', ';
                    }
                }?>
        </div>
        <?php
        $count++;
        if ($count % 4 == 0) {
            echo '</div><br>';
        }
    }

    // Zapri zadnjo vrstico, če skupno število knjig ni večkratnik števila 4
    if ($count % 4 != 0) {
        echo '</div><br>';
    }

    require_once "./template/footer.php";
?>
