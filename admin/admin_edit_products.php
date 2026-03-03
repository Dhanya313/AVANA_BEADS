<?php
session_start();
include("../login/db_connect.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login/login.php");
    exit();
}

if(!isset($_GET['id'])){
    header("Location: admin_products.php");
    exit();
}

$id = intval($_GET['id']);

// Fetch product
$product_query = mysqli_query($conn, "SELECT * FROM product WHERE product_id='$id'");
$product = mysqli_fetch_assoc($product_query);

// Fetch inventory for stock
$inventory_query = mysqli_query($conn, "SELECT stock_quantity FROM inventory WHERE product_id='$id'");
$inventory = mysqli_fetch_assoc($inventory_query);
$current_stock = $inventory['stock_quantity'] ?? 0;

// Fetch categories
$category_query = mysqli_query($conn, "SELECT * FROM category");

// Handle update
if (isset($_POST['update_product'])) {
    $product_code = $_POST['product_code'];
    $product_name = $_POST['product_name'];
    $category_id = $_POST['category_id'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $stock_quantity = $_POST['stock_quantity'];

    if(empty($category_id)){
        echo "<script>alert('Please select category');</script>";
    } else {
        // Handle image upload
        $image_sql = "";
        if (!empty($_FILES['image']['name'])) {
            $image_name = $_FILES['image']['name'];
            $image_tmp = $_FILES['image']['tmp_name'];
            move_uploaded_file($image_tmp, "../uploads/" . $image_name);
            $image_sql = ", image='$image_name'";
        }

        // Update product
        mysqli_query($conn, "
            UPDATE product SET 
product_code='$product_code',
product_name='$product_name',
category_id='$category_id',
price='$price',
description='$description',
is_new='$is_new'
$image_sql
WHERE product_id='$id'
        ");

        // Update or insert stock
        $check_inventory = mysqli_query($conn, "SELECT * FROM inventory WHERE product_id='$id'");
        if(mysqli_num_rows($check_inventory) > 0){
            mysqli_query($conn, "
                UPDATE inventory
                SET stock_quantity='$stock_quantity',
                    last_updated=NOW()
                WHERE product_id='$id'
            ");
        } else {
            mysqli_query($conn, "
                INSERT INTO inventory (product_id, stock_quantity, last_updated)
                VALUES ('$id', '$stock_quantity', NOW())
            ");
        }
        $is_new = isset($_POST['is_new']) ? 1 : 0;
        echo "<script>alert('Product Updated Successfully'); window.location='admin_products.php';</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin:0;
            display:flex;
        }
        /* Sidebar */
        .sidebar {
            width: 200px;
            background: #222;
            color: white;
            min-height: 100vh;
            padding: 20px 0;
        }
        .sidebar a {
            display: block;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
        }
        .sidebar a:hover {
            background: #444;
        }

        /* Main content */
        .main {
            flex: 1;
            padding: 20px 40px;
        }
        h2 { margin-bottom: 20px; }

        form {
            max-width: 500px;
        }

        input[type="text"], input[type="number"], select, textarea {
            width: 100%;
            padding: 8px;
            margin: 8px 0;
            box-sizing: border-box;
            font-size: 14px;
        }
        textarea {
            height: 80px;
        }

        button {
            padding: 10px 20px;
            background-color: orange;
            color: white;
            border: none;
            cursor: pointer;
        }
        img { margin-top: 10px; }
    </style>
</head>
<body>

<?php include("sidebar.php"); ?>

<div class="main">
    <h2>Edit Product: <?php echo $product['product_name']; ?></h2>

    <form method="POST" enctype="multipart/form-data">
        <label>Product Code</label>
        <input type="text" name="product_code" value="<?php echo $product['product_code']; ?>" required>

        <label>Product Name</label>
        <input type="text" name="product_name" value="<?php echo $product['product_name']; ?>" required>

        <label>Category</label>
        <select name="category_id" required>
            <option value="">--Select Category--</option>
            <?php while($cat = mysqli_fetch_assoc($category_query)) { ?>
                <option value="<?php echo $cat['category_id']; ?>"
                    <?php if($cat['category_id'] == $product['category_id']) echo "selected"; ?>>
                    <?php echo $cat['category_code']." - ".$cat['category_name']; ?>
                </option>
            <?php } ?>
        </select>

        <label>Price (₹)</label>
        <input type="number" step="0.01" name="price" value="<?php echo $product['price']; ?>" required>

        <label>Stock Quantity</label>
        <input type="number" name="stock_quantity" value="<?php echo $current_stock; ?>" required>

        <label>Description</label>
        <textarea name="description"><?php echo $product['description']; ?></textarea>

        <label>
        <input type="checkbox" name="is_new" value="1" <?php if($product['is_new'] == 1) echo "checked"; ?>> Mark as NEW Product</label>

        <label>Current Image</label><br>
        <img src="../uploads/<?php echo $product['image']; ?>" width="120"><br><br>

        <label>Change Image</label>
        <input type="file" name="image">

        <button type="submit" name="update_product">Update Product</button>
    </form>
</div>

</body>
</html>