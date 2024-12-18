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

            // Redirect based on user role [Customer, Employee, ]
            if ($role === 'Customer') {
                header("Location: index.php");
            } elseif ($role === 'Employee') {
                header("Location: employee/pending-orders.php");
            } elseif ($role === 'Owner') {
                header("Location: owner/sales-chart.php");
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

// SIGN UP 
if (isset($_POST['sign_up_btn'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match.'); location.href='signup.php';</script>";
        exit();
    }

    // Check if username already exists
    $check_user_query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($db, $check_user_query);
    if (mysqli_num_rows($result) > 0) {
        echo "<script>alert('Username already exists. Please choose a different one.'); location.href='signup.php';</script>";
        exit();
    }

    // Insert the user into the database
    $insert_query = "INSERT INTO users (first_name, last_name, username, password, role) 
                     VALUES ('$first_name', '$last_name', '$username', '$password', 'Customer')";
    $insert_result = mysqli_query($db, $insert_query);

    if ($insert_result) {
        echo "<script>alert('Welcome $username! Please login below.'); location.href='login.php';</script>";
    } else {
        echo "<script>alert('Error: Unable to register user.'); location.href='signup.php';</script>";
    }
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

// REMOVE - CART
if (isset($_POST['remove_from_cart'])) {
    $user_id = mysqli_real_escape_string($db, $_POST['user_id']);
    $product_id = mysqli_real_escape_string($db, $_POST['product_id']);

    // Get product name for alert message
    $get_product_name_query = "SELECT name FROM products WHERE id = '$product_id'";
    $product_result = mysqli_query($db, $get_product_name_query);
    $product = mysqli_fetch_assoc($product_result);
    $product_name = $product['name'];

    // Remove product from cart
    $remove_query = "DELETE FROM carts WHERE user_id = '$user_id' AND product_id = '$product_id'";
    $remove_result = mysqli_query($db, $remove_query);

    if ($remove_result) {
        echo "<script>alert('$product_name was removed from your cart'); location.href='cart.php';</script>";
    } else {
        echo "<script>alert('Error: Unable to remove $product_name from your cart.'); location.href='cart.php';</script>";
    }
}


// CREATE - ORDER
if (isset($_POST['create_order'])) {
    $user_id = $_POST['user_id'];

    $email = $_POST['email'];
    $date = date('Y-m-d H:i:s');

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
                $insertOrder = "INSERT INTO orders (user_id, product_id, quantity, total_price, email, status, created_at) 
                            VALUES ('$user_id', '$product_id', '$quantity', '$total_price', '$email', 'Pending', '$date')";
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

// UPDATE - ORDER TO FOR PICKUP
if (isset($_POST['update_order_status_to_for_pickup'])) {

    // Get the order ID, customer info, and ordered product details
    $order_id = $_POST['order_id'];
    $user_first_name = $_POST['user_first_name'];
    $user_last_name = $_POST['user_last_name'];

    $date = date('Y-m-d H:i:s');

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

    // Update the order status to "For Pickup"
    $update_status_query = "UPDATE orders SET status = 'For Pickup', paid_at = '$date'  WHERE id = '$order_id'";
    $updateStatus = mysqli_query($db, $update_status_query);


    if ($updateProduct && $updateStatus) {
        // Success message and redirection
        echo "<script>alert('Order is set ready for pickup by $user_first_name $user_last_name'); location.href='pending-orders.php'</script>";
    } else {
        // Handle any errors
        echo "<script>alert('An error occurred while updating the order status and product quantity.'); location.href='pending-orders.php'</script>";
    }
}


// UPDATE - ORDER TO RECEIVED
if (isset($_POST['update_order_status_to_received'])) {
    // Get the order ID and customer info from the form submission
    $order_id = $_POST['order_id'];
    $date = date('Y-m-d H:i:s');

    // Update the order status to "Received"
    $update_status_query = "UPDATE orders SET status = 'Received', received_at = '$date' WHERE id = '$order_id'";

    if (mysqli_query($db, $update_status_query)) {
        // Success message and redirection
        echo "<script>alert('You have confirmed that the order has been received.'); location.href='orders-for-pickup.php'</script>";
    } else {
        // Handle any errors
        echo "<script>alert('An error occurred while updating the order status.'); location.href='orders-for-pickup.php'</script>";
    }
}

// CREATE - NEW PRODUCT
if (isset($_POST['create_new_product'])) {
    // Get the inputs
    $product_name = mysqli_real_escape_string($db, $_POST['product_name']);
    $brand = mysqli_real_escape_string($db, $_POST['brand']);
    $quantity = intval($_POST['quantity']); // Ensure it's an integer
    $price = floatval($_POST['price']); // Ensure it's a float
    $description = mysqli_real_escape_string($db, $_POST['description']);

    // Determine category
    if (!empty($_POST['new_category'])) {
        // Use the new category
        $new_category = mysqli_real_escape_string($db, $_POST['new_category']);
        // Insert the new category into the database
        $insert_category = "INSERT INTO product_categories (category_name) VALUES ('$new_category')";
        mysqli_query($db, $insert_category);
        // Get the ID of the newly created category
        $category_id = mysqli_insert_id($db);
    } else {
        // Use an existing category
        $category_id = intval($_POST['existing_category']); // Ensure it's an integer
    }

    // Insert the new product
    $insert_new_product = "INSERT INTO products (category, brand, name, quantity, price, description) 
                           VALUES ($category_id, '$brand', '$product_name', $quantity, $price, '$description')";
    $result = mysqli_query($db, $insert_new_product);

    // Check if the product was successfully added
    if ($result) {
        echo "<script>alert('You have added $quantity of \"$product_name\" to the inventory'); location.href='inventory.php'</script>";
    } else {
        echo "<script>alert('Error adding product: " . mysqli_error($db) . "'); location.href='inventory.php'</script>";
    }
}

// UPDATE - PRODUCT
if (isset($_POST['update_product_quantity_and_price'])) {
    // Get the form data
    $product_id = intval($_POST['product_id']);
    $quantity = isset($_POST['product_quantity']) ? intval($_POST['product_quantity']) : null;
    $price = isset($_POST['product_price']) ? floatval($_POST['product_price']) : null;

    // Initialize the update query
    $update_query = "UPDATE products SET ";
    $fields_to_update = [];

    // Add fields to update based on user input
    if ($quantity !== null) {
        $fields_to_update[] = "quantity = $quantity";
    }
    if ($price !== null) {
        $fields_to_update[] = "price = $price";
    }

    // If there are fields to update, proceed
    if (!empty($fields_to_update)) {
        $update_query .= implode(", ", $fields_to_update) . " WHERE id = $product_id";

        // Execute the update query
        $result = mysqli_query($db, $update_query);

        if ($result) {
            echo "<script>alert('Product updated successfully'); location.href='inventory.php';</script>";
        } else {
            echo "<script>alert('Error updating product: " . mysqli_error($db) . "'); location.href='inventory.php';</script>";
        }
    } else {
        // If no fields were updated, notify the user
        echo "<script>alert('No changes made to the product'); location.href='inventory.php';</script>";
    }
}
