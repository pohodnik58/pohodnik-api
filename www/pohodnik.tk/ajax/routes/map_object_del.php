<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();
$id = intval($_POST['id']);

	if($mysqli->query("DELETE FROM `route_objects` WHERE id={$id}")){

		exit(json_encode(array("success"=>"Обьект успешно удален", "id"=> $mysqli->affected_rows)));
	}else{exit(json_encode(array("error"=>"Ошибка удаления объекта. \r\n".$mysqli->error)));}

?>