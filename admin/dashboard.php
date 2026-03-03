<?php
session_start();
include("../login/db_connect.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login/login.php");
    exit();
}

/* ---------------- STATISTICS ---------------- */

// Total Orders
$order_result = mysqli_query($conn, "SELECT COUNT(order_id) AS total_orders FROM orders");
$total_orders = mysqli_fetch_assoc($order_result)['total_orders'];

// Pending Orders
$pending_result = mysqli_query($conn, "SELECT COUNT(*) AS pending_orders FROM orders WHERE order_status='Pending'");
$pending_orders = mysqli_fetch_assoc($pending_result)['pending_orders'];

// Total Products
$product_result = mysqli_query($conn, "SELECT COUNT(*) AS total_products FROM product");
$total_products = mysqli_fetch_assoc($product_result)['total_products'];

// Total Customers
$customer_result = mysqli_query($conn, "SELECT COUNT(*) AS total_customers FROM customer WHERE role='customer'");
$total_customers = mysqli_fetch_assoc($customer_result)['total_customers'];

// Total Revenue
$revenue_result = mysqli_query($conn, "
SELECT COALESCE(SUM(total_price), 0) AS total_revenue 
FROM billing 
WHERE payment_status='Paid'
");
$row = mysqli_fetch_assoc($revenue_result);
$total_revenue = $row['total_revenue'] ? $row['total_revenue'] : 0;

// Low Stock
$low_stock_result = mysqli_query($conn, "SELECT COUNT(*) AS low_stock FROM inventory WHERE stock_quantity < 5");
$low_stock = mysqli_fetch_assoc($low_stock_result)['low_stock'];

/* ---------------- BEST PRODUCT ---------------- */
$best_product_query = "
SELECT p.product_name, SUM(oi.quantity) AS total_sold
FROM order_items oi
JOIN product p ON oi.product_id = p.product_id
GROUP BY oi.product_id
ORDER BY total_sold DESC
LIMIT 1
";
$best_product_result = mysqli_query($conn, $best_product_query);
$best_product = mysqli_fetch_assoc($best_product_result);

/* ---------------- MONTHLY SALES ---------------- */
$monthly_query = "
SELECT MONTH(sales_date) AS month, SUM(total_price) AS monthly_total
FROM sales
GROUP BY MONTH(sales_date)
";
$monthly_result = mysqli_query($conn, $monthly_query);

$months = [];
$sales = [];

while ($row = mysqli_fetch_assoc($monthly_result)) {
    $months[] = $row['month'];
    $sales[] = $row['monthly_total'];
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
body {
    margin:0;
    font-family:Arial;
    display:flex;
    background:#f4f6f9;
}

.main {
    flex:1;
}

/* CARDS GRID */
.cards {
    display:grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap:25px;
    padding:30px;
}

.card-link {
    text-decoration:none;
    color:inherit;
}

.card {
    background:white;
    padding:25px;
    border-radius:15px;
    box-shadow:0 6px 18px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    cursor:pointer;
    min-height:130px;
    display:flex;
    flex-direction:column;
    justify-content:center;
}

.card:hover {
    transform: translateY(-6px);
    box-shadow:0 12px 28px rgba(0,0,0,0.15);
}

.card h3 {
    margin:0;
    font-size:14px;
    color:#888;
    letter-spacing:0.5px;
}

.card p {
    font-size:28px;
    font-weight:bold;
    margin-top:12px;
}

.highlight {
    color:#e74c3c;
}

/* CHART */
.chart-container {
    width:85%;
    margin:40px auto;
    background:white;
    padding:25px;
    border-radius:15px;
    box-shadow:0 6px 18px rgba(0,0,0,0.08);
}
</style>
</head>

<body>

<?php include("sidebar.php"); ?>

<div class="main">

<?php include("navbar.php"); ?>

<div class="cards">

    <a href="admin_orders.php" class="card-link">
        <div class="card">
            <h3>Total Orders</h3>
            <p><?php echo $total_orders; ?></p>
        </div>
    </a>

    <a href="admin_orders.php?status=pending" class="card-link">
    <div class="card">
        <h3>Pending Orders</h3>
        <p class="highlight"><?php echo $pending_orders; ?></p>
    </div>
</a>

    <a href="dashb_revenue_results.php" class="card-link">
        <div class="card">
            <h3>Total Revenue</h3>
            <p>₹<?php echo number_format($total_revenue,2); ?></p>
        </div>
    </a>

    
        <div class="card">
            <h3>Total Products</h3>
            <p><?php echo $total_products; ?></p>
        </div>
   
    
        <div class="card">
            <h3>Total Customers</h3>
            <p><?php echo $total_customers; ?></p>
        </div>
   

    <a href="inventory_details.php?low_stock=1" class="card-link">
        <div class="card">
            <h3>Low Stock Items</h3>
            <p class="highlight"><?php echo $low_stock; ?></p>
        </div>
    </a>

    <a href="sales_report.php" class="card-link">
        <div class="card">
            <h3>Best Selling Product</h3>
            <p>
            <?php 
            if ($best_product) {
                echo $best_product['product_name'] . " (" . $best_product['total_sold'] . ")";
            } else {
                echo "No sales yet";
            }
            ?>
            </p>
        </div>
    </a>

</div>
</body>
</html>