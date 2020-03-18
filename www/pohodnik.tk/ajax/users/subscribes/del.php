<?php
	include('../../../blocks/db.php');
	include("../../../blocks/for_auth.php"); //Только для авторизованных
	$id_user = $_COOKIE["user"];
	$id = intval($_POST['id']);

	$q = $mysqli->query("DELETE FROM user_subscribes WHERE id={$id} AND id_user={$id_user}");
	if(!$q){die(json_encode(array("error"=>$mysqli->error)));}

	$q = $mysqli->query("DELETE FROM user_subscribes_regions WHERE id_subs={$id}");
	$q = $mysqli->query("DELETE FROM 	user_subscribes_types WHERE id_subs={$id}");

	die(json_encode(array("success"=>true)));
