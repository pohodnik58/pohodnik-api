<?php
	include('../../../blocks/db.php');
	include("../../../blocks/for_auth.php"); //Только для авторизованных
	$id_user = $_COOKIE["user"];
	$id = intval($_POST['id']);
	$value = intval($_POST['value']);

	$q = $mysqli->query("UPDATE user_subscribes SET is_active={$value} WHERE id={$id} AND id_user={$id_user}");
	if(!$q){die(json_encode(array("error"=>$mysqli->error)));}
	die(json_encode(array("success"=>true)));
