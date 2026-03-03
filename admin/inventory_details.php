<?php
include("../login/db_connect.php");

// 1️⃣ Pagination settings
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// 2️⃣ Filters
$categoryFilter = isset($_GET['category_id']) ? $_GET['category_id'] : '';
$lowStockFilter = isset($_GET['low_stock']) ? true : false;

// 3️⃣ Fetch categories
$catSql = "SELECT * FROM category ORDER BY category_name ASC";
$categories = $conn->query($catSql);

// 4️⃣ Count total products
$totalSql = "SELECT COUNT(*) AS total FROM product p 
             LEFT JOIN inventory i ON p.product_id=i.product_id
             WHERE 1 ";
if($categoryFilter) $totalSql .= " AND p.category_id='$categoryFilter'";
if($lowStockFilter) $totalSql .= " AND i.stock_quantity < 5";
$totalResult = $conn->query($totalSql);
$totalRow = $totalResult->fetch_assoc();
$totalProducts = $totalRow['total'];
$totalPages = ceil($totalProducts / $limit);

// 5️⃣ Fetch products
$sql = "SELECT p.*, i.stock_quantity 
        FROM product p
        LEFT JOIN inventory i ON p.product_id=i.product_id
        WHERE 1 ";

if($categoryFilter) $sql .= " AND p.category_id='$categoryFilter'";
if($lowStockFilter) $sql .= " AND i.stock_quantity < 5";

$sql .= " ORDER BY p.product_name ASC 
          LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin - Inventory</title>
<style>
body { font-family: Arial,sans-serif; padding:20px; background:#f9f9f9; }
h1 { margin-bottom:20px; }
.filters { margin-bottom:15px; }
table { width:100%; border-collapse: collapse; background:white; }
th, td { padding:10px; border:1px solid #ddd; text-align:left; }
th { background:#f4f4f4; cursor:pointer; }
.low-stock { background:#fff3cd; } /* Orange for low stock */
.out-stock { background:#f8d7da; } /* Red for out of stock */
.sufficient-stock { background:#d4edda; } /* Green for sufficient stock */
.search-box { margin-bottom:10px; padding:6px; width:50%; }
button { padding:4px 8px; margin:2px; cursor:pointer; }
.pagination { margin-top:15px; }
.pagination a { margin:0 5px; padding:5px 10px; border:1px solid #ccc; text-decoration:none; }
.pagination a.active { background:#007bff; color:white; border-color:#007bff; }
</style>
</head>
<body>

<h1>Inventory Management</h1>

<form class="filters" method="GET">
    <label>Category:
        <select name="category_id">
            <option value="">All</option>
            <?php while($cat = $categories->fetch_assoc()): ?>
                <option value="<?php echo $cat['category_id']; ?>" <?php echo ($categoryFilter == $cat['category_id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($cat['category_name']); ?>
                </option>
            <?php endwhile; ?>
        </select>
    </label>

    <label>
        <input type="checkbox" name="low_stock" value="1" <?php if($lowStockFilter) echo 'checked'; ?>> Low Stock Only
    </label>

    <button type="submit">Filter</button>
</form>

<input type="text" id="searchInput" class="search-box" placeholder="Search by name, code, or category">

<div class="export-buttons">
    <form method="POST" action="export_inventory.php" style="display:inline;">
        <button type="submit" name="export_csv">Export CSV</button>
        <button type="submit" name="export_excel">Export Excel</button>
    </form>
</div>

<table id="inventoryTable">
    <thead>
        <tr>
            <th>Product ID</th>
            <th>Product Code</th>
            <th>Name</th>
            <th>Category</th>
            <th>Price (₹)</th>
            <th>Stock</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    <?php if($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <?php
                $stockClass = 'sufficient-stock';
                if($row['stock_quantity'] === null) $row['stock_quantity'] = 0;
                if($row['stock_quantity'] < 5 && $row['stock_quantity'] > 0) $stockClass = 'low-stock';
                if($row['stock_quantity'] <= 0) $stockClass = 'out-stock';
            ?>
            <tr class="<?php echo $stockClass; ?>">
                <td><?php echo $row['product_id']; ?></td>
                <td><?php echo htmlspecialchars($row['product_code']); ?></td>
                <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                <td><?php echo $row['category_id']; ?></td>
                <td>₹<?php echo number_format($row['price'],2); ?></td>
                <td>
                    <?php echo $row['stock_quantity']; ?>
                    <form method="POST" action="update_stock.php" style="display:inline;">
                        <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                        <button type="submit" name="adjust_stock" value="increase">+</button>
                        <button type="submit" name="adjust_stock" value="decrease">-</button>
                    </form>
                </td>
                <td><?php echo isset($row['is_active']) && $row['is_active'] ? 'Active' : 'Inactive'; ?></td>
                <td>
                    <a href="admin_edit_products.php?id=<?php echo $row['product_id']; ?>">Edit</a> |
                    <a href="admin_delete_product.php?id=<?php echo $row['product_id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="8">No products found.</td></tr>
    <?php endif; ?>
    </tbody>
</table>

<div class="pagination">
    <?php if($page > 1): ?><a href="?page=<?php echo $page-1; ?>">&laquo; Previous</a><?php endif; ?>
    <?php for($i=1;$i<=$totalPages;$i++): ?>
        <a href="?page=<?php echo $i; ?>" class="<?php echo $i==$page?'active':''; ?>"><?php echo $i; ?></a>
    <?php endfor; ?>
    <?php if($page < $totalPages): ?><a href="?page=<?php echo $page+1; ?>">Next &raquo;</a><?php endif; ?>
</div>

<script>
const searchInput = document.getElementById('searchInput');
const tableBody = document.getElementById('inventoryTable').getElementsByTagName('tbody')[0];

searchInput.addEventListener('keyup', function(){
    const filter = searchInput.value.toLowerCase();
    Array.from(tableBody.rows).forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(filter) ? '' : 'none';
    });
});
</script>

</body>
</html>