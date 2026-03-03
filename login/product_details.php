<?php
session_start();
include("db_connect.php");
include("customer_navbar.php");

if(!isset($_GET['id'])){
    header("Location: home.php");
    exit();
}

$product_id = intval($_GET['id']);

$product_query = mysqli_query($conn, "
    SELECT p.*, i.stock_quantity 
    FROM product p
    LEFT JOIN inventory i ON p.product_id = i.product_id
    WHERE p.product_id = $product_id
");

$product = mysqli_fetch_assoc($product_query);

if(!$product){
    echo "Product not found.";
    exit();
}
?>

<style>
.product-container{
    display: flex;
    justify-content: space-between;
    padding: 40px;
    gap: 40px;
    width: 100%;
}

.left-section{
    width: 60%;
}

.right-section{
    width: 35%;
    background: #f9f9f9;
    padding: 25px;
    border-radius: 10px;
}

.product-image{
    width: 100%;
    height: 400px;
    object-fit: cover;
    border-radius: 10px;
}

.product-info{
    margin-top: 25px;
}

.price{
    font-size: 22px;
    font-weight: bold;
    color: #b76e79;
}

.stock{
    margin-top: 5px;
    font-weight: 500;
    color: green;
}

.custom-box{
    margin-top: 20px;
}

textarea{
    width: 100%;
    padding: 10px;
    border-radius: 5px;
}

input[type="file"]{
    margin-top: 10px;
}

.add-btn{
    margin-top: 20px;
    padding: 12px 25px;
    background: #b76e79;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.add-btn:hover{
    background: #a05560;
}
</style>

<div class="product-container">

    <!-- LEFT SIDE -->
    <div class="left-section">

        <img src="../images/<?php echo $product['image']; ?>" class="product-image">

        <div class="product-info">
            <h2><?php echo $product['product_name']; ?></h2>

            <p class="price">₹ <?php echo $product['price']; ?></p>

            <p class="stock">
                Available Stock: 
                <?php echo $product['stock_quantity'] ?? 0; ?>
            </p>

            <p><?php echo $product['description']; ?></p>

            <!-- Add To Cart Form -->
            <form action="add_to_cart.php" method="POST" enctype="multipart/form-data">

                <input type="hidden" name="product_id" 
                       value="<?php echo $product['product_id']; ?>">

                <div class="custom-box">
                    <label><strong>Customization (Optional):</strong></label>
                    <textarea name="custom_text" 
                              placeholder="Enter your customization details here..."></textarea>

                    <label><strong>Upload Reference Image (Optional):</strong></label>
                    <input type="file" name="custom_image">
                </div>

                <button type="submit" class="add-btn">
                    Add to Cart
                </button>

            </form>

        </div>
    </div>

    <!-- RIGHT SIDE (Ratings Section) -->
    <div class="right-section">
        <h3>Ratings & Reviews</h3>
        <hr>

        <?php
        $rating_query = mysqli_query($conn, "
    SELECT f.*, c.cust_name
    FROM product_feedback f
    JOIN customer c ON f.cust_id = c.cust_id
    WHERE f.product_id = $product_id
");

if(mysqli_num_rows($rating_query) > 0){
    while($rating = mysqli_fetch_assoc($rating_query)){
        echo "<p><strong>".$rating['cust_name']."</strong></p>";
        echo "<p>⭐ ".$rating['rating']."</p>";
        echo "<p>".$rating['feedback_text']."</p>";
        echo "<small>".$rating['feedback_date']."</small>";
        echo "<hr>";
    }
}else{
    echo "<p>No ratings yet.</p>";
}
        ?>
    </div>

</div>