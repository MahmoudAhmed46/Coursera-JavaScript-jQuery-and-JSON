<?php
require_once 'pdo.php';
require_once 'util.php';
require_once 'head.php';
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
if(isset($_POST['first_name'])&&isset($_POST['last_name'])&&isset($_POST['email'])&&isset($_POST['headline'])&&isset($_POST['summary'])){
    $msg=validateProfile();
    if(is_string($msg)){
        $_SESSION['error']=$msg;
        header('location:edit.php?profile_id='.$_REQUEST["profile_id"]);
        return;
    }
    $msg=validatePos();
    if(is_string($msg)){
       $_SESSION['error']=$msg;
       header('location:edit.php?profile_id='.$_REQUEST["profile_id"]);
       return;
   }
    $msg=validateEdu();
    if(is_string($msg)){
        $_SESSION['error']=$msg;
        header('location:edit.php?profile_id='.$_REQUEST["profile_id"]);
        return;
    }
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
        $stmt = $pdo->prepare('DELETE FROM position where profile_id = :p_id');
        $stmt->execute(array(":p_id" => $_REQUEST['profile_id']));

        insertPosition($pdo,$_REQUEST['profile_id']);

        $stmt = $pdo->prepare('DELETE FROM Education where profile_id = :p_id');
        $stmt->execute(array(":p_id" => $_REQUEST['profile_id']));

        insertEducation($pdo,$_REQUEST['profile_id']);

        $_SESSION['success'] = 'Profile updated';
        header('Location: index.php');
        return;
}
$profile = loadProfile($pdo,$_REQUEST['profile_id']);
$rowPos=loadPos($pdo,$_REQUEST['profile_id']);
$schools=loadEdu($pdo,$_REQUEST['profile_id']);

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
        if(isset($_SESSION['error'])){
            echo "<h3 style='color: red;text-align: center;'>".$_SESSION['error']."</h3>";
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
            <?php

                $countEdu = 0;

                echo('<p>Education: <input type="button" id="addEdu" value="+">' . "\n");
                echo('<div id="edu_field">');
                if (count($schools) > 0) {
                    foreach ($schools as $school) {
                        $countEdu++;
                        echo('<div id="education' . $countEdu . '">');
                        echo
                            '<p>Year: <input type="text" name="edu_year' . $countEdu . '" value="' . $school['year'] . '">
                            
<input type="button" value="-" onclick="$(\'#education' . $countEdu . '\').remove();return false;"></p>
<p>School: <input type="text" size="80" name="edu_school' . $countEdu . '" class="school" 
value="' . htmlentities($school['name']) . '" >';
                        echo "\n</div>\n";
                    }

                }
                echo "</div></p>\n";

                $countPos = 0;

                echo('<p>Position: <input type="button" id="addPos" value="+">' . "\n");
                echo('<div id="pos_field">');
                if (count($rowPos) > 0) {
                    foreach ($rowPos as $position) {
                        $countPos++;
                        echo('<div  id="position' . $countPos . '">');
                        echo
                            '<br>Year: <input type="text" name="year' . $countPos . '" value="' . htmlentities($position['year']) . '">
<input type="button" value="-" onclick="$(\'#position' . $countPos . '\').remove();return false;"><br>';
                        echo '<textarea name="desc' . $countPos . '"rows="8" cols="80">' . "\n";
                        echo htmlentities($position['description']) . "\n";
                        echo "\n</textarea>\n</div>\n";

                    }

                }
                ?>
             </div>
            <input type="submit" value="Save">
            <input type="submit" name="cancel" value="Cancel">
        </form>
    </div>
    <!--<script src="jquery3.2.1.min.js"></script>-->
    <script>
         var cntPos=<?=$countPos?>;
         var cntEdu=<?=$countEdu?>;
         console.log(cntPos);
         console.log(cntEdu);
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