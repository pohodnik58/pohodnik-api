<?php
	include('../../../blocks/db.php');
	include("../../../blocks/for_auth.php");
	$id_subs = intva($_POST['id_subs']);
	$id_type = intva($_POST['id_type']);
	$q = $mysqli->query("DELETE FROM user_subscribes_types WHERE id_subs={$id_subs} AND id_type={$id_type}");
	if(!$q){die(json_encode(array("error"=>$mysqli->error)));}
	die(json_encode(array("success"=>true)));	