<?php
require_once  'pdo.php';
session_start();
unset($_SESSION['name']);
unset($_SESSION['user_id']);
if(isset($_POST['cancel'])){
    header('location:index.php');
    return;
}
if (isset($_SESSION['error'])) {
    echo "<h3 style='color: red;text-align: center;'>" . $_SESSION['error'] . "</h3>";
    unset($_SESSION['error']);
}
$salt= 'XyZzy12*_';
if(isset($_POST['email'])&&isset($_POST['pass'])){
    if(strlen($_POST['email'])<1||strlen($_POST['pass'])<1){
        $_SESSION['error']="Email and Password are Required";
        header('location:login.php');
        return;
    }
    $check = hash('md5', $salt.$_POST['pass']);
    $stmt = $pdo->prepare('SELECT user_id, name FROM users WHERE email = :em AND password = :pw');
    $stmt->execute(array( ':em' => $_POST['email'], ':pw' => $check));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row !== false ) {
    $_SESSION['name'] = $row['name'];
    $_SESSION['user_id'] = $row['user_id'];
    $_SESSION['success']="Login Success";
    // Redirect the browser to index.php
    header("Location: index.php");
    return;
}
else{
    $_SESSION['error']="Incorrect Email or Password";
    header("Location: login.php");
    return;
}
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Mahmoud Ahmed Mahmoud</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Please Log In</h1>
        <form method="post" action="login.php">
            <label for="email">Email</label>
            <input type="text" name="email" id="email"><br>
            <label for="id_1723">Password</label>
            <input type="password" name="pass" id="id_1723"><br><br>
            <input type="submit" value="Log In" onclick="return Validate();">
            <input type="submit" value="Cancel" name="cancel">
        </form>
        <p>
            For a password hint, view source and find an account and password hint
            in the HTML comments
        </p>
        <p id="error"></p>
    </div>
<script>
    function  Validate() {
        console.log("validating....");
        try{
            var mail=document.getElementById('email').value;
            var pass=document.getElementById('id_1723').value;
            console.log("validating email="+mail+",password="+pass);
            if(mail==null||mail==""||pass==null||pass==""){
                //alert("Both fields must be filled out");
                document.getElementById('error').innerHTML="Both fields must be filled out";
                document.getElementById('error').style.color="red";
                return false;
            }
            if(mail.indexOf('@')==-1){
                //alert("Invalid Email Address");
                document.getElementById('email').style.borderColor="red";
                document.getElementById('email').value="Invalid Email";
                return  false;
            }
            return  true;
        }
        catch (e) {
            return false;
        }
        return  false;
    }
</script>
</body>
</html>