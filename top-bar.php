<div class="top-bar margin-bottom">
    <!-- TOP-TOP-BAR -->
    <div class="container flex align-items-center">
        <!-- SYSTEM TITLE -->
        <h2 class="title-text margin-right-auto"><a href="index.php">Aliman Merchandise</a></h2>

        <!-- SEARCH BAR -->
        <form action="index.php" method="post">
            <div class="flex align-items-center search-bar">
                <input type="text" class="search-input" name="search_product" id="search_product" placeholder="Search for brand, product, or category">
                <button type="submit" class="search-btn"><i class="bi bi-search"></i></button>
            </div>
        </form>

        <!-- ACOUNT/INFO -->
        <div class="flex align-items-center">
            <span class="user-dropdown">
                <span id="user-name"><?php echo $_SESSION['username']; ?></span>
                <div id="tooltip" class="tooltip hidden">
                    <a href="server.php?logout=1" class="logout-link">Log out</a>
                </div>
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
        $categories = "SELECT id, category_name FROM product_categories"; // Include the `id` of the category
        $result = mysqli_query($db, $categories);

        while ($row = mysqli_fetch_assoc($result)) {
            $category_id = $row['id'];
            $category_name = $row['category_name'];
        ?>
            <a href="index.php?category_id=<?php echo $category_id; ?>"><?php echo $category_name; ?></a>
        <?php
        }
        ?>
    </div>

</div>

<!-- SCRIPTS -->

<!-- HANDLES DISPLAY OF LOGOUT BTN IN TOPBAR -->
<script>
    const userName = document.getElementById('user-name');
    const tooltip = document.getElementById('tooltip');

    userName.addEventListener('click', () => {
        // Toggle the hidden class
        tooltip.classList.toggle('hidden');
    });

    // Hide the tooltip if clicked anywhere else
    document.addEventListener('click', (event) => {
        if (!event.target.closest('.user-dropdown')) {
            tooltip.classList.add('hidden');
        }
    });
</script>