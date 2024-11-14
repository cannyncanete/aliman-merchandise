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
            <h2 class="margin-bottom"><i class="bi bi-people-fill"></i> Customers</h2>
            <p class="margin-bottom">Here are all the customers who have an account in the system</p>

            <table id="myTable" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>Customer ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Pending Orders</th>
                        <th>Orders On Delivery</th>
                        <th>Received Orders</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $users = "SELECT 
                    users.id AS user_id,
                    users.first_name,
                    users.last_name,
                    COUNT(CASE WHEN orders.status = 'Pending' THEN 1 END) AS pending_orders,
                    COUNT(CASE WHEN orders.status = 'On Delivery' THEN 1 END) AS on_delivery_orders,
                    COUNT(CASE WHEN orders.status = 'Received' THEN 1 END) AS received_orders
                    FROM 
                        users
                    LEFT JOIN 
                        orders ON users.id = orders.user_id
                    WHERE 
                        users.role = 'Customer'
                    GROUP BY 
                        users.id";

                    $result = mysqli_query($db, $users);

                    while ($row = mysqli_fetch_assoc($result)) {
                        $customer_id = $row['user_id'];
                        $first_name = $row['first_name'];
                        $last_name = $row['last_name'];
                        $pending_orders = $row['pending_orders'];
                        $on_delivery_orders = $row['on_delivery_orders'];
                        $received_orders = $row['received_orders'];
                    ?>
                        <tr>
                            <td><?php echo $customer_id ?></td>
                            <td><?php echo $first_name ?></td>
                            <td><?php echo $last_name ?></td>
                            <td><?php echo $pending_orders ?></td>
                            <td><?php echo $on_delivery_orders ?></td>
                            <td><?php echo $received_orders ?></td>
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