<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up Account</title>
    <link href="../LoginStyles.css?v=<?php echo time(); ?>" rel="stylesheet" type="text/css">
</head>

<body>

<!-- Content of the web page-->
<header>
    <div class="row">
        <div class="logo-row">
            <img src="../images/Murdoch.png" alt="logo" class="logo">
        </div>
    </div>
</header>

<form action="includes/signup.inc.php" onsubmit="return validateForm()" method="POST"> 
<div class="center">

    <label class="alignText">Username: </label>
    <input type="text" name="username" placeholder="User Name" required><br>
    <p>
    <label class="alignText">Email: </label>
    <input type="email" name="email" pattern="[A-Za-z0-9_]+@student.murdoch\.edu.au" 
                title="Must adhere the required format - @student.murdoch.edu.au"
                placeholder="Murdoch Email" required><br>
    </p>
    <p>
    <label class="alignText">Password: </label>
    <input type="password" id="psw" name="psw" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters"
                placeholder="Password" required><br>
    </p>
    <label class="alignText">Confirm Password: </label>
    <input type="password" id="psw2" name="psw2" placeholder="Confirm Password" required><br>
    <p style="padding-top: 30px;"> <button class="SignUpBox" type="submit" name="submit">Sign Up</button></p>

</div>
</form>

<div id="message">
    <h3>Password must contain the following:</h3>
    <p id="letter" class="invalid">A <b>lowercase</b> letter</p>
    <p id="capital" class="invalid">A <b>capital (uppercase)</b> letter</p>
    <p id="number" class="invalid">A <b>number</b></p>
    <p id="length" class="invalid">Minimum <b>8 characters</b></p> 
</div>

<div style="text-align:center; margin-top: 15px;">
    <label class="Policy">By signing up, you agree to our</label>
    <a
        href="https://www.murdoch.edu.au/docs/default-source/study/study-online/terms-and-conditions-301118.pdf?sfvrsn=c118bb07_6">Term</a>
    <label>, </label>
    <a href="http://goto.murdoch.edu.au/Privacy">Privacy Policy</a>
    <label> and </label>
    <a href="https://www.murdochuniversitydubai.com/cookie_policy">Cookies</a>
</div>

<script src="../Register.js"></script>

</body>

</html>