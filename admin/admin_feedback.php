<?php
session_start();
include("../login/db_connect.php");

if(!isset($_SESSION['admin_id'])){
    header("Location: ../login/login.php");
    exit();
}

$feedbacks = mysqli_query($conn, "
    SELECT pf.*, p.product_name, c.cust_name
    FROM product_feedback pf
    JOIN product p ON pf.product_id = p.product_id
    JOIN customer c ON pf.cust_id = c.cust_id
    ORDER BY pf.feedback_date DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Feedback</title>
<style>
body { font-family: Arial; margin:0; display:flex; }
.main { flex:1; padding:20px; }
table { width:100%; border-collapse:collapse; }
th, td { border:1px solid #ddd; padding:10px; text-align:center; }
th { background:#333; color:white; }
.rating { color:orange; font-weight:bold; }
</style>
</head>
<body>

<?php include("sidebar.php"); ?>

<div class="main">
<h2>Product Feedback & Ratings</h2>

<table>
<tr>
    <th>ID</th>
    <th>Customer</th>
    <th>Product</th>
    <th>Rating</th>
    <th>Feedback</th>
    <th>Date</th>
</tr>

<?php while($row = mysqli_fetch_assoc($feedbacks)){ ?>
<tr>
    <td><?php echo $row['feedback_id']; ?></td>
    <td><?php echo $row['cust_name']; ?></td>
    <td><?php echo $row['product_name']; ?></td>
    <td class="rating"><?php for($i=1;$i<=5;$i++){ echo $i <= $row['rating'] ? "⭐" : "☆"; } ?> </td>
    <td><?php echo $row['feedback_text']; ?></td>
    <td><?php echo date("d-m-Y", strtotime($row['feedback_date'])); ?></td>
</tr>
<?php } ?>

</table>

</div>
</body>
</html>