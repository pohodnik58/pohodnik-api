<?php
	include('../../../blocks/db.php');
	include("../../../blocks/for_auth.php"); //Только для авторизованных
	$id_user = $_COOKIE["user"];
	$q = $mysqli->query("SELECT id, unix_timestamp(confirm_date) as uts FROM `user_subscribes` WHERE id_user={$id_user} LIMIT 1");
	if(!$q){die(json_encode(array("error"=>$mysqli->error)));}
	$r = $q -> fetch_assoc();


	die(json_encode(array("count"=>$q->num_rows, "confirm"=>$r['uts']>0)));
?>