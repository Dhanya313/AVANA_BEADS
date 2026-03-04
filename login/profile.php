<?php  
session_start();  
include("db_connect.php");  

if (!isset($_SESSION['cust_id'])) {  
    header("Location: ../login/login.php");  
    exit();  
}  

$cust_id = $_SESSION['cust_id'];  
$query = mysqli_query($conn, "SELECT * FROM customer WHERE cust_id='$cust_id'");  
$customer = mysqli_fetch_assoc($query);  

// Count items in cart for navbar
$count = 0;
$cart_query = mysqli_query($conn,
    "SELECT SUM(quantity) AS total_items
     FROM cart_items ci
     JOIN cart c ON ci.cart_id = c.cart_id
     WHERE c.cust_id = $cust_id");
$cart_data = mysqli_fetch_assoc($cart_query);
$count = $cart_data['total_items'] ?? 0;

$message = "";

// Handle Profile Updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['update_name'])) {
        $new_name = mysqli_real_escape_string($conn, $_POST['cust_name']);
        mysqli_query($conn, "UPDATE customer SET cust_name='$new_name' WHERE cust_id='$cust_id'");
        $message = "Name updated successfully!";
    }

    if (isset($_POST['update_email'])) {
        $new_email = mysqli_real_escape_string($conn, $_POST['email']);
        mysqli_query($conn, "UPDATE customer SET email='$new_email' WHERE cust_id='$cust_id'");
        $message = "Email updated successfully!";
    }

    if (isset($_POST['update_phone'])) {
        $new_phone = mysqli_real_escape_string($conn, $_POST['contact']);
        mysqli_query($conn, "UPDATE customer SET contact='$new_phone' WHERE cust_id='$cust_id'");
        $message = "Phone updated successfully!";
    }

    if (isset($_POST['update_address'])) {
        $new_address = mysqli_real_escape_string($conn, $_POST['address']);
        mysqli_query($conn, "UPDATE customer SET address='$new_address' WHERE cust_id='$cust_id'");
        $message = "Address updated successfully!";
    }

    if (isset($_POST['update_password'])) {
        $current = $_POST['current_password'];
        $new = $_POST['new_password'];
        $confirm = $_POST['confirm_password'];

        if (password_verify($current, $customer['password'])) {
            if ($new === $confirm) {
                $hash = password_hash($new, PASSWORD_DEFAULT);
                mysqli_query($conn, "UPDATE customer SET password='$hash' WHERE cust_id='$cust_id'");
                $message = "Password changed successfully!";
            } else {
                $message = "New password and confirm password do not match!";
            }
        } else {
            $message = "Current password is incorrect!";
        }
    }

    // Refresh customer data
    $query = mysqli_query($conn, "SELECT * FROM customer WHERE cust_id='$cust_id'");  
    $customer = mysqli_fetch_assoc($query);  
}
?>  

<!DOCTYPE html>
<html>
<head>
<title>My Profile | AVANA</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
<style>
/* Navbar */
.navbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 30px;
    background: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(10px);
    color: white;
    position: fixed;
    top:0;
    width:100%;
    z-index: 1000;
}

.navbar .logo { font-size: 22px; font-weight: bold;}
.navbar ul { list-style: none; display: flex; gap: 20px; margin: 0; padding: 0;}
.navbar ul li a { color: white; text-decoration: none; font-weight: 500;}
.navbar ul li a:hover { text-decoration: underline;}
.account { position: relative; display: inline-block;}
.account-btn { cursor: pointer; padding:5px 10px;}
.account-content { display: none; position: absolute; top:100%; right: 0; background: rgba(0,0,0,0.85); min-width: 160px; border-radius: 5px; z-index: 2000;}
.account-content a { display: block; padding: 10px; color: white; text-decoration: none;}
.account-content a:hover { background: rgba(255,255,255,0.1);}
.logout-link { display: block; width: 100%; padding: 10px; background: none; border: none; color: white; text-align: left; font-size: inherit; font-family: inherit; cursor: pointer;}
.logout-link:hover { background: rgba(255,255,255,0.1);}

