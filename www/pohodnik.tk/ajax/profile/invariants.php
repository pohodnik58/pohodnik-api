<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result=array();
$id_user = $_COOKIE["user"];
$q = $mysqli->query("SELECT `id`, `id_user`, `login`, `password`, `network` FROM `user_login_variants` WHERE id_user={$id_user} ");
while($r = $q->fetch_assoc()){
	$result[] = $r;
}

die(json_encode($result));