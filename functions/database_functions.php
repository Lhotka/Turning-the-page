<?php
function db_connect()
{
    $conn = mysqli_connect("localhost", "root", "", "final");
    if (!$conn) {
        die("Can't connect to the database.");
    }
    return $conn;
}

function checkAndResizeImage($sourcePath, $targetWidth)
// Check and resize image if needed, also check that it's less than 5MB
{
    // Fix the maximum file size to 5MB
    $maxFileSizeBytes = 5 * 1024 * 1024; // 5MB in bytes

    // Check file size
    if (filesize($sourcePath) > $maxFileSizeBytes) {
        echo "File size exceeds the allowed size limit.";
        return;
    }

    //Image width
    list($sourceWidth, $sourceHeight, $sourceType) = getimagesize($sourcePath);

    if ($sourceWidth > $targetWidth) {
        $targetHeight = ($targetWidth / $sourceWidth) * $sourceHeight;

        switch ($sourceType) {
            case IMAGETYPE_JPEG:
                $sourceImage = imagecreatefromjpeg($sourcePath);
                break;
            case IMAGETYPE_PNG:
                $sourceImage = imagecreatefrompng($sourcePath);
                break;
            case IMAGETYPE_GIF:
                $sourceImage = imagecreatefromgif($sourcePath);
                break;
            default:
                // Unsupported image type
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

function getUser($conn, $email, $password)
{
    $query = "SELECT id, password FROM user WHERE email = ?";
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            mysqli_stmt_bind_result($stmt, $userID, $hashedPassword);
            mysqli_stmt_fetch($stmt);

            if (password_verify($password, $hashedPassword)) {
                return $userID;
            }
        }
    }

    return null;
}

function getUserData($conn, $userid) {
    $query = "SELECT username, email, user_type FROM user WHERE id = $userid";

    $result = mysqli_query($conn, $query);

    if (!$result) {
        echo "Error: " . mysqli_error($conn);
        exit;
    }

    if ($result && mysqli_num_rows($result) > 0) {
        // Fetch associative array
        $user = mysqli_fetch_assoc($result);
        return $user;
    } else {
        return null; // User not found
    }
}

function getAllUsers($conn)
{
    $query = "SELECT * FROM user";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        echo "Can't retrieve data: " . mysqli_error($conn);
        exit;
    }

    $users = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }

    return $users;
}

function insertUser($conn, $username, $password)
{
    $query = "INSERT INTO user (username, password) VALUES ('$username', '$password')";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        echo "Error adding user: " . mysqli_error($conn);
        exit;
    }

    return mysqli_insert_id($conn);
}

function updateUser($conn, $userid, $username, $userType)
{
    $query = "UPDATE user SET username = '$username', user_type = '$userType' WHERE id = $userid";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        echo "Error updating user: " . mysqli_error($conn);
        exit;
    }
}


function deleteUser($conn, $userid)
{
    $query = "DELETE FROM user WHERE id = $userid";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        echo "Error deleting user: " . mysqli_error($conn);
        exit;
    }
}

