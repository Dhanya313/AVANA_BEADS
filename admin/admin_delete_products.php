<?php
session_start();
include("../login/db_connect.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login/login.php");
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: admin_products.php");
    exit();
}

$id = intval($_GET['id']);

$product_query = mysqli_query($conn, "SELECT image FROM product WHERE product_id='$id'");

if(mysqli_num_rows($product_query) == 0){
    header("Location: admin_products.php");
    exit();
}

$product = mysqli_fetch_assoc($product_query);

if (!empty($product['image']) && file_exists("../uploads/" . $product['image'])) {
    unlink("../uploads/" . $product['image']);
}

mysqli_query($conn, "DELETE FROM product WHERE product_id='$id'");

header("Location: admin_products.php");
exit();
?>