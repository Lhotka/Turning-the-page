<?php
$title = "Naročila";
require_once "../header.php";
checkAdmin();

// Connect to the database
$conn = dbConnectAdmin();

// Pagination variables
$ordersPerPage = 10; // Number of orders per page
$page = isset($_GET['page']) ? $_GET['page'] : 1; // Current page number

// Calculate the offset for the SQL query
$offset = ($page - 1) * $ordersPerPage;

// Query to retrieve orders for the current page
$query = "SELECT * FROM orders LIMIT $offset, $ordersPerPage";
$result = mysqli_query($conn, $query);

// Check if the query was successful
if (!$result) {
    echo "Can't retrieve data: " . mysqli_error($conn);
    exit;
}
?>

<div>
    <h2>Vsa naročila</h2>
    <table class="table" style="margin-top: 20px;">
        <tr>
            <th>ID</th>
            <th>Številka naročila</th>
            <th>Stranka ID</th>
            <th>Znesek</th>
            <th>Datum</th>
            <th>Možnosti</th>
        </tr>
        <?php while ($order = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td style="vertical-align: middle;"><?php echo $order['orderid']; ?></td>
                <td style="vertical-align: middle;"><?php echo $order['orderid']; ?></td>
                <td style="vertical-align: middle;"><?php echo $order['customerid']; ?></td>
                <td style="vertical-align: middle;"><?php echo $order['amount'] . "€"; ?></td>
                <td style="vertical-align: middle;"><?php echo $order['date']; ?></td>
                <td style="vertical-align: middle;">
                    <a href="orderdetails.php?id=<?php echo $order['orderid']; ?>" class="btn btn-primary">Podrobnosti</a>
                    <form action="orderdelete.php" method="post" style="display: inline-block;">
                        <input type="hidden" name="order_id" value="<?php echo $order['orderid']; ?>">
                        <button type="submit" class="btn btn-danger">Izbriši</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>

<!-- Pagination -->
<div class="pagination">
    <ul class="pagination justify-content-center">
        <?php
        // Count total number of orders
        $totalOrdersQuery = "SELECT COUNT(*) AS total FROM orders";
        $totalOrdersResult = mysqli_query($conn, $totalOrdersQuery);
        $totalOrdersRow = mysqli_fetch_assoc($totalOrdersResult);
        $totalOrders = $totalOrdersRow['total'];

        // Calculate total pages
        $totalPages = ceil($totalOrders / $ordersPerPage);

        // Pagination links
        for ($i = 1; $i <= $totalPages; $i++) {
            echo '<li class="page-item ';
            if ($page == $i) {
                echo 'active';
            }
            echo '"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
        }
        ?>
    </ul>
</div>

<?php
require_once "../footer.php";
?>