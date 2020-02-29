<?php
require_once  'pdo.php';
require_once 'util.php';
require_once 'head.php';
session_start();
if(!isset($_SESSION['user_id'])){
    $_SESSION['error']="Please Login";
    header('location:index.php');
        return;
}
if(isset($_SESSION['error'])){
    echo "<h3 style='color: red;text-align: center;'>".$_SESSION['error']."</h3>";
    unset($_SESSION['error']);
}

if(isset($_POST['cancel'])){
    header('location:index.php');
    return;
}
if(isset($_POST['first_name'])&&isset($_POST['last_name'])&&isset($_POST['email'])&&isset($_POST['headline'])&&isset($_POST['summary'])){
    $msg=validateProfile();
    if(is_string($msg)){
        $_SESSION['error']=$msg;
        header('location:add.php');
        return;
    }
    $msg=validatePos();
    if(is_string($msg)){
        $_SESSION['error']=$msg;
        header('location:add.php');
        return;
    }
   $msg=validateEdu();
   if(is_string($msg)){
       $_SESSION['error']=$msg;
       header('location:add.php');
       return;
   }
        $stmt = $pdo->prepare('INSERT INTO Profile(user_id,first_name,last_name,email,headline,summary) VALUES(:uid,:fn,:ln,:em,:he,:su)');
        $stmt->execute(array(
            ':uid' => $_SESSION['user_id'],
            ':fn' => $_POST['first_name'],
            ':ln' => $_POST['last_name'],
            ':em' => $_POST['email'],
            ':he' => $_POST['headline'],
            ':su' => $_POST['summary']));
        $profile_id=$pdo->lastInsertId();
        insertPosition($pdo,$profile_id);
        insertEducation($pdo,$profile_id);
        $_SESSION['success'] = "Profile added";
        header('location:index.php');
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
            <p>
                <label>Education:</label>
                <input type="button" value="+" id="addEdu">
            <div id="edu_field">
            </div>
            </p>
            <p>
                <label>Position</label>
                <input type="button" value="+" id="addPos">
                <div id="pos_field">
                </div>
            </p>
                <input type="submit" value="Add">
                <input type="submit" name="cancel" value="Cancel">
        </form>
    </div>
    <!--<script src="jquery3.2.1.min.js"></script>-->
    <script>
    var cntPos=0;
    var cntEdu=0;
    $('#addEdu').on('click',function (e) {
        if(cntEdu>=9){
            alert("Maximum of nine education entries exceeded");
            console.log(cntPos);
            return;
        }
        cntEdu++;
        console.log("Adding Education"+cntEdu);
        $('#edu_field').append(
            '<div id="education'+cntEdu+'">' +
            '<p>Year: <input type="text" name="edu_year'+cntEdu+'" value="">' +
            '<input type="button" value="-" onclick="$(\'#education'+cntEdu+'\').remove();return false;"></p>' +
            '<p>School:<input type="text" size="80" name="edu_school'+cntEdu+'" class="school" value=""></p></div>')
        $('.school').autocomplete({source:"school.php"});
    });

    $('#addPos').on('click',function (e) {
        if(cntPos>=9){
            alert("Maximum of nine position entries exceeded");
            console.log(cntPos);
            return;
        }
        cntPos++;
        console.log("Adding Position"+cntPos);
        $('#pos_field').append(
            '<div id="position'+cntPos+'">' +
            '<p>Year: <input type="text" name="year'+cntPos+'" value="">' +
            '<input type="button" value="-" onclick="$(\'#position'+cntPos+'\').remove();return false;">' +
            '</p><textarea  name="desc'+cntPos+'" rows="8" cols="80"></textarea>\</div>')
    });
</script>
</body>
</html>