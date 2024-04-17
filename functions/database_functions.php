<?php

function dbConnect()
{
    $conn = mysqli_connect("localhost", "user", "", "final");
    if (!$conn) {
        die("Ne morem se povezati z bazo podatkov.");
    }
    return $conn;
}

function dbConnectAdmin()
{
    $conn = mysqli_connect("localhost", "admin", "U15weXS#KySu2gVePtL%", "final");
    if (!$conn) {
        die("Ne morem se povezati z bazo podatkov.");
    }
    return $conn;
}

function getOrderId($conn, $id)
{
    $query = "SELECT orderid FROM orders WHERE customerid = '$id'";
    $result = mysqli_query($conn, $query);
    if (!$result) {
        echo "Ne morem pridobiti podatkov: " . mysqli_error($conn);
        exit;
    }
    $row = mysqli_fetch_assoc($result);
    return $row['orderid'];
}
function insertIntoOrder($conn, $id, $total_price, $date, $ship_name, $ship_address, $ship_city, $ship_zip_code, $ship_country)
{
    // Trim input data to remove leading and trailing spaces
    $ship_name = trim($ship_name);
    $ship_address = trim($ship_address);
    $ship_city = trim($ship_city);
    $ship_zip_code = trim($ship_zip_code);
    $ship_country = trim($ship_country);

    // Modify the query to include the correct column names and placeholders for values
    $query = "INSERT INTO orders (customerid, amount, date, ship_name, ship_address, ship_city, ship_zip_code, ship_country) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    // Prepare the query
    $stmt = mysqli_prepare($conn, $query);

    // Bind parameters to the prepared statement
    mysqli_stmt_bind_param($stmt, "idssssss", $id, $total_price, $date, $ship_name, $ship_address, $ship_city, $ship_zip_code, $ship_country);

    // Execute the statement
    $result = mysqli_stmt_execute($stmt);

    // Return true if the insertion was successful, false otherwise
    return $result;
}

function getPublisherName($conn, $pubid)
{
    $query = "SELECT publisher_name FROM publisher WHERE publisher_id = '$pubid'";
    $result = mysqli_query($conn, $query);
    if (!$result) {
        echo "Ne morem pridobiti podatkov: " . mysqli_error($conn);
        exit;
    }
    if (mysqli_num_rows($result) == 0) {
        echo "Nekaj je narobe!";
        exit;
    }

    $row = mysqli_fetch_assoc($result);
    return $row['publisher_name'];
}

function getAllPublishers($conn)
{
    $query = "SELECT * FROM publisher";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        echo "Ne morem pridobiti podatkov: " . mysqli_error($conn);
        exit;
    }

    return $result;
}

function getAllPublishersPaginated($conn, $limit, $offset)
{
    $query = "SELECT * FROM publisher LIMIT $limit OFFSET $offset";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        echo "Ne morem pridobiti podatkov: " . mysqli_error($conn);
        exit;
    }

    return $result;
}

function getPublisherBookCount($conn, $publisherId)
{
    $query = "SELECT COUNT(*) AS book_count FROM book WHERE publisher_id = '$publisherId'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['book_count'];
}

function getPublishersCount($conn)
{
    // Query to count the total number of publishers
    $query = "SELECT COUNT(*) AS total_publishers FROM publisher";

    // Execute the query
    $result = mysqli_query($conn, $query);

    // Check if the query was successful
    if (!$result) {
        // If not successful, display an error message
        echo "Ne morem pridobiti števila založnikov: " . mysqli_error($conn);
        exit;
    }

    // Fetch the result as an associative array
    $row = mysqli_fetch_assoc($result);

    // Return the total number of publishers
    return $row['total_publishers'];
}

