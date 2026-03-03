<?php
if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login/login.php");
    exit();
}
?>

<style>
body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
}

/* TOP NAVBAR */
.navbar {
    width: 100%;
    height: 60px;
    background: #d63384;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 25px;
    box-sizing: border-box;
}

.navbar h2 {
    margin: 0;
    font-size: 20px;
}

.navbar a {
    color: white;
    text-decoration: none;
    font-weight: 500;
}
</style>

<div class="navbar">
    <h2>AVANA Admin Panel</h2>
    <div>
        <?php echo $_SESSION['admin_email']; ?> |
        <a href="../login/logout.php">Logout</a>
    </div>
</div>