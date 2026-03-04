<?php
include("../login/db_connect.php");

// 1️⃣ Pagination settings
$limit = 5; // customers per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// 2️⃣ Count total customers
$totalSql = "SELECT COUNT(*) AS total FROM customer";
$totalResult = $conn->query($totalSql);
$totalRow = $totalResult->fetch_assoc();
$totalCustomers = $totalRow['total'];
$totalPages = ceil($totalCustomers / $limit);

// 3️⃣ Fetch customers for current page along with last order and total spent
$sql = "SELECT c.cust_id, c.cust_name, c.email, c.contact, c.address, c.password,
               IFNULL((SELECT COUNT(*) FROM orders o WHERE o.cust_id = c.cust_id),0) AS total_orders,
               IFNULL((SELECT MAX(order_date) FROM orders o WHERE o.cust_id = c.cust_id),'N/A') AS last_order,
               IFNULL((SELECT SUM(total_amount) FROM orders o WHERE o.cust_id = c.cust_id),0) AS total_spent
        FROM customer c
        ORDER BY c.cust_name ASC
        LIMIT $limit OFFSET $offset";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin - Customers</title>
<style>
body { font-family: Arial,sans-serif; margin:20px; background:#f9f9f9; color:#333; }
h1 { text-align:center; margin-bottom:30px; }
.search-box { margin-bottom:15px; padding:8px; width:50%; border-radius:5px; border:1px solid #ccc; }
.export-button { float:right; margin-bottom:15px; padding:6px 12px; background:#28a745; color:white; border:none; border-radius:5px; cursor:pointer; }
.export-button:hover { background:#218838; }
table { width:100%; border-collapse: collapse; background:white; border-radius:10px; overflow:hidden; box-shadow:0 0 10px rgba(0,0,0,0.1); }
th, td { padding:12px; border-bottom:1px solid #ddd; text-align:left; font-size:14px; }
th { background:#007bff; color:white; font-weight:600; }
tr:hover { background:#f1f1f1; }
.status-badge { padding:4px 8px; border-radius:5px; font-size:12px; color:white; display:inline-block; }
.new { background:#17a2b8; }
.inactive { background:#6c757d; }
.vip { background:#ffc107; color:#212529; }
.actions button { margin-right:5px; padding:5px 10px; border:none; border-radius:5px; cursor:pointer; color:white; }
.view-btn { background:#007bff; }
.view-btn:hover { background:#0056b3; }
.edit-btn { background:#28a745; }
.edit-btn:hover { background:#218838; }
.delete-btn { background:#dc3545; }
.delete-btn:hover { background:#c82333; }
.pagination { margin-top:20px; text-align:center; }
.pagination a { margin:0 5px; padding:5px 10px; border:1px solid #ccc; text-decoration:none; color:#333; border-radius:5px; }
.pagination a.active { background:#007bff; color:white; border-color:#007bff; }
.bulk-actions { margin-bottom:15px; }
.bulk-actions button { padding:5px 12px; margin-right:5px; border:none; border-radius:5px; cursor:pointer; color:white; }
.bulk-delete { background:#dc3545; }
.bulk-delete:hover { background:#c82333; }
.bulk-email { background:#17a2b8; }
.bulk-email:hover { background:#117a8b; }
</style>
</head>
<body>

<h1>Customer Management</h1>

<!-- Search & Export -->
<input type="text" id="searchInput" class="search-box" placeholder="Search by name, email, or phone">
<form method="POST" action="export_customers.php" style="display:inline;">
    <button type="submit" class="export-button">Export CSV</button>
</form>

<!-- Bulk Actions -->
<div class="bulk-actions">
    <button class="bulk-delete" onclick="bulkAction('delete')">Delete Selected</button>
    <button class="bulk-email" onclick="bulkAction('email')">Send Email</button>
</div>

<!-- Customers table -->
<table id="customersTable">
    <thead>
        <tr>
            <th><input type="checkbox" id="selectAll"></th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Address</th>
            <th>Total Orders</th>
            <th>Last Order</th>
            <th>Total Spent (₹)</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php if($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <?php
                $badges = '';
                $daysSince = $row['last_order'] !== 'N/A' ? (time() - strtotime($row['last_order']))/(60*60*24) : null;
                if($daysSince !== null && $daysSince <= 7) $badges .= '<span class="status-badge new">New</span> ';
                if($row['total_orders'] >= 5) $badges .= '<span class="status-badge vip">VIP</span> ';
                if($row['total_orders'] == 0) $badges .= '<span class="status-badge inactive">Inactive</span>';
            ?>
            <tr>
                <td><input type="checkbox" class="selectCustomer" value="<?php echo $row['cust_id']; ?>"></td>
                <td><?php echo htmlspecialchars($row['cust_name']); ?> <?php echo $badges; ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo htmlspecialchars($row['contact']); ?></td>
                <td><?php echo htmlspecialchars($row['address']); ?></td>
                <td><?php echo $row['total_orders']; ?></td>
                <td><?php echo $row['last_order']; ?></td>
                <td>₹<?php echo number_format($row['total_spent'],2); ?></td>
                <td class="actions">
                    <button class="view-btn" onclick="window.location.href='customer_orders.php?cust_id=<?php echo $row['cust_id']; ?>'">View Orders</button>
                    <button class="edit-btn" onclick="window.location.href='edit_customer.php?id=<?php echo $row['cust_id']; ?>'">Edit</button>
                    <button class="delete-btn" onclick="if(confirm('Are you sure?')) window.location.href='delete_customer.php?id=<?php echo $row['cust_id']; ?>'">Delete</button>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="9">No customers found.</td></tr>
    <?php endif; ?>
    </tbody>
</table>

<!-- Pagination -->
<div class="pagination">
    <?php if($page > 1): ?><a href="?page=<?php echo $page-1; ?>">&laquo; Previous</a><?php endif; ?>
    <?php for($i=1;$i<=$totalPages;$i++): ?>
        <a href="?page=<?php echo $i; ?>" class="<?php echo $i==$page?'active':''; ?>"><?php echo $i; ?></a>
    <?php endfor; ?>
    <?php if($page < $totalPages): ?><a href="?page=<?php echo $page+1; ?>">Next &raquo;</a><?php endif; ?>
</div>

<script>
// Search functionality
const searchInput = document.getElementById('searchInput');
const tableBody = document.getElementById('customersTable').getElementsByTagName('tbody')[0];

searchInput.addEventListener('keyup', function(){
    const filter = searchInput.value.toLowerCase();
    Array.from(tableBody.rows).forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(filter) ? '' : 'none';
    });
});

// Select all checkbox
document.getElementById('selectAll').addEventListener('change', function(){
    const checked = this.checked;
    document.querySelectorAll('.selectCustomer').forEach(cb => cb.checked = checked);
});

// Bulk actions
function bulkAction(action){
    const selected = Array.from(document.querySelectorAll('.selectCustomer:checked')).map(cb => cb.value);
    if(selected.length === 0){ alert('Select at least one customer'); return; }

    if(action === 'delete'){
        if(confirm('Delete selected customers?')){
            window.location.href = 'bulk_delete_customers.php?ids=' + selected.join(',');
        }
    } else if(action === 'email'){
        window.location.href = 'bulk_email_customers.php?ids=' + selected.join(',');
    }
}
</script>

</body>
</html>