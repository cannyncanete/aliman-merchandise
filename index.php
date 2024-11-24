<?php
include("server.php");

if (empty($_SESSION['logged_in'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

?>

<html>

<head>
    <title>Aliman Merchandise</title>

    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="css/style.css">

    <!-- Boostrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
    <?php include 'top-bar.php'; ?>

    <div id="main" class="container grid">
        <?php
        // Base query to display products
        $products = "SELECT products.id AS product_id,  products.brand AS product_brand, products.name AS product_name, 
        products.price AS product_price, products.image_path AS image_path, product_categories.category_name AS category_name
        FROM products
        JOIN product_categories ON products.category = product_categories.id
        WHERE quantity > 0"; // Default condition

        // Check if the user clicked a category
        if (isset($_GET['category_id'])) {
            $category_id = mysqli_real_escape_string($db, $_GET['category_id']);
            $products .= " AND products.category = '$category_id'";
        }

        // Check if the search form was submitted
        if (isset($_POST['search_product'])) {
            $search_input = mysqli_real_escape_string($db, $_POST['search_product']);
            $products .= " AND (
                        LOWER(products.brand) LIKE LOWER('%$search_input%') OR 
                        LOWER(products.name) LIKE LOWER('%$search_input%') OR 
                        LOWER(product_categories.category_name) LIKE LOWER('%$search_input%')
                    )";
        }

        // Execute the query
        $result = mysqli_query($db, $products);

        // Display products if any are returned
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $product_id = $row['product_id'];
                $product_brand = $row['product_brand'];
                $product_name = $row['product_name'];
                $product_price = $row['product_price'];
                $image_path = $row['image_path'];
        ?>
                <div class="grid-box">
                    <div class="img-container">
                        <a href="product-page.php?id=<?php echo $product_id; ?>">
                            <img src="<?php echo $image_path; ?>" alt="<?php echo $product_name; ?>" class="product-img">
                        </a>
                    </div>

                    <div class="product-info padding-1">
                        <p class="product-brand margin-right-auto"><?php echo $product_brand; ?></p>
                        <h3 class="product-name" style="margin-bottom: 0.25rem;">
                            <a href="product-page.php?id=<?php echo $product_id; ?>">
                                <?php echo $product_name; ?>
                            </a>
                        </h3>
                        <p><?php echo '₱' . number_format($product_price, 2); ?></p>
                    </div>
                </div>
        <?php
            }
        } else {
            echo "<p>No products found.</p>";
        }
        ?>
    </div><br>


    <!-- <div class="container">
        <p>@2022 Aliman Merchandise: Ordering and Inventory Management System</p>
    </div> -->

    <!-- SCRIPT -->
</body>

</html>