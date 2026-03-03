<?php
session_start();
include("../login/db_connect.php");

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = "All fields are required.";
    } else {

        // Check user from customer table
        $query = "SELECT * FROM customer 
                  WHERE email='$email' AND password='$password'";

        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) == 1) {

            $row = mysqli_fetch_assoc($result);

            // 🔴 If Admin
            if ($row['role'] == 'admin') {

                $_SESSION['admin_id'] = $row['cust_id'];
                $_SESSION['admin_email'] = $row['email'];

                header("Location: ../admin/dashboard.php");
                exit;
            }

            // 🟢 If Customer
            else {

                $_SESSION['cust_id'] = $row['cust_id'];
                $_SESSION['cust_name'] = $row['cust_name'];
                $_SESSION['cust_email'] = $row['email'];

                header("Location: home.php");
                exit;
            }
        } 
        else {
            $error = "Invalid credentials. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Avana Beads | Login</title>
<style>
body {
    margin: 0;
    height: 100vh;
    background: url("../images/bg/bg3.jpeg") no-repeat center/cover;
    display: flex;
    justify-content: center;
    align-items: center;
    font-family: Arial, sans-serif;
}

.container {
    display: flex;
    gap: 30px;
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(15px);
    padding: 40px;
    border-radius: 15px;
    color: white;
}

.logo img {
    width: 140px;
}

form {
    width: 280px;
}

input {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border-radius: 5px;
    border: none;
}

button {
    width: 100%;
    padding: 10px;
    border: none;
    background: #00c6ff;
    font-weight: bold;
    cursor: pointer;
}

a {
    color: white;
    display: block;
    margin-top: 8px;
    text-align: center;
    text-decoration: none;
}

.error {
    color: #ffcccc;
    margin-top: 10px;
    text-align: center;
}
</style>

<script>
function validateForm() {
    let email = document.forms["loginForm"]["email"].value;
    let password = document.forms["loginForm"]["password"].value;

    if (email === "" || password === "") {
        alert("All fields are required");
        return false;
    }

    let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email)) {
        alert("Enter a valid email address");
        return false;
    }

    return true;
}
</script>
</head>

<body>

<div class="container">
    <div class="logo">
        <img src="../images/logo.jpeg" alt="Havana Beads">
    </div>

    <form name="loginForm" method="post" onsubmit="return validateForm();">
        <h2>Login</h2>

        <input type="email" name="email" placeholder="Email">
        <input type="password" name="password" placeholder="Password">

        <button type="submit">Login</button>

        <a href="forgot_password.php">Forgot Password?</a>
        <a href="signup.php">New user? Register</a>

        <?php if ($error != "") { ?>
            <div class="error"><?php echo $error; ?></div>
        <?php } ?>
    </form>
</div>

</body>
</html>