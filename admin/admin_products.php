<?php
session_start();
include("../login/db_connect.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login/login.php");
    exit();
}

$selected_category = "";

$filter = "";
if(isset($_GET['category_id']) && $_GET['category_id'] != ""){
    $selected_category = intval($_GET['category_id']);
    $filter = "WHERE p.category_id = '$selected_category'";
}

$query = mysqli_query($conn, "
SELECT p.*, c.category_name,
(SELECT COUNT(*) FROM product_feedback pf WHERE pf.product_id = p.product_id) as total_reviews
FROM product p
LEFT JOIN category c ON p.category_id = c.category_id
$filter
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Products</title>
<style>
body { font-family: Arial; margin:0; display:flex; }
.sidebar { width:220px; background:#222; height:100vh; padding:20px; }
.sidebar a { display:block; color:white; text-decoration:none; margin:15px 0; }
.main { flex:1; padding:20px; }
table { width:100%; border-collapse:collapse; }
table, th, td { border:1px solid #ddd; }
th, td { padding:10px; text-align:center; }
.btn { padding:5px 10px; text-decoration:none; }
.add-btn { background:green; color:white; }
.edit-btn { background:orange; color:white; }
.delete-btn { background:red; color:white; }
</style>
</head>

<body>

<?php include("sidebar.php"); ?>

<div class="main">
<h2>Manage Categories & Products</h2>
<a href="admin_add_products.php" class="btn add-btn">+ Add Product</a>
<br><br>

<form method="GET">
<select name="category_id" onchange="this.form.submit()">
<option value="">All Categories</option>

<?php
$cats = mysqli_query($conn, "SELECT * FROM category");
while($c = mysqli_fetch_assoc($cats)){
?>
<option value="<?php echo $c['category_id']; ?>"
<?php if($selected_category == $c['category_id']) echo "selected"; ?>>
<?php echo $c['category_name']; ?>
</option>
<?php } ?>
</select>
</form>
<br>

<table>
<tr>
    <th>ID</th>
    <th>Code</th>
    <th>Name</th>
    <th>Category</th>
    <th>Price</th>
    <th>Action</th>
    <th>Total Reviews</th>
    <th>New</th>
</tr>

<?php while($row = mysqli_fetch_assoc($query)) { ?>
<tr>
    <td><?php echo $row['product_id']; ?></td>
    <td><?php echo $row['product_code']; ?></td>
    <td><?php echo $row['product_name']; ?></td>
    <td><?php echo $row['category_name']; ?></td>
    <td>₹<?php echo $row['price']; ?></td>
    <td><?php echo $row['total_reviews']; ?></td>
    <td><?php echo $row['is_new'] ? "Yes" : "No"; ?></td>
    <td>
        <a href="admin_edit_products.php?id=<?php echo $row['product_id']; ?>" class="btn edit-btn">Edit</a>
        <a href="admin_delete_products.php?id=<?php echo $row['product_id']; ?>" 
        onclick="return confirm('Are you sure?');">Delete</a>
    </td>
</tr>
<?php } ?>

</table>

</div>
</body>
</html>