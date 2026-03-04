<?php
session_start();
include("../login/db_connect.php");

if (!isset($_SESSION['cust_id'])) {
    die("Please login first.");
}

$cust_id = $_SESSION['cust_id'];

// Optional: Pre-order confirmation can go here (if needed)

// --- Pre-order confirmation ---
if (!isset($_POST['confirm_order'])) {

    // Fetch current customer info
    $cust_query = mysqli_query($conn, "SELECT cust_name, contact, address FROM customer WHERE cust_id='$cust_id'");
    $customer = mysqli_fetch_assoc($cust_query);

    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Confirm Address & Contact</title>
        <style>
            body { font-family: Arial,sans-serif; background:#f3f0ff; padding:50px; }
            .confirm-card { background:#fff; padding:30px; border-radius:20px; max-width:500px; margin:auto; box-shadow:0 15px 40px rgba(0,0,0,0.1);}
            h2 { text-align:center; color:#8e44ad; margin-bottom:20px; }
            label { display:block; margin:10px 0 5px; font-weight:600; }
            input, textarea { width:100%; padding:10px; border-radius:8px; border:1px solid #ccc; }
            .btn { margin-top:20px; padding:12px 20px; background:linear-gradient(45deg,#ff8c94,#8e44ad); border:none; color:white; font-weight:600; cursor:pointer; border-radius:30px; width:100%; font-size:16px; }
            .btn:hover { opacity:0.9; }
        </style>
    </head>
    <body>
        <div class="confirm-card">
            <h2>Confirm Your Details</h2>
            <form method="POST">
                <label>Name</label>
                <input type="text" name="cust_name" value="<?php echo htmlspecialchars($customer['cust_name']); ?>" required>

                <label>Contact</label>
                <input type="text" name="contact" value="<?php echo htmlspecialchars($customer['contact']); ?>" required>

                <label>Address</label>
                <textarea name="address" rows="3" required><?php echo htmlspecialchars($customer['address']); ?></textarea>

                <input type="hidden" name="confirm_order" value="1">
                <button type="submit" class="btn">Confirm & Place Order</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit(); // stop execution until user confirms
}

// --- Use confirmed values from form ---
$cust_name = $_POST['cust_name'];
$contact = $_POST['contact'];
$address = $_POST['address'];

// Begin transaction and try-catch
$conn->begin_transaction();
try {

    // Get cart_id
    $stmt = $conn->prepare("SELECT cart_id FROM cart WHERE cust_id = ?");
    $stmt->bind_param("i", $cust_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 0) {
        throw new Exception("Cart not found.");
    }
    $cart = $result->fetch_assoc();
    $cart_id = $cart['cart_id'];
    $stmt->close();

    // Get cart items
    $stmt = $conn->prepare("
        SELECT ci.product_id, ci.quantity, ci.customization_text, p.price
        FROM cart_items ci
        JOIN product p ON ci.product_id = p.product_id
        WHERE ci.cart_id = ?
    ");
    $stmt->bind_param("i", $cart_id);
    $stmt->execute();
    $items = $stmt->get_result();
    if ($items->num_rows == 0) {
        throw new Exception("Cart is empty.");
    }

    $total_amount = 0;
    $cart_products = [];
    while ($row = $items->fetch_assoc()) {
        $total_amount += ($row['price'] * $row['quantity']);
        $cart_products[] = $row;
    }
    $stmt->close();

    // Insert order
    $stmt = $conn->prepare("
        INSERT INTO orders 
        (cust_id, order_date, order_status, payment_method, payment_status, total_amount)
        VALUES (?, CURDATE(), 'Pending', 'Cash on Delivery', 'Pending', ?)
    ");
    $stmt->bind_param("id", $cust_id, $total_amount);
    $stmt->execute();
    $order_id = $stmt->insert_id;
    $stmt->close();

    // Insert order_items + reduce stock
    foreach ($cart_products as $item) {
        $stmt = $conn->prepare("
            INSERT INTO order_items 
            (order_id, product_id, quantity, price, customization_text)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("iiids", $order_id, $item['product_id'], $item['quantity'], $item['price'], $item['customization_text']);
        $stmt->execute();
        $stmt->close();

        // Reduce inventory
        $stmt = $conn->prepare("UPDATE inventory SET stock_quantity = stock_quantity - ? WHERE product_id = ?");
        $stmt->bind_param("ii", $item['quantity'], $item['product_id']);
        $stmt->execute();
        $stmt->close();
    }

    // Insert billing
    $stmt = $conn->prepare("
        INSERT INTO billing 
        (order_id, bill_date, total_price, payment_status, payment_method)
        VALUES (?, CURDATE(), ?, 'Pending', 'Cash on Delivery')
    ");
    $stmt->bind_param("id", $order_id, $total_amount);
    $stmt->execute();
    $stmt->close();

    // Insert tracking
    $stmt = $conn->prepare("
        INSERT INTO tracking 
        (order_id, delivery_status, expected_delivery_date)
        VALUES (?, 'Order Placed', DATE_ADD(CURDATE(), INTERVAL 5 DAY))
    ");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $stmt->close();

    // Clear cart
    $stmt = $conn->prepare("DELETE FROM cart_items WHERE cart_id = ?");
    $stmt->bind_param("i", $cart_id);
    $stmt->execute();
    $stmt->close();

    $conn->commit();

    header("Location: invoice.php?order_id=" . $order_id);
    exit();

} catch (Exception $e) {
    $conn->rollback();
    echo "Order Failed: " . $e->getMessage();
}
?>