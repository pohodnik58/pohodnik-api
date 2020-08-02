<?php

	include("../../../blocks/db.php"); //подключение к БД
	include("../../../blocks/for_auth.php"); //Только для авторизованных
	$id_user = $_COOKIE["user"];

	$id = intval($_POST['id']);
	$id_hiking = $_POST['id_hiking'];
	
	if(!($id_hiking>0)){die(json_encode(array("error"=>"id_hiking is undefined")));}
	if(!($id>0)){die(json_encode(array("error"=>"id is undefined")));}

	$q = $mysqli->query("SELECT id FROM hiking WHERE id={$id_hiking}  AND id_author = {$id_user} LIMIT 1");
	if($q && $q->num_rows===0){
		$q = $mysqli->query("SELECT id FROM hiking_editors WHERE id_hiking={$id_hiking}  AND is_financier=1  AND id_user = {$id_user} LIMIT 1");
		if($q && $q->num_rows===0){
			die(json_encode(array("error"=>"Нет доступа")));
		}
	}
	

	$q=$mysqli->query("DELETE FROM `hiking_finance_payment` WHERE id={$id} AND id_hiking={$id_hiking}");
	if(!$q){exit(json_encode(array("error"=>"Ошибка\r\n".$mysqli->error)));}
	exit(json_encode(array( "success"=>true, "affected" => $mysqli->affected_rows )));
		
		
?>