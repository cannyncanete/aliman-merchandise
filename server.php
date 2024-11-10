<?php
session_start();

$errors = array();
$db = mysqli_connect('localhost', 'root', '', 'aliman_mechandise') or die($db);

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

// claim edit
if (isset($_POST['save-edit-claim'])) {

}