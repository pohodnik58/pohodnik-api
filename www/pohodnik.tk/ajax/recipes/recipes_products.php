<?php
include("../../blocks/db.php"); //подключение к БД

$result = array();
$id = intval($_GET['id']);
$q = $mysqli->query("SELECT 
						recipes_products.id AS id_product, 
						recipes_products.name, 
						recipes_products.protein, 
						recipes_products.fat, 
						recipes_products.carbohydrates, 
						recipes_products.energy, 
						recipes_products.weight, 
						recipes_products.cost, 
						recipes_structure.id,
						recipes_structure.amount
					
					FROM recipes_structure LEFT JOIN recipes_products ON recipes_products.id = recipes_structure.id_product  WHERE recipes_structure.id_recipe={$id}");
if(!$q || $q->num_rows === 0){ die(json_encode(array("error"=>$mysqli->error))); }
while($r = $q->fetch_assoc()){
	$r['kkal'] = ($r['energy']/100)*$r['amount'];
	$result[] = $r;
}
		
echo json_encode($result);