<?php
session_start();
include("../login/db_connect.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login/login.php");
    exit();
}

// Fetch categories
$category_query = mysqli_query($conn, "SELECT * FROM category ORDER BY category_name ASC");

$category_query = mysqli_query($conn, "SELECT * FROM category ORDER BY category_name ASC");
// Insert new category
if (isset($_POST['add_category'])) {

    $category_code = $_POST['category_code'];
    $category_name = $_POST['category_name'];

    if(!empty($category_code) && !empty($category_name)){

        mysqli_query($conn, "
            INSERT INTO category (category_code, category_name)
            VALUES ('$category_code', '$category_name')
        ");

        echo "<script>alert('Category Added Successfully'); window.location='admin_add_products.php';</script>";
        exit();
    }
}


// Insert product
if (isset($_POST['add_product'])) {

    $product_code = $_POST['product_code'];
    $product_name = $_POST['product_name'];
    $category_id = $_POST['category_id'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    if(empty($category_id)){
        echo "<script>alert('Please select category');</script>";
    } else {

        $image_name = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];
        move_uploaded_file($image_tmp, "../uploads/" . $image_name);

        $is_new = isset($_POST['is_new']) ? 1 : 0;

$insert = mysqli_query($conn, "
    INSERT INTO product 
    (product_code, product_name, category_id, price, description, image, is_new)
    VALUES 
    ('$product_code', '$product_name', '$category_id', '$price', '$description', '$image_name', '$is_new')
");
        if ($insert) {
            echo "<script>alert('Product Added Successfully'); window.location='admin_products.php';</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Add Product</title>
<style>
body { font-family: Arial; margin:0; display:flex; }
.main { flex:1; padding:20px; }
input, select, textarea { width:100%; padding:8px; margin:10px 0; }
button { padding:10px 15px; background:green; color:white; border:none; }
</style>
</head>


<script>
function toggleCategory(){
    var form = document.getElementById("categoryForm");
    if(form.style.display === "none"){
        form.style.display = "block";
    } else {
        form.style.display = "none";
    }
}
</script>

<body>

<?php include("sidebar.php"); ?>

<div class="main">
<div style="display:flex; justify-content:space-between; align-items:center;">
    <h2>Add Product</h2>
    <button type="button" onclick="toggleCategory()" 
    style="background:#007bff;">+ Create New Category</button>
</div>

<!-- Hidden Category Form -->
<div id="categoryForm" style="display:none; border:1px solid #ccc; padding:15px; margin:15px 0;">
    <h3>Create New Category</h3>
    <form method="POST">
        Category Code:
        <input type="text" name="category_code" required>

        Category Name:
        <input type="text" name="category_name" required>

        <button type="submit" name="add_category">Add Category</button>
    </form>
</div>

<form method="POST" enctype="multipart/form-data">
    Product Code:
    <input type="text" name="product_code" required>

    Product Name:
    <input type="text" name="product_name" required>

    Category:
    <select name="category_id" required>
        <option value="">Select Category</option>
        <?php while($cat = mysqli_fetch_assoc($category_query)) { ?>
            <option value="<?php echo $cat['category_id']; ?>">
                <?php echo $cat['category_code']." - ".$cat['category_name']; ?>
            </option>
        <?php } ?>
    </select>

    Product Image:
    <input type="file" name="image" required>

    <label>
    <input type="checkbox" name="is_new" value="1">Mark as NEW Product</label>

    Price:
    <input type="number" step="0.01" name="price" required>

    Description:
    <textarea name="description"></textarea>

    <button type="submit" name="add_product">Add Product</button>
</form>

</div>
</body>
</html>