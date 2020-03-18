<?php //recipes_product_add.php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();
$id_user = $_COOKIE["user"];
$id = intval($_POST['id']);
$amount = floatval($_POST['amount']);
if(!($id>0)){die(json_encode(array("error"=>"Undefined ID")));}
	
if($mysqli->query("UPDATE `recipes_structure_alt` SET amount={$amount} WHERE id={$id}")){
	die(json_encode(array("success"=>true)));
} else {
	die(json_encode(array("error"=>"Ошибка. ".$mysqli->error)));
}