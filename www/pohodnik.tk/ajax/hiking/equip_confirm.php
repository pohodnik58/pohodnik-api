<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
$id = intval($_POST['id']);
$id_hiking = intval($_POST['id_hiking']);
$confirm = intval($_POST['confirm']);
$id_user = $_COOKIE["user"];

$q = $mysqli->query("SELECT id FROM hiking WHERE id_author={$id_user} AND id={$id_hiking}");
if(!$q || $q->num_rows===0){
	$q = $mysqli->query("SELECT id FROM hiking_editors WHERE id_user={$id_user} AND id_hiking={$id_hiking}");
	if(!$q || $q->num_rows===0){
		die(json_encode(array("error"=>"Нет доступа . \r\n".$mysqli->error)));
	}
}


	if($mysqli->query("UPDATE `hiking_equipment` SET 
						`is_confirm`='{$confirm}'
					   WHERE id={$id}
					  ")){
		exit(json_encode(array("success"=>true, "id"=> $id)));
	}else{exit(json_encode(array("error"=>"Ошибка обновления . \r\n".$mysqli->error)));}

?>