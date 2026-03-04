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
             LEFT JOIN category c ON p.category_id=c.category_id
             WHERE 1 ";
if($categoryFilter) $totalSql .= " AND p.category_id='$categoryFilter'";
if($lowStockFilter) $totalSql .= " AND i.stock_quantity < 5";
$totalResult = $conn->query($totalSql);
$totalRow = $totalResult->fetch_assoc();
$totalProducts = $totalRow['total'];
$totalPages = ceil($totalProducts / $limit);

// 5️⃣ Fetch products with category names
$sql = "SELECT p.*, i.stock_quantity, c.category_name 
        FROM product p
        LEFT JOIN inventory i ON p.product_id=i.product_id
        LEFT JOIN category c ON p.category_id=c.category_id
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
body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f6f9; padding:20px; }
h1 { margin-bottom:20px; color:#333; }
.filters { margin-bottom:15px; }
.filters select, .filters input[type="checkbox"] { padding:5px 8px; margin-right:10px; }
.filters button { padding:6px 12px; background:#007bff; color:white; border:none; border-radius:4px; cursor:pointer; }
.filters button:hover { background:#0056b3; }

table { width:100%; border-collapse: collapse; background:white; box-shadow:0 2px 8px rgba(0,0,0,0.1); border-radius:8px; overflow:hidden; }
th, td { padding:12px 10px; border-bottom:1px solid #ddd; text-align:left; font-size:14px; }
th { background:#007bff; color:white; cursor:default; }
tr:hover { background:#f1f7ff; }

.low-stock { background:#fff3cd; } /* Orange */
.out-stock { background:#f8d7da; } /* Red */
.sufficient-stock { background:#d4edda; } /* Green */

.search-box { margin-bottom:15px; padding:8px; width:50%; border-radius:4px; border:1px solid #ccc; }

.pagination { margin-top:15px; text-align:center; }
.pagination a { margin:0 5px; padding:6px 12px; border:1px solid #007bff; text-decoration:none; color:#007bff; border-radius:4px; }
.pagination a.active { background:#007bff; color:white; }
.pagination a:hover { background:#0056b3; color:white; }

.export-buttons { margin-bottom:10px; }
.export-buttons button { padding:6px 12px; margin-right:5px; background:#28a745; color:white; border:none; border-radius:4px; cursor:pointer; }
.export-buttons button:hover { background:#1e7e34; }

.status-badge { padding:4px 8px; border-radius:4px; font-weight:bold; color:white; display:inline-block; font-size:12px; }
.status-sufficient { background:#28a745; }
.status-low { background:#ffc107; color:#212529; }
.status-out { background:#dc3545; }

.action-links a { margin-right:5px; color:#007bff; text-decoration:none; }
.action-links a:hover { text-decoration:underline; }
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
                $stockQty = $row['stock_quantity'] ?? 0;
                $stockClass = 'sufficient-stock';
                $statusBadge = '<span class="status-badge status-sufficient">Sufficient</span>';

                if($stockQty <= 0){
                    $stockClass = 'out-stock';
                    $statusBadge = '<span class="status-badge status-out">Out of Stock</span>';
                } elseif($stockQty < 5){
                    $stockClass = 'low-stock';
                    $statusBadge = '<span class="status-badge status-low">Low Stock</span>';
                }
            ?>
            <tr class="<?php echo $stockClass; ?>">
                <td><?php echo $row['product_id']; ?></td>
                <td><?php echo htmlspecialchars($row['product_code']); ?></td>
                <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                <td>₹<?php echo number_format($row['price'],2); ?></td>
                <td><?php echo $stockQty; ?></td>
                <td><?php echo $statusBadge; ?></td>
                <td class="action-links">
                    <a href="admin_edit_products.php?id=<?php echo $row['product_id']; ?>">Edit</a>
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