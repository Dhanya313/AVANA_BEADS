<?php
session_start();
include("db_connect.php");
include("customer_navbar.php");

if (!isset($_SESSION['cust_id'])) {
    header("Location: ../login/login.php");
    exit();
}

$cust_id = $_SESSION['cust_id'];
$query = mysqli_query($conn, "SELECT * FROM customer WHERE cust_id='$cust_id'");
$customer = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html>
<head>
<title>My Profile | AVANA</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

body{
    height:100vh;
    background: linear-gradient(135deg,#fff6f9,#f3f0ff);
    display:flex;
    justify-content:center;
    align-items:center;
}

/* Decorative soft circles */
body::before, body::after{
    content:"";
    position:absolute;
    border-radius:50%;
    opacity:0.15;
}

body::before{
    width:300px;
    height:300px;
    background:#b76e79;
    top:-80px;
    left:-80px;
}

body::after{
    width:350px;
    height:350px;
    background:#8e44ad;
    bottom:-100px;
    right:-100px;
}

/* Profile Card */
.profile-card{
    width:600px;
    background:white;
    padding:50px;
    border-radius:30px;
    box-shadow:0 25px 50px rgba(0,0,0,0.1);
    position:relative;
    z-index:2;
}

.profile-title{
    text-align:center;
    font-size:28px;
    font-weight:600;
    color:#b76e79;
    margin-bottom:35px;
}

.info-row{
    display:flex;
    justify-content:space-between;
    padding:15px 0;
    border-bottom:1px solid #eee;
}

.label{
    font-weight:600;
    color:#8e44ad;
}

.value{
    color:#444;
}

.button-group{
    text-align:center;
    margin-top:35px;
}

.btn{
    padding:12px 28px;
    border:none;
    border-radius:30px;
    background:linear-gradient(45deg,#b76e79,#8e44ad);
    color:white;
    font-size:14px;
    cursor:pointer;
    transition:0.3s ease;
}

.btn:hover{
    transform:scale(1.05);
}
</style>
</head>

<body>

<div class="profile-card">
    <div class="profile-title">✨ My AVANA Profile ✨</div>

    <div class="info-row">
        <div class="label">Full Name</div>
        <div class="value"><?php echo $customer['cust_name']; ?></div>
    </div>

    <div class="info-row">
        <div class="label">Email</div>
        <div class="value"><?php echo $customer['email']; ?></div>
    </div>

    <div class="info-row">
        <div class="label">Phone</div>
        <div class="value"><?php echo $customer['contact']; ?></div>
    </div>

    <div class="info-row">
        <div class="label">Address</div>
        <div class="value"><?php echo $customer['address']; ?></div>
    </div>

    

    <div class="button-group">
        <button class="btn">Edit Profile (Coming Soon)</button>
    </div>
</div>

</body>
</html>