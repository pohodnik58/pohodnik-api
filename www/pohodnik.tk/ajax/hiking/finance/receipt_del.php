<?php

	include("../../../blocks/db.php"); //подключение к БД
	include("../../../blocks/for_auth.php"); //Только для авторизованных
	$id_user = $_COOKIE["user"];

	$id = intval($_POST['id']);
	
	$q=$mysqli->query("SELECT `name`,`date`,`id_user`,`id_hiking`,`img_600`,`img_100`,`img_orig` FROM `hiking_finance_receipt` WHERE id=".$id." AND id_user=".$id_user."");
	if(!$q || $q->num_rows===0){exit(json_encode(array("error"=>"Ошибка\r\n".$mysqli->error)));}
	$r = $q->fetch_assoc();

	$q = $mysqli->query("SELECT id FROM hiking_finance WHERE id_receipt={$id} LIMIT 1");
	if($q && $q->num_rows==1){
		exit(json_encode(array( "error"=>"Используется как подтверждение расходов" )));
	}
	
	unlink('../../../'.$r['img_600']);
	unlink('../../../'.$r['img_100']);
	unlink('../../../'.$r['img_orig']);

	$q=$mysqli->query("DELETE FROM `hiking_finance_receipt` WHERE id=".$id." AND id_user=".$id_user."");
	if(!$q){exit(json_encode(array("error"=>"Ошибка\r\n".$mysqli->error)));}
	exit(json_encode(array( "success"=>true )));
		
		
?>