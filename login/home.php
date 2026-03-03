<?php
session_start();
include("customer_navbar.php");
?>
<?php
$conn = new mysqli("localhost", "root", "", "avana_beads");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$today = date("Y-m-d");

$sql = "SELECT * FROM offers 
        WHERE status='active' 
        AND start_date <= '$today' 
        AND end_date >= '$today'
        LIMIT 1";

$result = $conn->query($sql);
$offer = $result->fetch_assoc();

$cat_sql = "SELECT * FROM category ORDER BY category_name ASC";
$cat_result = $conn->query($cat_sql);
?>

<!DOCTYPE html>
<html>
<head>
<title>AVANA_BEADS | Customer Home</title>

<style>
body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background: #f8f8f8;
}

/* ================= HERO SLIDER ================= */

.slider {
    width: 100%;
    height: 450px;
    overflow: hidden;
    position: relative;
}

.slides {
    display: flex;
    width: 500%;
    animation: slide 15s infinite;
}

.slides img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

@keyframes slide {
    0% { margin-left: 0; }
    20% { margin-left: 0; }

    25% { margin-left: -100%; }
    40% { margin-left: -100%; }

    45% { margin-left: -200%; }
    60% { margin-left: -200%; }

    65% { margin-left: -300%; }
    80% { margin-left: -300%; }

    85% { margin-left: -400%; }
    100% { margin-left: -400%; }
}

/* ================= SECTION STYLING ================= */

.section {
    padding: 40px;
    text-align: center;
}

.section h2 {
    margin-bottom: 20px;
}

/* ================= CATEGORIES ================= */

.categories {
    display: flex;
    justify-content: center;
    gap: 20px;
    flex-wrap: wrap;
}

.category-card {
    background: white;
    padding: 20px;
    width: 200px;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transition: 0.3s;
}

.category-card:hover {
    transform: translateY(-8px);
}

/* ================= NEW PRODUCTS ================= */

.products {
    display: flex;
    justify-content: center;
    gap: 20px;
    flex-wrap: wrap;
}

.product-card {
    background: white;
    padding: 15px;
    width: 220px;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transition: 0.3s;
}

.product-card:hover {
    transform: scale(1.05);
}

/* ================= OFFER BANNER ================= */

.offer-banner {
    background: linear-gradient(45deg, #ff416c, #ff4b2b);
    color: white;
    padding: 2px;
    font-size: 22px;
    font-weight: bold;
    text-align: center;
    animation: floatText 3s infinite alternate;
}

@keyframes floatText {
    from { letter-spacing: 2px; }
    to { letter-spacing: 6px; }
}

/* ================= SOCIAL CONNECT ================= */

.social-connect {
    background: #ffffff;
    padding: 30px;
    text-align: center;
    margin-top: 30px;
}

.social-connect h2 {
    margin-bottom: 15px;
}

.social-icons {
    display: flex;
    justify-content: center;
    gap: 30px;
}

.social-icons img {
    width: 50px;
    height: 50px;
    transition: 0.3s ease;
    cursor: pointer;
}

.social-icons img:hover {
    transform: scale(1.2);
}


</style>
</head>

<body>

<!-- ================= SLIDER ================= -->

<div class="slider">
    <div class="slides">
        <img src="../images/bg/slide1.jpeg">
        <img src="../images/bg/slide2.jpeg">
        <img src="../images/bg/slide3.jpeg">
        <img src="../images/bg/slide4.jpeg">
        <img src="../images/bg/slide5.jpeg">
    </div>
</div>

<!-- ================= OFFERS ================= -->

<?php if ($offer) { ?>
<div class="offer-banner">
    <h2><?php echo $offer['title']; ?></h2>
    <p><?php echo $offer['description']; ?></p>
    <strong><?php echo $offer['discount_percent']; ?>% OFF</strong>
</div>
<?php } ?>

<!-- ================= CATEGORIES ================= -->

<div class="section">
    <h2>Browse Categories</h2>
    <div class="categories">

    <?php
    if($cat_result->num_rows > 0){
        while($cat = $cat_result->fetch_assoc()){
    ?>
        <a href="category.php?code=<?php echo $cat['category_code']; ?>" 
           style="text-decoration:none; color:inherit;">
           
            <div class="category-card">
                <?php echo $cat['category_name']; ?>
            </div>

        </a>
    <?php
        }
    } else {
        echo "No Categories Available";
    }
    ?>

    </div>
</div>

<!-- ================= NEW PRODUCTS ================= -->

<div class="section">
    <h2>Upcoming  Products</h2>
    <div class="products">
        <div class="product-card">
            <img src="../images/bg/prod1.jpeg" width="100%">
            <p>Infinity Tie</p>
        </div>

        <div class="product-card">
            <img src="../images/bg/prod2.jpeg" width="100%">
            <p>Rose Gold Bracelet</p>
        </div>

        <div class="product-card">
            <img src="../images/bg/slide2.jpeg" width="100%">
            <p>Brooches And Pins</p>
        </div>
    </div>
</div>

<!-- ================= CONNECT WITH US ================= -->

<div class="social-connect">
    <h2>Connect With Us</h2>

    <div class="social-icons">
        <a href="https://www.instagram.com/avana_beadsandbeyond" target="_blank">
            <img src="https://cdn-icons-png.flaticon.com/512/2111/2111463.png" alt="Instagram">
        </a>

        <a href="https://wa.me/971529814374" target="_blank">
            <img src="https://cdn-icons-png.flaticon.com/512/733/733585.png" alt="WhatsApp">
        </a>
    </div>
</div>

</body>
</html>