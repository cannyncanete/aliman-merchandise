<?php
include("server.php");
?>
<html>

<head>
    <title>Aliman Merchandise</title>

     <!-- CSS -->
     <link rel="stylesheet" type="text/css" href="css/style.css">

    <!-- Boostrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
    <div class="wrapper">
        <div class="main">
            <?php //include("errors.php"); 
            ?>
            <h2>Login</h2>

            <form method="post" action="login.php">
                <input type="text" class="input-field" name="username" placeholder="Username" required>
                <input type="password" class="input-field" name="password" placeholder="Password" required>

                <button class="btn-login" id="btn" type="submit" name="login"><span>Log in</span></button>
            </form>
        </div>
    </div>
</body>

</html>