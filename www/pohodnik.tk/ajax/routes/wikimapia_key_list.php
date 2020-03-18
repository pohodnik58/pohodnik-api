<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();
$id_user = $_COOKIE["user"];
$q = $mysqli->query("SELECT * FROM `user_wikimapia_keys` WHERE `id_user`={$id_user} ORDER BY date DESC");
if($q){
	while($r=$q->fetch_assoc()){
		$result[]=$r;
	}
	die(json_encode($result));
} else {
	die(json_encode(array("error"=>"Ошибка.".$mysqli->error)));
}