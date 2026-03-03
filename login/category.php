<?php
include("db_connect.php");
include("customer_navbar.php");


if(isset($_GET['code'])) {

    $code = $_GET['code'];

    // Get category details
    $cat_query = "SELECT * FROM category WHERE category_code='$code'";
    $cat_result = mysqli_query($conn, $cat_query);

    if(mysqli_num_rows($cat_result) > 0) {

        $cat_row = mysqli_fetch_assoc($cat_result);
        $category_id = $cat_row['category_id'];
        $category_name = $cat_row['category_name'];

        echo "<h2>$category_name</h2>";
        echo"<div class='product-container'>";

        // Get products under that category
        $prod_query = "SELECT * FROM product WHERE category_id=$category_id";
        $prod_result = mysqli_query($conn, $prod_query);

        if(mysqli_num_rows($prod_result) > 0) {

            while($row = mysqli_fetch_assoc($prod_result)) {
                
                echo "<div>";
                echo "<img src='../uploads/".$row['image']."' width='150'><br>";
                echo "<b>".$row['product_name']."</b><br>";
                echo $row['description']."<br><br>";
                echo "<a href='product_details.php?id=".$row['product_id']."'>";
                echo "<button>View</button>";
                echo "</a><br><br>";
                echo "</div>";
            }
            echo"</div>";

        } else {
            echo "No products available.";
        }

    } else {
        echo "Invalid Category.";
    }

} else {
    echo "Category not selected.";
}
?>

<style>
.product-container {
    display: flex;
    flex-wrap: wrap;
    gap: 30px;
}

.product-container > div {
    width: 45%;
    background: #ffffff;
    padding: 15px;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.product-container img {
    width: 100%;
    max-width: 150px;
}
</style>