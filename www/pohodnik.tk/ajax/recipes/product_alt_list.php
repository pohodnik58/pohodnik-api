<?php //recipes_product_add.php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных

$id_product	= intval( $_GET['id_product'] );					

$q = $mysqli->query("
					SELECT recipes_products.*,  recipes_products_alt.*
					FROM recipes_products_alt 
					LEFT JOIN recipes_products ON recipes_products_alt.id_alt = recipes_products.id 
					WHERE recipes_products_alt.id_product={$id_product}
				");
if($q){
	$res = array();
	while($r = $q->fetch_assoc()){
		$res[] = $r;
	}
	die(json_encode($res));
} else {
	die(json_encode(array("error"=>"Ошибка. ".$mysqli->error)));
}