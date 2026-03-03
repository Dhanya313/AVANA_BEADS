<?php
session_start();
include("../login/db_connect.php");

if (!isset($_GET['order_id'])) {
    die("Invalid Access");
}

$order_id = $_GET['order_id'];

// Fetch Order Details
$order_query = mysqli_query($conn, "
    SELECT o.*, c.cust_name, c.email, c.contact, c.address
    FROM orders o
    JOIN customer c ON o.cust_id = c.cust_id
    WHERE o.order_id = '$order_id'
");

$order = mysqli_fetch_assoc($order_query);

if (!$order) {
    die("Order not found");
}

// Fetch Order Items
$items_query = mysqli_query($conn, "
    SELECT oi.*, p.product_name
    FROM order_items oi
    JOIN product p ON oi.product_id = p.product_id
    WHERE oi.order_id = '$order_id'
");

$total = 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Invoice</title>
</head>

<body style="font-family: Arial, sans-serif; background:#f4f4f4; padding:30px;">

<div style="max-width:900px; margin:auto; background:#fff; padding:30px; box-shadow:0 0 15px rgba(0,0,0,0.1);">

    <!-- HEADER -->
<!-- CENTERED HEADER BLOCK -->
<div style="text-align:center; border-bottom:3px solid #222; padding-bottom:20px; margin-bottom:25px;">

    <!-- This inner div keeps everything centered -->
    <div style="display:inline-flex; align-items:center; text-align:left;">

        <!-- LOGO -->
        <img src="../images/logo.jpeg" style="width:110px; margin-right:20px;">

        <!-- DETAILS BESIDE LOGO -->
        <div>
            <div style="font-size:30px; font-weight:bold; letter-spacing:2px;">
                AVANA BEADS AND BEOND
            </div>

            <div style="font-size:15px; margin-top:6px; font-weight:600;">
                Proprietor: Ms. Shanvi H
            </div>

            <div style="font-size:14px; margin-top:6px; color:#444; line-height:1.6;">
                CBD Cluster <br>
                International City, Dubai <br>
                Phone: +97 1529814374
            </div>
        </div>

    </div>

</div>

    <!-- INVOICE DETAILS -->
    <div style="display:flex; justify-content:space-between; margin-top:25px;">

        <div style="width:48%;">
            <p><b>Invoice No:</b> #<?php echo $order['order_id']; ?></p>
            <p><b>Order ID:</b> <?php echo $order['order_id']; ?></p>
        </div>

        <div style="width:48%; text-align:right;">
            <p><b>Order Date:</b> <?php echo $order['order_date']; ?></p>
            <p><b>Payment Method:</b> <?php echo $order['payment_method']; ?></p>
            <p><b>Payment Status:</b> <?php echo $order['payment_status']; ?></p>
        </div>

    </div>

    <!-- CUSTOMER DETAILS -->
    <div style="display:flex; justify-content:space-between; margin-top:25px; border-top:1px solid #ccc; padding-top:20px;">

        <div style="width:48%;">
            <h3 style="margin-bottom:5px;">Customer Details</h3>
            <p><b>Name:</b> <?php echo $order['cust_name']; ?></p>
            <p><b>Email:</b> <?php echo $order['email']; ?></p>
            <p><b>Phone:</b> <?php echo $order['contact']; ?></p>
        </div>

        <div style="width:48%; text-align:right;">
            <h3 style="margin-bottom:5px;">Shipping Address</h3>
            <p><?php echo $order['address']; ?></p>
        </div>

    </div>

    <!-- PRODUCTS TABLE -->
    <table style="width:100%; border-collapse:collapse; margin-top:30px;">

        <tr style="background:#f2f2f2;">
            <th style="padding:10px; border:1px solid #ddd;">Product</th>
            <th style="border:1px solid #ddd;">Price</th>
            <th style="border:1px solid #ddd;">Qty</th>
            <th style="border:1px solid #ddd;">Total</th>
        </tr>

        <?php while ($item = mysqli_fetch_assoc($items_query)) { 
            $sub_total = $item['price'] * $item['quantity'];
            $total += $sub_total;
        ?>

        <tr>
            <td style="padding:10px; border:1px solid #ddd;">
                <?php echo $item['product_name']; ?>
            </td>
            <td style="text-align:center; border:1px solid #ddd;">
                ₹<?php echo $item['price']; ?>
            </td>
            <td style="text-align:center; border:1px solid #ddd;">
                <?php echo $item['quantity']; ?>
            </td>
            <td style="text-align:center; border:1px solid #ddd;">
                ₹<?php echo $sub_total; ?>
            </td>
        </tr>

        <?php } ?>

    </table>

    <!-- GRAND TOTAL -->
    <div style="text-align:right; margin-top:20px; font-size:18px;">
        <b>Grand Total: ₹<?php echo $total; ?></b>
    </div>

    <!-- FOOTER -->
    <div style="text-align:center; margin-top:40px; border-top:1px solid #ccc; padding-top:15px; color:gray;">
        Thank you for shopping with AVANA BEADS ❤️ <br>
        Visit our website for more products!
    </div>

</div>

</body>
</html>