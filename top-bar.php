<div class="top-bar margin-bottom">
    <!-- TOP-TOP-BAR -->
    <div class="container flex align-items-center">
        <!-- SYSTEM TITLE -->
        <h2 class="title-text margin-right-auto"><a href="index.php">Aliman Merchandise</a></h2>

        <!-- SEARCH BAR -->
        <form action="#">
            <div class="flex align-items-center search-bar">
                <input type="text" class="search-input" name="search_product" id="search_product">
                <button type="submit" class="search-btn"><i class="bi bi-search"></i></button>
            </div>
        </form>

        <!-- ACOUNT/INFO -->
        <div class="flex align-items-center">
            <span>
                <?php
                if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
                    echo $_SESSION['username'];
                } else {
                    echo 'Guest'; // Or any fallback text if the user is not logged in
                }
                ?>
            </span>

            <!-- CART LINK/ICON -->
            <?php
            // Query to count the products in the cart
            $query = "SELECT COUNT(*) AS count FROM carts WHERE user_id = '$user_id'";
            $result = mysqli_query($db, $query);
            $row = mysqli_fetch_assoc($result);
            $cart_count = $row['count'] ?? 0;
            ?>

            <a href="cart.php" class="icon-container">
                <i class="bi bi-cart-fill"></i>
                <?php if ($cart_count > 0) : ?>
                    <span class="cart-badge"><?php echo $cart_count; ?></span>
                <?php endif; ?>
            </a>

            <!-- ORDERS LINK/ICON -->
            <?php
            // Query to count the products in the order
            $query = "SELECT COUNT(*) AS count FROM orders WHERE user_id = '$user_id'";
            $result = mysqli_query($db, $query);
            $row = mysqli_fetch_assoc($result);
            $order_count = $row['count'] ?? 0;
            ?>

            <a href="orders.php" class="icon-container">
                <i class="bi bi-box-seam-fill"></i>
                <?php if ($order_count > 0) : ?>
                    <span class="cart-badge"><?php echo $order_count; ?></span>
                <?php endif; ?>
            </a>
        </div>
    </div>

    <br>

    <!-- BOTTOM-TOP-BAR / CATEGORIES -->
    <div class="container flex justify-content-center">
        <?php

        $categories = "SELECT category_name FROM product_categories LIMIT 5";
        $result = mysqli_query($db, $categories);

        while ($row = mysqli_fetch_assoc($result)) {
        ?>
            <a href="#"><?php echo $row['category_name'] ?></a>
        <?php
        }
        ?>
        <a href="#">More!</a>
    </div>
</div>