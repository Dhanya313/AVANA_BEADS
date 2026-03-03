<style>
/* MAIN LAYOUT */
.main-container {
    display: flex;
}

/* SIDEBAR */
.sidebar {
    width: 240px;
    background: linear-gradient(180deg, #f8c8dc, #f3a6c8);
    min-height: calc(107vh - 60px);
    padding-top: 20px;
    box-shadow: 4px 0 10px rgba(0,0,0,0.1);
}

.sidebar a {
    display: block;
    text-decoration: none;
    color: white;
    padding: 15px 25px;
    margin: 8px 15px;
    border-radius: 8px;
    transition: 0.3s ease;
    font-weight: 500;
}

.sidebar a:hover {
    background: rgba(255,255,255,0.3);
    transform: translateX(5px);
}

/* CONTENT AREA */
.content {
    flex: 1;
    padding: 30px;
    background: #f9f9f9;
    min-height: calc(100vh - 60px);
    box-sizing: border-box;
}
</style>

<div class="sidebar">
    <a href="dashboard.php">📊 Dashboard</a>
    <a href="admin_products.php">📦 Manage Products</a>
    <a href="admin_orders.php">🛒 View Orders</a>
    <a href="customer_details.php">👥 Customers</a>
    <a href="sales_report.php">💰 Sales Report</a>
    <a href="inventory_details.php">📋 Inventory</a>
    <a href="admin_feedback.php">💬 Feedback</a>
</div>