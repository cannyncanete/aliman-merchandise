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

    <?php
    $product_id = mysqli_real_escape_string($db, $_GET['id']);
    $query = "SELECT * FROM products WHERE id = '$product_id'";
    $result = mysqli_query($db, $query);

    $product = mysqli_fetch_assoc($result);
    ?>
    <div id="main" class="container grid-2-1">
        <!-- Product Image -->
        <img src="<?php echo $product['image_path']; ?>" alt="<?php echo $product['name']; ?>" class="product-img">

        <!-- Product Info -->
        <div>
            <h3 class="margin-bottom"><?php echo $product['brand'] ?></h3>
            <h1 class="margin-bottom"><?php echo $product['name'] ?></h1>
            <h3 class="margin-bottom product-price"><?php echo  '₱' . number_format($product['price'], 2); ?></h3>

            <p class="margin-bottom product-description"><?php echo $product['description'] ?></p><br><br>

            <form action="" method="post">
                <div class="flex">

                    <input type="number" class="product-quantity-input" name="product_quantity" min="1" value="1">
                    <input type="hidden" name="product_id" value="<?php echo $product_id ?>">
                    <input type="hidden" name="user_id" value="<?php echo $user_id ?>">

                    <button class="add-to-cart-btn flex" type="submit" name="add_to_cart">
                        <i class="bi bi-cart-plus-fill"></i>
                        <span>Add to cart</span>
                    </button>
                </div>
            </form>

        </div>
    </div><br>

    <!-- <div class="container">
        <p>@2022 Aliman Merchandise: Ordering and Inventory Management System</p>
    </div> -->

    <!-- SCRIPT -->
</body>

</html>