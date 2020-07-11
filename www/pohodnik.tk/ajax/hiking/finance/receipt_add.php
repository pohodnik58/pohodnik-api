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

$name = isset($_POST['name']) && strlen($_POST['name'])>0?$mysqli->real_escape_string(trim($_POST['name'])):"Чек от ".date('d.m.Y');

$summ = floatval($_POST['summ']);
$img_orig = $mysqli->real_escape_string($_POST['img_orig']);
$img_600 = $mysqli->real_escape_string($_POST['img_600']);
$img_100 = $mysqli->real_escape_string($_POST['img_100']);

$q=$mysqli->query("INSERT INTO `hiking_finance_receipt` SET `name`='{$name}',`date`=NOW(),`id_user`={$id_user},`id_author`={$id_author},`id_hiking`={$id_hiking},`img_600`='{$img_600}',`img_100`='{$img_100}', `img_orig`='{$img_orig}', summ={$summ}");
if(!$q){exit(json_encode(array("error"=>"Ошибка\r\n".$mysqli->error)));}
if($q){
	exit(json_encode(array(
		"success"=>true,
		"img_100"=>$img_100,
		"img_600"=>$img_600
	)));
} else {

}
			


if(!$q){exit(json_encode(array("error"=>"Ошибка при . \r\n")));}
echo(json_encode(array("success"=>true, "msg"=>"Данные успешно обновлены. \r\n")));
?>