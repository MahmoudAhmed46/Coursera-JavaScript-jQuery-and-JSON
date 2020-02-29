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
    return;
}
if(isset($_POST['Delete'])&&isset($_POST['profile_id'])){
    $sql="DELETE FROM Profile WHERE profile_id= :id";
    $stmt=$pdo->prepare($sql);
    $stmt->execute(array(":id"=>$_POST['profile_id']));
    $_SESSION['success']="Record Deleted";
    //header("Refresh:3;url=index.php");
    header("location:index.php");
    return;
}
if ( ! isset($_GET['profile_id']) ) {
    $_SESSION['error'] = "Missing user_id";
    header('Location: index.php');
    return;
}
$stmt = $pdo->prepare("SELECT first_name, last_name FROM Profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for profile id';
    header('Location: index.php');
    return;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Delete profile</title>
</head>
<body>
<div class="container">
    <p>First Name: <?php echo($row['first_name']); ?></p>
    <p>Last Name: <?php echo($row['last_name']); ?></p>
    <form method="post">
        <input type="hidden" name="profile_id" value="<?php echo ($_GET['profile_id'])?>">
        <input type="submit" name="Delete" value="Delete">
        <input type="submit" name="cancel" value="cancel">
    </form>
</div>
</body>
</html>