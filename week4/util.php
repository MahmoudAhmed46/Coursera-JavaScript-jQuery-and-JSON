<?php
function validateProfile(){
    if(strlen($_POST['first_name'])==0||strlen($_POST['last_name'])==0||strlen($_POST['email'])==0||strlen($_POST['headline'])==0|| strlen($_POST['summary'])==0){
        return "All fields are required";
    }
     if(strpos($_POST['email'],'@')===false){
        return "Email address must contain @";
    }
}
function validatePos(){
    for($i=1;$i<=9;$i++) {
        if (!isset($_POST['year' . $i])) continue;
        if(!isset($_POST['desc' . $i])) continue;
        $year=$_POST['year' . $i];
        $desc=$_POST['desc' . $i];
        if(strlen($year)==0||strlen($desc)==0){
            return "All fields are required";
        }
        if(!is_numeric($year)){
            return "Position year must be numeric";
        }
    }
    return true;
}
function validateEdu(){
    for($i=1;$i<=9;$i++) {
        if (!isset($_POST['edu_year' . $i])) continue;
        if(!isset($_POST['edu_school' . $i])) continue;
        $year=$_POST['edu_year' . $i];
        $school=$_POST['edu_school' . $i];
        if(strlen($year)==0||strlen($school)==0){
            return "All fields are required";
        }
        if(!is_numeric($year)){
            return "year must be numeric";
        }
    }
    return true;
}
function loadProfile($pdo,$profile_id)
{
    $stmt = $pdo->prepare("SELECT * FROM Profile where profile_id = :xyz");
    $stmt->execute(array(":xyz" => $profile_id));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row;
}
function loadPos($pdo,$profile_id)
{
    $positions=$pdo->prepare('SELECT * FROM position WHERE profile_id=:pid ORDER BY rank');
    $positions->execute(array(':pid'=>$profile_id));
    $res=$positions->fetchAll(PDO::FETCH_ASSOC);
    return $res;
}
function loadEdu($pdo,$profile_id)
{
    $stmt=$pdo->prepare('SELECT name,year FROM Education e,Institution i WHERE e.institution_id=i.institution_id AND profile_id=:pid ORDER BY rank');
    $stmt->execute(array('pid'=>$profile_id));
    $rows=$stmt->fetchAll(PDO::FETCH_ASSOC);
    return $rows;
}
function insertPosition($pdo,$profile_id){
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
}
function insertEducation($pdo,$profile_id){
    $rank=1;
    for($i=1;$i<=9;$i++){
        if(!isset($_POST['edu_year'.$i]))continue;
        if(!isset($_POST['edu_school'.$i]))continue;
        $edu_year=$_POST['edu_year'.$i];
        $edu_school=$_POST['edu_school'.$i];
        $stmt=$pdo->prepare('SELECT institution_id FROM Institution WHERE name=:name');
        $stmt->execute(array(':name'=>$edu_school));
        $row=$stmt->fetch(PDO::FETCH_ASSOC);
        if($row){
            $institution_id=$row['institution_id'];
        }
        else{
            $stmt=$pdo->prepare('INSERT INTO Institution (name) VALUES (:name)');
            $stmt->execute(array(':name'=>$edu_school));
            $institution_id=$pdo->lastInsertId();
        }
        $stmt=$pdo->prepare('INSERT INTO Education (profile_id,institution_id,rank,year) VALUES(:pid,:iid,:rank,:year)');
        $stmt->execute(array(
            ':pid'=>$profile_id,
            ':iid'=>$institution_id,
            ':rank'=>$rank,
            ':year'=>$edu_year
        ));
        $rank++;
    }
}