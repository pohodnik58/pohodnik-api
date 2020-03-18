<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();
$api_key = $mysqli->real_escape_string($_POST['key']);
$id_user = $_COOKIE["user"];
$q = $mysqli->query("INSERT INTO `user_wikimapia_keys`(`id_user`, `api_key`, `date`) VALUES ({$id_user},'{$api_key}',NOW())");
if($q){
	die(json_encode(array("success"=>true, "id"=>$mysqli->insert_id)));
} else {
	die(json_encode(array("error"=>"Ошибка добавления.".$mysqli->error)));
}