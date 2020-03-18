<?php //recipes_product_add.php
include("../../blocks/db.php"); //подключение к БД
$result = array();
$id_product = $_GET['id_product'];
$q = $mysqli->query("SELECT recipes_products_units.*, recipes_products_units_values.* FROM `recipes_products_units_values` LEFT JOIN  recipes_products_units ON recipes_products_units.id = recipes_products_units_values.id_unit WHERE id_product={$id_product}");
if($q){
	while( $r = $q->fetch_assoc() ){
		$result[] = $r;
	}
	die(json_encode( $result  ));
} else {
	die(json_encode(array("error"=>"Ошибка. ".$mysqli->error)));
}
		

