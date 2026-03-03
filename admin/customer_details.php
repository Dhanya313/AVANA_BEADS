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

// 3️⃣ Fetch customers for current page
$sql = "SELECT cust_id, cust_name AS custname, email,password, contact, address,
               (SELECT COUNT(*) FROM orders o WHERE o.cust_id = c.cust_id) AS total_orders
        FROM customer c
        ORDER BY cust_name ASC
        LIMIT $limit OFFSET $offset";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Customers</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
        th { background: #f4f4f4; cursor: pointer; }
        .actions button { margin-right: 5px; padding: 5px 10px; }
        .search-box { margin-bottom: 15px; width: 50%; padding: 8px; }
        .pagination { margin-top: 15px; }
        .pagination a { margin: 0 5px; padding: 5px 10px; border: 1px solid #ccc; text-decoration: none; }
        .pagination a.active { background: #007bff; color: white; border-color: #007bff; }
    </style>
</head>
<body>
    <h1>Customers</h1>

    <!-- Search box -->
    <input type="text" id="searchInput" class="search-box" placeholder="Search by name, email, or phone">

    <!-- Customers table -->
    <table id="customersTable">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Password</th>
                <th>Total Orders</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['custname']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['contact']); ?></td>
                    <td><?php echo htmlspecialchars($row['address']); ?></td>
                    <td><?php echo htmlspecialchars($row['password']); ?></td>
                    <td><?php echo $row['total_orders']; ?></td>
                    <td class="actions">
                        <button onclick="window.location.href='customer_orders.php?cust_id=<?php echo $row['cust_id']; ?>'">View Orders</button>
                        <button onclick="window.location.href='edit_customer.php?id=<?php echo $row['cust_id']; ?>'">Edit</button>
                        <button onclick="if(confirm('Are you sure?')) window.location.href='delete_customer.php?id=<?php echo $row['cust_id']; ?>'">Delete</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="7">No customers found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    <!-- Pagination links -->
    <div class="pagination">
        <?php if($page > 1): ?>
            <a href="?page=<?php echo $page - 1; ?>">&laquo; Previous</a>
        <?php endif; ?>

        <?php for($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?php echo $i; ?>" class="<?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>

        <?php if($page < $totalPages): ?>
            <a href="?page=<?php echo $page + 1; ?>">Next &raquo;</a>
        <?php endif; ?>
    </div>

    <!-- Search functionality -->
    <script>
        const searchInput = document.getElementById('searchInput');
        const table = document.getElementById('customersTable').getElementsByTagName('tbody')[0];

        searchInput.addEventListener('keyup', function() {
            const filter = searchInput.value.toLowerCase();
            Array.from(table.rows).forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    </script>
</body>
</html>