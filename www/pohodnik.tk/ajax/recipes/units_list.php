<?php //recipes_product_add.php
include("../../blocks/db.php"); //подключение к БД
$result = array();
$q = $mysqli->query("SELECT `id`, `name`, `short_name` FROM `recipes_products_units` ORDER BY name");
if($q){
	while( $r = $q->fetch_assoc() ){
		$result[] = $r;
	}
	die(json_encode( $result  ));
} else {
	die(json_encode(array("error"=>"Ошибка. ".$mysqli->error)));
}
		

