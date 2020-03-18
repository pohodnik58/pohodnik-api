<?php //recipes_product_add.php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$id	= intval( $_POST['id'] );
$id_product	= intval( $_POST['id_product'] );					
$id_unut	= intval( $_POST['id_unut'] );			
$weight		= intval( $_POST['weight'] );			
$value		= intval( $_POST['value'] );	
$cost		= intval( $_POST['cost'] );	
$note 		= $mysqli->real_escape_string(trim($_POST['note']));

$id_user = $_COOKIE["user"];
if($mysqli->query("
					UPDATE  `recipes_products_units_values` SET 
						`id_product`={$id_product},
						`id_unit`={$id_unut},
						`weight`= {$weight},
						`value`={$value},
						`cost`= {$cost},
						`note`='{$note}',
						`id_author` = {$id_user}
					WHERE id={$id}
				")){
	die(json_encode(array("success"=>true)));
} else {
	die(json_encode(array("error"=>"Ошибка. ".$mysqli->error)));
}
		

