<?php
require_once  'pdo.php';
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
function validatePos(){
    //echo "Hello";
    for($i=1;$i<=9;$i++) {
        if (!isset($_POST['year' . $i])) continue;
        if(!isset($_POST['desc' . $i])) continue;
        $year=$_POST['year' . $i];
        $desc=$_POST['desc' . $i];
        if(strlen($year)==0||strlen($desc)==0){
            return 0;
        }
        if(!is_numeric($year)){
            return 1;
        }
    }
    return true;
}
if(isset($_POST['cancel'])){
    header('location:index.php');
    return;
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
   if(validatePos()===0){
        $_SESSION['error']="All fields are required";
        header('location:add.php');
        return;
    }
   if(validatePos()===1){
        $_SESSION['error']="Position year must be numeric";
        header('location:add.php');
        return;
    }
    else {
        $stmt = $pdo->prepare('INSERT INTO Profile(user_id,first_name,last_name,email,headline,summary) VALUES(:uid,:fn,:ln,:em,:he,:su)');
        $stmt->execute(array(
            ':uid' => $_SESSION['user_id'],
            ':fn' => $_POST['first_name'],
            ':ln' => $_POST['last_name'],
            ':em' => $_POST['email'],
            ':he' => $_POST['headline'],
            ':su' => $_POST['summary']));
        $profile_id=$pdo->lastInsertId();
        $rank=1;
        for($i=1;$i<=9;$i++){
            if (!isset($_POST['year'.$i])) continue;
            if(!isset($_POST['desc'.$i])) continue;
                $year=$_POST['year'.$i];
                $desc=$_POST['desc'.$i];
                $stmt2=$pdo->prepare('INSERT INTO position
                    (profile_id,rank,year,description) VALUES (:pid,:rank,:year,:desc)');
                $stmt2->execute(array(
                   ':pid'=>$profile_id,
                   ':rank'=>$rank,
                   ':year'=>$year,
                   ':desc'=>$desc
                ));
                $rank++;
        }
        $rank++;
        $_SESSION['success'] = "Profile added";
        header('location:index.php');
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
                <label>Position</label>
                <input type="button" value="+" id="addPos">
            </p>
                <div id="pos_field">

                </div>
                <input type="submit" value="Add">
                <input type="submit" name="cancel" value="Cancel">
        </form>
    </div>
    <!--<script src="jquery3.2.1.min.js"></script>--><script>
    var cntPos=0;
    $('#addPos').on('click',function (e) {
        if(cntPos>=9){
            alert("Maximum of nine position entries exceeded");
            console.log(cntPos);
            return;
        }
        cntPos++;
        $('#pos_field').append(
            '<div id="position'+cntPos+'">' +
            '<p>Year: <input type="text" name="year'+cntPos+'" value="">' +
            '<input type="button" value="-" onclick="$(\'#position'+cntPos+'\').remove();return false;">' +
            '</p><textarea  name="desc'+cntPos+'" rows="8" cols="80"></textarea>\</div>')
    });
</script>
</body>
</html>