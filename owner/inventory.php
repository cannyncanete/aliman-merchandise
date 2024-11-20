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
            <h2 class="margin-bottom"><i class="bi bi-inboxes-fill"></i> Inventory</h2>
            <p class="margin-bottom">Here are all the products currently in the inventory</p>

            <form action="" method="POST" class="flex align-items-center">
                <div>
                    <label for="">Filter by Category</label>
                    <input type="text" name="filter_category" class="normal-input" placeholder="Category Name" value="<?php echo isset($_POST['filter_category']) ? htmlspecialchars($_POST['filter_category']) : ''; ?>">
                </div>

                <div>
                    <label for="">Filter by Brand</label>
                    <input type="text" name="filter_brand" class="normal-input" placeholder="Brand Name" value="<?php echo isset($_POST['filter_brand']) ? htmlspecialchars($_POST['filter_brand']) : ''; ?>">
                </div>

                <div>
                    <label for="">Filter by Product</label>
                    <input type="text" name="filter_product" class="normal-input" placeholder="Product Name" value="<?php echo isset($_POST['filter_product']) ? htmlspecialchars($_POST['filter_product']) : ''; ?>">
                </div>

                <div>
                    <br>
                    <button type="submit" name="filter_inventory" class="basic-btn">Filter</button>
                </div>
            </form><br>

            <!-- Modal Button -->
            <button id="open-modal" class="basic-btn">Add New Product</button>

            <!-- Overlay -->
            <div id="overlay"></div>

            <!-- Modal -->
            <div id="modal" class="modal" style="width: 64%">
                <div class="flex align-items-center" style="justify-content: space-between;">
                    <div class="icon-text">
                        <i class="bi bi-file-earmark-plus-fill"></i>
                        <h2>Add New Product</h2>
                    </div>

                    <i id="close-modal" class="bi bi-x-lg close-icon"></i>
                </div><br>

                <form action="" method="POST">
                    <div class="grid-1-1">
                        <div>
                            <div class="grid-1-1">
                                <div>
                                    <label class="bold" for="">Select Existing Category</label>
                                    <select style="margin-top: 0.5rem;" name="existing_category" id="existing_category" class="normal-select">
                                        <option value="" disabled selected>Select a category</option>
                                        <?php
                                        $existing_categories = "SELECT * FROM product_categories";
                                        $existing_categories_result = mysqli_query($db, $existing_categories);

                                        while ($row_existing_categories = mysqli_fetch_assoc($existing_categories_result)) {

                                            $category_id = $row_existing_categories['id'];
                                            $category_name = $row_existing_categories['category_name'];

                                        ?>
                                            <option value="<?php echo $category_id ?>"><?php echo $category_name ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="margin-bottom">
                                    <label class="bold" for="">New Category</label>
                                    <input type="text" class="normal-input" id="new_category" style="margin-top: 0.5rem;" name="new_category" placeholder="Add new category">
                                </div>
                            </div>

                            <div>
                                <label class="bold" for="">Product Name</label>
                                <input type="text" class="normal-input" style="margin-top: 0.5rem;" name="product_name" placeholder="Add new category">
                            </div>
                        </div>

                        <div>
                            <div class="margin-bottom">
                                <label class="bold" for="">Brand</label>
                                <input type="text" class="normal-input" style="margin-top: 0.5rem;" name="brand" placeholder="Product's brand">
                            </div>

                            <div class="grid-1-1">
                                <div>
                                    <label class="bold" for="">Quantity</label>
                                    <input type="number" class="normal-input" style="margin-top: 0.5rem;" name="quantity" placeholder="Product quantity">
                                </div>

                                <div>
                                    <label class="bold" for="">Price per item</label>
                                    <input type="number" class="normal-input" style="margin-top: 0.5rem;" name="price" placeholder="Price per item">
                                </div>
                            </div>

                        </div>
                    </div><br>

                    <div>
                        <label class="bold" for="">Description</label>
                        <textarea name="description" id="" cols="30" style="margin-top: 0.5rem;" rows="5"></textarea>
                    </div><br>

                    <button type="submit" name="create_new_product" class="basic-btn flex" style="margin-left: auto;">Save</button>
                </form>

            </div>

            <?php
            // Default query
            $products = "SELECT 
                    products.id,
                    product_categories.category_name,
                    products.brand,
                    products.name AS product_name,
                    products.quantity AS stock_quantity,
                    products.price AS product_price,
                    COALESCE(SUM(orders.quantity), 0) AS total_sold_items,
                    COALESCE(SUM(orders.total_price), 0) AS total_revenue
                FROM 
                    products
                JOIN 
                    product_categories ON product_categories.id = products.category
                LEFT JOIN 
                    orders ON orders.product_id = products.id
                WHERE 1=1"; // Base condition for dynamic filters

            // Check for filters
            if (isset($_POST['filter_inventory'])) {
                $filter_category = mysqli_real_escape_string($db, $_POST['filter_category']);
                $filter_brand = mysqli_real_escape_string($db, $_POST['filter_brand']);
                $filter_product = mysqli_real_escape_string($db, $_POST['filter_product']);

                // Apply filters dynamically
                if (!empty($filter_category)) {
                    $products .= " AND LOWER(product_categories.category_name) LIKE LOWER('%$filter_category%')";
                }

                if (!empty($filter_brand)) {
                    $products .= " AND LOWER(products.brand) LIKE LOWER('%$filter_brand%')";
                }

                if (!empty($filter_product)) {
                    $products .= " AND LOWER(products.name) LIKE LOWER('%$filter_product%')";
                }
            }

            // Finalize query with GROUP BY
            $products .= " GROUP BY 
                    products.id, product_categories.category_name, products.brand, products.name, products.quantity, products.price;";

            // Execute the query
            $result = mysqli_query($db, $products);
            ?>

            <div class="table-container">
                <table id="myTable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Product ID</th>
                            <th>Category</th>
                            <th>Brand</th>
                            <th>Product</th>
                            <th>In Stock</th>
                            <th style="text-align: right;">Price</th>
                            <th>Total Sold Items</th>
                            <th style="text-align: right;">Total Sold</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while ($row = mysqli_fetch_assoc($result)) {
                            $product_id = $row['id'];
                            $category = $row['category_name'];
                            $brand = $row['brand'];
                            $product = $row['product_name'];
                            $quantity = $row['stock_quantity'];
                            $price = $row['product_price'];
                            $total_sold_items = $row['total_sold_items'];
                            $total_revenue = $row['total_revenue'];
                        ?>
                            <tr>
                                <td><?php echo $product_id ?></td>
                                <td><?php echo $category ?></td>
                                <td><?php echo $brand ?></td>
                                <td><?php echo $product ?></td>
                                <td><?php echo $quantity ?></td>
                                <td style="text-align: right;">₱<?php echo number_format($price, 2) ?></td>
                                <td><?php echo $total_sold_items; ?></td>
                                <td style="text-align: right;">₱<?php echo number_format($total_revenue, 2); ?></td>
                            <?php
                        }
                            ?>
                    </tbody>
                </table>
            </div>
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
            $('#myTable').DataTable({
                scrollX: true // Enable horizontal scrolling
            });
        });
    </script>

    <!-- HANDLES MODAL -->
    <script>
        $(document).ready(function() {
            // Show modal when button is clicked
            document.getElementById('open-modal').addEventListener('click', function() {
                document.getElementById('modal').style.display = 'block';
                document.getElementById('overlay').style.display = 'block';
            });

            // Close modal on clicking the close icon
            document.getElementById('close-modal').addEventListener('click', function() {
                document.getElementById('modal').style.display = 'none';
                document.getElementById('overlay').style.display = 'none';
            });
        });
    </script>

    <!-- HANDLES SELECTION OF CATEGORY TO BE EITHER EXISTING OR NEW -->
    <script>
        $(document).ready(function() {
            // JavaScript to handle input clearing
            document.getElementById('existing_category').addEventListener('change', function() {
                // Clear the new category input when an existing category is selected
                document.getElementById('new_category').value = '';
            });

            document.getElementById('new_category').addEventListener('input', function() {
                // Reset the select to its placeholder when typing in new category
                document.getElementById('existing_category').selectedIndex = 0;
            });
        });
    </script>

</body>

</body>

</html>