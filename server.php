<?php
session_start();

$errors = array();
$db = mysqli_connect('localhost', 'root', '', 'aliman_merchandise') or die($db);

//set default time zone
date_default_timezone_set('Asia/Manila');

//login
if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $password = mysqli_real_escape_string($db, $_POST['password']);

    if (count($errors) == 0) {
        $get_id = "SELECT id FROM users WHERE username='$username'";
        $res_id = $db->query($get_id);
        $user_id = $res_id->fetch_array()[0] ?? '';

        $query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
        $result = mysqli_query($db, $query);
        $count = mysqli_num_rows($result);

        if ($count == 1) {

            $_SESSION['logged_in'] = true;
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $user_id;

            header("location: index.php");
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
