<?php
session_start();
include("db_connect.php");
include("customer_navbar.php");

if(!isset($_SESSION['cust_id'])){
    echo "Please login first.";
    exit();
}

$cust_id = $_SESSION['cust_id'];

/* GET CART ID */
$cart_query = mysqli_query($conn,
    "SELECT cart_id FROM cart WHERE cust_id = $cust_id");

if(mysqli_num_rows($cart_query) == 0){
    echo "Cart is empty.";
    exit();
}

$cart_row = mysqli_fetch_assoc($cart_query);
$cart_id = $cart_row['cart_id'];

/* REMOVE ITEM */
if(isset($_GET['remove'])){
    $product_id = $_GET['remove'];

    mysqli_query($conn,
        "DELETE FROM cart_items 
         WHERE cart_id = $cart_id 
         AND product_id = $product_id");
}

/* UPDATE QUANTITY */
if(isset($_POST['update'])){
    $product_id = $_POST['product_id'];
    $qty = $_POST['qty'];

    if($qty > 0){
        mysqli_query($conn,
            "UPDATE cart_items 
             SET quantity = $qty 
             WHERE cart_id = $cart_id 
             AND product_id = $product_id");
    }
}

echo "<h2>Your Cart</h2>";

/* FETCH CART ITEMS */
$item_query = mysqli_query($conn,
    "SELECT ci.*, p.product_name, p.price
     FROM cart_items ci
     JOIN product p ON ci.product_id = p.product_id
     WHERE ci.cart_id = $cart_id");

if(mysqli_num_rows($item_query) > 0){

    $total = 0;

    while($row = mysqli_fetch_assoc($item_query)){

        $product_id = $row['product_id'];
        $qty = $row['quantity'];
        $custom_text = $row['customization_text'];
        $price = $row['price'];
        $subtotal = $price * $qty;
        $total += $subtotal;

        echo "<div>";
        echo "<b>".$row['product_name']."</b><br>";
        echo "Price: ₹".$price."<br>";

        if(!empty($custom_text)){
            echo "<b>Customization:</b> ".$custom_text."<br>";
        }

        echo "Subtotal: ₹".$subtotal."<br>";

        /* UPDATE FORM */
        echo "<form method='POST'>";
        echo "<input type='hidden' name='product_id' value='".$product_id."'>";
        echo "Quantity: <input type='number' name='qty' value='".$qty."' min='1'>";
        echo "<button type='submit' name='update'>Update</button>";
        echo "</form>";

        /* REMOVE BUTTON */
        echo "<a href='cart.php?remove=".$product_id."'>";
        echo "<button>Remove</button>";
        echo "</a>";

        echo "<hr></div>";
    }

    echo "<h3>Total: ₹".$total."</h3>";
    echo "<a href='checkout.php'><button>Proceed to Checkout</button></a>";

} else {
    echo "Cart is empty.";
}
?>