<?php //recipes_product_add.php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных

$id_product	= intval( $_POST['id_product'] );					
$id_alt	= intval( $_POST['id_alt'] );			
$weight		= intval( $_POST['weight'] );
$id_user = $_COOKIE["user"];

if($mysqli->query("
					INSERT INTO `recipes_products_alt` SET 
						`id_product`={$id_product},
						`id_alt`={$id_alt},
						`weight`= {$weight}
				")){
	die(json_encode(array("success"=>true, "id"=>$mysqli->insert_id)));
} else {
	die(json_encode(array("error"=>"Ошибка. ".$mysqli->error)));
}