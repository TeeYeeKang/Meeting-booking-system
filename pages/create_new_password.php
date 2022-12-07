<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="../LoginStyles.css" rel="stylesheet" type="text/css">
</head>

<header>
    <div class="row">
        <div class="logo-row">
            <img src="../images/Murdoch.png" alt="logo" class="logo">
        </div>
    </div>
</header>

<?php
    $selector = $_GET["selector"];
    $validator = $_GET["validator"];

    if(empty($selector) || empty($validator)){
        echo "Could not validate your request !";
    } else {
        if(ctype_xdigit($selector) !== false && ctype_xdigit($validator)!== false){
?>
            <form class="center" action="includes/reset_password.inc.php" method="post">
                <input type="hidden" name="selector" value="<?php echo $selector; ?>">
                <input type="hidden" name="validator" value="<?php echo $validator; ?>">
                <input type="password" id="psw" name="pwd" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                    title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters"
                    placeholder="Enter New Password" required>
                <input type="password" id="psw2" name="pwd_repeat" placeholder="Re-enter password" required>
                <button type="submit" name="reset_password_submit">Reset Password</button>
            </form>
<?php
        }
    }
?>

<div id="message">
    <h3>Password must contain the following:</h3>
    <p id="letter" class="invalid">A <b>lowercase</b> letter</p>
    <p id="capital" class="invalid">A <b>capital (uppercase)</b> letter</p>
    <p id="number" class="invalid">A <b>number</b></p>
    <p id="length" class="invalid">Minimum <b>8 characters</b></p> 
</div>

<script src="../Register.js"></script>

</body>
</html>