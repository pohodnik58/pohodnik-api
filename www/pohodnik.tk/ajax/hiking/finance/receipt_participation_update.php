<?php

include("../../../blocks/db.php"); //подключение к БД
include("../../../blocks/for_auth.php"); //Только для авторизованных
include("../../../blocks/err.php"); //Только для авторизованных
include("../../../blocks/global.php"); //Только для авторизованных
$id_user = $_COOKIE["user"];
$id_receipt = intval($_POST['id_receipt']);
$id_hiking = intval($_POST['id_hiking']);
$users = $_POST['users'];

if(!($id_hiking>0)){die(json_encode(array("error"=>"id_hiking is undefined")));}
if(!is_array($users)){die(json_encode(array("error"=>"users - expect array")));}
if(!($id_receipt>0)){die(json_encode(array("error"=>"id_receipt is undefined")));}

$q = $mysqli->query("SELECT id FROM hiking WHERE id={$id_hiking}  AND id_author = {$id_user} LIMIT 1");
if($q && $q->num_rows===0){
    $q = $mysqli->query("SELECT id FROM hiking_editors WHERE id_hiking={$id_hiking}  AND is_financier=1  AND id_user = {$id_user} LIMIT 1");
    if($q && $q->num_rows===0){
        die(json_encode(array("error"=>"Нет доступа")));
    }
}

$forDelete = array();
$forInsert = array();
$alreadyExists = array();
$deletedCount = 0;
$insertedCount = 0;

$z = "SELECT * FROM hiking_finance_receipt_participation WHERE id_hiking_receipt={$id_receipt}";
$q = $mysqli->query($z);
if(!$q) { die(err($mysqli->error)); }
while($r = $q -> fetch_assoc()) {
    $alreadyExists[] = $r['id_user'];
    if (!in_array($r['id_user'], $users)){ $forDelete[] = $r['id_user']; }
}

if (count($forDelete) > 0) {
    $z = "DELETE FROM hiking_finance_receipt_participation WHERE id_hiking_receipt={$id_receipt} AND id_user IN(".implode(",", $forDelete).")";
    $q = $mysqli -> query($z);
    if(!$q) { die(err($mysqli->error, $z)); }
    
    $deletedCount = $mysqli->affected_rows;
}

foreach ($users as $user) {
    if (!in_array($user, $alreadyExists)){ $forInsert[] = $user; }
}

if (count($forInsert) > 0) {
    $ists = array();
    foreach ($forInsert as $uid) {
        $ists[] = "({$id_receipt}, {$uid}, {$id_user}, NOW())";
    }

    $z = "INSERT INTO `hiking_finance_receipt_participation`(`id_hiking_receipt`, `id_user`, `id_author`, `date_create`) VALUES ".implode(",",$ists);
    $q = $mysqli -> query($z);
    if(!$q) {die(err($mysqli->error, $z));}
    $insertedCount = $mysqli->affected_rows;
}

die(out(array(
    "success" => true,
    "inserted" => $insertedCount,
    "deleted" => $deletedCount
)));



