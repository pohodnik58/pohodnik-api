<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();
$id = intval($_POST['id']);
$id_user = $_COOKIE["user"];
$q = $mysqli->query("SELECT id_route FROM route_editors WHERE id={$id} LIMIT 1");
if($q && $q->num_rows === 1){
	$r = $q->fetch_row();
	$id_route = $r[0];
	$q = $mysqli->query("SELECT id FROM routes WHERE id_author={$id_user} AND id={$id_route} LIMIT 1");
	if($q && $q->num_rows === 1){
		
		if($mysqli->query("DELETE FROM route_editors WHERE id={$id}")){
			die(json_encode(array("success"=>true)));
		} else {
			die(json_encode(array("error"=>"Ошибка удаления.".$mysqli->error)));
		}
		
	} else {
		die(json_encode(array("error"=>"Доступ только у создателя маршрута.".$mysqli->error)));
	}
} else {
	die(json_encode(array("error"=>"Проблема с определением маршрута.".$mysqli->error)));
}