<?php //recipes_product_add.php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных

$id= intval( $_POST['id'] );					

if($mysqli->query("
					DELETE FROM `recipes_products_alt` WHERE `id`= {$id}
				")){
	die(json_encode(array("success"=>true, "id"=>$mysqli->insert_id)));
} else {
	die(json_encode(array("error"=>"Ошибка. ".$mysqli->error)));
}