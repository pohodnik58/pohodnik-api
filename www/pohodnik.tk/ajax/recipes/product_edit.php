<?php //recipes_product_add.php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();
$id = intval($_POST['id']);
$name = $mysqli->real_escape_string(trim($_POST['name']));
$protein = floatval($_POST['protein']);
$fat = floatval($_POST['fat']);
$carbohydrates = floatval($_POST['carbohydrates']);
$energy = floatval($_POST['energy']);
$id_user = $_COOKIE["user"];
$q = $mysqli->query("SELECT id FROM recipes_products WHERE `name`='{$name}' AND id<>{$id} LIMIT 1");
if($q && $q->num_rows === 1){ die(json_encode(array("error"=>"Уже есть продукт с таким наименованием.".$mysqli->error))); }	
if($mysqli->query("UPDATE `recipes_products` 
					SET `name`='{$name}',
						`protein`={$protein},`fat`={$fat},`carbohydrates`={$carbohydrates},
						`energy`={$energy}
					WHERE id={$id}")){
	die(json_encode(array("success"=>true)));
} else {
	die(json_encode(array("error"=>"Ошибка. ".$mysqli->error)));
}