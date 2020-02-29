<?php
require_once  'pdo.php';
session_start();
if(!isset($_SESSION['user_id'])){
    $_SESSION['error']="Please Login";
    header('location:index.php');
        return;
}
if(isset($_POST['cancel'])){
    header('location:index.php');
    return;
}
if(isset($_SESSION['error'])){
    echo "<h3 style='color: red;text-align: center;'>".$_SESSION['error']."</h3>";
    unset($_SESSION['error']);
}
if(isset($_POST['first_name'])&&isset($_POST['last_name'])&&isset($_POST['email'])&&isset($_POST['headline'])&&isset($_POST['summary'])){
    if(strlen($_POST['first_name'])==0||strlen($_POST['last_name'])==0||strlen($_POST['email'])==0||strlen($_POST['headline'])==0|| strlen($_POST['summary'])==0){
        $_SESSION['error']="All fields are required";
        header('location:add.php');
        return;
    }
    if(strpos($_POST['email'],'@')===false){
        $_SESSION['error']="Email address must contain @";
        header('location:add.php');
        return;
    }
    $stmt=$pdo->prepare('INSERT INTO Profile(user_id,first_name,last_name,email,headline,summary) VALUES(:uid,:fn,:ln,:em,:he,:su)');
    $stmt->execute(array(
        ':uid'=>$_SESSION['user_id'],
        ':fn'=>$_POST['first_name'],
        ':ln'=>$_POST['last_name'],
        ':em'=>$_POST['email'],
        ':he'=>$_POST['headline'],
        ':su'=>$_POST['summary']));
    $_SESSION['success']="Profile added";
    header('location:index.php');
    return;
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
        <h1>Adding Profile for <?php echo $_SESSION['name']?></h1>
        <form method="post" action="add.php">
            <label for="f_name">First Name:</label>
            <input type="text" id="f_name" name="first_name" size="60"><br>
            <label for="l_name">Last Name:</label>
            <input type="text" id="l_name" name="last_name" size="60"><br>
            <label for="e_mail">Email:</label>
            <input type="text" id="e_mail" name="email" size="40"><br>
            <p>
            <label for="head_line">HeadLine:</label>
            <input type="text" id="head_line" name="headline" size="60"></p>
            <p>Summary</p>
            <textarea name="summary" id="sm" rows="8" cols="80"></textarea><br><br>
            <input type="submit" value="Add">
            <input type="submit" name="cancel" value="Cancel">
        </form>
    </div>
</body>
</html>

