<?php
include("db_connect.php");
?>
<?php
$count = 0;

if(isset($_SESSION['cust_id'])){
    $cust_id = $_SESSION['cust_id'];

    $cart_query = mysqli_query($conn,
        "SELECT SUM(quantity) AS total_items
         FROM cart_items ci
         JOIN cart c ON ci.cart_id = c.cart_id
         WHERE c.cust_id = $cust_id");

    $cart_data = mysqli_fetch_assoc($cart_query);
    $count = $cart_data['total_items'] ?? 0;
} 
?>

<style>
.navbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 30px;
    background: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(10px);
    color: white;
    position:relative;
    z-index: 1000;
}

.navbar .logo {
    font-size: 22px;
    font-weight: bold;
}

.navbar ul {
    list-style: none;
    display: flex;
    gap: 20px;
    margin: 0;
    padding: 0;
}

.navbar ul li a {
    color: white;
    text-decoration: none;
    font-weight: 500;
}

.navbar ul li a:hover {
    text-decoration: underline;
}

.account {
    position: relative;
    display: inline-block;
}
.account-btn {
    cursor: pointer;
    padding:5px 10px;
}
.account-content {
    display: none;
    position: absolute;
    top:100%;
    right: 0;
    background: rgba(0,0,0,0.85);
    min-width: 160px;
    border-radius: 5px;
    z-index: 2000;
}

.account-content a {
    display: block;
    padding: 10px;
    color: white;
    text-decoration: none;
}

.account-content a:hover {
    background: rgba(255,255,255,0.1);
}

.logout-link {
    display: block;
    width: 100%;
    padding: 10px;
    background: none;
    border: none;
    color: white;
    text-align: left;
    font-size: inherit;
    font-family: inherit;
    cursor: pointer;
}

.logout-link:hover {
    background: rgba(255,255,255,0.1);
}

</style>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const btn = document.querySelector(".account-btn");
    const menu = document.querySelector(".account-content");

    btn.addEventListener("click", function(e) {
        e.stopPropagation();
        menu.style.display = (menu.style.display === "block") ? "none" : "block";
    });

    // Close dropdown when clicking outside
    document.addEventListener("click", function() {
        menu.style.display = "none";
    });
});
</script>
<div class="navbar">
    <div class="logo">AVANA_BEADS</div>

    <ul>
        <li><a href="home.php">Home</a></li>
        <li><a href="about_us.php">About Us</a></li>
        <li><a href="cart.php">Cart(<?php echo $count; ?>)</a></li>
    </ul>

    <div class="account">
        <div class="account-btn">My Account ▾</div>
        <div class="account-content">
            <a href="profile.php">Profile</a>
            <a href="my_orders.php">Orders</a>

            <!-- Secure Logout -->
            <form action="log_out_cust.php" method="POST" style="margin:0;">
                <button type="submit" class="logout-link"
                    onclick="return confirm('Are you sure you want to logout?');">
                    Logout
                </button>
            </form>

        </div>
    </div>
</div>