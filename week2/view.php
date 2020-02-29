<?php 
session_start();
require_once "pdo.php";
if (!isset($_GET['profile_id'])) {
    $_SESSION['error'] = "Missing Profile_id";
    header('Location: index.php');
    return;
}
$stmt = $pdo->prepare("SELECT * FROM Profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$positions=$pdo->prepare('SELECT * FROM position WHERE profile_id=:pid');
$positions->execute(array(':pid'=>$_GET['profile_id']));
$res=$positions->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Page</title>
    <!--<link rel="stylesheet" href="css/style.css">-->
    <link rel="stylesheet"
          href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"
          integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7"
          crossorigin="anonymous">

    <link rel="stylesheet"
          href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css"
          integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r"
          crossorigin="anonymous">

    <script
            src="https://code.jquery.com/jquery-3.2.1.js"
            integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE="
            crossorigin="anonymous"></script>
</head>
<body>

<div class="container">
    <h1>Profile information</h1>
    <p>First Name: <?php echo($row['first_name']); ?></p>
    <p>Last Name: <?php echo($row['last_name']); ?></p>
    <p>Email: <?php echo($row['email']); ?></p>
    <p>Headline:<br/> <?php echo($row['headline']); ?></p>
    <p>Summary: <br/><?php echo($row['summary']); ?></p>
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