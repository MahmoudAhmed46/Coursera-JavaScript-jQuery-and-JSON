<?php 
session_start();
require_once "pdo.php";
require_once 'util.php';
require_once "head.php";
if (!isset($_GET['profile_id'])) {
    $_SESSION['error'] = "Missing Profile_id";
    header('Location: index.php');
    return;
}
$row=loadProfile($pdo,$_GET['profile_id']);
$res=loadPos($pdo,$_GET['profile_id']);
$eduRows=loadEdu($pdo,$_GET['profile_id']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Page</title>
    <!--<link rel="stylesheet" href="css/style.css">-->
</head>
<body>
<div class="container">
    <h1>Profile information</h1>
    <p>First Name: <?php echo($row['first_name']); ?></p>
    <p>Last Name: <?php echo($row['last_name']); ?></p>
    <p>Email: <?php echo($row['email']); ?></p>
    <p>Headline:<br/> <?php echo($row['headline']); ?></p>
    <p>Summary: <br/><?php echo($row['summary']); ?></p>
    <p>Education:</p>
    <ul>
        <?php
            foreach ($eduRows as $r){
                echo '<li>'.$r['year'].':'.$r['name'].'</li>';
            }
        ?>
    </ul>
    <p>Position:</p>
    <ul>
        <?php
            foreach ($res as $v){
                echo '<li>'.$v['year'].':'.$v['description'].'</li>';
            }
        ?>
    </ul>
    <a href="index.php">Done</a>
</div>
</body>
</html>