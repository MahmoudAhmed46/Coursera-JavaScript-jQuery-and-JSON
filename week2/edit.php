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
if (!isset($_REQUEST['profile_id'])) {
    $_SESSION['error'] = "Missing profile_id";
    header('Location: index.php');
    return;
}
function validatePos(){
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
if(isset($_POST['first_name'])&&isset($_POST['last_name'])&&isset($_POST['email'])&&isset($_POST['headline'])&&isset($_POST['summary'])){
    if(strlen($_POST['first_name'])==0||strlen($_POST['last_name'])==0||strlen($_POST['email'])==0||strlen($_POST['headline'])==0|| strlen($_POST['summary'])==0){
        $_SESSION['error']="All fields are required";
    }
    else if(strpos($_POST['email'],'@')===false){
        $_SESSION['error']="Email address must contain @";
    }
   else if(validatePos()===0){
        $_SESSION['error']="All fields are required";
        //header('location:add.php?profile_id='.$_REQUEST["profile_id"]);
        //return;
    }
   else if(validatePos()===1){
        $_SESSION['error']="Position year must be numeric";
        //header('location:add.php?profile_id='.$_REQUEST["profile_id"]);
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
    //$_SESSION['success']="Record updated";
        $stmt = $pdo->prepare('DELETE FROM position where profile_id = :p_id');
        $stmt->execute(array(":p_id" => $_REQUEST['profile_id']));
        $rank=1;
        for($i=1;$i<=9;$i++){
            if (!isset($_POST['year' . $i])) continue;
            if (!isset($_POST['desc' . $i])) continue;
            $year = $_POST['year' . $i];
            $desc = $_POST['desc' . $i];
            $stmt = $pdo->prepare('INSERT INTO position
    (profile_id, rank, year, description)
    VALUES (:pid,:rank,:year,:desc)');
            $stmt->execute(array(
                    ':pid' => $_REQUEST['profile_id'],
                    ':rank' => $rank,
                    ':year' => $year,
                    ':desc' => $desc)
            );
            $rank++;
        }
        $_SESSION['success'] = 'Profile updated';
        header('Location: index.php');
        return;
   }
}
$stmt2 = $pdo->prepare("SELECT * FROM profile where profile_id = :id");
$stmt2->execute(array(":id" => $_REQUEST['profile_id']));
$profile = $stmt2->fetch(PDO::FETCH_ASSOC);
if ($profile === false) {
    $_SESSION['error'] = 'Bad value for user_id';
    header('Location: index.php');
    return;
}
$stmt = $pdo->prepare("SELECT * FROM position where profile_id = :xyz ORDER BY rank");
$stmt->execute(array(":xyz" => $_REQUEST['profile_id']));
$rowPos=array();
while($row1=$stmt->fetch(PDO::FETCH_ASSOC)){
    $rowPos[]=$row1;
}
//$rowPos = $stmt->fetchAll();

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
        <h1>Editing Profile for <?php echo $_SESSION['name']?></h1>
        <?php
        if (isset($_SESSION['error'])) {
        echo('<p style="color: red;">' . htmlentities($_SESSION['error']) . "</p>\n");
        unset($_SESSION['error']);
        }
       ?>
        <form method="post">
            <label for="f_name">First Name:</label>
            <input type="text" id="f_name" name="first_name" size="60" value="<?php echo $profile['first_name']?>"><br>
            <label for="l_name">Last Name:</label>
            <input type="text" id="l_name" name="last_name" size="60" value="<?php echo $profile['last_name']?>"><br>
            <label for="e_mail">Email:</label>
            <input type="text" id="e_mail" name="email" size="40" value="<?php echo $profile['email']?>"><br>
            <p>
            <label for="head_line">HeadLine:</label>
            <input type="text" id="head_line" name="headline" size="60" value="<?php echo $profile['headline']?>"></p>
            <p>Summary</p>
            <textarea name="summary" id="sm" rows="8" cols="80"><?php echo $profile['summary']?></textarea><br><br>
            <p>
                <label>Position</label>
                <input type="button" value="+" id="addPos">
            </p>
            <div id="pos_field">
                <?php
                $rank = 1;
                foreach ($rowPos as $row) {
                    echo "<div id=\"position" . $row['rank'] . "\">
                            <p>Year: <input type=\"text\" name=\"year1\" value=\"".$row['year']."\">
                            <input type=\"button\" value=\"-\" onclick=\"$('#position". $row['rank'] ."').remove();return false;\"></p>
                            <textarea name=\"desc". $rank ."\"').\" rows=\"8\" cols=\"80\">".$row['description']."</textarea>
                          </div>";
                    $rank++;
                }
                ?>
            </div>
            <input type="submit" value="Save">
            <input type="submit" name="cancel" value="Cancel">
        </form>
    </div>
    <!--<script src="jquery3.2.1.min.js"></script>-->
    <script>
         var cntPos=1;
        $('#addPos').on('click',function (e) {
            if(cntPos>=9){
                alert("Maximum of nine position entries exceeded");
                console.log(cntPos);
                return;
            }

            $('#pos_field').append(
                '<div id="position'+cntPos+'">' +
                '<p>Year: <input type="text" name="year'+cntPos+'" value="">' +
                '<input type="button" value="-" onclick="$(\'#position'+cntPos+'\').remove();return false;">' +
                '</p><textarea  name="desc'+cntPos+'" rows="8" cols="80"></textarea>\</div>')
            cntPos++;
        });
    </script>
</body>
</html>