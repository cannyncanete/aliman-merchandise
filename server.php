<?php
session_start();

$errors = array();
$db = mysqli_connect('localhost', 'root', '', 'aliman_merchandise') or die($db);

//set default time zone
date_default_timezone_set('Asia/Manila');

// Login
if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $password = mysqli_real_escape_string($db, $_POST['password']);

    if (count($errors) == 0) {
        // Get the user details (id, role) from the database
        $query = "SELECT id, role FROM users WHERE username='$username' AND password='$password'";
        $result = mysqli_query($db, $query);

        if ($result && mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);
            $user_id = $user['id'];
            $role = $user['role'];

            // Set session variables
            $_SESSION['logged_in'] = true;
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $user_id;
            $_SESSION['role'] = $role;

            // Redirect based on user role
            if ($role === 'Customer') {
                header("Location: index.php");
            } elseif ($role === 'Employee') {
                header("Location: employee/pending-orders.php");
            }
            exit();
        } else {
            array_push($errors, "Invalid Username or Password");
        }
    }
}

//logout
if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['logged_in']);
    header("location: login.php");
}

// CREATE - CART
if (isset($_POST['add_to_cart'])) {
    $user_id = $_POST['user_id'];
    $product_id = $_POST['product_id'];
    $product_quantity = $_POST['product_quantity'];

    $query = "SELECT price, brand, name FROM products WHERE id = '$product_id'";
    $result = mysqli_query($db, $query);
    $product = mysqli_fetch_assoc($result);

    $price = $product['price'];
    $product_name = $product['name'];
    // If you want to set a total price, you can do it here
    $total_price = $price * $product_quantity;

    $insert = "INSERT INTO carts(user_id, product_id, quantity, total_price) VALUES('$user_id', '$product_id', '$product_quantity', '$total_price')";
    $result = mysqli_query($db, $insert);

    echo "<script>alert('$product_quantity $product_name Added to Cart'); location.href='product-page.php?id=$product_id'</script>";
}

// CREATE - ORDER
if (isset($_POST['create_order'])) {
    $user_id = $_POST['user_id'];

    $email = $_POST['email'];
    $street = $_POST['street'];
    $city = $_POST['city'];
    $region = $_POST['region'];
    $zip_code = $_POST['zip_code'];

    $checkout_total = $_POST['checkout_total'];

    $query = "SELECT carts.id AS cart_id, carts.product_id AS product_id, carts.quantity AS cart_quantity, carts.total_price AS cart_total_price, 
                    products.brand AS product_brand, products.name AS product_name, products.price AS product_price 
                FROM carts 
                JOIN products ON carts.product_id = products.id 
                WHERE carts.user_id = '$user_id'";
    $result = mysqli_query($db, $query);

    // Check if there are items in the cart
    if (mysqli_num_rows($result) > 0) {

        try {
            // Insert each item from the cart into the orders table
            while ($row = mysqli_fetch_assoc($result)) {
                $product_id = $row['product_id'];
                $quantity = $row['cart_quantity'];
                $total_price = $row['cart_total_price'];

                // Insert into the orders table
                $insertOrder = "INSERT INTO orders (user_id, product_id, quantity, total_price, email, street, city, region, zip_code, status) 
                            VALUES ('$user_id', '$product_id', '$quantity', '$total_price', '$email', '$street', '$city', '$region', '$zip_code', 'Pending')";
                $insertResult = mysqli_query($db, $insertOrder);
            }

            // Delete all cart items for this user
            $deleteCart = "DELETE FROM carts WHERE user_id = '$user_id'";
            $deleteResult = mysqli_query($db, $deleteCart);

            // Order placed successfully and cart items removed
            echo "<script>alert('Order sent for verification'); location.href='index.php'</script>";
        } catch (Exception $e) {
            echo "<script>alert('An error occured in the server.'); location.href='checkout.php?checkout_total=$checkout_total'</script>";
        }
    } else {
        // No items in the cart to process
        echo "<script>alert('Cart is empty.'); location.href='checkout.php?checkout_total=$checkout_total'</script>";
    }
}

// UPDATE - ORDER TO ON DELIVERY
if (isset($_POST['update_order_status_to_on_delivery'])) {

    // Get the order ID, customer info, and ordered product details
    $order_id = $_POST['order_id'];
    $user_first_name = $_POST['user_first_name'];
    $user_last_name = $_POST['user_last_name'];

    // Fetch the product_id and order quantity for the current order
    $order_query = "SELECT product_id, quantity FROM orders WHERE id = '$order_id'";
    $order_result = mysqli_query($db, $order_query);
    $order_row = mysqli_fetch_assoc($order_result);

    $product_id = $order_row['product_id'];
    $ordered_quantity = $order_row['quantity'];

    // Get the current product quantity from the products table
    $product_query = "SELECT quantity FROM products WHERE id = '$product_id'";
    $product_result = mysqli_query($db, $product_query);
    $product_row = mysqli_fetch_assoc($product_result);

    $current_product_quantity = $product_row['quantity'];

    // Subtract the ordered quantity from the product's current quantity
    $new_product_quantity = $current_product_quantity - $ordered_quantity;

    // Update the product's quantity in the products table
    $update_product_query = "UPDATE products SET quantity = '$new_product_quantity' WHERE id = '$product_id'";
    $updateProduct = mysqli_query($db, $update_product_query);

    // Update the order status to "On Delivery"
    $update_status_query = "UPDATE orders SET status = 'On Delivery' WHERE id = '$order_id'";
    $updateStatus = mysqli_query($db, $update_status_query);


    if ($updateProduct && $updateStatus) {
        // Success message and redirection
        echo "<script>alert('Order has been sent to delivery for $user_first_name $user_last_name'); location.href='orders-on-delivery.php'</script>";
    } else {
        // Handle any errors
        echo "<script>alert('An error occurred while updating the order status and product quantity.'); location.href='orders-on-delivery.php'</script>";
    }
}

if (isset($_POST['update_order_status_to_received'])) {
    // Get the order ID and customer info from the form submission
    $order_id = $_POST['order_id'];

    // Update the order status to "Received"
    $update_status_query = "UPDATE orders SET status = 'Received' WHERE id = '$order_id'";

    if (mysqli_query($db, $update_status_query)) {
        // Success message and redirection
        echo "<script>alert('You have confirmed that the order has been received.'); location.href='orders.php'</script>";
    } else {
        // Handle any errors
        echo "<script>alert('An error occurred while updating the order status.'); location.href='orders.php'</script>";
    }
}