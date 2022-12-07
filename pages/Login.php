<?php
    session_start();
    $conn = mysqli_connect('localhost', 'root', '', 'meetme') or die ('Unable to connect');
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Account</title>
    <link href="../LoginStyles.css?v=<?php echo time(); ?>" rel="stylesheet" type="text/css">
</head>

<body>
    <header>
        <div class="row">
            <div class="logo-row">
                <img src="../images/Murdoch.png" alt="logo" class="logo">
            </div>
        </div>
    </header>

    <div> 
        <form action = "Login.php" method = "post">
            <h2 class="HeaderText"> Please Login </h2>
            <p><input class="IDBox" name = "Murdoch_Email" type="email" placeholder="Murdoch Email" 
                pattern="[A-Za-z0-9_]+@student.murdoch\.edu.au" 
                title="Must adhere the required format - @student.murdoch.edu.au" required></p>
            <p><input class="PasswordBox" type = "password" name = "Pass" placeholder = "Password" required></p> 
            <p><input class="LoginSubmit" type = "submit" name = "login" value = "Log in"></p> 
        </form>

    </div>

    <!--
    <div>
        <form action="../index.html"> 
            <p><input class="IDBox" type="email" placeholder="Murdoch Email" 
                pattern="[A-Za-z0-9_]+@murdoch\.edu.au" 
                title="Must adhere the required format - @murdoch.edu.au" required></p>
            <p><input class="PasswordBox" type="password" placeholder="Password" required></p>
            <p><input type="submit" class="LoginSubmit" value="Log in"></p>
        </form> 
    </div>
    -->

    <div class="ForgerPassLink">
        <p><a href="ForgetPassword.php">Forgot Password ?</a></p>
    </div>

    <div class="NoAccLink">
        <label class="NoAccount">Don't have an account ?</label>
        <a href="Register.php">Register Account</a>
    </div>

<?php

    if(isset($_GET["newpwd"])){
        if($_GET["newpwd"] == "passwordupdated"){
            echo '<script>';
            echo 'alert("Your password has been reset. Login to your account");';
            echo '</script>';
        } else if($_GET["newpwd"] == "pwdNotSame"){
            echo '<script>';
            echo 'alert("Password not matched. Plese redo again.");';
            echo '</script>';
        }
    }

    if(isset($_GET["newAccount"])){
        if($_GET["newAccount"] == "success"){
            echo '<script>';
            echo 'alert("Your account has been created successfully - Try login");';
            echo '</script>';
        }
    }

    if (isset($_POST['login'])){
        $Murdoch_Email = $_POST['Murdoch_Email'];
        $Pass = $_POST['Pass'];
    
        $select = mysqli_query($conn, "SELECT * FROM useraccount WHERE murdoch_email = '$Murdoch_Email' AND pass = '$Pass'");
        $row = mysqli_fetch_array($select);

        if(is_array($row)){
            $_SESSION["Murdoch_ID"] = $row;
            $_SESSION["Murdoch_Email"] = $row ['murdoch_email'];
        } else{
            echo '<script>';
            echo 'alert("Invalid Murdoch Email or Password");';
            echo 'window.location.href = "Login.php"';
            echo '</script>';
        }
    }

    if(isset($_SESSION["Murdoch_ID"])){
        header("Location:../pages/home.php");
    }
?>

</body>

</html>