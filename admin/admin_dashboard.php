<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
</head>
<body>

<h2>Welcome Admin</h2>

<p>You are successfully logged in.</p>

<a href="../login/logout.php">Logout</a>

</body>
</html>