function getAllBooks($conn, $sortingOption = 'latest', $limit = null, $offset = 0)
{
    $allowedSortingOptions = ['latest', 'popular', 'alphabetical_asc', 'alphabetical_desc', 'price_asc', 'price_desc', 'rating'];

    // Validate the sorting option
    if (!in_array($sortingOption, $allowedSortingOptions)) {
        $sortingOption = 'latest'; // Default to 'latest' if an invalid option is provided
    }

    $orderBy = '';

    switch ($sortingOption) {
        case 'popular':
            $orderBy = 'b.book_sold DESC'; // Assuming there's a column for sale number
            break;
        case 'alphabetical_asc':
            $orderBy = 'b.book_title ASC';
            break;
        case 'alphabetical_desc':
            $orderBy = 'b.book_title DESC';
            break;
        case 'price_asc':
            $orderBy = 'b.book_price ASC';
            break;
        case 'price_desc':
            $orderBy = 'b.book_price DESC';
            break;
        case 'rating':
            $orderBy = 'b.book_rating DESC'; // Assuming 'book_rating' is the column for ratings
            break;
        default:
            $orderBy = 'b.date_added DESC'; // Default to 'latest'
    }

    $limitClause = '';
    if ($limit !== null) {
        $limitClause = "LIMIT $limit OFFSET $offset";
    }

    $query = "SELECT b.*, GROUP_CONCAT(a.author_name SEPARATOR ', ') AS authors
              FROM book b
              LEFT JOIN book_author ba ON b.book_isbn = ba.book_isbn
              LEFT JOIN author a ON ba.author_id = a.author_id
              GROUP BY b.book_isbn
              ORDER BY $orderBy $limitClause";

    $result = mysqli_query($conn, $query);

    if (!$result) {
        echo "Ne morem pridobiti podatkov: " . mysqli_error($conn);
        exit;
    }

    return $result;
}

function getBookCount($conn)
{
    $query = "SELECT COUNT(*) AS total_count FROM book";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        echo "Ne morem pridobiti podatkov: " . mysqli_error($conn);
        exit;
    }

    $row = mysqli_fetch_assoc($result);
    return $row['total_count'];
}

function getBookPrice($conn, $isbn)
{
    $query = "SELECT book_price FROM book WHERE book_isbn = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $isbn);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (!$result) {
        echo "Napaka: " . mysqli_error($conn);
        exit;
    }
    $row = mysqli_fetch_assoc($result);
    return $row['book_price'];
}

function getBookByIsbn($conn, $isbn)
{
    $isbn = mysqli_real_escape_string($conn, $isbn);

    $query = "SELECT b.*, GROUP_CONCAT(a.author_name SEPARATOR ', ') AS authors
              FROM book b
              LEFT JOIN book_author ba ON b.book_isbn = ba.book_isbn
              LEFT JOIN author a ON ba.author_id = a.author_id
              WHERE b.book_isbn = '$isbn'
              GROUP BY b.book_isbn";

    $result = mysqli_query($conn, $query);

    if (!$result) {
        echo "Napaka pri pridobivanju podatkov knjige: " . mysqli_error($conn);
        exit;
    }

    $book = mysqli_fetch_assoc($result);

    return $book;
}

function getAllAuthors($conn)
{
    $query = "SELECT * FROM author";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        echo "Ne morem pridobiti podatkov: " . mysqli_error($conn);
        exit;
    }

    $authors = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $authors[] = $row;
    }

    return $authors;
}

function getAllAuthorsWithBookCount($conn, $limit, $offset)
{
    $query = "SELECT a.*, COUNT(ba.book_isbn) AS book_count
              FROM author a
              LEFT JOIN book_author ba ON a.author_id = ba.author_id
              GROUP BY a.author_id
              LIMIT $limit OFFSET $offset";

    $result = mysqli_query($conn, $query);

    if (!$result) {
        echo "Ne morem pridobiti podatkov: " . mysqli_error($conn);
        exit;
    }

    $authors = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $authors[] = $row;
    }

    return $authors;
}

function getAuthorsByISBN($conn, $isbn)
{
    $query = "SELECT a.author_name FROM book_author ba
                  JOIN author a ON ba.author_id = a.author_id
                  WHERE ba.book_isbn = '$isbn'";

    $result = mysqli_query($conn, $query);

    if (!$result) {
        echo "Ne morem pridobiti podatkov: " . mysqli_error($conn);
        exit;
    }

    $authors = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $authors[] = $row['author_name'];
    }

    return $authors;
}

