<?php //recipes_product_add.php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();
$id_user = $_COOKIE["user"];
$id_rs = intval($_GET['id_rs']);
if(!($id_rs>0)){die(json_encode(array("error"=>"Undefined ID")));}

$q = $mysqli->query("SELECT recipes_structure_alt.id, recipes_structure_alt.amount, recipes_products.name, recipes_products.protein, recipes_products.fat, recipes_products.carbohydrates, recipes_products.energy, recipes_products.weight, recipes_products.cost FROM recipes_structure_alt LEFT JOIN recipes_products ON recipes_products.id = recipes_structure_alt.id_product WHERE recipes_structure_alt.id_rs={$id_rs}");
if(!$q){die(json_encode(array("error"=>$mysqli->error)));}
while($r = $q->fetch_assoc()){
	$result[] = $r;
}
echo json_encode($result);