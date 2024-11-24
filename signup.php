<?php
include("server.php");
?>
<html>

<head>
    <title>Aliman Merchandise</title>

    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="css/login.css">

    <!-- Boostrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
    <div class="wrapper">
        <div class="main">
            <h1>Aliman Merchandise</h1>

            <form method="post" action="signup.php">
                <input type="text" class="input-field" name="first_name" placeholder="First Name" required>
                <input type="text" class="input-field" name="last_name" placeholder="Last Name" required><br>

                <input type="text" class="input-field" name="username" placeholder="Username" required><br>
                <input type="password" class="input-field" name="password" placeholder="Password" required>
                <input type="password" class="input-field" name="confirm_password" placeholder="Confirm Password" required>

                <button class="btn-login" id="btn" type="submit" name="sign_up_btn"><span>Sign Up</span></button>
            </form>
            <a href="login.php">Login</a>
        </div>
    </div>
</body>

<!-- SCRIPTS -->

<!-- HANDLES CONFIRM PASSWORD -->
<script>
    document.querySelector('form').addEventListener('submit', function(event) {
        var password = document.querySelector('[name="password"]').value;
        var confirmPassword = document.querySelector('[name="confirm_password"]').value;

        if (password !== confirmPassword) {
            alert('Passwords do not match!');
            event.preventDefault(); // Prevent form submission
        }
    });
</script>


</html>