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

            <form method="post" action="login.php">
                <input type="text" class="input-field" name="username" placeholder="Username" required>
                <input type="password" class="input-field" name="password" placeholder="Password" required>

                <button class="btn-login" id="btn" type="submit" name="login"><span>Log in</span></button>
            </form>
            <a href="signup.php">Sign up</a>
        </div>
    </div>
</body>

</html>