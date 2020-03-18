<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();
$id = intval($_POST['id']);
$id_user = $_COOKIE["user"];

$q = $mysqli->query("SELECT id FROM hiking WHERE id_author={$id_user} LIMIT 1");
if(!$q || $q->num_rows===0){ die( json_encode(array("error"=>"Access Denied! ".$mysqli->error))); }

$q = $mysqli->query("SELECT id_hiking FROM hiking_editors WHERE id={$id} LIMIT 1");
if($q && $q->num_rows === 1){
	$r = $q->fetch_row();
	$id_hiking = $r[0];
	$q = $mysqli->query("SELECT id FROM hiking WHERE id_author={$id_user} AND id={$id_hiking} LIMIT 1");
	if($q && $q->num_rows === 1){
		
		if($mysqli->query("DELETE FROM hiking_editors WHERE id={$id}")){
			die(json_encode(array("success"=>true)));
		} else {
			die(json_encode(array("error"=>"Ошибка удаления.".$mysqli->error)));
		}
		
	} else {
		die(json_encode(array("error"=>"Доступ только у создателя hiking.".$mysqli->error)));
	}
} else {
	die(json_encode(array("error"=>"Проблема с определением hiking.".$mysqli->error)));
}