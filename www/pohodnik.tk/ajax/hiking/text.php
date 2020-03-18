<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$id = intval($_POST['id']);
$id_user = $_COOKIE["user"];
$q = $mysqli->query("SELECT `text` FROM `hiking` WHERE id = {$id} LIMIT 1");

if($q){
	$r = $q->fetch_row();
	echo($r[0]);
	exit();
}else{exit(json_encode(array("error"=>"Не могу получить текст\r\n".$mysqli->error)));}

?>