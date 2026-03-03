<?php
session_start();
include("../login/db_connect.php");

if(!isset($_SESSION['admin_id'])){
    header("Location: ../login/login.php");
    exit();
}

/* ================= FETCH REVENUE PRODUCTS ================= */
// Only include orders where payment_status='Paid'
$sql = "
    SELECT p.product_id, p.product_name, 
           SUM(oi.quantity) AS total_sold, 
           SUM(oi.price * oi.quantity) AS total_revenue
    FROM order_items oi
    JOIN product p ON oi.product_id = p.product_id
    JOIN orders o ON oi.order_id = o.order_id
    WHERE o.payment_status = 'Paid'
    GROUP BY p.product_id, p.product_name
    ORDER BY total_revenue DESC
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Revenue Details</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        h2 { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
        th { background: #333; color: white; }
    </style>
</head>
<body>
    <h2>Products That Generated Revenue (Paid Orders Only)</h2>

    <table>
        <thead>
            <tr>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>Total Quantity Sold</th>
                <th>Total Revenue (₹)</th>
            </tr>
        </thead>
        <tbody>
        <?php if($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['product_id']; ?></td>
                    <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                    <td><?php echo $row['total_sold']; ?></td>
                    <td>₹<?php echo number_format($row['total_revenue'], 2); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="4">No products have been paid for yet.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</body>
</html>