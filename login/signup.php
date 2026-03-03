<?php
include("db_connect.php");

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cust_name = trim($_POST['cust_name']);
    $email     = trim($_POST['email']);
    $password  = trim($_POST['password']);
    $contact   = trim($_POST['contact']);
    $address   = trim($_POST['address']);

    if (empty($cust_name) || empty($email) || empty($password) || empty($contact) || empty($address)) {
        $message = "All fields are required.";
    } else {
        // Check if email already exists
        $check = mysqli_query($conn, "SELECT * FROM customer WHERE email='$email'");
        if (mysqli_num_rows($check) > 0) {
            $message = "Email already registered. Please login.";
        } else {
            // Insert customer
            $insert = "INSERT INTO customer (cust_name, email, password, contact, address)
                       VALUES ('$cust_name', '$email', '$password', '$contact', '$address')";
            if (mysqli_query($conn, $insert)) {
                $message = "Registration successful. Please login.";
            } else {
                $message = "Registration failed. Try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title> avana Beads | Sign Up</title>
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
    width: 320px;
}

input, textarea {
    width: 100%;
    padding: 10px;
    margin: 8px 0;
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
    color: white;
    text-align: center;
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
    let email = document.forms["signupForm"]["email"].value;
    let password = document.forms["signupForm"]["password"].value;
    let contact = document.forms["signupForm"]["contact"].value;

    let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email)) {
        alert("Enter a valid email");
        return false;
    }

    if (password.length < 6) {
        alert("Password must be at least 6 characters");
        return false;
    }

    if (contact.length < 10) {
        alert("Enter valid contact number");
        return false;
    }

    return true;
}
</script>
</head>

<body>

<div class="box">
    <h2>Customer Registration</h2>

    <form name="signupForm" method="post" onsubmit="return validateForm();">
        <input type="text" name="cust_name" placeholder="Full Name">
        <input type="email" name="email" placeholder="Email">
        <input type="password" name="password" placeholder="Password">
        <input type="text" name="contact" placeholder="Contact Number">
        <textarea name="address" placeholder="Address"></textarea>

        <button type="submit">Register</button>
    </form>

    <a href="login.php"><b>Already have an account? Login</b></a>

    <?php if ($message != "") { ?>
        <div class="msg"><?php echo $message; ?></div>
    <?php } ?>
</div>

</body>
</html>