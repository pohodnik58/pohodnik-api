<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
include("../../blocks/dates.php"); //Только для авторизованных

$id_hiking = intval($_POST['id_hiking']);
$id_user = intval($_POST['id_user']);
$user = $_COOKIE["user"];

if(!$id_user>0){die(json_encode(array("error"=>"User id is required")));}
if(!$id_hiking>0){die(json_encode(array("error"=>"id_hiking id is required")));}

$q = $mysqli->query("SELECT id FROM `hiking` WHERE hiking.id = {$id_hiking}  AND hiking.id_author = {$user} LIMIT 1");
if(!$q){die(json_encode(array("error"=>$mysqli->error)));}
if($q->num_rows===0){die(json_encode(array("error"=>"Доступ только у создателя похода")));}
$q = $mysqli->query("INSERT INTO `hiking_members` SET `id_hiking`={$id_hiking},`id_user`={$id_user},`date`=NOW()");
if(!$q){die(json_encode(array("error"=>$mysqli->error)));}
die(json_encode(array("success"=>true)));