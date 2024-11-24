<?php
include("../server.php");

if (empty($_SESSION['logged_in'])) {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

?>

<html>

<head>
    <title>Aliman Merchandise</title>

    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="../css/style.css">

    <!-- Boostrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.4.1.js"></script>
</head>

<body>
    <div class="wrapper">
        <?php include 'side-bar.php'; ?>

        <div class="main">
            <h2 class="margin-bottom"><i class="bi bi-clipboard-check-fill" style="color: #f4a261;"></i> Received Orders</h2>
            <p class="margin-bottom">Here are all the orders on their way to the customer</p>

            <table id="myTable" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th style="text-align: right;">Total Price</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $orders = "SELECT orders.*, 
                    products.brand AS product_brand, 
                    products.name AS product_name, 
                    users.first_name AS user_first_name,
                    users.last_name AS user_last_name
                    FROM orders
                    JOIN products ON orders.product_id = products.id
                    JOIN users ON orders.user_id = users.id
                    WHERE orders.status = 'Received' ";


                    $result = mysqli_query($db, $orders);

                    while ($row = mysqli_fetch_assoc($result)) {
                        $order_id = $row['id'];

                        $product_brand = $row['product_brand'];
                        $product_name = $row['product_name'];

                        $user_first_name = $row['user_first_name'];
                        $user_last_name = $row['user_last_name'];

                        $quantity = $row['quantity'];
                        $total_price = $row['total_price'];
                        $status = $row['status'];
                    ?>
                        <tr>
                            <td><?php echo $order_id ?></td>
                            <td><?php echo $user_first_name . " " . $user_last_name ?></td>
                            <td><?php echo $product_brand . " " . $product_name ?></td>
                            <td><?php echo $quantity ?></td>
                            <td style="text-align: right;">₱<?php echo number_format($total_price, 2) ?></td>
                            <td><p class="order-status status-received"><?php echo $status ?></p></td>
                        </tr>

                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- <div class="container">
        <p>@2022 Aliman Merchandise: Ordering and Inventory Management System</p>
    </div> -->

    <!-- SCRIPT -->
    <!-- DataTables JavaScript -->
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>

    <!-- Initialize DataTable -->
    <script>
        $(document).ready(function() {
            $('#myTable').DataTable();
        });
    </script>
</body>

</body>

</html>