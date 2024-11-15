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
        <h2 class="margin-bottom"><i class="bi bi-box-seam-fill"></i> Checkout</h2>

        <?php
        // Query to count the number of items in the cart for the logged-in user
        $user_id = $_SESSION['user_id']; // Assuming user ID is stored in session
        $query = "SELECT COUNT(*) AS item_count FROM carts WHERE user_id = '$user_id'";
        $result = mysqli_query($db, $query);
        $row = mysqli_fetch_assoc($result);
        $item_count = $row['item_count'];
        ?>

        <p class="margin-bottom bold">You have <?php echo $item_count; ?> product<?php echo $item_count != 1 ? 's' : ''; ?> to checkout</p>
        <p>Your items aren't reserved yet—complete your payment to order them.</p>

        <br><br>
        <form action="" method="post">
            <div class="grid-2-1">
                <!-- Form for creating order -->
                <form action="" method="post">
                    <!-- Checkout Items -->
                    <div style="width: 100%;">
                        <div class="margin-bottom flex" style="border-bottom: 1px solid #ccc; padding-bottom: 1rem">
                            <p class="icon-text"><i class="bi bi-cart-fill"></i> Products to checkout:</p>
                            <?php
                            $cart = "SELECT carts.id AS cart_id, 
                        products.brand AS product_brand, products.name AS product_name
                        FROM carts 
                        JOIN products ON carts.product_id = products.id 
                        WHERE carts.user_id = '$user_id'";

                            $result = mysqli_query($db, $cart);

                            while ($row = mysqli_fetch_assoc($result)) {
                                $cart_id = $row['cart_id']; // Cart ID
                                $product_name = $row['product_name']; // Product name from products table
                                $product_brand = $row['product_brand'];
                            ?>
                                <p class="bold"><?php echo $product_brand . " " . $product_name . ","; ?> </p>
                            <?php
                            }
                            ?>
                        </div>

                        <?php
                        $query = "SELECT * FROM users WHERE id = '$user_id'";
                        $result = mysqli_query($db, $query);

                        $user = mysqli_fetch_assoc($result);

                        $user_first_name = $user['first_name'];
                        $user_last_name = $user['last_name'];
                        ?>

                        <div style="width: 100%;">
                            <p class="bold margin-bottom icon-text"><i class="bi bi-person-fill"></i> <?php echo $user_first_name . " " . $user_last_name ?></p>
                            <p class="margin-bottom icon-text"><i class="bi bi-envelope-fill"></i> Email Address</p>
                            <div class="grid-1-1">
                                <input type="text" class="normal-input margin-bottom" name="email" placeholder="Email" required>
                                <p></p>
                            </div>
                        </div>
                    </div>

                    <!-- Checkout Summary -->
                    <div class="cart-summary">
                        <h2 class="margin-bottom">Checkout Summary</h2>

                        <div class="flex align-items-center margin-bottom" style="border-bottom: 1px solid #ccc; padding-bottom: 1rem">
                            <p class="margin-right-auto">Total Amount</p>
                            <?php
                            $checkout_total = $_GET['checkout_total'];
                            ?>
                            <p class="product-price bold">₱<?php echo number_format($checkout_total, 2); ?></p>
                        </div>

                        <div class="flex align-items-center margin-bottom" style="border-bottom: 1px solid #ccc; padding-bottom: 1rem">
                            <p class="margin-right-auto">Payment Method</p>
                            <img src="imgs/gcash/GCash-Logo.png" class="gcash-logo-img">
                        </div>

                        <p>Scan the QR Code below:</p>
                        <img src="imgs/gcash/randomQRcode.png" class="gcash-qrcode-img ">

                        <input type="hidden" name="user_id" value="<?php echo $user_id ?>">
                        <input type="hidden" name="checkout_total" value="<?php echo $checkout_total ?>">

                        <button type="submit" class="checkout-btn margin-bottom" name="create_order">
                            <span>₱<?php echo number_format($checkout_total, 2); ?> Send Payment</span>
                        </button>
                    </div>
                </form>
            </div>
        </form>
    </div><br>

    <!-- <div class="container">
        <p>@2022 Aliman Merchandise: Ordering and Inventory Management System</p>
    </div> -->

    <!-- SCRIPT -->
</body>

</html>