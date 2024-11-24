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
        <h2 class="margin-bottom"><i class="bi bi-box-seam-fill"></i> Orders</h2>

        <?php
        // Query to count the number of items in the cart for the logged-in user
        $user_id = $_SESSION['user_id']; // Assuming user ID is stored in session
        $query = "SELECT COUNT(*) AS item_count FROM orders WHERE user_id = '$user_id'";
        $result = mysqli_query($db, $query);
        $row = mysqli_fetch_assoc($result);
        $item_count = $row['item_count'];
        ?>

        <p class="margin-bottom bold">You have <?php echo $item_count; ?> product<?php echo $item_count != 1 ? 's' : ''; ?> in your orders</p>
        <p>Your items will be delivered after we have confirmed your payment.</p>

        <br><br>
        <div class="grid-2-1">
            <!-- Cart Items -->
            <div>
                <table class="orders-table" style="width: 100%;">
                    <tr>
                        <td>Product</td>
                        <td>Price</td>
                        <td>Quantity</td>
                        <td>Total</td>
                        <td>Status</td>
                        <td>Last Updated</td>
                    </tr>

                    <?php
                    $orders = "SELECT orders.*, products.brand AS product_brand, products.name AS product_name, products.price AS product_price, products.image_path AS image_path
                    FROM orders
                    JOIN products on orders.product_id = products.id 
                    WHERE orders.user_id = '$user_id'";

                    $result = mysqli_query($db, $orders);

                    while ($row = mysqli_fetch_assoc($result)) {
                        $order_id = $row['id'];
                        $product_id = $row['product_id'];
                        $product_brand = $row['product_brand'];
                        $product_name = $row['product_name'];
                        $product_price = $row['product_price'];
                        $quantity = $row['quantity'];
                        $total_price = $row['total_price'];
                        $email = $row['email'];
                        $status = $row['status'];
                        $created_at = $row['created_at'];
                        $paid_at = $row['paid_at'];
                        $received_at = $row['received_at'];
                        $image_path = $row['image_path'];

                        // For class name in the Status Cell & Determine the date to display
                        $status_class = '';
                        $last_updated = '';
                        switch ($status) {
                            case 'Pending':
                                $status_class = 'status-pending';
                                $last_updated = $created_at;
                                break;
                            case 'For Pickup':
                                $status_class = 'status-for-pickup';
                                $last_updated = $paid_at;
                                break;
                            case 'Received':
                                $status_class = 'status-received';
                                $last_updated = $received_at;
                                break;
                        }

                    ?>
                        <tr>
                            <td>
                                <div class="flex align-items-center cart-item">
                                    <div class="img-container" style="width: 128px;">
                                        <a href="product-page.php?id=<?php echo $product_id; ?>">
                                            <img src="<?php echo $image_path; ?>" alt="<?php echo $product_name; ?>" class="product-img">
                                        </a>
                                    </div>
                                    <div>
                                        <p><?php echo $product_brand; ?></p>
                                        <h3 class="margin-right-auto"><?php echo $product_name; ?></h3>
                                    </div>
                                </div>
                            </td>
                            <td>₱<?php echo number_format($product_price, 2); ?></td>
                            <td><?php echo $quantity; ?></td>
                            <td>
                                <p class="product-price bold">₱<?php echo number_format($total_price, 2); ?></p>
                            </td>
                            <td>
                                <p class="order-status <?php echo $status_class; ?>"><?php echo $status; ?></p>
                            </td>
                            <td>
                                <p><?php echo date('d F Y h:i A', strtotime($last_updated)); ?></p>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </table>
            </div>

            <!-- Cart Summary -->
            <div class="">
            </div>
        </div>
    </div><br>

    <!-- <div class="container">
        <p>@2022 Aliman Merchandise: Ordering and Inventory Management System</p>
    </div> -->

    <!-- SCRIPT -->
</body>

</html>