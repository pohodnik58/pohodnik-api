<?php //recipes_product_add.php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();
$id_user = $_COOKIE["user"];
$id = intval($_POST['id']);
if(!($id>0)){die(json_encode(array("error"=>"Undefined ID")));}
	
if($mysqli->query("DELETE FROM `recipes_structure_alt` WHERE id={$id}")){
	die(json_encode(array("success"=>true)));
} else {
	die(json_encode(array("error"=>"Ошибка. ".$mysqli->error)));
}