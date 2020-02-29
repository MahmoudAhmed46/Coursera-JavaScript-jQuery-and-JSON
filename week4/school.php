<?php
require_once  'pdo.php';
$stmt=$pdo->prepare('SELECT name FROM Institution WHERE name LIKE :prefix');
$stmt->execute(array(':prefix'=>$_REQUEST['term']."%"));
$res=array();
while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
    $res[]=$row['name'];
}
echo(json_encode($res, JSON_PRETTY_PRINT));
