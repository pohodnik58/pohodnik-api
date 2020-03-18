<?php
	include('../../../blocks/db.php');
	include("../../../blocks/for_auth.php"); //Только для авторизованных
	$id_user = $_COOKIE["user"];
	$code = $mysqli->real_escape_string($_POST['code']);
	$q = $mysqli->query("SELECT `id`, `id_user`,`confirm_code`, UNIX_TIMESTAMP(`confirm_date`) AS uts FROM `user_subscribes` WHERE id_user={$id_user} AND 	confirm_code='{$code}' LIMIT 1");
	if(!$q){die(json_encode(array("error"=>$mysqli->error)));}
	if($q->num_rows===0){die(json_encode(array("error"=>"Ссылка недействительна, код не найден.")));}
	$r = $q->fetch_assoc();
	if($r['uts']>0){die(json_encode(array("error"=>"Рассылка уже подтверждена")));}
	$q = $mysqli->query("UPDATE user_subscribes SET confirm_date=NOW(), is_active=1");
	if(!$q){die(json_encode(array("error"=>$mysqli->error)));}

	
die(json_encode(array("success"=>true)));

?>