<?php //recipes_product_add.php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();
$name = $mysqli->real_escape_string(trim($_POST['name']));
$short_name = $mysqli->real_escape_string(trim($_POST['short_name']));
$id_user = $_COOKIE["user"];
$q = $mysqli->query("SELECT id FROM recipes_products_units WHERE `name`='{$name}' LIMIT 1");
if($q && $q->num_rows === 1){ die(json_encode(array("error"=>"Уже есть с таким наименованием.".$mysqli->error, "code"=>1))); }	

$q = $mysqli->query("SELECT id FROM recipes_products_units WHERE `short_name`='{$short_name}' LIMIT 1");
if($q && $q->num_rows === 1){ die(json_encode(array("error"=>"Уже есть с таким сокращением.".$mysqli->error, "code"=>2))); }	

if($mysqli->query("INSERT INTO `recipes_products_units` 
					SET 
						`name`='{$name}',
						`short_name`='short_name'
				  ")){
	die(json_encode(array("success"=>true, "id"=>$mysqli->insert_id)));
} else {
	die(json_encode(array("error"=>"Ошибка. ".$mysqli->error)));
}
		

