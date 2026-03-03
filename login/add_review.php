<?php
session_start();
include("db_connect.php");

if(!isset($_SESSION['cust_id'])){
    header("Location: login.php");
    exit();
}

if(!isset($_GET['order_id'])){
    header("Location: my_orders.php");
    exit();
}

$cust_id = $_SESSION['cust_id'];
$order_id = intval($_GET['order_id']);

// Verify order belongs to customer & completed
$stmt = $conn->prepare("SELECT * FROM orders 
                        WHERE order_id=? AND cust_id=? 
                        AND order_status='Completed' 
                        AND payment_status='Paid'");
$stmt->bind_param("ii", $order_id, $cust_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if(!$order){
    echo "Invalid order.";
    exit();
}

// When form submitted
if($_SERVER['REQUEST_METHOD'] == "POST"){
    $product_id = intval($_POST['product_id']);
    $rating = intval($_POST['rating']);
    $feedback_text = $_POST['feedback_text'];

    // Prevent duplicate review
    $check = $conn->prepare("SELECT * FROM product_feedback 
                             WHERE cust_id=? AND product_id=?");
    $check->bind_param("ii", $cust_id, $product_id);
    $check->execute();
    $exists = $check->get_result();

    if($exists->num_rows == 0){
        $insert = $conn->prepare("INSERT INTO product_feedback 
            (cust_id, product_id, rating, feedback_text, feedback_date) 
            VALUES (?, ?, ?, ?, NOW())");
        $insert->bind_param("iiis", $cust_id, $product_id, $rating, $feedback_text);
        $insert->execute();
    }

    header("Location: my_orders.php");
    exit();
}

// Fetch ordered products
$items = mysqli_query($conn, "
    SELECT oi.product_id, p.product_name 
    FROM order_items oi
    JOIN product p ON oi.product_id = p.product_id
    WHERE oi.order_id = $order_id
");
?>

<h2>Add Review</h2>

<form method="POST">
<?php while($item = mysqli_fetch_assoc($items)){ ?>
    <h3><?php echo $item['product_name']; ?></h3>
    <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">

    <label>Rating (1-5):</label>
    <select name="rating" required>
        <option value="">Select</option>
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
    </select>

    <br><br>

    <textarea name="feedback_text" placeholder="Write your review..." required></textarea>

    <br><br><hr><br>
<?php } ?>

<button type="submit">Submit Review</button>
</form>