function getAuthorsCount($conn)
{
    $query = "SELECT COUNT(*) AS total FROM author";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['total'];
}

function checkLoggedIn()
{

    // Check if the user is logged in
    if (!isset($_SESSION['user_id'])) {
        // Set the redirect message
        $_SESSION['redirect_message'] = 'cart';
        // Redirect the user to the login page
        header("Location: login.php");
        exit;
    }
}

function isLoggedIn()
{

    // Check if the user is logged in. If he is = true
    if (isset($_SESSION['user_id'])) {
        return true;
    }
}

function searchBooks($conn, $searchQuery, $booksPerPage, $offset)
{
    // Escape the search query to prevent SQL injection
    $searchQuery = mysqli_real_escape_string($conn, $searchQuery);

    // Construct the SQL query to search for books
    $query = "SELECT b.*, GROUP_CONCAT(a.author_name SEPARATOR ', ') AS authors
              FROM book b
              LEFT JOIN book_author ba ON b.book_isbn = ba.book_isbn
              LEFT JOIN author a ON ba.author_id = a.author_id
              WHERE b.book_title LIKE '%$searchQuery%'
              OR a.author_name LIKE '%$searchQuery%'
              OR b.book_isbn LIKE '%$searchQuery%'
              GROUP BY b.book_isbn";

    // Execute the query
    $result = mysqli_query($conn, $query);

    if (!$result) {
        echo "Ne morem pridobiti podatkov: " . mysqli_error($conn);
        exit;
    }

    return $result;
}

function countSearchBooks($conn, $searchQuery)
{
    // Escape the search query to prevent SQL injection
    $searchQuery = mysqli_real_escape_string($conn, $searchQuery);

    // Construct the SQL query to count the number of search results
    $query = "SELECT COUNT(*) AS count
              FROM (
                  SELECT DISTINCT b.book_isbn
                  FROM book b
                  LEFT JOIN book_author ba ON b.book_isbn = ba.book_isbn
                  LEFT JOIN author a ON ba.author_id = a.author_id
                  WHERE b.book_title LIKE '%$searchQuery%'
                  OR a.author_name LIKE '%$searchQuery%'
                  OR b.book_isbn LIKE '%$searchQuery%'
              ) AS search_results";

    // Execute the query
    $result = mysqli_query($conn, $query);

    if (!$result) {
        echo "Ne morem pridobiti podatkov: " . mysqli_error($conn);
        exit;
    }

    $row = mysqli_fetch_assoc($result);
    return $row['count'];
}

function getUserData($conn, $userid)
{
    // Poizvedba za pridobitev podatkov uporabnika glede na uporabniški ID
    $query = "SELECT username, email, user_type FROM user WHERE id = $userid";

    // Izvedba poizvedbe
    $result = mysqli_query($conn, $query);

    // Preveri, ali je izvedba poizvedbe uspešna
    if (!$result) {
        // Če poizvedba ne uspe, prikaži sporočilo o napaki
        echo "Napaka: " . mysqli_error($conn);
        exit;
    }

    // Preveri, ali so podatki uporabnika najdeni
    if ($result && mysqli_num_rows($result) > 0) {
        // Pridobi asociativni niz, ki vsebuje podatke uporabnika
        $user = mysqli_fetch_assoc($result);
        return $user;
    } else {
        // Vrne nič, če uporabnik ni najden
        return null;
    }
}

function getUsers($conn, $usersPerPage, $offset)
{
    // Query to fetch users for the current page
    $query = "SELECT * FROM user LIMIT $usersPerPage OFFSET $offset";

    // Execute the query
    $result = mysqli_query($conn, $query);

    // Check if the query was successful
    if (!$result) {
        // If not successful, display an error message
        echo "Ne morem pridobiti uporabnikov za trenutno stran: " . mysqli_error($conn);
        exit;
    }

    // Initialize an empty array to store users
    $users = array();

    // Loop through the results and fetch each user
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }

    // Return the array of users for the current page
    return $users;
}

