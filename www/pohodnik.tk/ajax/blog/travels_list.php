<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
include("../../blocks/dates.php"); //Только для авторизованных
$result = array();
$id_user = $_COOKIE["user"];
$wh = "";
if(isset($_GET['id'])){
	$wh = " AND id=".$_GET['id'];
}
$q = $mysqli->query("SELECT `id`,`name`, `description`, UNIX_TIMESTAMP(date)+{$time_offset} AS date FROM `blog_travels` WHERE `id_user` ={$id_user} {$wh} ORDER BY date desc");
while($r = $q->fetch_assoc()){
	$result[] = $r;
}

die(json_encode($result));