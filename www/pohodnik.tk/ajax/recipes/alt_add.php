<?php //recipes_product_add.php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();
$id_user = $_COOKIE["user"];
$id_rs = intval($_POST['id_rs']);
$id_product = intval($_POST['id_product']);
$amount = floatval($_POST['amount']);
if(!($id_rs>0)){die(json_encode(array("error"=>"Undefined ID")));}
if(!($id_product>0)){die(json_encode(array("error"=>"Undefined id_product")));}

$q = $mysqli->query("SELECT id FROM recipes_structure_alt WHERE id_rs={$id_rs} AND id_product={$id_product} LIMIT 1");
if($q && $q->num_rows === 1){ die(json_encode(array("error"=>"Уже добавлен.".$mysqli->error))); }	
if($mysqli->query("INSERT INTO `recipes_structure_alt` 
				   SET  id_rs={$id_rs}, id_product={$id_product}, amount={$amount}
")){
	die(json_encode(array("success"=>true, "id"=>$mysqli->insert_id)));
} else {
	die(json_encode(array("error"=>"Ошибка. ".$mysqli->error)));
}