<?php
include("../../blocks/db.php"); //подключение к БД
$result=array();
$id_user = $_COOKIE["user"];
$id = intval($_GET['id']);
$optimize = isset($_GET['optimize']);
$q = $mysqli->query("SELECT recipes.id, {$id_user}=recipes.id_author AS iauthor, recipes.name,recipes.name_opt, recipes.promo_text, recipes.text, recipes_categories.name AS category, CONCAT(users.name,' ',users.surname) AS author FROM `recipes` 
						LEFT JOIN recipes_categories ON recipes_categories.id = recipes.id_category
						LEFT JOIN users ON users.id = recipes.id_author
					 WHERE recipes.id = {$id} LIMIT 1
					");
if(!$q){die(json_encode(array("error"=>$mysqli->error)));}
if($q->num_rows===0){die(json_encode(array("error"=>"Нет рецепта с таким идентификатором")));}
$result = $q->fetch_assoc();


if($optimize == false ){
$q = $mysqli->query("SELECT 
						recipes_products.id AS id_product, 
						recipes_products.name, 
						recipes_products.protein/100*recipes_structure.amount AS protein, 
						recipes_products.fat/100*recipes_structure.amount AS fat, 
						recipes_products.carbohydrates/100*recipes_structure.amount AS carbohydrates, 
						recipes_products.energy/100*recipes_structure.amount AS energy, 
						recipes_products.weight, 
						recipes_products.cost, 
						recipes_structure.id,
						recipes_structure.amount
					FROM recipes_structure LEFT JOIN recipes_products ON recipes_products.id = recipes_structure.id_product 
					WHERE recipes_structure.id_recipe={$id} ORDER BY recipes_structure.amount DESC");
} else {

$q = $mysqli->query("SELECT DISTINCT

					IFNULL(rp1.id, IFNULL(rp2.id, recipes_products.id)) AS id_product,
					IFNULL(rp1.name, IFNULL(rp2.name, recipes_products.name)) AS name,
					IFNULL(rp1.protein/100*recipes_structure_alt.amount, IFNULL(rp2.protein/100*recipes_products_alt.weight, recipes_products.protein/100*recipes_structure.amount)) AS protein,
					IFNULL(rp1.fat/100*recipes_structure_alt.amount, IFNULL(rp2.fat/100*recipes_products_alt.weight, recipes_products.fat/100*recipes_structure.amount)) AS fat,
					IFNULL(rp1.carbohydrates/100*recipes_structure_alt.amount, IFNULL(rp2.carbohydrates/100*recipes_products_alt.weight, recipes_products.carbohydrates/100*recipes_structure.amount)) AS carbohydrates,
					IFNULL(rp1.energy/100*recipes_structure_alt.amount, IFNULL(rp2.energy/100*recipes_products_alt.weight, recipes_products.energy/100*recipes_structure.amount)) AS energy,

					IFNULL(recipes_structure_alt.amount, IFNULL(recipes_products_alt.weight, recipes_structure.amount)) AS amount

						
						
					FROM recipes_structure 
					
						LEFT JOIN recipes_products ON  recipes_structure.id_product = recipes_products.id  
						LEFT JOIN recipes_structure_alt ON recipes_structure_alt.id_rs = recipes_structure.id 
						LEFT JOIN recipes_products AS rp1 ON recipes_structure_alt.id_product = rp1.id
						LEFT JOIN recipes_products_alt ON recipes_products_alt.id_product = recipes_products.id 
						LEFT JOIN recipes_products AS rp2 ON recipes_products_alt.id_alt = rp2.id
						
						
					WHERE recipes_structure.id_recipe={$id} ORDER BY recipes_structure.amount DESC");

}
if(!$q ){ die(json_encode(array("error"=>$mysqli->error))); }
$result['products'] = array();
$result['summ'] = array("protein"=>0, "fat"=>0,"carbohydrates"=>0,"energy"=>0, "amount"=>0);
while($r = $q->fetch_assoc()){
	$result['summ']['protein']+= $r['protein'];
	$result['summ']['fat']+= $r['fat'];
	$result['summ']['carbohydrates']+= $r['carbohydrates'];
	$result['summ']['energy']+= $r['energy'];
$result['summ']['amount']+= $r['amount'];
	$result['products'][] = $r;
}



exit(json_encode($result));
?>