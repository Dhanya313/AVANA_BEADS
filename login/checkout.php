<?php
session_start();
include("../login/db_connect.php");

if (!isset($_SESSION['cust_id'])) {
    die("Please login first.");
}

$cust_id = $_SESSION['cust_id'];

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
        $stmt->bind_param(
            "iiids",
            $order_id,
            $item['product_id'],
            $item['quantity'],
            $item['price'],
            $item['customization_text']
        );
        $stmt->execute();
        $stmt->close();

        // Reduce inventory
        $stmt = $conn->prepare("
            UPDATE inventory 
            SET stock_quantity = stock_quantity - ?
            WHERE product_id = ?
        ");
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

    if(!$order_id){
        die("Order failed. Please try again."); 
    }
} catch (Exception $e) {

    $conn->rollback();
    echo "Order Failed: " . $e->getMessage();
}
?>