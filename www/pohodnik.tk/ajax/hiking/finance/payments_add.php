<?php

include("../../../blocks/db.php"); //подключение к БД
include("../../../blocks/for_auth.php"); //Только для авторизованных
include("../../../blocks/err.php"); //Только для авторизованных
$id_user = isset($_POST['id_user'])?intval($_POST['id_user']):$_COOKIE["user"];
$id_author = $_COOKIE["user"];
$id_hiking = $_POST['id_hiking'];
if(!($id_hiking>0)){die(json_encode(array("error"=>"id_hiking is undefined")));}
if($id_user != $id_author){
	$q = $mysqli->query("SELECT id FROM hiking WHERE id={$id_hiking}  AND id_author = {$id_author} LIMIT 1");
	if($q && $q->num_rows===0){
		$q = $mysqli->query("SELECT id FROM hiking_editors WHERE id_hiking={$id_hiking}  AND is_financier=1  AND id_user = {$id_author} LIMIT 1");
		if($q && $q->num_rows===0){
			die(json_encode(array("error"=>"Нет доступа")));
		}
	}
}


$summ = floatval($_POST['summ']);
$comment = isset($_POST['comment']) && !empty($_POST['comment'])
		? $mysqli->real_escape_string($_POST['comment'])
		: "";
$date = isset($_POST['date']) && !empty($_POST['date'])
		? $mysqli->real_escape_string($_POST['date'])
		: date("Y-m-d");		

$q=$mysqli->query("INSERT INTO `hiking_finance_payment` SET `comment`='{$comment}', `date`='{$date}',`id_user`={$id_user},`id_author`={$id_author},`id_hiking`={$id_hiking}, date_create=NOW(), total={$summ}");
if(!$q){exit(json_encode(array("error"=>"Ошибка\r\n".$mysqli->error)));}
if($q){
	exit(json_encode(array(
		"success"=>true,
		"id" => $mysqli->insert_id
	)));
} else {

}
			


if(!$q){exit(json_encode(array("error"=>"Ошибка при . \r\n")));}
echo(json_encode(array("success"=>true, "msg"=>"Данные успешно обновлены. \r\n")));
?>