function setUserId($name, $address, $city, $zip_code, $country)
{
    $conn = db_connect();
    $query = "INSERT INTO user (name, address, city, zip_code, country) VALUES 
        ('$name', '$address', '$city', '$zip_code', '$country')";

    $result = mysqli_query($conn, $query);
    if (!$result) {
        echo "Insert user failed: " . mysqli_error($conn);
        exit;
    }

    $id = mysqli_insert_id($conn);
    return $id;
}

	function selectLatestBooks($conn)
    {
		$row = array();
        $query = "SELECT book_isbn, book_image FROM book ORDER BY date_added DESC LIMIT 4";
		$result = mysqli_query($conn, $query);
		if(!$result){
		    echo "Can't retrieve data " . mysqli_error($conn);
		    exit;
		}
		for($i = 0; $i < 4; $i++){
			array_push($row, mysqli_fetch_assoc($result));
		}
		return $row;
	}

    function getAuthorsByISBN($conn, $isbn)
    {
        $query = "SELECT a.author_name FROM book_author ba
                  JOIN author a ON ba.author_id = a.author_id
                  WHERE ba.book_isbn = '$isbn'";
    
        $result = mysqli_query($conn, $query);
    
        if (!$result) {
            echo "Can't retrieve data " . mysqli_error($conn);
            exit;
        }
    
        $authors = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $authors[] = $row['author_name'];
        }
    
        return $authors;
    }

	function getOrderId($conn, $id)
    {
		$query = "SELECT orderid FROM orders WHERE id = '$id'";
		$result = mysqli_query($conn, $query);
		if(!$result){
			echo "retrieve data failed!" . mysqli_error($conn);
			exit;
		}
		$row = mysqli_fetch_assoc($result);
		return $row['orderid'];
	}

    function insertIntoOrder($conn, $id, $total_price, $date, $ship_name, $ship_address, $ship_city, $ship_zip_code, $ship_country)
    {
        $query = "INSERT INTO orders (column1, column2, column3, column4, column5, column6, column7, column8) VALUES 
        ('', '$id', '$total_price', '$date', '$ship_name', '$ship_address', '$ship_city', '$ship_zip_code', '$ship_country')";
        $result = mysqli_query($conn, $query);
    
        if(!$result){
            echo "Insert orders failed " . mysqli_error($conn);
            exit;
        }
    }
    

	function getbookprice($isbn)
    {
		$conn = db_connect();
		$query = "SELECT book_price FROM book WHERE book_isbn = '$isbn'";
		$result = mysqli_query($conn, $query);
		if(!$result){
			echo "get book price failed! " . mysqli_error($conn);
			exit;
		}
		$row = mysqli_fetch_assoc($result);
		return $row['book_price'];
	}
    

	function getPubName($conn, $pubid)
    {
		$query = "SELECT publisher_name FROM publisher WHERE publisher_id = '$pubid'";
		$result = mysqli_query($conn, $query);
		if(!$result){
			echo "Can't retrieve data " . mysqli_error($conn);
			exit;
		}
		if(mysqli_num_rows($result) == 0){
			echo "Something is wrong!";
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
        echo "Can't retrieve data: " . mysqli_error($conn);
        exit;
    }

    return $result;
}

function getAllBooks($conn, $sortingOption = 'latest')
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

    $query = "SELECT b.*, GROUP_CONCAT(a.author_name SEPARATOR ', ') AS authors
              FROM book b
              LEFT JOIN book_author ba ON b.book_isbn = ba.book_isbn
              LEFT JOIN author a ON ba.author_id = a.author_id
              GROUP BY b.book_isbn
              ORDER BY $orderBy";

    $result = mysqli_query($conn, $query);

    if (!$result) {
        echo "Can't retrieve data " . mysqli_error($conn);
        exit;
    }

    return $result;
}
function getAll($conn)
{
    $query = "SELECT * from book ORDER BY book_isbn DESC";
    $result = mysqli_query($conn, $query);
    if (!$result) {
        echo "Can't retrieve data " . mysqli_error($conn);
        exit;
    }
    return $result;
}

function isEmailExists($email)
{
    $conn = db_connect();
    $email = mysqli_real_escape_string($conn, $email);
    $query = "SELECT * FROM user WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        echo "Error checking email existence: " . mysqli_error($conn);
        return false;
    }

    $count = mysqli_num_rows($result);
    mysqli_close($conn);

    return $count > 0;
}

function addUser($conn, $email, $username, $password)
{
    // Hash the password before storing it in the database
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $email = mysqli_real_escape_string($conn, $email);
    $username = mysqli_real_escape_string($conn, $username);

    $query = "INSERT INTO user (email, username, password) VALUES ('$email', '$username', '$hashedPassword')";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        echo "Error adding user: " . mysqli_error($conn);
        return false;
    }

    return true;
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
        echo "Error getting book details: " . mysqli_error($conn);
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
        echo "Can't retrieve data: " . mysqli_error($conn);
        exit;
    }

    $authors = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $authors[] = $row;
    }

    return $authors;
}
function countWords($text) {
    // Remove HTML tags and trim whitespaces
    $cleanText = trim(strip_tags($text));

    // Count words
    $wordCount = str_word_count($cleanText);

    return $wordCount;
}