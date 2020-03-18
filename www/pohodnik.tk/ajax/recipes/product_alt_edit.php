<?php //recipes_product_add.php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных

				
$id	= intval( $_POST['id'] );			
$weight		= intval( $_POST['weight'] );
$id_user = $_COOKIE["user"];

if($mysqli->query("
					UPDATE `recipes_products_alt` SET  `weight`= {$weight} WHERE id={$id}
				")){
	die(json_encode(array("success"=>true)));
} else {
	die(json_encode(array("error"=>"Ошибка. ".$mysqli->error)));
}