/* Body & background */
body{
    font-family:'Poppins',sans-serif;
    background: linear-gradient(135deg,#fff6f9,#f3f0ff);
    padding-top:80px; /* space for navbar */
    display:flex;
    justify-content:center;
    min-height:100vh;
    position:relative;
}

/* Floating background blobs */
body::before, body::after {
    content:"";
    position:absolute;
    border-radius:50%;
    opacity:0.15;
    z-index:0;
}
body::before{
    width:350px;
    height:350px;
    background: radial-gradient(circle at top left, #ff8c94, #ffaaa5);
    top:-100px;
    left:-100px;
}
body::after{
    width:400px;
    height:400px;
    background: radial-gradient(circle at bottom right, #6a4c93, #8e44ad);
    bottom:-120px;
    right:-120px;
}

/* Profile card */
.profile-card {
    position: relative;
    background: #fff;
    padding: 50px 40px;
    border-radius: 25px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    z-index: 2;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    width: 600px;
}
.profile-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 25px 50px rgba(0,0,0,0.15);
}

/* Profile icon placeholder */
.profile-icon {
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg, #ff8c94, #ffaaa5);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
    font-weight: 700;
    color: white;
    margin: 0 auto 20px auto;
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}

/* Profile title */
.profile-title{ text-align:center; font-size:28px; font-weight:600; color:#b76e79; margin-bottom:20px;}

/* Info rows */
.info-row {
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:15px 0;
    border-bottom:1px solid #eee;
    transition: background 0.3s ease;
}
.info-row:hover {
    background: #f7f2ff;
    border-radius: 15px;
    padding-left: 10px;
}
.label{ font-weight:600; color:#8e44ad;}
.value{ color:#444; margin-right: 10px;}

/* Inputs and textareas */
input, textarea {
    padding:10px;
    border-radius:12px;
    border:1px solid #ccc;
    font-size:14px;
    width:60%;
    transition: all 0.3s ease;
}
input:focus, textarea:focus {
    border: 2px solid #8e44ad;
    outline: none;
    box-shadow: 0 0 10px rgba(142,68,173,0.2);
}

/* Buttons */
.btn {
    padding:10px 20px;
    border-radius: 30px;
    border:none;
    background: linear-gradient(135deg, #ff8c94, #8e44ad);
    color:white;
    font-weight:600;
    cursor:pointer;
    transition: all 0.3s ease;
}
.btn:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
}

/* Password section */
.password-section {
    margin-top:30px;
    border-top:1px solid #eee;
    padding-top:20px;
}

/* Success message */
.message{ text-align:center; margin-bottom:15px; color:green; font-weight:600;}
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const btn = document.querySelector(".account-btn");
    const menu = document.querySelector(".account-content");

    btn.addEventListener("click", function(e) {
        e.stopPropagation();
        menu.style.display = (menu.style.display === "block") ? "none" : "block";
    });

    document.addEventListener("click", function() {
        menu.style.display = "none";
    });
});

function toggleEdit(id){
    var display = document.getElementById('display_'+id);
    var edit = document.getElementById('edit_'+id);
    if(display.style.display === 'none'){
        display.style.display = 'flex';
        edit.style.display = 'none';
    } else {
        display.style.display = 'none';
        edit.style.display = 'flex';
    }
}
</script>
</head>

<body>
<!-- Navbar -->
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
            <form action="log_out_cust.php" method="POST" style="margin:0;">
                <button type="submit" class="logout-link"
                    onclick="return confirm('Are you sure you want to logout?');">
                    Logout
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Profile Card -->
<div class="profile-card">

<div class="profile-icon">
    <?php echo strtoupper(substr($customer['cust_name'],0,1)); ?>
</div>

<div class="profile-title">✨ My AVANA Profile ✨</div>  

<?php if($message != "") echo "<div class='message'>$message</div>"; ?>  

<!-- Full Name -->
<div class="info-row" id="display_name">
    <div class="label">Full Name</div>
    <div class="value"><?php echo $customer['cust_name']; ?></div>
    <button class="btn" onclick="toggleEdit('name')">Edit</button>
</div>
<div class="info-row" id="edit_name" style="display:none;">
    <form method="POST" style="display:flex; width:100%; justify-content:space-between; align-items:center;">
        <input type="text" name="cust_name" value="<?php echo $customer['cust_name']; ?>" required>
        <button class="btn" name="update_name">Save</button>
        <button type="button" class="btn" onclick="toggleEdit('name')">Cancel</button>
    </form>
</div>

<!-- Email -->
<div class="info-row" id="display_email">
    <div class="label">Email</div>
    <div class="value"><?php echo $customer['email']; ?></div>
    <button class="btn" onclick="toggleEdit('email')">Edit</button>
</div>
<div class="info-row" id="edit_email" style="display:none;">
    <form method="POST" style="display:flex; width:100%; justify-content:space-between; align-items:center;">
        <input type="email" name="email" value="<?php echo $customer['email']; ?>" required>
        <button class="btn" name="update_email">Save</button>
        <button type="button" class="btn" onclick="toggleEdit('email')">Cancel</button>
    </form>
</div>

<!-- Phone -->
<div class="info-row" id="display_phone">
    <div class="label">Phone</div>
    <div class="value"><?php echo $customer['contact']; ?></div>
    <button class="btn" onclick="toggleEdit('phone')">Edit</button>
</div>
<div class="info-row" id="edit_phone" style="display:none;">
    <form method="POST" style="display:flex; width:100%; justify-content:space-between; align-items:center;">
        <input type="tel" name="contact" value="<?php echo $customer['contact']; ?>" required>
        <button class="btn" name="update_phone">Save</button>
        <button type="button" class="btn" onclick="toggleEdit('phone')">Cancel</button>
    </form>
</div>

<!-- Address -->
<div class="info-row" id="display_address">
    <div class="label">Address</div>
    <div class="value"><?php echo $customer['address']; ?></div>
    <button class="btn" onclick="toggleEdit('address')">Edit</button>
</div>
<div class="info-row" id="edit_address" style="display:none;">
    <form method="POST" style="display:flex; width:100%; justify-content:space-between; align-items:center;">
        <textarea name="address" required><?php echo $customer['address']; ?></textarea>
        <button class="btn" name="update_address">Save</button>
        <button type="button" class="btn" onclick="toggleEdit('address')">Cancel</button>
    </form>
</div>

<!-- Password Change -->
<div class="password-section">
    <div class="label" style="text-align:center; margin-bottom:10px;">Change Password</div>
    <form method="POST" style="display:flex; flex-direction:column; gap:10px;">
        <input type="password" name="current_password" placeholder="Current Password" required>
        <input type="password" name="new_password" placeholder="New Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
        <div class="button-group">
            <button class="btn" name="update_password">Update Password</button>
        </div>
    </form>
</div>

</div> <!-- profile-card -->
</body>
</html>