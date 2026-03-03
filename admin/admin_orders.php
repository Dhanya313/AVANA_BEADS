<?php
session_start();
include("../login/db_connect.php");

if(!isset($_SESSION['admin_id'])){
    header("Location: ../login/login.php");
    exit();
}

/* ================= UPDATE SECTION ================= */

if(isset($_POST['update_status'])){

    $order_id = $_POST['order_id'];
    $order_status = $_POST['status'];
    $payment_update = isset($_POST['payment_status']) ? $_POST['payment_status'] : null;

    /* 1️⃣ Update Order Status */
    mysqli_query($conn, "
        UPDATE orders 
        SET order_status='$order_status' 
        WHERE order_id=$order_id
    ");

    /* 2️⃣ If Admin Marks Payment as Paid */
    if($payment_update == 'Paid'){

        mysqli_query($conn, "
            UPDATE orders 
            SET payment_status='Paid' 
            WHERE order_id=$order_id
        ");

        mysqli_query($conn, "
            UPDATE billing 
            SET payment_status='Paid' 
            WHERE order_id=$order_id
        ");

        /* 3️⃣ Insert into Sales Table (Only Once) */

        $check = mysqli_query($conn, "
            SELECT * FROM sales 
            WHERE order_id=$order_id
        ");

        if(mysqli_num_rows($check) == 0){

            $qty_result = mysqli_query($conn, "
                SELECT SUM(quantity) AS total_qty, 
                       SUM(price*quantity) AS total_price 
                FROM order_items 
                WHERE order_id=$order_id
            ");

            $qty_data = mysqli_fetch_assoc($qty_result);

            mysqli_query($conn, "
                INSERT INTO sales 
                (order_id, sales_date, quantity_sold, total_price)
                VALUES 
                ($order_id, CURDATE(), 
                 {$qty_data['total_qty']}, 
                 {$qty_data['total_price']})
            ");
        }
    }

    header("Location: admin_orders.php");
    exit();
}

/* ================= FETCH ORDERS ================= */

if(isset($_GET['status']) && $_GET['status'] == 'pending') {
    $orders = mysqli_query($conn, "
        SELECT o.*, c.cust_name AS customer_name
        FROM orders o
        LEFT JOIN customer c ON o.cust_id=c.cust_id
        WHERE o.order_status != 'Completed'
        ORDER BY o.order_date DESC
    ");
} else {
    $orders = mysqli_query($conn, "
        SELECT o.*, c.cust_name AS customer_name
        FROM orders o
        LEFT JOIN customer c ON o.cust_id=c.cust_id
        ORDER BY o.order_date DESC
    ");
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Orders</title>
<style>
body { font-family: Arial, sans-serif; margin:0; display:flex; background:#f5f5f5; }
.sidebar { width:220px; background:#333; color:white; min-height:100vh; padding:20px; }
.sidebar a { color:white; display:block; margin:10px 0; text-decoration:none; }
.sidebar a:hover { background:#444; padding-left:5px; }
.main { flex:1; padding:30px; }
table { width:100%; border-collapse: collapse; background:white; border-radius:8px; overflow:hidden; }
th, td { padding:10px; border-bottom:1px solid #ddd; text-align:left; vertical-align:top; }
th { background:#333; color:white; }
button { padding:5px 10px; background:orange; color:white; border:none; border-radius:4px; cursor:pointer; }
select { padding:4px; border-radius:4px; }
.custom-box { font-size:13px; color:#444; margin-top:4px; }
.custom-img { width:60px; border-radius:6px; margin-top:4px; border:1px solid #ccc; }
</style>
</head>
<body>

<div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="admin_products.php">Products</a>
    <a href="admin_categories.php">Categories</a>
    <a href="inventory_details.php">Inventory</a>
    <a href="admin_orders.php">Orders</a>
    <a href="customer_details.php">Customers</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main">
    <h2>Orders</h2>
    <table>
        <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Total Amount</th>
            <th>Payment Method</th>
            <th>Payment Status</th>
            <th>Order Status</th>
            <th>Items</th>
            <th>Action</th>
        </tr>

        <?php while($order = mysqli_fetch_assoc($orders)) { ?>
            <tr>
                <td><?php echo $order['order_id']; ?></td>
                <td><?php echo $order['customer_name']; ?></td>
                <td>₹<?php echo $order['total_amount']; ?></td>
                <td><?php echo $order['payment_method']; ?></td>
                <td><?php echo $order['payment_status']; ?></td>
                <td><?php echo $order['order_status']; ?></td>
                <td>
                    <ul>
                    <?php 
                        $items = mysqli_query($conn, "
                            SELECT oi.*, p.product_name 
                            FROM order_items oi 
                            JOIN product p 
                            ON oi.product_id=p.product_id 
                            WHERE oi.order_id=".$order['order_id']
                        );
                        while($item = mysqli_fetch_assoc($items)){
                            echo "<li>";
                            echo "<strong>".$item['product_name']."</strong> x ".$item['quantity'];

                            // Show Customization Text
                            if(!empty($item['customization_text'])){
                                echo "<div class='custom-box'><b>Customization:</b> ".$item['customization_text']."</div>";
                            }

                            // Show Customization Image
                            if(!empty($item['customization_image'])){
                                echo "<div class='custom-box'><b>Reference Image:</b><br>
                                      <img src='../uploads/customization/".$item['customization_image']."' class='custom-img'>
                                      </div>";
                            }

                            echo "</li>";
                        }
                    ?>
                    </ul>
                </td>
                <td>
                    <form method="POST" style="display:flex; flex-direction:column; gap:5px;">
                        <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">

                        <select name="status" required>
                            <option value="Pending" <?php if($order['order_status']=='Pending') echo "selected"; ?>>Pending</option>
                            <option value="Processing" <?php if($order['order_status']=='Processing') echo "selected"; ?>>Processing</option>
                            <option value="Completed" <?php if($order['order_status']=='Completed') echo "selected"; ?>>Completed</option>
                            <option value="Cancelled" <?php if($order['order_status']=='Cancelled') echo "selected"; ?>>Cancelled</option>
                        </select>

                        <?php if($order['payment_status'] != 'Paid'){ ?>
                            <select name="payment_status">
                                <option value="">--Mark Payment--</option>
                                <option value="Paid">Paid</option>
                            </select>
                        <?php } ?>

                        <button type="submit" name="update_status">Update</button>
                    </form>
                </td>
            </tr>
        <?php } ?>

    </table>
</div>

</body>
</html>