<?php

include("../../../blocks/db.php"); //подключение к БД
include("../../../blocks/for_auth.php"); //Только для авторизованных
$id_user = $_COOKIE["user"];

$wh = "";
if(isset($_GET['id_hiking'])){
	$id_hiking = intval($_GET['id_hiking']);
	$wh .= " AND hiking_finance.id_hiking={$id_hiking}";
}

if(isset($_GET['id_receipt'])){
	$id_receipt = intval($_GET['id_receipt']);
	$wh .= " AND hiking_finance.id_receipt={$id_receipt}";
}

if(isset($_GET['my'])){
	$wh .= " AND hiking_finance.id_user = {$id_user}";
}





//`id`, `id_hiking`, `id_user`, `id_product`, `id_unit`, `weight`, `cost`, `id_receipt`, `date`, `is_confirm`, `id_author`
$q = $mysqli->query("SELECT hiking_finance_receipt.*, recipes_products.*, recipes_products.name AS product_name, hiking_finance.*, (hiking_finance.id_user = {$id_user}) AS my, recipes_products_units.name AS unit_name, recipes_products_units.short_name AS unit_short_name
	FROM `hiking_finance` 
		LEFT JOIN recipes_products ON recipes_products.id = hiking_finance.id_product
		LEFT JOIN hiking_finance_receipt ON hiking_finance_receipt.id = hiking_finance.id_receipt
		LEFT JOIN recipes_products_units ON recipes_products_units.id = hiking_finance.id_unit
	WHERE 1 {$wh}");

if(!$q){exit(json_encode(array("error"=>"Ошибка при . \r\n")));}
$res = array();
while($r = $q->fetch_assoc()){
	$r['access'] = $r['id_author'] == $id_user;
	$res[]=$r;
}


echo(json_encode($res));
?>