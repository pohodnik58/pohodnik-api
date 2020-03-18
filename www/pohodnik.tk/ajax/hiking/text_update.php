<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$id = intval($_POST['id']);
$text = $mysqli->real_escape_string($_POST['text']);
$id_user = $_COOKIE["user"];
$q = $mysqli->query("UPDATE hiking SET `text`='".$text."' WHERE id = {$id} LIMIT 1");
if($q){
	exit(json_encode(array("success"=>true)));
}else{exit(json_encode(array("error"=>"Не могу получить текст\r\n".$mysqli->error)));}

?>