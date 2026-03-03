<?php
session_start();
include("db_connect.php");

if(!isset($_SESSION['cust_id'])){
    echo "Please login first.";
    exit();
}

if(!isset($_POST['product_id'])){
    header("Location: home.php");
    exit();
}

$cust_id = $_SESSION['cust_id'];
$product_id = intval($_POST['product_id']);
$custom_text = mysqli_real_escape_string($conn, $_POST['custom_text'] ?? "");

// -------------------- IMAGE UPLOAD --------------------
$custom_image_name = "";

if(isset($_FILES['custom_image']) && $_FILES['custom_image']['error'] == 0){

    $target_dir = "uploads/customizations/";
    
    if(!is_dir($target_dir)){
        mkdir($target_dir, 0777, true);
    }

    $file_name = time() . "_" . basename($_FILES["custom_image"]["name"]);
    $target_file = $target_dir . $file_name;

    if(move_uploaded_file($_FILES["custom_image"]["tmp_name"], $target_file)){
        $custom_image_name = $file_name;
    }
}
// ------------------------------------------------------

// Check if cart exists
$cart_result = mysqli_query($conn, "SELECT cart_id FROM cart WHERE cust_id=$cust_id");

if(mysqli_num_rows($cart_result) > 0){
    $cart_id = mysqli_fetch_assoc($cart_result)['cart_id'];
}else{
    mysqli_query($conn, "INSERT INTO cart (cust_id) VALUES ($cust_id)");
    $cart_id = mysqli_insert_id($conn);
}

// Check available stock
$stock_result = mysqli_query($conn, "SELECT stock_quantity FROM inventory WHERE product_id=$product_id");
$available_stock = mysqli_fetch_assoc($stock_result)['stock_quantity'] ?? 0;

// Check current quantity in cart
$cart_item_result = mysqli_query($conn, 
    "SELECT quantity FROM cart_items 
     WHERE cart_id=$cart_id AND product_id=$product_id");

$current_in_cart = mysqli_num_rows($cart_item_result) 
    ? mysqli_fetch_assoc($cart_item_result)['quantity'] 
    : 0;

if($current_in_cart + 1 > $available_stock){
    echo "<script>alert('Insufficient stock available'); 
    window.location='product_details.php?id=$product_id';</script>";
    exit();
}

// Add or update cart item
if($current_in_cart > 0){
    mysqli_query($conn, 
        "UPDATE cart_items 
         SET quantity=quantity+1 
         WHERE cart_id=$cart_id AND product_id=$product_id");
}else{
    mysqli_query($conn, 
        "INSERT INTO cart_items 
        (cart_id, product_id, quantity, customization_text, customization_image) 
        VALUES 
        ($cart_id, $product_id, 1, '$custom_text', '$custom_image_name')");
}

header("Location: cart.php");
exit();
?>