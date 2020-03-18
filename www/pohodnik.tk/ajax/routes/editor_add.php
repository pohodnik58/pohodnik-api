<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();
$id_route = intval($_POST['id_route']);
$id_editor = intval($_POST['id_editor']);

$id_user = $_COOKIE["user"];


$q = $mysqli->query("SELECT id FROM  route_editors WHERE  id_route={$id_route} AND id_user={$id_editor}");
if($q && $q->num_rows>0){
	die(json_encode(array("error"=>"Уже добавлен.".$mysqli->error)));
}

	$q = $mysqli->query("SELECT id FROM routes WHERE id_author={$id_user} AND id={$id_route} LIMIT 1");
	if($q && $q->num_rows === 1){
		
		if($mysqli->query("INSERT INTO route_editors SET id_route={$id_route}, id_user={$id_editor}")){
			die(json_encode(array("success"=>true, "id"=>$mysqli->insert_id)));
		} else {
			die(json_encode(array("error"=>"Ошибка добавления.".$mysqli->error)));
		}
		
	} else {
		die(json_encode(array("error"=>"Доступ только у создателя маршрута.".$mysqli->error)));
	}
