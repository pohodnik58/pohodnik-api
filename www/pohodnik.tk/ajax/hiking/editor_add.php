<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();
$id_hiking = intval($_POST['id_hiking']);
$id_editor = intval($_POST['id_editor']);

$id_user = $_COOKIE["user"];

$q = $mysqli->query("SELECT id FROM hiking WHERE id_author={$id_user} LIMIT 1");
if(!$q || $q->num_rows===0){ die( json_encode(array("error"=>"Access Denied! ".$mysqli->error))); }

$q = $mysqli->query("SELECT id FROM  hiking_editors WHERE  id_hiking={$id_hiking} AND id_user={$id_editor}");
if($q && $q->num_rows>0){
	die(json_encode(array("error"=>"Уже добавлен.".$mysqli->error)));
}

	$q = $mysqli->query("SELECT id FROM hiking WHERE id_author={$id_user} AND id={$id_hiking} LIMIT 1");
	if($q && $q->num_rows === 1){
		
		if($mysqli->query("INSERT INTO hiking_editors SET id_hiking={$id_hiking}, id_user={$id_editor}")){
			die(json_encode(array("success"=>true, "id"=>$mysqli->insert_id)));
		} else {
			die(json_encode(array("error"=>"Ошибка добавления.".$mysqli->error)));
		}
		
	} else {
		die(json_encode(array("error"=>"Доступ только у создателя похода.".$mysqli->error)));
	}
