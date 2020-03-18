<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();
$id = intval($_POST['id']);
$id_hiking = intval($_POST['id_hiking']);

$id_user = $_COOKIE["user"];
$q = $mysqli->query("SELECT id FROM  hiking WHERE  id_author={$id_user} AND id={$id_hiking} LIMIT 1");
if($q && $q->num_rows===1){
	$q = $mysqli->query("DELETE FROM hiking_keypoints WHERE id={$id}");
	if($q){
		$result['success'] = true;
		die(json_encode($result));
	} else {
		die(json_encode(array("error"=>"Error ".$mysqli->error. " \r\n")));
	}
	
} else {
	die(json_encode(array("error"=>"Access denied. ".$mysqli->error)));
}