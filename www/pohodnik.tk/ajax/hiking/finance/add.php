<?php

include("../../../blocks/db.php"); //подключение к БД
include("../../../blocks/for_auth.php"); //Только для авторизованных
$id_user = isset($_POST['id_user'])?intval($_POST['id_user']):$_COOKIE["user"];
$id_author = $_COOKIE["user"];
$id_hiking = $_POST['id_hiking'];
if(!($id_hiking>0)){die(json_encode(array("error"=>"id_hiking is undefined")));}
$q = $mysqli->query("SELECT id FROM hiking WHERE id={$id_hiking}  AND id_author = {$id_author} LIMIT 1");
if($q && $q->num_rows===0){
	$q = $mysqli->query("SELECT id FROM hiking_editors WHERE id_hiking={$id_hiking}  AND is_financier=1  AND id_user = {$id_author} LIMIT 1");
	if($q && $q->num_rows===0){
		die(json_encode(array("error"=>"Нет доступа")));
	}
}

$ava_folder = 'receipts/'.$id_user;

$id_product = isset($_POST['id_product'])?intval($_POST['id_product']):'NULL';
$id_unit = isset($_POST['id_unit'])?intval($_POST['id_unit']):'NULL';
$id_receipt = isset($_POST['id_receipt'])?intval($_POST['id_receipt']):'NULL';
$weight = intval($_POST['weight']);
$amount = floatval($_POST['amount']);
$cost = floatval($_POST['cost']);
$name = isset($_POST['name'])?$mysqli->real_escape_string(trim($_POST['name'])):'';

$q = $mysqli->query("
INSERT INTO `hiking_finance` SET 
	`id_hiking`={$id_hiking},
	`id_user`={$id_user},
	`id_product`={$id_product},
	`id_unit`={$id_unit},
	`weight`={$weight},
	`amount`={$amount},
	`cost`={$cost},
	`id_receipt`={$id_receipt},
	`date`=NOW(),
	`id_author`={$id_author},
	`name`='{$name}'
");

if(!$q){exit(json_encode(array("error"=>"Ошибка при . \r\n")));}
echo(json_encode(array("success"=>true, "id"=>$mysqli->insert_id, "msg"=>"Данные успешно сохранены")));
?>