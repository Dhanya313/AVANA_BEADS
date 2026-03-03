<?php
session_start();
include("db_connect.php");
include("customer_navbar.php");

if(!isset($_SESSION['cust_id'])){
    header("Location: login.php");
    exit();
}

$cust_id = $_SESSION['cust_id'];

$stmt = $conn->prepare("SELECT * FROM orders WHERE cust_id=? ORDER BY order_date DESC");
$stmt->bind_param("i", $cust_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
<title>My Orders</title>
<style>
body {
    font-family: Arial, sans-serif;
    background: #f4f4f4;
    margin: 0;
    padding: 0;
}

.container {
    width: 90%;
    max-width: 1000px;
    margin: 40px auto;
    background: white;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 0 15px rgba(0,0,0,0.1);
}

h2 {
    text-align: center;
    margin-bottom: 30px;
    font-size: 28px;
    color: #333;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 12px 10px;
    border: 1px solid #ddd;
    text-align: center;
    font-size: 14px;
}

th {
    background: #333;
    color: white;
    font-weight: 600;
}

.status {
    font-weight: bold;
    padding: 5px 12px;
    border-radius: 4px;
    display: inline-block;
}

.pending { background: orange; color: white; }
.paid { background: green; color: white; }
.failed { background: red; color: white; }

.order-status-pending { background: orange; color: white; }
.order-status-processing { background: #007bff; color: white; }
.order-status-completed { background: green; color: white; }
.order-status-cancelled { background: red; color: white; }

.btn {
    padding: 6px 12px;
    background: #00c6ff;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    transition: 0.3s;
}

.btn:hover {
    background: #0072ff;
}

/* NEW Review Button Style */
.review-btn {
    padding: 6px 12px;
    background: #b76e79;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    transition: 0.3s;
}

.review-btn:hover {
    background: #a05560;
}

.no-order {
    text-align: center;
    padding: 20px;
    font-weight: bold;
    color: #555;
}

tr:hover { background: #f9f9f9; }
</style>
</head>
<body>

<div class="container">
    <h2>My Orders</h2>

    <?php if($result->num_rows > 0){ ?>
        <table>
            <tr>
                <th>Order ID</th>
                <th>Date</th>
                <th>Total (₹)</th>
                <th>Order Status</th>
                <th>Payment Status</th>
                <th>Invoice</th>
                <th>Review</th> <!-- NEW COLUMN -->
            </tr>

            <?php while($row = $result->fetch_assoc()){ 
                $order_status_class = "order-status-" . strtolower($row['order_status']);
                $payment_status_class = strtolower($row['payment_status']);
            ?>
                <tr>
                    <td>#<?php echo $row['order_id']; ?></td>
                    <td><?php echo date("d-m-Y", strtotime($row['order_date'])); ?></td>
                    <td>₹ <?php echo number_format($row['total_amount'], 2); ?></td>
                    <td>
                        <span class="status <?php echo $order_status_class; ?>">
                            <?php echo ucfirst($row['order_status']); ?>
                        </span>
                    </td>
                    <td>
                        <span class="status <?php echo $payment_status_class; ?>">
                            <?php echo ucfirst($row['payment_status']); ?>
                        </span>
                    </td>
                    <td>
                        <a class="btn" href="invoice.php?order_id=<?php echo $row['order_id']; ?>">
                            View
                        </a>
                    </td>

                    <!-- ONLY THIS PART IS NEW -->
                    <td>
                        <?php
                        if(strtolower($row['order_status']) == 'completed' 
                           && strtolower($row['payment_status']) == 'paid'){
                        ?>
                            <a class="review-btn" 
                               href="add_review.php?order_id=<?php echo $row['order_id']; ?>">
                               Add Review
                            </a>
                        <?php
                        } else {
                            echo "-";
                        }
                        ?>
                    </td>

                </tr>
            <?php } ?>
        </table>
    <?php } else { ?>
        <div class="no-order">No orders found.</div>
    <?php } ?>

</div>

</body>
</html>