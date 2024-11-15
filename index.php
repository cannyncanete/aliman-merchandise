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
        $products = "SELECT * FROM products WHERE quantity > 0";
        $result = mysqli_query($db, $products);

        while ($row = mysqli_fetch_assoc($result)) {
            $product_id = $row['id'];
            $product_brand = $row['brand'];
            $product_name = $row['name'];
            $product_price = $row['price'];
            ?>
            <div class="grid-box">
                <div class="img-container">
                    <a href="#">
                        <!-- <img src="#" class="product-img" alt=""> -->
                    </a>
                </div>

                <div class="product-info padding-1">
                    <p class="product-brand margin-right-auto"><?php echo $product_brand ?></p>
                    <h3 class="product-name">
                        <a href="product-page.php?id=<?php echo $product_id; ?>">
                            <?php echo $product_name; ?>
                        </a>
                    </h3>
                    <p class="bold"><?php echo '₱' . number_format($product_price, 2); ?></p>
                </div>
            </div>
            <?php
        }
        ?>
    </div><br>

    <div class="container">
        <button onclick="window.location.href='server.php?logout=1'" class="logout-btn">Log out</button>
    </div>

    <!-- <div class="container">
        <p>@2022 Aliman Merchandise: Ordering and Inventory Management System</p>
    </div> -->

    <!-- SCRIPT -->
</body>

</html>