function updateUser($conn, $userid, $username, $email, $userType)
{
    $query = "UPDATE user SET username = '$username', email = '$email', user_type = '$userType' WHERE id = $userid";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        echo "Napaka pri posodabljanju uporabnika: " . mysqli_error($conn);
        exit;
    }
}

function deleteUser($conn, $userid)
{
    $query = "DELETE FROM user WHERE id = $userid";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        echo "Napaka pri odstranjevanju uporabnika: " . mysqli_error($conn);
        exit;
    }
}
function addUser($conn, $email, $username, $password)
{
    // Hash the password before storing it in the database
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Escape user input to prevent SQL injection
    $email = mysqli_real_escape_string($conn, $email);
    $username = mysqli_real_escape_string($conn, $username);

    // Perform the database query
    $query = "INSERT INTO user (email, username, password) VALUES ('$email', '$username', '$hashedPassword')";
    $result = mysqli_query($conn, $query);

    // Check if the query was successful
    if (!$result) {
        echo "Napaka pri dodajanju uporabnika: " . mysqli_error($conn);
        return false;
    }

    return true;
}

function isEmailExists($conn, $email)
{
    $email = mysqli_real_escape_string($conn, $email);
    $query = "SELECT * FROM user WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        echo "Napaka pri preverjanju obstoječnosti e-naslova: " . mysqli_error($conn);
        return false;
    }

    $count = mysqli_num_rows($result);
    return $count > 0;
}

function countUsers($conn)
{
    // Query to count the total number of users
    $query = "SELECT COUNT(*) AS total_users FROM user";

    // Execute the query
    $result = mysqli_query($conn, $query);

    // Check if the query was successful
    if (!$result) {
        // If not successful, display an error message
        echo "Ne morem pridobiti števila uporabnikov: " . mysqli_error($conn);
        exit;
    }

    // Fetch the result as an associative array
    $row = mysqli_fetch_assoc($result);

    // Return the total number of users
    return $row['total_users'];
}

function countWords($text)
{
    // Remove HTML tags and trim whitespaces
    $cleanText = trim(strip_tags($text));

    // Count words
    $wordCount = str_word_count($cleanText);

    return $wordCount;
}

function checkAndResizeImage($sourcePath, $targetWidth)
{
    //Funkcija preveri in prilagodi sliko po potrebi, preveri tudi, ali je manjša od 5 MB

    // Določite največjo velikost datoteke na 5 MB
    $maxFileSizeBytes = 5 * 1024 * 1024; // 5 MB v bajtih

    // Preveri velikost datoteke
    if (filesize($sourcePath) > $maxFileSizeBytes) {
        echo "Velikost datoteke presega dovoljeno omejitev velikosti.";
        return;
    }

    //Širina slike
    list($sourceWidth, $sourceHeight, $sourceType) = getimagesize($sourcePath);

    if ($sourceWidth > $targetWidth) {
        $targetHeight = ($targetWidth / $sourceWidth) * $sourceHeight;

        switch ($sourceType) {
            case IMAGETYPE_JPEG:
                echo '<br/>' . $sourcePath;
                $sourceImage = imagecreatefromjpeg($sourcePath);
                break;
            case IMAGETYPE_PNG:
                $sourceImage = imagecreatefrompng($sourcePath);
                break;
            case IMAGETYPE_GIF:
                $sourceImage = imagecreatefromgif($sourcePath);
                break;
            default:
                // Nepodprt tip slike
                return;
        }

        $targetImage = imagecreatetruecolor($targetWidth, $targetHeight);

        imagecopyresampled($targetImage, $sourceImage, 0, 0, 0, 0, $targetWidth, $targetHeight, $sourceWidth, $sourceHeight);

        switch ($sourceType) {
            case IMAGETYPE_JPEG:
                imagejpeg($targetImage, $sourcePath);
                break;
            case IMAGETYPE_PNG:
                imagepng($targetImage, $sourcePath);
                break;
            case IMAGETYPE_GIF:
                imagegif($targetImage, $sourcePath);
                break;
        }

        imagedestroy($sourceImage);
        imagedestroy($targetImage);
    }
}
