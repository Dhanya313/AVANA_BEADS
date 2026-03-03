<?php
// sales_report.php - Admin Sales Report
include("../login/db_connect.php");

// 1️⃣ Date filter
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01'); // first day of month
$end_date   = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');       // today

// 2️⃣ Summary queries
$summarySql = "SELECT SUM(total_amount) AS total_sales, COUNT(order_id) AS total_orders
               FROM `orders`
               WHERE DATE(order_date) BETWEEN '$start_date' AND '$end_date'";
$summaryRes = $conn->query($summarySql)->fetch_assoc();

$totalCustomersSql = "SELECT COUNT(DISTINCT cust_id) AS total_customers
                      FROM `orders`
                      WHERE DATE(order_date) BETWEEN '$start_date' AND '$end_date'";
$totalCustomers = $conn->query($totalCustomersSql)->fetch_assoc()['total_customers'];

// 3️⃣ Orders table query
$ordersSql = "SELECT o.order_id, c.cust_name, o.order_date, o.order_status, o.payment_method, o.payment_status, o.total_amount
              FROM `orders` o
              JOIN customer c ON o.cust_id = c.cust_id
              WHERE DATE(o.order_date) BETWEEN '$start_date' AND '$end_date'
              ORDER BY o.order_date DESC";
$ordersResult = $conn->query($ordersSql);

// 4️⃣ Top-customers query
$topCustomersSql = "SELECT c.cust_name, SUM(o.total_amount) AS total_spent, COUNT(o.order_id) AS orders_count
                    FROM `orders` o
                    JOIN customer c ON o.cust_id = c.cust_id
                    WHERE DATE(o.order_date) BETWEEN '$start_date' AND '$end_date'
                    GROUP BY c.cust_id
                    ORDER BY total_spent DESC
                    LIMIT 10";
$topCustomers = $conn->query($topCustomersSql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Sales Report</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f9f9f9; }
        h1 { margin-bottom: 20px; }
        .summary { display: flex; gap: 20px; margin-bottom: 20px; }
        .card { flex: 1; background: #007bff; color: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .card h2 { margin: 0 0 10px; font-size: 20px; }
        .filters { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; background: white; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background: #f4f4f4; }
        .top-customers { margin-top: 30px; }
        .export-buttons { margin: 10px 0; }
        .export-buttons button { margin-right: 10px; padding: 8px 12px; background: green; color: white; border: none; border-radius: 5px; cursor: pointer; }
    </style>
</head>
<body>

<h1>Sales Report</h1>

<!-- Filters -->
<form class="filters" method="GET">
    <label>Start Date: <input type="date" name="start_date" value="<?php echo $start_date; ?>"></label>
    <label>End Date: <input type="date" name="end_date" value="<?php echo $end_date; ?>"></label>
    <button type="submit">Filter</button>
</form>

<!-- Summary Cards -->
<div class="summary">
    <div class="card">
        <h2>Total Sales</h2>
        <p>₹ <?php echo number_format($summaryRes['total_sales'], 2); ?></p>
    </div>
    <div class="card">
        <h2>Total Orders</h2>
        <p><?php echo $summaryRes['total_orders']; ?></p>
    </div>
    <div class="card">
        <h2>Customers</h2>
        <p><?php echo $totalCustomers; ?></p>
    </div>
</div>

<!-- Export buttons -->
<div class="export-buttons">
    <button onclick="alert('Export CSV coming soon')">Export CSV</button>
    <button onclick="alert('Export Excel coming soon')">Export Excel</button>
</div>

<!-- Orders Table -->
<h2>Orders Detail</h2>
<table>
    <thead>
        <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Order Date</th>
            <th>Order Status</th>
            <th>Payment Method</th>
            <th>Payment Status</th>
            <th>Total Amount</th>
        </tr>
    </thead>
    <tbody>
        <?php if($ordersResult->num_rows > 0): ?>
            <?php while($row = $ordersResult->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['order_id']; ?></td>
                    <td><?php echo htmlspecialchars($row['cust_name']); ?></td>
                    <td><?php echo $row['order_date']; ?></td>
                    <td><?php echo $row['order_status']; ?></td>
                    <td><?php echo $row['payment_method']; ?></td>
                    <td><?php echo $row['payment_status']; ?></td>
                    <td>₹ <?php echo number_format($row['total_amount'], 2); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="7">No orders found in this date range.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<!-- Top Customers Table -->
<div class="top-customers">
    <h2>Top Customers (by Total Spend)</h2>
    <table>
        <thead>
            <tr>
                <th>Customer Name</th>
                <th>Orders Count</th>
                <th>Total Spent</th>
            </tr>
        </thead>
        <tbody>
            <?php if($topCustomers->num_rows > 0): ?>
                <?php while($c = $topCustomers->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($c['cust_name']); ?></td>
                        <td><?php echo $c['orders_count']; ?></td>
                        <td>₹ <?php echo number_format($c['total_spent'], 2); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="3">No customers found in this date range.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>