<?php //recipes_product_add.php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$id	= intval( $_POST['id'] );
$id_user = $_COOKIE["user"];
if($mysqli->query("
					DELETE FROM `recipes_products_units_values` WHERE id={$id}
				")){
	die(json_encode(array("success"=>true)));
} else {
	die(json_encode(array("error"=>"Ошибка. ".$mysqli->error)));
}
		

