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

    <div class="container">
        <h2 class="margin-bottom"><i class="bi bi-cart-fill"></i> Cart</h2>

        <?php
        // Query to count the number of items in the cart for the logged-in user
        $user_id = $_SESSION['user_id']; // Assuming user ID is stored in session
        $query = "SELECT COUNT(*) AS item_count FROM carts WHERE user_id = '$user_id'";
        $result = mysqli_query($db, $query);
        $row = mysqli_fetch_assoc($result);
        $item_count = $row['item_count'];
        ?>

        <p class="margin-bottom bold">You have <?php echo $item_count; ?> product<?php echo $item_count != 1 ? 's' : ''; ?> in your cart</p>
        <p>Your items aren't reserved yet—complete your purchase at checkout to order them.</p>

        <br><br>
        <div class="grid-2-1">
            <!-- Cart Items -->
            <div>
                <div class="flex align-items-center margin-bottom" style="gap: 5rem">
                    <p class="margin-right-auto">Product</p>
                    <p>Price</p>
                    <p>Quantity</p>
                    <p>Total</p>
                </div>

                <?php
                $cart = "SELECT carts.id AS cart_id, carts.quantity AS cart_quantity, carts.total_price AS cart_total_price, 
                    products.brand AS product_brand, products.name AS product_name, products.price AS product_price 
                FROM carts 
                JOIN products ON carts.product_id = products.id 
                WHERE carts.user_id = '$user_id'";

                $result = mysqli_query($db, $cart);

                $subtotal = 0;

                while ($row = mysqli_fetch_assoc($result)) {
                    $cart_id = $row['cart_id']; // Cart ID
                    $product_name = $row['product_name']; // Product name from products table
                    $product_price = $row['product_price']; // Product price from products table
                    $product_quantity = $row['cart_quantity']; // Quantity from carts table
                    $product_total_price = $row['cart_total_price']; // Total price from carts table
                    $product_brand = $row['product_brand'];

                    $subtotal += $product_total_price;
                ?>
                    <div class="cart-item grid-1-3 margin-bottom" style="border-bottom: 1px solid #ccc; padding-bottom: 1rem">
                        <div class="img-container margin-right-auto"></div>

                        <div class="cart-item-info">
                            <p><?php echo $product_brand; ?></p>

                            <div class="flex align-items-center" style="gap: 3rem">
                                <h3 class="margin-right-auto"><?php echo $product_name; ?></h3>

                                <p>₱<?php echo number_format($product_price, 2); ?></p>
                                <p><?php echo $product_quantity; ?></p>
                                <p class="product-price bold">₱<?php echo number_format($product_total_price, 2); ?></p>
                            </div>

                        </div>
                    </div>

                <?php
                }
                ?>
            </div>

            <!-- Cart Summary -->
            <div class="cart-summary">
                <h2 class="margin-bottom">Order Summary</h2>

                <div class="flex align-items-center margin-bottom" style="border-bottom: 1px solid #ccc; padding-bottom: 1rem">
                    <p class="margin-right-auto">Subtotal</p>
                    <p>₱<?php echo number_format($subtotal, 2); ?></p>
                </div>

                <div class="flex align-items-center margin-bottom" style="border-bottom: 1px solid #ccc; padding-bottom: 1rem">
                    <p class="margin-right-auto">Shipping</p>
                    <!-- Placeholder for shipping fee, 2% of subtotal -->
                    <p>₱<?php echo number_format($subtotal * 0.02, 2); ?></p>
                </div>

                <div class="flex align-items-center margin-bottom" style="border-bottom: 1px solid #ccc; padding-bottom: 1rem">
                    <p class="margin-right-auto bold">Total</p>
                    <?php   
                        $checkout_total = ($subtotal * 0.02) + $subtotal;
                    ?>
                    <p class="product-price bold">₱<?php echo number_format($checkout_total, 2); ?></p>
                </div>

                <a href="checkout.php?checkout_total=<?php echo $checkout_total; ?>" class="checkout-btn flex justify-content-center align-items-center margin-bottom"><i class="bi bi-cart-check-fill"></i><span>Checkout</span></a>

            </div>
        </div>
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