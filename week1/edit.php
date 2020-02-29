<?php
require_once 'pdo.php';
session_start();
if ( ! isset($_SESSION['user_id']) ) {
    $_SESSION['error'] = "Please Login";
    header('Location: index.php');
    return;
}
if(isset($_POST['cancel'])){
    header('Location: index.php');
    unset($_SESSION['success']);
    return;
}
if(isset($_SESSION['error'])){
    //echo "I'm here in Edit page";
    echo "<h3 style='color: red;text-align: center;'>".$_SESSION['error']."</h3>";
    unset($_SESSION['error']);
    //header('Location: edit.php');
    //return;
}
if(isset($_POST['first_name'])&&isset($_POST['last_name'])&&isset($_POST['email'])&&isset($_POST['headline'])&&isset($_POST['summary'])){
    if(strlen($_POST['first_name'])==0||strlen($_POST['last_name'])==0||strlen($_POST['email'])==0||strlen($_POST['headline'])==0|| strlen($_POST['summary'])==0){
        $_SESSION['error']="All fields are required";
        //header('location:edit.php');
        //return;
    }
    else if(strpos($_POST['email'],'@')===false){
        $_SESSION['error']="Email address must contain @";
        //header('location:edit.php');
        //return;
    }
    else{
    $sql="UPDATE Profile SET first_name=:first_name,last_name=:last_name,email=:email,headline=:headline,summary=:summary WHERE profile_id=:profile_id";
    $stmt=$pdo->prepare($sql);
    $stmt->execute(array(
    ':first_name'=>$_POST['first_name'],
    ':last_name'=>$_POST['last_name'],
    ':email'=>$_POST['email'],
    ':headline'=>$_POST['headline'],
    ':summary'=>$_POST['summary'],
    ':profile_id'=>$_GET['profile_id']
    ));
    $_SESSION['success']="Record updated";
    header("location:index.php");
    return;
}
}
if (!isset($_GET['profile_id'])) {
    $_SESSION['error'] = "Missing profile_id";
    header('Location: index.php');
    return;
}
$stmt2 = $pdo->prepare("SELECT * FROM Profile where profile_id = :id");
$stmt2->execute(array(":id" => $_GET['profile_id']));
$row = $stmt2->fetch(PDO::FETCH_ASSOC);
if ($row === false) {
    $_SESSION['error'] = 'Bad value for user_id';
    header('Location: index.php');
    return;
}
?>
<!DOCTYPE html>
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
        <h1>Editing Profile for <?php echo $_SESSION['name']?></h1>
        <?php
        if (isset($_SESSION['error'])) {
        echo('<p style="color: red;">' . htmlentities($_SESSION['error']) . "</p>\n");
        unset($_SESSION['error']);
        }
       ?>
        <form method="post">
            <label for="f_name">First Name:</label>
            <input type="text" id="f_name" name="first_name" size="60" value="<?php echo $row['first_name']?>"><br>
            <label for="l_name">Last Name:</label>
            <input type="text" id="l_name" name="last_name" size="60" value="<?php echo $row['last_name']?>"><br>
            <label for="e_mail">Email:</label>
            <input type="text" id="e_mail" name="email" size="40" value="<?php echo $row['email']?>"><br>
            <p>
            <label for="head_line">HeadLine:</label>
            <input type="text" id="head_line" name="headline" size="60" value="<?php echo $row['headline']?>"></p>
            <p>Summary</p>
            <textarea name="summary" id="sm" rows="8" cols="80"><?php echo $row['summary']?></textarea><br><br>
            <input type="submit" value="Save">
            <input type="submit" name="cancel" value="Cancel">
        </form>
    </div>
</body>
</html>