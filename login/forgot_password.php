<?php
include("db_connect.php");

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    if (empty($email)) {
        $message = "Please enter your email.";
    } else {
        // Check admin
        $admin = mysqli_query($conn, "SELECT * FROM admin WHERE email='$email'");

        // Check customer
        $customer = mysqli_query($conn, "SELECT * FROM customer WHERE email='$email'");

        if (mysqli_num_rows($admin) == 1 || mysqli_num_rows($customer) == 1) {
            $message = "Email verified. You can reset your password.";
            // Later → redirect to reset_password.php
        } else {
            $message = "Email not registered. Please sign up.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Havana Beads | Forgot Password</title>
<style>
body {
    margin: 0;
    height: 100vh;
    background: url("../images/bg/bg1.jpeg") no-repeat center/cover;
    display: flex;
    justify-content: center;
    align-items: center;
    font-family: Arial, sans-serif;
}

.box {
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(15px);
    padding: 35px;
    border-radius: 15px;
    color: white;
    width: 300px;
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
    background: #00c6ff;
    border: none;
    font-weight: bold;
    cursor: pointer;
}

a {
    display: block;
    margin-top: 10px;
    text-align: center;
    color: white;
    text-decoration: none;
}

.msg {
    margin-top: 10px;
    text-align: center;
    color: #ffdddd;
}
</style>

<script>
function validateForm() {
    let email = document.forms["forgotForm"]["email"].value;
    if (email === "") {
        alert("Email is required");
        return false;
    }
    return true;
}
</script>
</head>

<body>

<div class="box">
    <h2>Forgot Password</h2>

    <form name="forgotForm" method="post" onsubmit="return validateForm();">
        <input type="email" name="email" placeholder="Enter your registered email">
        <button type="submit">Verify Email</button>
    </form>

    <a href="login.php">Back to Login</a>

    <?php if ($message != "") { ?>
        <div class="msg"><?php echo $message; ?></div>
    <?php } ?>
</div>

</body>
</html>