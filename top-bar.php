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

            <?php
            // Query to count the products in the cart for this user
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
        </div>
    </div>

    <br>

    <!-- BOTTOM-TOP-BAR / CATEGORIES -->
    <div class="container flex  justify-content-center">
        <a href="#">Mobiles & Gadgets</a>
        <a href="#">Clothing & Apparel</a>
        <a href="#">Home & Kitchen</a>
        <a href="#">Health & Wellness</a>
        <a href="#">Sports & Outdoors</a>
        <a href="#">More!</a>
    </div>
</div>