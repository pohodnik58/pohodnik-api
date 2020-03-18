<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
include("../../blocks/dates.php"); //Только для авторизованных
$result = array();
$id_user = $_COOKIE["user"];
$id_travel = $_GET["id_travel"];

$q = $mysqli->query("SELECT `id`, `id_travel`, `order_item`, `note`, `lat`, `lon`, UNIX_TIMESTAMP(date)+{$time_offset} AS date FROM `blog_travel_notes` WHERE id_travel={$id_travel} ORDER BY date");

if(!$q){die(json_encode(array('error'=>$mysqli->error)));}
while($r = $q->fetch_assoc()){
	$result[] = $r;
}
die(json_encode($result));