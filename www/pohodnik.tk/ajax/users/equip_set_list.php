<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$id_user = $_COOKIE["user"];
$res = array();
$claus = "";
if(isset($_GET['id_hiking'])){
	$claus = " AND id_hiking=".intval($_GET['id_hiking']);
}

$q = $mysqli->query("SELECT `id`, `id_user`, `name`, `description`, `date_update` FROM `user_equip_sets` WHERE id_user={$id_user} {$claus}");
if(!$q){die(json_encode(array("error"=>$mysqli->error)));}
while($r = $q->fetch_assoc()){
	$res[] = $r;
}
die(json_encode($res));