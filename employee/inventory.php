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
            <h2 class="margin-bottom"><i class="bi bi-inboxes-fill" style="color: #9d4edd;"></i> Inventory</h2>
            <p class="margin-bottom">Here are all the products currently in the inventory</p>

            <table id="myTable" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Category</th>
                        <th>Brand</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th style="text-align: right;">Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $products = "SELECT products.*, product_categories.category_name AS category_name
                    FROM products
                    JOIN product_categories on product_categories.id = products.category";


                    $result = mysqli_query($db, $products);

                    while ($row = mysqli_fetch_assoc($result)) {
                        $product_id = $row['id'];
                        $category = $row['category_name'];
                        $brand = $row['brand'];
                        $product = $row['name'];
                        $quantity = $row['quantity'];
                        $price = $row['price'];
                    ?>
                        <tr>
                            <td><?php echo $product_id ?></td>
                            <td><?php echo $category ?></td>
                            <td><?php echo $brand ?></td>
                            <td><?php echo $product ?></td>
                            <td><?php echo $quantity ?></td>
                            <td style="text-align: right;">₱<?php echo number_format($price, 2) ?></td>
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