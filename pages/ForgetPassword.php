<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forget Password</title>
    <link href="../LoginStyles.css" rel="stylesheet" type="text/css">
</head>

<header>
    <div class="row">
        <div class="logo-row">
            <img src="../images/Murdoch.png" alt="logo" class="logo">
        </div>
    </div>
</header>

<body>
    <h2 class="HeaderText"> Enter email to reset password</h2>

    <form class="center" action = "includes/reset_request.inc.php" method="post">
        <p>Email: <input type="text" name="email" placeholder="Murdoch-Email" required>
        <button class="resetPassButton" type="submit" name="reset_request_submit"> Sent Email </button></p>
    </form>

    <!-- Display error or success message. Refer reset_request_inc.php - line 62 "reset" function-->
    <?php
        if(isset($_GET["reset"])){
            if($_GET["reset"] == "success"){
                echo '<script>';
                echo 'alert("An reset password email has send to you. Check your email");';
                echo '</script>';
            }
        }
    ?>

    <div class="BackToLogin">
        <label>Login to your account - </label>
        <a href="Login.php">LOGIN</a>
    </div>

</body>
</html>